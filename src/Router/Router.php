<?php
/**
 * API Router
 * 
 * Handles API routing with full debugging and tracking capabilities
 * 
 * @package ExpenseTracker\Router
 */

namespace ExpenseTracker\Router;

use Exception;
use ExpenseTracker\Services\RequestLogger;

class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private array $publicRoutes = []; // Routes that don't require authentication
    private string $basePath = '';
    private array $debugInfo = [];
    private $logger;
    
    /**
     * Add a GET route
     */
    public function get(string $path, callable|array $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }
    
    /**
     * Add a POST route
     */
    public function post(string $path, callable|array $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }
    
    /**
     * Add a PUT route
     */
    public function put(string $path, callable|array $handler): self
    {
        return $this->addRoute('PUT', $path, $handler);
    }
    
    /**
     * Add a PATCH route
     */
    public function patch(string $path, callable|array $handler): self
    {
        return $this->addRoute('PATCH', $path, $handler);
    }
    
    /**
     * Add a DELETE route
     */
    public function delete(string $path, callable|array $handler): self
    {
        return $this->addRoute('DELETE', $path, $handler);
    }
    
    /**
     * Add middleware
     */
    public function middleware(callable $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }
    
    /**
     * Add a route to the routing table
     */
    private function addRoute(string $method, string $path, callable|array $handler): self
    {
        $fullPath = $this->basePath . $path;
        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'handler' => $handler,
            'pattern' => $this->convertToPattern($fullPath)
        ];
        
        return $this;
    }
    
    /**
     * Mark a route as public (no authentication required)
     */
    public function publicRoute(string $method, string $path): self
    {
        $this->publicRoutes[] = $method . ' ' . $this->basePath . $path;
        return $this;
    }
    
    /**
     * Check if a route is public
     */
    private function isPublicRoute(string $method, string $path): bool
    {
        $routeKey = $method . ' ' . $path;
        return in_array($routeKey, $this->publicRoutes);
    }
    
    /**
     * Convert path with parameters to regex pattern
     */
    private function convertToPattern(string $path): string
    {
        // Convert {param} to named capture groups
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    /**
     * Dispatch the request
     */
    public function dispatch(): void
    {
        $this->logger = RequestLogger::getInstance();
        
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Get path from REQUEST_URI (may have been modified by physical API files)
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        
        // Handle different routing scenarios:
        // 1. Physical API files (e.g., /api/auth/login.php) - REQUEST_URI already set correctly
        // 2. Rewritten requests via .htaccess (e.g., /api/auth/login -> api.php)
        // 3. Direct api.php access
        
        // If REQUEST_URI was modified by a physical API file, use it directly
        if (isset($_SERVER['ORIGINAL_REQUEST_URI'])) {
            // REQUEST_URI was already set correctly by the physical API file
            // No modification needed
        } 
        // If accessing api.php directly or via rewrite, use REQUEST_URI as-is
        elseif (basename($scriptName) === 'api.php' || strpos($path, '/api/') === 0) {
            // Keep the full path including /api/ prefix
            // Path is already correct from REQUEST_URI
        } 
        // For other direct file access, remove script directory from path
        else {
            $scriptDir = dirname($scriptName);
            if ($scriptDir !== '/' && $scriptDir !== '.') {
                $path = str_replace($scriptDir, '', $path);
            }
        }
        
        // Normalize path (remove leading/trailing slashes except root)
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }
        
        // Store debug info
        $this->debugInfo['request'] = [
            'method' => $method,
            'path' => $path,
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        // Log request
        $requestId = $this->debugInfo['request']['timestamp'] . '_' . uniqid();
        $this->logger->logRequest($this->debugInfo['request']);
        
        $startTime = microtime(true);
        
        try {
            // Find matching route
            $route = $this->findRoute($method, $path);
            
            if (!$route) {
                $this->handleNotFound($path, $method);
                return;
            }
            
            // Extract route parameters
            preg_match($route['pattern'], $path, $matches);
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            
            // Store route info for debugging
            $this->debugInfo['route'] = [
                'method' => $method,
                'path' => $route['path'],
                'params' => $params
            ];
            
            // Run middlewares only if route is not public
            if (!$this->isPublicRoute($method, $route['path'])) {
                foreach ($this->middlewares as $middleware) {
                    $middleware();
                }
            }
            
            // Execute handler
            $handler = $route['handler'];
            
            if (is_array($handler)) {
                [$controller, $method] = $handler;
                
                // Store controller info
                $this->debugInfo['controller'] = [
                    'class' => is_string($controller) ? $controller : get_class($controller),
                    'method' => $method
                ];
                
                if (is_string($controller)) {
                    $controller = new $controller();
                }
                
                $result = $controller->$method($params);
            } else {
                $this->debugInfo['controller'] = [
                    'type' => 'closure'
                ];
                $result = $handler($params);
            }
            
            // Calculate execution time
            $executionTime = microtime(true) - $startTime;
            $this->debugInfo['performance'] = [
                'execution_time' => round($executionTime * 1000, 2) . 'ms'
            ];
            
            // If result is ApiResponse, add debug info and send
            if ($result instanceof ApiResponse) {
                $result->setDebugInfo($this->debugInfo);
                
                // Log response
                $statusCode = 200; // ApiResponse will set actual status
                $this->logger->logResponse($requestId, $result->toArray(), $statusCode);
                
                $result->send();
            } elseif (is_array($result)) {
                // Convert array to ApiResponse
                $response = new ApiResponse($result);
                $response->setDebugInfo($this->debugInfo);
                
                // Log response
                $this->logger->logResponse($requestId, $result, 200);
                
                $response->send();
            } else {
                // Direct output
                echo $result;
            }
            
        } catch (Exception $e) {
            // Log error
            $this->logger->logError($e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->handleError($e, microtime(true) - $startTime);
        }
    }
    
    /**
     * Find matching route
     */
    private function findRoute(string $method, string $path): ?array
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path)) {
                return $route;
            }
        }
        return null;
    }
    
    /**
     * Handle 404 Not Found
     */
    private function handleNotFound(string $path, string $method): void
    {
        http_response_code(404);
        header('Content-Type: application/json');
        
        $availableRoutes = [];
        foreach ($this->routes as $route) {
            $availableRoutes[] = $route['method'] . ' ' . $route['path'];
        }
        
        echo json_encode([
            'success' => false,
            'error' => 'Route not found',
            'debug' => [
                'requested' => $method . ' ' . $path,
                'available_routes' => $availableRoutes,
                'request' => $this->debugInfo['request']
            ]
        ], JSON_PRETTY_PRINT);
    }
    
    /**
     * Handle errors
     */
    private function handleError(Exception $e, float $executionTime): void
    {
        http_response_code(500);
        header('Content-Type: application/json');
        
        error_log("API Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
        
        $errorMessage = $e->getMessage();
        
        // Provide helpful hints for database connection errors
        $hint = null;
        if (strpos($errorMessage, 'Database connection') !== false) {
            $hint = 'Check your database configuration in config.php. Verify database credentials and that the PostgreSQL extension is installed.';
        } elseif (strpos($errorMessage, 'PostgreSQL extension') !== false) {
            $hint = 'The PostgreSQL extension (pgsql or pdo_pgsql) is not installed on this server. Contact your hosting provider.';
        } elseif (strpos($errorMessage, 'Cannot connect to database server') !== false) {
            $hint = 'Cannot reach the database server. Check network connectivity, host/port settings, and firewall rules.';
        } elseif (strpos($errorMessage, 'authentication failed') !== false) {
            $hint = 'Database authentication failed. Check username and password in config.php.';
        } elseif (strpos($errorMessage, 'does not exist') !== false) {
            $hint = 'Database does not exist. Check database name in config.php.';
        }
        
        $errorResponse = [
            'success' => false,
            'error' => $errorMessage,
            'debug' => array_merge($this->debugInfo, [
                'error' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => explode("\n", $e->getTraceAsString())
                ],
                'performance' => [
                    'execution_time' => round($executionTime * 1000, 2) . 'ms'
                ]
            ])
        ];
        
        if ($hint) {
            $errorResponse['hint'] = $hint;
        }
        
        echo json_encode($errorResponse, JSON_PRETTY_PRINT);
    }
    
    /**
     * Get all registered routes
     */
    public function getRoutes(): array
    {
        $routes = [];
        foreach ($this->routes as $route) {
            $routes[] = [
                'method' => $route['method'],
                'path' => $route['path'],
                'handler' => is_array($route['handler']) 
                    ? (is_string($route['handler'][0]) ? $route['handler'][0] : get_class($route['handler'][0])) . '@' . $route['handler'][1]
                    : 'Closure'
            ];
        }
        return $routes;
    }
}

