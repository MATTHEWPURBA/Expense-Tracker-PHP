<?php
/**
 * Debug Login Script
 * 
 * This script helps debug login issues by providing detailed logging
 * and testing database connectivity
 * 
 * @package ExpenseTracker
 */

require_once __DIR__ . '/bootstrap.php';

use ExpenseTracker\Services\Auth;
use ExpenseTracker\Models\User;
use ExpenseTracker\Services\Database;

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Login Debug Tool</h1>";

// Test 1: Database Connection
echo "<h2>1. Database Connection Test</h2>";
try {
    $db = Database::getInstance()->getConnection();
    echo "✅ Database connection successful<br>";
    
    // Test basic query
    $stmt = $db->query("SELECT version()");
    $version = $stmt->fetchColumn();
    echo "📊 PostgreSQL Version: " . htmlspecialchars($version) . "<br>";
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "<br>";
    exit;
}

// Test 2: Users Table
echo "<h2>2. Users Table Test</h2>";
try {
    $stmt = $db->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    echo "👥 Total users in database: " . $userCount . "<br>";
    
    if ($userCount > 0) {
        $stmt = $db->query("SELECT id, name, email, created_at FROM users ORDER BY id LIMIT 5");
        $users = $stmt->fetchAll();
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Created</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "❌ Users table test failed: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// Test 3: User Model Test
echo "<h2>3. User Model Test</h2>";
try {
    $userModel = new User();
    
    // Test finding user by email
    $testEmail = "robhertomatt@gmail.com";
    echo "🔍 Testing find by email: " . htmlspecialchars($testEmail) . "<br>";
    
    $user = $userModel->findByEmail($testEmail);
    if ($user) {
        echo "✅ User found: " . htmlspecialchars($user['name']) . " (ID: " . $user['id'] . ")<br>";
        echo "🔑 Password hash exists: " . (isset($user['password']) ? "Yes" : "No") . "<br>";
    } else {
        echo "❌ User not found<br>";
    }
    
    // Test findByUsernameOrEmail
    echo "<br>🔍 Testing findByUsernameOrEmail: " . htmlspecialchars($testEmail) . "<br>";
    $user2 = $userModel->findByUsernameOrEmail($testEmail);
    if ($user2) {
        echo "✅ User found via username/email: " . htmlspecialchars($user2['name']) . "<br>";
    } else {
        echo "❌ User not found via username/email<br>";
    }
    
} catch (Exception $e) {
    echo "❌ User model test failed: " . htmlspecialchars($e->getMessage()) . "<br>";
}

// Test 4: Authentication Test
echo "<h2>4. Authentication Test</h2>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "🔐 Testing login with:<br>";
    echo "- Username/Email: " . htmlspecialchars($username) . "<br>";
    echo "- Password: " . str_repeat('*', strlen($password)) . "<br><br>";
    
    try {
        $result = Auth::login($username, $password);
        
        if ($result['success']) {
            echo "✅ Login successful!<br>";
            echo "👤 User: " . htmlspecialchars($result['user']['name']) . "<br>";
            echo "📧 Email: " . htmlspecialchars($result['user']['email']) . "<br>";
        } else {
            echo "❌ Login failed: " . htmlspecialchars($result['error']) . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Login test failed: " . htmlspecialchars($e->getMessage()) . "<br>";
    }
}

// Test 5: API Endpoint Test
echo "<h2>5. API Endpoint Test</h2>";
echo "🌐 Testing API endpoint: /api/auth/login<br>";

$apiUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
          "://$_SERVER[HTTP_HOST]/api/auth/login";

echo "📍 API URL: <a href='$apiUrl' target='_blank'>$apiUrl</a><br>";

// Test form
echo "<h2>6. Login Test Form</h2>";
echo "<form method='POST' style='border: 1px solid #ccc; padding: 20px; margin: 10px 0;'>";
echo "<label>Username/Email: <input type='text' name='username' value='robhertomatt@gmail.com' style='margin: 5px;'></label><br><br>";
echo "<label>Password: <input type='password' name='password' placeholder='Enter password' style='margin: 5px;'></label><br><br>";
echo "<input type='submit' name='test_login' value='Test Login' style='padding: 10px 20px; background: #007cba; color: white; border: none; cursor: pointer;'>";
echo "</form>";

// Show recent error logs
echo "<h2>7. Recent Error Logs</h2>";
$logFile = __DIR__ . '/logs/error.log';
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $lines = explode("\n", $logs);
    $recentLines = array_slice($lines, -20); // Last 20 lines
    
    echo "<div style='background: #f5f5f5; padding: 10px; font-family: monospace; font-size: 12px; max-height: 300px; overflow-y: auto;'>";
    foreach ($recentLines as $line) {
        if (trim($line)) {
            echo htmlspecialchars($line) . "<br>";
        }
    }
    echo "</div>";
} else {
    echo "❌ Error log file not found<br>";
}

echo "<br><hr>";
echo "<p><strong>💡 Next Steps:</strong></p>";
echo "<ul>";
echo "<li>If database connection fails, check your config.php settings</li>";
echo "<li>If user not found, check if the user exists in the database</li>";
echo "<li>If login fails, verify the password is correct</li>";
echo "<li>Check the error logs above for specific error messages</li>";
echo "</ul>";
?>
