<?php
/**
 * Logout Handler
 */

// Error handling
error_reporting(0);
ini_set('display_errors', 0);

// Load configuration
$config_file = __DIR__ . '/config.php';
if (!file_exists($config_file)) {
    die('⚠️ Configuration file not found!');
}

$config = require_once $config_file;

// Database Configuration
define('DB_HOST', $config['db_host']);
define('DB_PORT', $config['db_port']);
define('DB_NAME', $config['db_name']);
define('DB_USER', $config['db_user']);
define('DB_PASS', $config['db_pass']);
define('DB_SSL', $config['db_ssl']);

// Database Connection
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=" . DB_SSL;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    return $pdo;
}

// Load authentication functions
require_once __DIR__ . '/auth.php';

// Logout user
logoutUser();

// Redirect to login page with success message
header('Location: login.php?logout=1');
exit;

