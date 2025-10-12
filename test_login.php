<?php
/**
 * Simple Login Test Script
 */

session_start();
require_once __DIR__ . '/bootstrap.php';

use ExpenseTracker\Services\Auth;
use ExpenseTracker\Models\User;

echo "=== LOGIN TEST ===\n";

try {
    // Step 1: Test user lookup
    echo "1. Testing user lookup...\n";
    $userModel = new User();
    $user = $userModel->findByEmail('robhertomatt@gmail.com');
    
    if (!$user) {
        echo "❌ User not found!\n";
        exit(1);
    }
    
    echo "✅ User found: " . $user['name'] . " (ID: " . $user['id'] . ")\n";
    
    // Step 2: Test password verification
    echo "2. Testing password verification...\n";
    $password = '12345678';
    $isValidPassword = password_verify($password, $user['password']);
    
    if (!$isValidPassword) {
        echo "❌ Password verification failed!\n";
        exit(1);
    }
    
    echo "✅ Password verification successful\n";
    
    // Step 3: Test full login
    echo "3. Testing full login...\n";
    $result = Auth::login('robhertomatt@gmail.com', $password);
    
    if ($result['success']) {
        echo "✅ Login successful!\n";
        echo "User: " . $result['user']['name'] . "\n";
        echo "Session user_id: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
    } else {
        echo "❌ Login failed: " . $result['error'] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "=== TEST COMPLETE ===\n";
?>
