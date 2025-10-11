<?php
/**
 * Migration Script: JSON to PostgreSQL
 * Migrates existing JSON data to the PostgreSQL database
 */

// Include the main application file to get database functions
require_once 'index.php';

echo "<h1>Expense Tracker Migration</h1>\n";
echo "<p>Migrating from JSON files to PostgreSQL database...</p>\n";

try {
    $pdo = getDBConnection();
    echo "✅ Database connection successful<br>\n";
    
    // Check if we have existing JSON data
    $hasExpenses = file_exists(EXPENSES_FILE) && filesize(EXPENSES_FILE) > 2;
    $hasCategories = file_exists(CATEGORIES_FILE) && filesize(CATEGORIES_FILE) > 2;
    
    if ($hasExpenses) {
        echo "📁 Found existing expenses.json file<br>\n";
        
        // Load JSON expenses
        $jsonExpenses = json_decode(file_get_contents(EXPENSES_FILE), true);
        
        if (!empty($jsonExpenses)) {
            // Insert expenses into database
            $stmt = $pdo->prepare("
                INSERT INTO expenses (id, amount, category, description, date, created_at) 
                VALUES (?, ?, ?, ?, ?, ?)
                ON CONFLICT (id) DO NOTHING
            ");
            
            $migrated = 0;
            foreach ($jsonExpenses as $expense) {
                try {
                    $stmt->execute([
                        $expense['id'],
                        $expense['amount'],
                        $expense['category'],
                        $expense['description'],
                        $expense['date'],
                        $expense['created_at']
                    ]);
                    $migrated++;
                } catch (Exception $e) {
                    echo "⚠️ Failed to migrate expense {$expense['id']}: " . $e->getMessage() . "<br>\n";
                }
            }
            
            echo "✅ Migrated {$migrated} expenses to database<br>\n";
        } else {
            echo "ℹ️ No expenses found in JSON file<br>\n";
        }
    } else {
        echo "ℹ️ No expenses.json file found<br>\n";
    }
    
    if ($hasCategories) {
        echo "📁 Found existing categories.json file<br>\n";
        
        // Load JSON categories
        $jsonCategories = json_decode(file_get_contents(CATEGORIES_FILE), true);
        
        if (!empty($jsonCategories)) {
            // Insert categories into database
            $stmt = $pdo->prepare("
                INSERT INTO categories (id, name, color, icon) 
                VALUES (?, ?, ?, ?)
                ON CONFLICT (id) DO NOTHING
            ");
            
            $migrated = 0;
            foreach ($jsonCategories as $category) {
                try {
                    $stmt->execute([
                        $category['id'],
                        $category['name'],
                        $category['color'],
                        $category['icon']
                    ]);
                    $migrated++;
                } catch (Exception $e) {
                    echo "⚠️ Failed to migrate category {$category['id']}: " . $e->getMessage() . "<br>\n";
                }
            }
            
            echo "✅ Migrated {$migrated} categories to database<br>\n";
        } else {
            echo "ℹ️ No categories found in JSON file<br>\n";
        }
    }
    
    // Verify migration
    echo "<h2>Verification</h2>\n";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM expenses");
    $stmt->execute();
    $expenseCount = $stmt->fetchColumn();
    echo "📊 Total expenses in database: {$expenseCount}<br>\n";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories");
    $stmt->execute();
    $categoryCount = $stmt->fetchColumn();
    echo "📂 Total categories in database: {$categoryCount}<br>\n";
    
    // Show sample data
    if ($expenseCount > 0) {
        echo "<h3>Sample Expenses</h3>\n";
        $stmt = $pdo->prepare("SELECT * FROM expenses ORDER BY created_at DESC LIMIT 5");
        $stmt->execute();
        $sampleExpenses = $stmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse;'>\n";
        echo "<tr><th>ID</th><th>Amount</th><th>Category</th><th>Description</th><th>Date</th></tr>\n";
        foreach ($sampleExpenses as $expense) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($expense['id']) . "</td>";
            echo "<td>$" . number_format($expense['amount'], 2) . "</td>";
            echo "<td>" . htmlspecialchars($expense['category']) . "</td>";
            echo "<td>" . htmlspecialchars($expense['description']) . "</td>";
            echo "<td>" . htmlspecialchars($expense['date']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
    }
    
    echo "<h2>Migration Complete! 🎉</h2>\n";
    echo "<p>Your expense tracker is now using PostgreSQL database.</p>\n";
    echo "<p><a href='index.php'>Go to Expense Tracker</a></p>\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "<br>\n";
    echo "<p>Please check your database configuration and try again.</p>\n";
}
?>
