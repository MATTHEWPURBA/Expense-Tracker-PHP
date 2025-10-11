<?php
/**
 * Logout Entry Point
 * 
 * Handles user logout using the new micro-architecture
 * 
 * @package ExpenseTracker
 */

require_once __DIR__ . '/bootstrap.php';

use ExpenseTracker\Controllers\AuthController;

$authController = new AuthController();
$authController->logout();

