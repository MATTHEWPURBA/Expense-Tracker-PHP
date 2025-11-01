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
use ExpenseTracker\Router\ApiResponse;
use ExpenseTracker\Models\Model;

class AuthController
{
    /**
     * Handle user login (API)
     */
    public function login(): ApiResponse
    {
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        $usernameOrEmail = Auth::sanitize($input['username'] ?? '');
        $password = $input['password'] ?? '';
        
        if (empty($usernameOrEmail) || empty($password)) {
            return ApiResponse::error('Please enter both username/email and password', 400);
        }
        
        try {
            $result = Auth::login($usernameOrEmail, $password);
            
            if ($result['success']) {
                return ApiResponse::success([
                    'user' => $result['user'] ?? Auth::user()
                ], 'Login successful')->setQueries(Model::getQueries());
            }
            
            return ApiResponse::error($result['error'] ?? 'Login failed', 401);
        } catch (Exception $e) {
            // If it's a database connection error, provide helpful message
            if (strpos($e->getMessage(), 'Database connection') !== false) {
                return ApiResponse::error($e->getMessage(), 500);
            }
            throw $e;
        }
    }
    
    /**
     * Handle user registration (API)
     */
    public function register(): ApiResponse
    {
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        $name = Auth::sanitize($input['username'] ?? '');
        $email = Auth::sanitize($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $confirmPassword = $input['confirm_password'] ?? '';
        $currency = strtoupper($input['currency'] ?? 'USD');
        
        // Validate inputs
        $errors = [];
        
        if (empty($name)) {
            $errors['username'] = 'Username is required';
        }
        
        if (empty($email)) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }
        
        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Password must be at least 6 characters';
        }
        
        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        if (!empty($errors)) {
            return ApiResponse::error('Validation failed', 422, $errors);
        }
        
        $result = Auth::register($name, $email, $password, $currency);
        
        if ($result['success']) {
            return ApiResponse::created([
                'user' => $result['user'] ?? Auth::user()
            ], 'Registration successful')->setQueries(Model::getQueries());
        }
        
        return ApiResponse::error($result['error'] ?? 'Registration failed', 400);
    }
    
    /**
     * Handle user logout (API)
     */
    public function logout(): ApiResponse
    {
        Auth::logout();
        return ApiResponse::success([], 'Logged out successfully');
    }
}

