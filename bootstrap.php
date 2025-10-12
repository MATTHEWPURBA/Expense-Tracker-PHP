<?php
/**
 * Application Bootstrap
 * 
 * This file initializes the application, sets up autoloading,
 * and configures the environment.
 * 
 * @package ExpenseTracker
 * @version 1.0.0
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error handling configuration
error_reporting(E_ALL);
ini_set('display_errors', '0'); // Disable in production
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/logs/error.log');

// Set timezone
date_default_timezone_set('UTC');

// Define application paths
define('APP_ROOT', __DIR__);
define('SRC_PATH', APP_ROOT . '/src');
define('VIEWS_PATH', APP_ROOT . '/views');
define('DATA_PATH', APP_ROOT . '/data');
define('LOGS_PATH', APP_ROOT . '/logs');

// Create necessary directories
$directories = [DATA_PATH, LOGS_PATH];
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Load Composer autoloader if exists
if (file_exists(APP_ROOT . '/vendor/autoload.php')) {
    require_once APP_ROOT . '/vendor/autoload.php';
}

// Simple PSR-4 autoloader
spl_autoload_register(function ($class) {
    // Project namespace prefix
    $prefix = 'ExpenseTracker\\';
    
    // Base directory for the namespace prefix
    $base_dir = SRC_PATH . '/';
    
    // Does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // No, move to the next registered autoloader
        return;
    }
    
    // Get the relative class name
    $relative_class = substr($class, $len);
    
    // Replace namespace separators with directory separators
    // and append .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // If the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Load configuration
$config_file = APP_ROOT . '/config.php';
if (!file_exists($config_file)) {
    http_response_code(500);
    die('⚠️ Configuration file not found!<br><br>
         Please follow these steps:<br>
         1. Copy <code>config.example.php</code> to <code>config.php</code><br>
         2. Edit <code>config.php</code> with your database credentials<br>
         3. Refresh this page<br><br>
         See <a href="GET_STARTED.md">GET_STARTED.md</a> for detailed instructions.');
}

$config = require_once $config_file;

// Store config in a global accessible way
$GLOBALS['app_config'] = $config;

// Load Gemini API key from config for AI features
if (isset($config['gemini_api_key']) && !empty($config['gemini_api_key'])) {
    putenv('GEMINI_API_KEY=' . $config['gemini_api_key']);
}

// Initialize database (create tables if needed)
use ExpenseTracker\Services\Database;

try {
    $db = Database::getInstance();
    $db->initializeTables();
} catch (Exception $e) {
    error_log("Database initialization failed: " . $e->getMessage());
    if (ini_get('display_errors')) {
        die("Database connection failed. Please check your configuration.");
    }
}

// Helper function to get config
function config($key, $default = null) {
    return $GLOBALS['app_config'][$key] ?? $default;
}

// Helper function to redirect
function redirect($url, $statusCode = 302) {
    header("Location: $url", true, $statusCode);
    exit;
}

// Helper function to render view
function view($viewPath, $data = []) {
    extract($data);
    $viewFile = VIEWS_PATH . '/' . str_replace('.', '/', $viewPath) . '.php';
    
    if (!file_exists($viewFile)) {
        throw new Exception("View not found: $viewPath");
    }
    
    require $viewFile;
}

// Helper function to return JSON response
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

