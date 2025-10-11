<?php
/**
 * Authentication Helper Functions
 * Handles user authentication, session management, and security
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require user to be logged in, redirect to login if not
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Get current logged-in user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current logged-in user data
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, name, email, created_at FROM users WHERE id = ?");
        $stmt->execute([getCurrentUserId()]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Failed to get current user: " . $e->getMessage());
        return null;
    }
}

/**
 * Register a new user
 */
function registerUser($username, $email, $password) {
    try {
        $pdo = getDBConnection();
        
        // Check if username or email already exists
        $checkResult = checkUserExists($username, $email);
        if ($checkResult['exists']) {
            return ['success' => false, 'error' => $checkResult['message']];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user (using existing table structure)
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, created_at, updated_at) 
            VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
        ");
        $stmt->execute([$username, $email, $hashedPassword]);
        
        // Get the auto-generated ID
        $userId = $pdo->lastInsertId();
        
        return ['success' => true, 'user_id' => $userId];
    } catch (Exception $e) {
        error_log("Registration failed: " . $e->getMessage());
        return ['success' => false, 'error' => 'Registration failed. Please try again.'];
    }
}

/**
 * Login user (accepts both name and email)
 */
function loginUser($usernameOrEmail, $password) {
    try {
        $pdo = getDBConnection();
        
        // Try to find user by name first, then by email
        $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE name = ? OR email = ?");
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'error' => 'Invalid username/email or password'];
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'error' => 'Invalid username/email or password'];
        }
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        
        // Update last login (if column exists)
        try {
            $stmt = $pdo->prepare("UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$user['id']]);
        } catch (Exception $e) {
            // Ignore if column doesn't exist
        }
        
        return ['success' => true, 'user' => $user];
    } catch (Exception $e) {
        error_log("Login failed: " . $e->getMessage());
        return ['success' => false, 'error' => 'Login failed. Please try again.'];
    }
}

/**
 * Logout user
 */
function logoutUser() {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

/**
 * Validate email format
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 */
function validatePassword($password) {
    // At least 6 characters
    if (strlen($password) < 6) {
        return ['valid' => false, 'error' => 'Password must be at least 6 characters long'];
    }
    
    return ['valid' => true];
}

/**
 * Validate username (name field in existing table)
 */
function validateUsername($username) {
    // 3-255 characters (matching existing table structure)
    if (strlen($username) < 3 || strlen($username) > 255) {
        return ['valid' => false, 'error' => 'Name must be between 3 and 255 characters'];
    }
    
    // Allow spaces and more characters for names
    if (!preg_match('/^[a-zA-Z0-9\s._-]+$/', $username)) {
        return ['valid' => false, 'error' => 'Name can only contain letters, numbers, spaces, dots, underscores, and hyphens'];
    }
    
    return ['valid' => true];
}

/**
 * Check if username or email already exists
 */
function checkUserExists($username, $email) {
    try {
        $pdo = getDBConnection();
        
        // Check if name already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE name = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            return ['exists' => true, 'type' => 'name', 'message' => 'This name is already taken'];
        }
        
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['exists' => true, 'type' => 'email', 'message' => 'This email is already registered'];
        }
        
        return ['exists' => false];
    } catch (Exception $e) {
        error_log("User check failed: " . $e->getMessage());
        return ['exists' => false, 'error' => 'Unable to verify user information'];
    }
}

/**
 * Sanitize input
 */
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

