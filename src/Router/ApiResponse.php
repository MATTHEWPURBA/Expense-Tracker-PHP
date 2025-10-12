<?php
/**
 * API Response
 * 
 * Standardized API response with debugging metadata
 * 
 * @package ExpenseTracker\Router
 */

namespace ExpenseTracker\Router;

class ApiResponse
{
    private array $data;
    private int $statusCode;
    private array $headers = [];
    private array $debugInfo = [];
    private array $queries = [];
    
    public function __construct(array $data = [], int $statusCode = 200)
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
        $this->headers['Content-Type'] = 'application/json';
    }
    
    /**
     * Create success response
     */
    public static function success(array $data = [], string $message = null): self
    {
        $response = [
            'success' => true,
            'data' => $data
        ];
        
        if ($message) {
            $response['message'] = $message;
        }
        
        return new self($response, 200);
    }
    
    /**
     * Create error response
     */
    public static function error(string $message, int $statusCode = 400, array $errors = []): self
    {
        $response = [
            'success' => false,
            'error' => $message
        ];
        
        if (!empty($errors)) {
            $response['errors'] = $errors;
        }
        
        return new self($response, $statusCode);
    }
    
    /**
     * Create created response
     */
    public static function created(array $data = [], string $message = 'Resource created successfully'): self
    {
        return new self([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], 201);
    }
    
    /**
     * Create no content response
     */
    public static function noContent(): self
    {
        return new self([], 204);
    }
    
    /**
     * Add header
     */
    public function withHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }
    
    /**
     * Set debug info
     */
    public function setDebugInfo(array $debugInfo): self
    {
        $this->debugInfo = $debugInfo;
        return $this;
    }
    
    /**
     * Add SQL query info
     */
    public function addQuery(string $query, array $params = [], float $executionTime = 0): self
    {
        $this->queries[] = [
            'query' => $query,
            'params' => $params,
            'execution_time' => round($executionTime * 1000, 2) . 'ms'
        ];
        return $this;
    }
    
    /**
     * Set queries from model
     */
    public function setQueries(array $queries): self
    {
        $this->queries = $queries;
        return $this;
    }
    
    /**
     * Send the response
     */
    public function send(): void
    {
        // Set status code
        http_response_code($this->statusCode);
        
        // Set headers
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        
        // Build response with debug info
        $response = $this->data;
        
        // Add debug information if in debug mode or development
        if ($this->shouldIncludeDebug()) {
            $response['_debug'] = $this->buildDebugInfo();
        }
        
        // Output JSON
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Check if debug info should be included
     */
    private function shouldIncludeDebug(): bool
    {
        // Include debug info if:
        // 1. Debug mode is enabled in config
        // 2. Or X-Debug header is present
        // 3. Or debug query parameter is present
        
        $debugEnabled = defined('DEBUG_MODE') && DEBUG_MODE;
        $debugHeader = isset($_SERVER['HTTP_X_DEBUG']) && $_SERVER['HTTP_X_DEBUG'] === 'true';
        $debugQuery = isset($_GET['debug']) && $_GET['debug'] === 'true';
        
        return $debugEnabled || $debugHeader || $debugQuery;
    }
    
    /**
     * Build debug information
     */
    private function buildDebugInfo(): array
    {
        $debug = array_merge($this->debugInfo, [
            'queries' => [
                'count' => count($this->queries),
                'list' => $this->queries
            ],
            'memory' => [
                'usage' => $this->formatBytes(memory_get_usage()),
                'peak' => $this->formatBytes(memory_get_peak_usage())
            ]
        ]);
        
        return $debug;
    }
    
    /**
     * Format bytes to human readable
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return $this->data;
    }
}

