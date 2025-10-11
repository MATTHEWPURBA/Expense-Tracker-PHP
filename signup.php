<?php
/**
 * Signup Entry Point
 * 
 * User registration page using the new micro-architecture
 * 
 * @package ExpenseTracker
 */

require_once __DIR__ . '/bootstrap.php';

use ExpenseTracker\Controllers\AuthController;
use ExpenseTracker\Middleware\AuthMiddleware;
use ExpenseTracker\Services\Currency;

// Redirect if already logged in
$authMiddleware = new AuthMiddleware();
$authMiddleware->handleGuest();

$authController = new AuthController();
$error = '';
$success = '';
$currencies = Currency::getAll();
$defaultCurrency = Currency::detectUserCurrency();

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $authController->register();
    
    if ($result['success']) {
        $success = 'Registration successful! Redirecting to login...';
        header('Refresh: 2; URL=/login.php');
    } else {
        $error = $result['error'];
    }
}

// Render signup view
require_once __DIR__ . '/views/auth/signup.php';

