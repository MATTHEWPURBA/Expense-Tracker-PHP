<?php
/**
 * Login Logger Service
 * 
 * Comprehensive logging for login attempts and failures
 * 
 * @package ExpenseTracker\Services
 */

namespace ExpenseTracker\Services;

class LoginLogger
{
    private static $logFile;
    
    public static function init(): void
    {
        self::$logFile = __DIR__ . '/../../logs/login_attempts.log';
        
        // Ensure logs directory exists
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    /**
     * Log login attempt
     */
    public static function logAttempt(string $username, string $ip, string $userAgent, bool $success, ?string $error = null): void
    {
        self::init();
        
        $timestamp = date('Y-m-d H:i:s');
        $status = $success ? 'SUCCESS' : 'FAILED';
        $errorInfo = $error ? " | Error: $error" : '';
        
        $logEntry = "[$timestamp] $status | Username: $username | IP: $ip | UA: " . substr($userAgent, 0, 100) . $errorInfo . PHP_EOL;
        
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log database query issues
     */
    public static function logDatabaseIssue(string $operation, string $error, array $context = []): void
    {
        self::init();
        
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? " | Context: " . json_encode($context) : '';
        
        $logEntry = "[$timestamp] DATABASE_ERROR | Operation: $operation | Error: $error$contextStr" . PHP_EOL;
        
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Get recent login attempts
     */
    public static function getRecentAttempts(int $limit = 50): array
    {
        self::init();
        
        if (!file_exists(self::$logFile)) {
            return [];
        }
        
        $lines = file(self::$logFile, FILE_IGNORE_NEW_LINES);
        $lines = array_reverse(array_slice($lines, -$limit));
        
        $attempts = [];
        foreach ($lines as $line) {
            if (preg_match('/\[([^\]]+)\] (\w+) \| Username: ([^|]+) \| IP: ([^|]+) \| UA: ([^|]+)(?:\s*\|\s*Error:\s*(.+))?/', $line, $matches)) {
                $attempts[] = [
                    'timestamp' => $matches[1],
                    'status' => $matches[2],
                    'username' => $matches[3],
                    'ip' => $matches[4],
                    'user_agent' => $matches[5],
                    'error' => $matches[6] ?? null
                ];
            }
        }
        
        return $attempts;
    }
    
    /**
     * Clear old logs (keep last 1000 entries)
     */
    public static function cleanOldLogs(): void
    {
        self::init();
        
        if (!file_exists(self::$logFile)) {
            return;
        }
        
        $lines = file(self::$logFile, FILE_IGNORE_NEW_LINES);
        
        if (count($lines) > 1000) {
            $keepLines = array_slice($lines, -1000);
            file_put_contents(self::$logFile, implode(PHP_EOL, $keepLines) . PHP_EOL);
        }
    }
}
