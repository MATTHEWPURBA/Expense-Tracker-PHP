<?php
/**
 * Login Entry Point
 * 
 * User login page using the new micro-architecture
 * 
 * @package ExpenseTracker
 */

require_once __DIR__ . '/bootstrap.php';

use ExpenseTracker\Controllers\AuthController;
use ExpenseTracker\Middleware\AuthMiddleware;

// Redirect if already logged in
$authMiddleware = new AuthMiddleware();
$authMiddleware->handleGuest();

$authController = new AuthController();
$error = '';
$success = '';

// Check for logout message
if (isset($_GET['logout'])) {
    $success = 'You have been logged out successfully';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $authController->login();
    
    if ($result['success']) {
        header('Location: /index.php');
        exit;
    } else {
        $error = $result['error'];
    }
}

// Render login view
require_once __DIR__ . '/views/auth/login.php';

