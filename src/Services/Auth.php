<?php
/**
 * Authentication Service
 * 
 * Handles user authentication, registration, and session management
 * 
 * @package ExpenseTracker\Services
 */

namespace ExpenseTracker\Services;

use ExpenseTracker\Models\User;

class Auth
{
    /**
     * Check if user is logged in
     */
    public static function check(): bool
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Get current user ID
     */
    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get current user
     */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        
        $userModel = new User();
        return $userModel->find(self::id());
    }
    
    /**
     * Login user
     */
    public static function login(string $usernameOrEmail, string $password): array
    {
        $userModel = new User();
        $user = $userModel->findByUsernameOrEmail($usernameOrEmail);
        
        if (!$user) {
            return [
                'success' => false,
                'error' => 'Invalid username/email or password'
            ];
        }
        
        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'error' => 'Invalid username/email or password'
            ];
        }
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user'] = $user;
        
        // Update last login
        $userModel->updateLastLogin($user['id']);
        
        return [
            'success' => true,
            'user' => $user
        ];
    }
    
    /**
     * Register new user
     */
    public static function register(string $name, string $email, string $password, string $currency = 'USD'): array
    {
        $userModel = new User();
        
        // Validate inputs
        $validation = self::validateRegistration($name, $email, $password);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'error' => $validation['error']
            ];
        }
        
        // Check if user exists
        if ($userModel->findByEmail($email)) {
            return [
                'success' => false,
                'error' => 'Email already registered'
            ];
        }
        
        if ($userModel->findByUsername($name)) {
            return [
                'success' => false,
                'error' => 'Username already taken'
            ];
        }
        
        // Validate currency
        if (!Currency::isValid($currency)) {
            $currency = 'USD'; // Fallback to USD if invalid
        }
        
        // Create user
        $userId = $userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'currency' => strtoupper($currency)
        ]);
        
        if ($userId) {
            return [
                'success' => true,
                'user_id' => $userId
            ];
        }
        
        return [
            'success' => false,
            'error' => 'Registration failed'
        ];
    }
    
    /**
     * Logout user
     */
    public static function logout(): void
    {
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    /**
     * Validate registration data
     */
    private static function validateRegistration(string $name, string $email, string $password): array
    {
        // Validate name
        if (strlen($name) < 3 || strlen($name) > 255) {
            return [
                'valid' => false,
                'error' => 'Name must be between 3 and 255 characters'
            ];
        }
        
        if (!preg_match('/^[a-zA-Z0-9\s._-]+$/', $name)) {
            return [
                'valid' => false,
                'error' => 'Name can only contain letters, numbers, spaces, dots, underscores, and hyphens'
            ];
        }
        
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'error' => 'Invalid email address'
            ];
        }
        
        // Validate password
        if (strlen($password) < 6) {
            return [
                'valid' => false,
                'error' => 'Password must be at least 6 characters'
            ];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Sanitize input
     */
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}

