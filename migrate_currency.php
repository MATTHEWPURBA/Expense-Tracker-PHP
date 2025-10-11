<?php
/**
 * Currency Migration
 * 
 * Adds currency support to users table
 */

require_once __DIR__ . '/bootstrap.php';

use ExpenseTracker\Services\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    echo "🔄 Starting currency migration...\n\n";
    
    // Check if currency column already exists
    $checkSql = "SELECT column_name FROM information_schema.columns 
                 WHERE table_name = 'users' AND column_name = 'currency'";
    $stmt = $db->query($checkSql);
    $exists = $stmt->fetch();
    
    if ($exists) {
        echo "✓ Currency column already exists!\n";
    } else {
        // Add currency column to users table
        echo "📝 Adding currency column to users table...\n";
        $db->exec("ALTER TABLE users ADD COLUMN currency VARCHAR(3) DEFAULT 'USD' NOT NULL");
        echo "✓ Currency column added successfully!\n";
    }
    
    // Update existing users without a currency to USD
    echo "\n📝 Updating existing users with default currency (USD)...\n";
    $updateStmt = $db->prepare("UPDATE users SET currency = 'USD' WHERE currency IS NULL OR currency = ''");
    $updateStmt->execute();
    $affected = $updateStmt->rowCount();
    echo "✓ Updated {$affected} user(s)\n";
    
    echo "\n✅ Migration completed successfully!\n";
    echo "\n📚 Available currencies:\n";
    echo "   - USD (US Dollar)\n";
    echo "   - EUR (Euro)\n";
    echo "   - GBP (British Pound)\n";
    echo "   - JPY (Japanese Yen)\n";
    echo "   - CNY (Chinese Yuan)\n";
    echo "   - INR (Indian Rupee)\n";
    echo "   - And 24 more currencies...\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

