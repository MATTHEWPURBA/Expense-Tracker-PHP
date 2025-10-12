<?php
/**
 * Authentication Middleware
 * 
 * Handles authentication checks for protected routes
 * 
 * @package ExpenseTracker\Middleware
 */

namespace ExpenseTracker\Middleware;

use ExpenseTracker\Services\Auth;
use ExpenseTracker\Router\ApiResponse;

class AuthMiddleware
{
    /**
     * Handle authentication check
     * Redirect to login if not authenticated
     */
    public function handle(): void
    {
        if (!Auth::check()) {
            redirect('/login.php');
        }
    }
    
    /**
     * Handle API authentication check
     * Return JSON error if not authenticated
     */
    public function handleApi(): void
    {
        if (!Auth::check()) {
            ApiResponse::error('Unauthorized. Please login.', 401)->send();
        }
    }
    
    /**
     * Handle guest check
     * Redirect to dashboard if already authenticated
     */
    public function handleGuest(): void
    {
        if (Auth::check()) {
            redirect('/index.php');
        }
    }
    
    /**
     * Static method to require authentication
     * Redirect to login if not authenticated
     */
    public static function requireAuth(): void
    {
        if (!Auth::check()) {
            redirect('/login.php');
        }
    }
    
    /**
     * Static method to require guest (not authenticated)
     * Redirect to dashboard if already authenticated
     */
    public static function requireGuest(): void
    {
        if (Auth::check()) {
            redirect('/index.php');
        }
    }
}
