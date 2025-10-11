<?php
/**
 * User Login Page
 */

// Error handling
error_reporting(0);
ini_set('display_errors', 0);

// Load configuration
$config_file = __DIR__ . '/config.php';
if (!file_exists($config_file)) {
    die('⚠️ Configuration file not found! Please set up config.php first.');
}

$config = require_once $config_file;

// Database Configuration
define('DB_HOST', $config['db_host']);
define('DB_PORT', $config['db_port']);
define('DB_NAME', $config['db_name']);
define('DB_USER', $config['db_user']);
define('DB_PASS', $config['db_pass']);
define('DB_SSL', $config['db_ssl']);

// Database Connection
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=" . DB_SSL;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    return $pdo;
}

// Initialize default categories
$defaultCategories = [
    ['id' => 'food', 'name' => 'Food & Dining', 'color' => '#FF6384', 'icon' => '🍔'],
    ['id' => 'transport', 'name' => 'Transportation', 'color' => '#36A2EB', 'icon' => '🚗'],
    ['id' => 'utilities', 'name' => 'Utilities', 'color' => '#FFCE56', 'icon' => '💡'],
    ['id' => 'entertainment', 'name' => 'Entertainment', 'color' => '#4BC0C0', 'icon' => '🎮'],
    ['id' => 'healthcare', 'name' => 'Healthcare', 'color' => '#9966FF', 'icon' => '🏥'],
    ['id' => 'shopping', 'name' => 'Shopping', 'color' => '#FF9F40', 'icon' => '🛍️'],
    ['id' => 'other', 'name' => 'Other', 'color' => '#C9CBCF', 'icon' => '📦']
];

// Initialize database tables
function initializeDatabase() {
    try {
        $pdo = getDBConnection();
        
        // Check if users table exists, if not create it
        $stmt = $pdo->prepare("SELECT EXISTS (
            SELECT FROM information_schema.tables 
            WHERE table_schema = 'public' 
            AND table_name = 'users'
        )");
        $stmt->execute();
        $tableExists = $stmt->fetchColumn();
        
        if (!$tableExists) {
            // Create users table if it doesn't exist
            $pdo->exec("
                CREATE TABLE users (
                    id VARCHAR(50) PRIMARY KEY,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    email VARCHAR(100) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    last_login TIMESTAMP
                )
            ");
        }
        
        // Create categories table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS categories (
                id VARCHAR(50) PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                color VARCHAR(7) NOT NULL,
                icon VARCHAR(10) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Create expenses table with user_id
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS expenses (
                id VARCHAR(50) PRIMARY KEY,
                user_id VARCHAR(50) NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                category VARCHAR(50) NOT NULL,
                description TEXT,
                date DATE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (category) REFERENCES categories(id) ON DELETE CASCADE
            )
        ");
        
        // Insert default categories if they don't exist
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            global $defaultCategories;
            $insertStmt = $pdo->prepare("
                INSERT INTO categories (id, name, color, icon) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($defaultCategories as $category) {
                $insertStmt->execute([
                    $category['id'],
                    $category['name'],
                    $category['color'],
                    $category['icon']
                ]);
            }
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Database initialization failed: " . $e->getMessage());
        return false;
    }
}

// Initialize database
initializeDatabase();

// Load authentication functions
require_once __DIR__ . '/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Check for logout message
if (isset($_GET['logout'])) {
    $success = 'You have been logged out successfully';
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($usernameOrEmail) || empty($password)) {
        $error = 'Please enter both username/email and password';
    } else {
        $result = loginUser($usernameOrEmail, $password);
        
        if ($result['success']) {
            header('Location: index.php');
            exit;
        } else {
            $error = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Expense Tracker</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #667eea;
            --primary-dark: #5a67d8;
            --secondary: #764ba2;
            --success: #48bb78;
            --danger: #f56565;
            --light: #f7fafc;
            --dark: #2d3748;
            --gray: #718096;
            --border: #e2e8f0;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .auth-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .logo p {
            color: var(--gray);
            font-size: 1.1rem;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        
        .alert-error {
            background: #fed7d7;
            color: #742a2a;
            border-left: 4px solid var(--danger);
        }
        
        .alert-success {
            background: #c6f6d5;
            color: #22543d;
            border-left: 4px solid var(--success);
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            color: var(--gray);
        }
        
        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .remember-me input[type="checkbox"] {
            width: auto;
            margin-right: 8px;
        }
        
        .remember-me label {
            margin: 0;
            font-weight: normal;
            color: var(--gray);
        }
        
        @media (max-width: 500px) {
            .auth-container {
                padding: 30px 20px;
            }
            
            .logo h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <h1>💰</h1>
            <h2 style="color: var(--dark); margin-bottom: 5px;">Welcome Back</h2>
            <p>Sign in to your account</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <strong>✗</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <strong>✓</strong> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Full Name or Email</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    autocomplete="username"
                    placeholder="Enter your username or email"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                >
                <div class="password-requirements">You can login with either your full name or email address</div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="Enter your password"
                >
            </div>
            
            <button type="submit" class="btn">
                🔓 Sign In
            </button>
        </form>
        
        <div class="auth-footer">
            Don't have an account? <a href="signup.php">Sign Up</a>
        </div>
    </div>
</body>
</html>

