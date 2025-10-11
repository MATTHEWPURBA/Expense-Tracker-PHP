<?php
/**
 * Authentication Controller
 * 
 * Handles user authentication operations
 * 
 * @package ExpenseTracker\Controllers
 */

namespace ExpenseTracker\Controllers;

use ExpenseTracker\Services\Auth;

class AuthController
{
    /**
     * Handle user login
     */
    public function login(): array
    {
        $usernameOrEmail = Auth::sanitize($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($usernameOrEmail) || empty($password)) {
            return [
                'success' => false,
                'error' => 'Please enter both username/email and password'
            ];
        }
        
        return Auth::login($usernameOrEmail, $password);
    }
    
    /**
     * Handle user registration
     */
    public function register(): array
    {
        $name = Auth::sanitize($_POST['username'] ?? '');
        $email = Auth::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $currency = strtoupper($_POST['currency'] ?? 'USD');
        
        // Validate inputs
        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
            return [
                'success' => false,
                'error' => 'All fields are required'
            ];
        }
        
        if ($password !== $confirmPassword) {
            return [
                'success' => false,
                'error' => 'Passwords do not match'
            ];
        }
        
        return Auth::register($name, $email, $password, $currency);
    }
    
    /**
     * Handle user logout
     */
    public function logout(): void
    {
        Auth::logout();
        redirect('/login.php?logout=1');
    }
}

