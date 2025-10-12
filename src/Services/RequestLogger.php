<?php
/**
 * Request Logger Service
 * 
 * Logs all API requests and responses for debugging and auditing
 * 
 * @package ExpenseTracker\Services
 */

namespace ExpenseTracker\Services;

class RequestLogger
{
    private static $instance = null;
    private $logFile;
    private $enabled;
    private $logLevel;
    private $requests = [];
    
    private function __construct()
    {
        $this->logFile = LOGS_PATH . '/api-requests.log';
        $this->enabled = defined('LOG_API_REQUESTS') ? LOG_API_REQUESTS : true;
        $this->logLevel = defined('LOG_LEVEL') ? LOG_LEVEL : 'INFO'; // DEBUG, INFO, WARNING, ERROR
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Log API request
     */
    public function logRequest(array $data): void
    {
        if (!$this->enabled) {
            return;
        }
        
        $requestId = $this->generateRequestId();
        $timestamp = date('Y-m-d H:i:s');
        
        $logEntry = [
            'id' => $requestId,
            'timestamp' => $timestamp,
            'type' => 'REQUEST',
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
            'path' => $_SERVER['REQUEST_URI'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'user_id' => $_SESSION['user']['id'] ?? null,
            'data' => $data
        ];
        
        $this->requests[$requestId] = $logEntry;
        $this->writeToFile($logEntry);
    }
    
    /**
     * Log API response
     */
    public function logResponse(string $requestId, array $data, int $statusCode): void
    {
        if (!$this->enabled) {
            return;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        
        $logEntry = [
            'id' => $this->generateRequestId(),
            'request_id' => $requestId,
            'timestamp' => $timestamp,
            'type' => 'RESPONSE',
            'status_code' => $statusCode,
            'data' => $data
        ];
        
        $this->writeToFile($logEntry);
    }
    
    /**
     * Log error
     */
    public function logError(string $message, array $context = []): void
    {
        if (!$this->enabled) {
            return;
        }
        
        $logEntry = [
            'id' => $this->generateRequestId(),
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => 'ERROR',
            'message' => $message,
            'context' => $context,
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
            'path' => $_SERVER['REQUEST_URI'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        $this->writeToFile($logEntry);
    }
    
    /**
     * Write log entry to file
     */
    private function writeToFile(array $entry): void
    {
        try {
            // Format log entry
            $formatted = sprintf(
                "[%s] [%s] %s\n%s\n%s\n",
                $entry['timestamp'],
                $entry['type'],
                $entry['id'] ?? '',
                json_encode($entry, JSON_PRETTY_PRINT),
                str_repeat('-', 80)
            );
            
            // Append to log file
            file_put_contents($this->logFile, $formatted, FILE_APPEND | LOCK_EX);
            
            // Rotate log if too large (> 10MB)
            if (file_exists($this->logFile) && filesize($this->logFile) > 10 * 1024 * 1024) {
                $this->rotateLog();
            }
        } catch (\Exception $e) {
            error_log("Failed to write to API log: " . $e->getMessage());
        }
    }
    
    /**
     * Rotate log file
     */
    private function rotateLog(): void
    {
        $timestamp = date('Y-m-d_His');
        $archiveFile = LOGS_PATH . "/api-requests-{$timestamp}.log";
        
        if (file_exists($this->logFile)) {
            rename($this->logFile, $archiveFile);
            
            // Compress old log
            if (function_exists('gzencode')) {
                $content = file_get_contents($archiveFile);
                file_put_contents($archiveFile . '.gz', gzencode($content, 9));
                unlink($archiveFile);
            }
        }
    }
    
    /**
     * Generate unique request ID
     */
    private function generateRequestId(): string
    {
        return sprintf(
            'req_%s_%s',
            date('YmdHis'),
            substr(md5(uniqid(mt_rand(), true)), 0, 8)
        );
    }
    
    /**
     * Get recent logs
     */
    public function getRecentLogs(int $limit = 100): array
    {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        try {
            $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $entries = [];
            $currentEntry = '';
            
            foreach (array_reverse($lines) as $line) {
                if (strpos($line, str_repeat('-', 80)) !== false) {
                    if (!empty($currentEntry)) {
                        // Extract JSON from entry
                        preg_match('/\{[\s\S]*\}/', $currentEntry, $matches);
                        if (isset($matches[0])) {
                            $data = json_decode($matches[0], true);
                            if ($data) {
                                $entries[] = $data;
                            }
                        }
                        $currentEntry = '';
                        
                        if (count($entries) >= $limit) {
                            break;
                        }
                    }
                } else {
                    $currentEntry = $line . "\n" . $currentEntry;
                }
            }
            
            return $entries;
        } catch (\Exception $e) {
            error_log("Failed to read API log: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Clear logs
     */
    public function clearLogs(): bool
    {
        try {
            if (file_exists($this->logFile)) {
                return unlink($this->logFile);
            }
            return true;
        } catch (\Exception $e) {
            error_log("Failed to clear API log: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get log statistics
     */
    public function getStatistics(): array
    {
        $logs = $this->getRecentLogs(1000);
        
        $stats = [
            'total_requests' => 0,
            'total_errors' => 0,
            'methods' => [],
            'status_codes' => [],
            'endpoints' => [],
            'users' => []
        ];
        
        foreach ($logs as $log) {
            if ($log['type'] === 'REQUEST') {
                $stats['total_requests']++;
                
                $method = $log['method'] ?? 'UNKNOWN';
                $stats['methods'][$method] = ($stats['methods'][$method] ?? 0) + 1;
                
                $path = $log['path'] ?? 'unknown';
                $stats['endpoints'][$path] = ($stats['endpoints'][$path] ?? 0) + 1;
                
                if (isset($log['user_id'])) {
                    $stats['users'][$log['user_id']] = ($stats['users'][$log['user_id']] ?? 0) + 1;
                }
            } elseif ($log['type'] === 'ERROR') {
                $stats['total_errors']++;
            } elseif ($log['type'] === 'RESPONSE') {
                $statusCode = $log['status_code'] ?? 0;
                $stats['status_codes'][$statusCode] = ($stats['status_codes'][$statusCode] ?? 0) + 1;
            }
        }
        
        return $stats;
    }
    
    /**
     * Enable logging
     */
    public function enable(): void
    {
        $this->enabled = true;
    }
    
    /**
     * Disable logging
     */
    public function disable(): void
    {
        $this->enabled = false;
    }
    
    /**
     * Check if logging is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}

