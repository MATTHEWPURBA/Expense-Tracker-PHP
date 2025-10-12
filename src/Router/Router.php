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
        $this->routes[] = [
            'method' => $method,
            'path' => $this->basePath . $path,
            'handler' => $handler,
            'pattern' => $this->convertToPattern($this->basePath . $path)
        ];
        
        return $this;
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
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove script name from path if present
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $scriptDir = dirname($scriptName);
        if ($scriptDir !== '/') {
            $path = str_replace($scriptDir, '', $path);
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
            
            // Run middlewares
            foreach ($this->middlewares as $middleware) {
                $middleware();
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
        
        $errorResponse = [
            'success' => false,
            'error' => $e->getMessage(),
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

