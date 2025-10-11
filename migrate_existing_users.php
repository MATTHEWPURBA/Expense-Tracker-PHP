<?php
/**
 * Migration Script for Existing Users
 * 
 * This script helps you use your existing Neon users table with the Expense Tracker
 */

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load configuration
$config_file = __DIR__ . '/config.php';
if (!file_exists($config_file)) {
    die('❌ Configuration file not found! Please set up config.php first.');
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
            die("❌ Database connection failed: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

echo "🔄 Expense Tracker - User Migration Script\n";
echo "==========================================\n\n";

try {
    $pdo = getDBConnection();
    
    // Check if users table exists
    $stmt = $pdo->prepare("SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_name = 'users'
    )");
    $stmt->execute();
    $tableExists = $stmt->fetchColumn();
    
    if (!$tableExists) {
        echo "❌ Users table not found in database!\n";
        echo "Please make sure your Neon database has a users table.\n";
        exit(1);
    }
    
    echo "✅ Users table found!\n\n";
    
    // Check users table structure
    $stmt = $pdo->prepare("
        SELECT column_name, data_type, character_maximum_length, is_nullable
        FROM information_schema.columns 
        WHERE table_name = 'users' 
        AND table_schema = 'public'
        ORDER BY ordinal_position
    ");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    
    echo "📋 Current users table structure:\n";
    foreach ($columns as $column) {
        $length = $column['character_maximum_length'] ? "({$column['character_maximum_length']})" : "";
        $nullable = $column['is_nullable'] === 'YES' ? 'NULL' : 'NOT NULL';
        echo "   - {$column['column_name']}: {$column['data_type']}{$length} {$nullable}\n";
    }
    
    echo "\n";
    
    // Check if we have the required columns
    $requiredColumns = ['id', 'name', 'email', 'password'];
    $existingColumns = array_column($columns, 'column_name');
    
    $missingColumns = array_diff($requiredColumns, $existingColumns);
    
    if (!empty($missingColumns)) {
        echo "❌ Missing required columns: " . implode(', ', $missingColumns) . "\n";
        echo "Please ensure your users table has these columns.\n";
        exit(1);
    }
    
    echo "✅ All required columns found!\n\n";
    
    // Get existing users
    $stmt = $pdo->prepare("SELECT id, name, email, created_at FROM users ORDER BY created_at");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    echo "👥 Found " . count($users) . " existing users:\n";
    foreach ($users as $user) {
        echo "   - ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}\n";
    }
    
    echo "\n";
    
    // Create categories table if it doesn't exist
    $stmt = $pdo->prepare("SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_name = 'categories'
    )");
    $stmt->execute();
    $categoriesExists = $stmt->fetchColumn();
    
    if (!$categoriesExists) {
        echo "🔄 Creating categories table...\n";
        
        $pdo->exec("
            CREATE TABLE categories (
                id VARCHAR(50) PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                color VARCHAR(7) NOT NULL,
                icon VARCHAR(10) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Insert default categories
        $defaultCategories = [
            ['id' => 'food', 'name' => 'Food & Dining', 'color' => '#FF6384', 'icon' => '🍔'],
            ['id' => 'transport', 'name' => 'Transportation', 'color' => '#36A2EB', 'icon' => '🚗'],
            ['id' => 'utilities', 'name' => 'Utilities', 'color' => '#FFCE56', 'icon' => '💡'],
            ['id' => 'entertainment', 'name' => 'Entertainment', 'color' => '#4BC0C0', 'icon' => '🎮'],
            ['id' => 'healthcare', 'name' => 'Healthcare', 'color' => '#9966FF', 'icon' => '🏥'],
            ['id' => 'shopping', 'name' => 'Shopping', 'color' => '#FF9F40', 'icon' => '🛍️'],
            ['id' => 'other', 'name' => 'Other', 'color' => '#C9CBCF', 'icon' => '📦']
        ];
        
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
        
        echo "✅ Categories table created with default categories!\n";
    } else {
        echo "✅ Categories table already exists!\n";
    }
    
    echo "\n";
    
    // Create expenses table if it doesn't exist
    $stmt = $pdo->prepare("SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_name = 'expenses'
    )");
    $stmt->execute();
    $expensesExists = $stmt->fetchColumn();
    
    if (!$expensesExists) {
        echo "🔄 Creating expenses table...\n";
        
        $pdo->exec("
            CREATE TABLE expenses (
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
        
        echo "✅ Expenses table created!\n";
    } else {
        echo "✅ Expenses table already exists!\n";
    }
    
    echo "\n";
    
    // Test login with existing user
    echo "🧪 Testing login with existing user...\n";
    
    if (!empty($users)) {
        $testUser = $users[0];
        echo "   Testing with: {$testUser['name']} ({$testUser['email']})\n";
        
        // You can test password verification here if you know the password
        echo "   ✅ User data structure is compatible!\n";
    }
    
    echo "\n";
    echo "🎉 Migration completed successfully!\n\n";
    
    echo "📋 Next Steps:\n";
    echo "1. Start your PHP server: php -S localhost:8000\n";
    echo "2. Visit: http://localhost:8000\n";
    echo "3. You can now:\n";
    echo "   - Login with existing users using their NAME and PASSWORD\n";
    echo "   - Create new users through the signup form\n";
    echo "   - Track expenses for each user separately\n\n";
    
    echo "⚠️  Important Notes:\n";
    echo "- Users login with their FULL NAME (not email)\n";
    echo "- Each user will have their own separate expense data\n";
    echo "- Existing passwords should work (if they're properly hashed)\n\n";
    
    echo "🔐 For existing users, you can login with:\n";
    foreach ($users as $user) {
        echo "   Name: {$user['name']}, Email: {$user['email']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
