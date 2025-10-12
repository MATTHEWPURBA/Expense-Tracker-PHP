<?php
/**
 * Signup Entry Point
 * 
 * User registration page using the new micro-architecture
 * 
 * @package ExpenseTracker
 */

require_once __DIR__ . '/bootstrap.php';

use ExpenseTracker\Services\Auth;
use ExpenseTracker\Middleware\AuthMiddleware;
use ExpenseTracker\Services\Currency;

// Redirect if already logged in
$authMiddleware = new AuthMiddleware();
$authMiddleware->handleGuest();

$error = '';
$success = '';
$currencies = Currency::getAll();
$defaultCurrency = Currency::detectUserCurrency();

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = Auth::sanitize($_POST['username'] ?? '');
    $email = Auth::sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $currency = strtoupper($_POST['currency'] ?? 'USD');
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        $result = Auth::register($name, $email, $password, $currency);
        
        if ($result['success']) {
            $success = 'Registration successful! Redirecting to login...';
            header('Refresh: 2; URL=/login.php');
        } else {
            $error = $result['error'];
        }
    }
}

// Render signup view
require_once __DIR__ . '/views/auth/signup.php';

