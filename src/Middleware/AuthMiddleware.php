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
     * Handle guest check
     * Redirect to dashboard if already authenticated
     */
    public function handleGuest(): void
    {
        if (Auth::check()) {
            redirect('/index.php');
        }
    }
}
