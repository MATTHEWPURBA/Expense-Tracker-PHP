<?php
/**
 * Personal Expense Tracker
 * A modern expense tracking application with PostgreSQL database
 * 
 * @author Your Name
 * @version 2.0.0
 * @license MIT
 */

// Error handling for production
error_reporting(0);
ini_set('display_errors', 0);

// Load database configuration from external file
$config_file = __DIR__ . '/config.php';
if (!file_exists($config_file)) {
    die('⚠️ Configuration file not found!<br><br>
         Please follow these steps:<br>
         1. Copy <code>config.example.php</code> to <code>config.php</code><br>
         2. Edit <code>config.php</code> with your database credentials<br>
         3. Refresh this page<br><br>
         See <a href="GET_STARTED.md">GET_STARTED.md</a> for detailed instructions.');
}

$config = require_once $config_file;

// Database Configuration
define('DB_HOST', $config['db_host']);
define('DB_PORT', $config['db_port']);
define('DB_NAME', $config['db_name']);
define('DB_USER', $config['db_user']);
define('DB_PASS', $config['db_pass']);
define('DB_SSL', $config['db_ssl']);

// Legacy data directory for migration purposes
define('DATA_DIR', __DIR__ . '/data');
define('EXPENSES_FILE', DATA_DIR . '/expenses.json');
define('CATEGORIES_FILE', DATA_DIR . '/categories.json');

// Create data directory if it doesn't exist (for migration)
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
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

// Initialize database tables
function initializeDatabase() {
    try {
        $pdo = getDBConnection();
        
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
        
        // Create expenses table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS expenses (
                id VARCHAR(50) PRIMARY KEY,
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

// Database Functions
function loadExpenses() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT e.*, c.name as category_name, c.color, c.icon 
            FROM expenses e 
            LEFT JOIN categories c ON e.category = c.id 
            ORDER BY e.date DESC, e.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Failed to load expenses: " . $e->getMessage());
        return [];
    }
}

function loadCategories() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Failed to load categories: " . $e->getMessage());
        return [];
    }
}

function saveExpense($expense) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO expenses (id, amount, category, description, date) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $expense['id'],
            $expense['amount'],
            $expense['category'],
            $expense['description'],
            $expense['date']
        ]);
    } catch (Exception $e) {
        error_log("Failed to save expense: " . $e->getMessage());
        return false;
    }
}

function deleteExpense($id) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (Exception $e) {
        error_log("Failed to delete expense: " . $e->getMessage());
        return false;
    }
}

function getExpenseStats() {
    try {
        $pdo = getDBConnection();
        
        // Total expenses
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM expenses");
        $stmt->execute();
        $total = $stmt->fetchColumn();
        
        // This month expenses
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(amount), 0) as monthly 
            FROM expenses 
            WHERE EXTRACT(YEAR FROM date) = EXTRACT(YEAR FROM CURRENT_DATE) 
            AND EXTRACT(MONTH FROM date) = EXTRACT(MONTH FROM CURRENT_DATE)
        ");
        $stmt->execute();
        $monthly = $stmt->fetchColumn();
        
        // Average transaction
        $stmt = $pdo->prepare("SELECT COALESCE(AVG(amount), 0) as average FROM expenses");
        $stmt->execute();
        $average = $stmt->fetchColumn();
        
        // Total transactions
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM expenses");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        return [
            'total' => floatval($total),
            'monthly' => floatval($monthly),
            'average' => floatval($average),
            'count' => intval($count)
        ];
    } catch (Exception $e) {
        error_log("Failed to get expense stats: " . $e->getMessage());
        return [
            'total' => 0,
            'monthly' => 0,
            'average' => 0,
            'count' => 0
        ];
    }
}

function getCategoryBreakdown() {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            SELECT 
                c.id,
                c.name,
                c.color,
                c.icon,
                COALESCE(SUM(e.amount), 0) as total
            FROM categories c
            LEFT JOIN expenses e ON c.id = e.category
            GROUP BY c.id, c.name, c.color, c.icon
            ORDER BY total DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Failed to get category breakdown: " . $e->getMessage());
        return [];
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_expense') {
        $newExpense = [
            'id' => uniqid('exp_', true),
            'amount' => floatval($_POST['amount']),
            'category' => $_POST['category'],
            'description' => htmlspecialchars($_POST['description']),
            'date' => $_POST['date']
        ];
        
        if (saveExpense($newExpense)) {
            $newExpense['created_at'] = date('Y-m-d H:i:s');
            echo json_encode(['success' => true, 'expense' => $newExpense]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to save expense']);
        }
        exit;
    }
    
    if ($action === 'delete_expense') {
        $id = $_POST['id'];
        
        if (deleteExpense($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete expense']);
        }
        exit;
    }
    
    if ($action === 'get_expenses') {
        $expenses = loadExpenses();
        echo json_encode(['success' => true, 'expenses' => $expenses]);
        exit;
    }
}

// Handle Export Requests
if (isset($_GET['export'])) {
    $expenses = loadExpenses();
    $exportType = $_GET['export'];
    $filename = 'expenses_' . date('Y-m-d');
    
    switch ($exportType) {
        case 'csv':
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Date', 'Category', 'Description', 'Amount', 'Created At']);
            
            foreach ($expenses as $expense) {
                fputcsv($output, [
                    $expense['date'],
                    $expense['category_name'] ?? $expense['category'],
                    $expense['description'],
                    number_format($expense['amount'], 2),
                    $expense['created_at']
                ]);
            }
            
            fclose($output);
            break;
            
        case 'json':
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . $filename . '.json"');
            
            $exportData = [
                'export_date' => date('Y-m-d H:i:s'),
                'total_expenses' => count($expenses),
                'total_amount' => array_sum(array_column($expenses, 'amount')),
                'expenses' => array_map(function($expense) {
                    return [
                        'id' => $expense['id'],
                        'date' => $expense['date'],
                        'category' => $expense['category'],
                        'category_name' => $expense['category_name'] ?? $expense['category'],
                        'description' => $expense['description'],
                        'amount' => floatval($expense['amount']),
                        'created_at' => $expense['created_at']
                    ];
                }, $expenses)
            ];
            
            echo json_encode($exportData, JSON_PRETTY_PRINT);
            break;
            
        case 'xml':
            header('Content-Type: application/xml');
            header('Content-Disposition: attachment; filename="' . $filename . '.xml"');
            
            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><expenses></expenses>');
            $xml->addAttribute('export_date', date('Y-m-d H:i:s'));
            $xml->addAttribute('total_count', count($expenses));
            $xml->addAttribute('total_amount', array_sum(array_column($expenses, 'amount')));
            
            foreach ($expenses as $expense) {
                $expenseNode = $xml->addChild('expense');
                $expenseNode->addChild('id', htmlspecialchars($expense['id']));
                $expenseNode->addChild('date', $expense['date']);
                $expenseNode->addChild('category', htmlspecialchars($expense['category']));
                $expenseNode->addChild('category_name', htmlspecialchars($expense['category_name'] ?? $expense['category']));
                $expenseNode->addChild('description', htmlspecialchars($expense['description']));
                $expenseNode->addChild('amount', number_format($expense['amount'], 2));
                $expenseNode->addChild('created_at', $expense['created_at']);
            }
            
            echo $xml->asXML();
            break;
            
        case 'excel':
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
            
            echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
            echo '    xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
            echo '  <Worksheet ss:Name="Expenses">' . "\n";
            echo '    <Table>' . "\n";
            
            // Header row
            echo '      <Row>' . "\n";
            echo '        <Cell><Data ss:Type="String">Date</Data></Cell>' . "\n";
            echo '        <Cell><Data ss:Type="String">Category</Data></Cell>' . "\n";
            echo '        <Cell><Data ss:Type="String">Description</Data></Cell>' . "\n";
            echo '        <Cell><Data ss:Type="String">Amount</Data></Cell>' . "\n";
            echo '        <Cell><Data ss:Type="String">Created At</Data></Cell>' . "\n";
            echo '      </Row>' . "\n";
            
            // Data rows
            foreach ($expenses as $expense) {
                echo '      <Row>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($expense['date']) . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($expense['category_name'] ?? $expense['category']) . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($expense['description']) . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="Number">' . number_format($expense['amount'], 2) . '</Data></Cell>' . "\n";
                echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($expense['created_at']) . '</Data></Cell>' . "\n";
                echo '      </Row>' . "\n";
            }
            
            echo '    </Table>' . "\n";
            echo '  </Worksheet>' . "\n";
            echo '</Workbook>';
            break;
            
        case 'pdf':
            header('Content-Type: text/html');
            header('Content-Disposition: inline; filename="' . $filename . '.html"');
            
            $stats = getExpenseStats();
            
            echo '<!DOCTYPE html><html><head>';
            echo '<meta charset="UTF-8">';
            echo '<title>Expense Report - ' . date('Y-m-d') . '</title>';
            echo '<style>
                body { font-family: Arial, sans-serif; padding: 40px; max-width: 1000px; margin: 0 auto; }
                h1 { color: #667eea; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
                .summary { background: #f7fafc; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
                .summary-item { background: white; padding: 15px; border-radius: 5px; border-left: 4px solid #667eea; }
                .summary-label { color: #718096; font-size: 0.9em; }
                .summary-value { font-size: 1.5em; font-weight: bold; color: #2d3748; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background: #667eea; color: white; padding: 12px; text-align: left; }
                td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
                tr:hover { background: #f7fafc; }
                .amount { color: #f56565; font-weight: bold; }
                .category { display: inline-block; padding: 4px 10px; border-radius: 15px; font-size: 0.85em; }
                .footer { margin-top: 40px; padding-top: 20px; border-top: 2px solid #e2e8f0; color: #718096; text-align: center; }
                @media print { body { padding: 20px; } }
            </style>';
            echo '</head><body>';
            echo '<h1>💰 Expense Report</h1>';
            echo '<p><strong>Generated:</strong> ' . date('F d, Y h:i A') . '</p>';
            
            echo '<div class="summary">';
            echo '<h2>Summary Statistics</h2>';
            echo '<div class="summary-grid">';
            echo '<div class="summary-item"><div class="summary-label">Total Expenses</div><div class="summary-value">$' . number_format($stats['total'], 2) . '</div></div>';
            echo '<div class="summary-item"><div class="summary-label">This Month</div><div class="summary-value">$' . number_format($stats['monthly'], 2) . '</div></div>';
            echo '<div class="summary-item"><div class="summary-label">Transactions</div><div class="summary-value">' . $stats['count'] . '</div></div>';
            echo '<div class="summary-item"><div class="summary-label">Average</div><div class="summary-value">$' . number_format($stats['average'], 2) . '</div></div>';
            echo '</div></div>';
            
            echo '<h2>Expense Details</h2>';
            echo '<table>';
            echo '<thead><tr><th>Date</th><th>Category</th><th>Description</th><th>Amount</th></tr></thead>';
            echo '<tbody>';
            
            foreach ($expenses as $expense) {
                $catName = htmlspecialchars($expense['category_name'] ?? $expense['category']);
                $desc = htmlspecialchars($expense['description']);
                $amount = number_format($expense['amount'], 2);
                
                echo '<tr>';
                echo '<td>' . date('M d, Y', strtotime($expense['date'])) . '</td>';
                echo '<td><span class="category">' . $catName . '</span></td>';
                echo '<td>' . $desc . '</td>';
                echo '<td class="amount">$' . $amount . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
            echo '<div class="footer">';
            echo '<p>This report contains ' . count($expenses) . ' expense entries.</p>';
            echo '<p><em>Tip: Use your browser\'s print function (Ctrl+P / Cmd+P) to save as PDF.</em></p>';
            echo '</div>';
            echo '</body></html>';
            break;
            
        default:
            header('HTTP/1.1 400 Bad Request');
            echo 'Invalid export format';
            break;
    }
    
    exit;
}

// Load data for display
$expenses = loadExpenses();
$categories = loadCategories();

// Sort expenses by date (newest first)
usort($expenses, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// Get statistics from database
$stats = getExpenseStats();
$totalExpenses = $stats['total'];
$monthlyTotal = $stats['monthly'];
$avgExpense = $stats['average'];
$expenseCount = $stats['count'];

// Get category breakdown from database
$categoryBreakdown = getCategoryBreakdown();
$categoryTotals = [];
foreach ($categoryBreakdown as $cat) {
    $categoryTotals[$cat['id']] = floatval($cat['total']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Personal Expense Tracker - Track your expenses with beautiful analytics">
    <meta name="author" content="Your Name">
    <title>💰 Expense Tracker - Personal Finance Manager</title>
    
    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    
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
            --warning: #ed8936;
            --info: #4299e1;
            --light: #f7fafc;
            --dark: #2d3748;
            --gray: #718096;
            --border: #e2e8f0;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
            color: var(--dark);
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            animation: fadeInDown 0.6s ease-out;
        }
        
        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .header p {
            font-size: 1.1rem;
            opacity: 0.95;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }
        
        .stat-card .icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .stat-card .label {
            color: var(--gray);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--shadow);
            animation: fadeInUp 0.6s ease-out 0.4s both;
        }
        
        .card h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
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
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            width: 100%;
            justify-content: center;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-success {
            background: var(--success);
            color: white;
        }
        
        .btn-success:hover {
            background: #38a169;
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
            padding: 6px 12px;
            font-size: 0.85rem;
        }
        
        .btn-danger:hover {
            background: #e53e3e;
        }
        
        .expense-list {
            max-height: 500px;
            overflow-y: auto;
            animation: fadeInUp 0.6s ease-out 0.6s both;
        }
        
        .expense-list::-webkit-scrollbar {
            width: 8px;
        }
        
        .expense-list::-webkit-scrollbar-track {
            background: var(--light);
            border-radius: 10px;
        }
        
        .expense-list::-webkit-scrollbar-thumb {
            background: var(--gray);
            border-radius: 10px;
        }
        
        .expense-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 2px solid var(--border);
            border-radius: 10px;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            animation: slideInRight 0.4s ease-out;
        }
        
        .expense-item:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-sm);
            transform: translateX(-3px);
        }
        
        .expense-info {
            flex: 1;
        }
        
        .expense-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }
        
        .category-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            color: white;
        }
        
        .expense-description {
            color: var(--dark);
            font-weight: 600;
            margin-bottom: 3px;
        }
        
        .expense-date {
            color: var(--gray);
            font-size: 0.85rem;
        }
        
        .expense-amount {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--danger);
            margin-right: 15px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray);
        }
        
        .empty-state .icon {
            font-size: 5rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .chart-container {
            position: relative;
            height: 400px;
            margin-top: 20px;
        }
        
        .export-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid var(--border);
        }
        
        .export-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 10px;
        }
        
        .btn-export {
            padding: 10px 15px;
            font-size: 0.9rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .btn-csv {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
        }
        
        .btn-csv:hover {
            background: linear-gradient(135deg, #38a169, #2f855a);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(72, 187, 120, 0.4);
        }
        
        .btn-json {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
        }
        
        .btn-json:hover {
            background: linear-gradient(135deg, #3182ce, #2c5282);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(66, 153, 225, 0.4);
        }
        
        .btn-excel {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
        }
        
        .btn-excel:hover {
            background: linear-gradient(135deg, #38a169, #2f855a);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(72, 187, 120, 0.4);
        }
        
        .btn-xml {
            background: linear-gradient(135deg, #ed8936, #dd6b20);
            color: white;
        }
        
        .btn-xml:hover {
            background: linear-gradient(135deg, #dd6b20, #c05621);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(237, 137, 54, 0.4);
        }
        
        .btn-pdf {
            background: linear-gradient(135deg, #f56565, #e53e3e);
            color: white;
        }
        
        .btn-pdf:hover {
            background: linear-gradient(135deg, #e53e3e, #c53030);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(245, 101, 101, 0.4);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            animation: slideInDown 0.3s ease-out;
        }
        
        .alert.show {
            display: block;
        }
        
        .alert-success {
            background: #c6f6d5;
            color: #22543d;
            border-left: 4px solid var(--success);
        }
        
        .alert-error {
            background: #fed7d7;
            color: #742a2a;
            border-left: 4px solid var(--danger);
        }
        
        .footer {
            text-align: center;
            color: white;
            margin-top: 40px;
            padding: 20px;
            opacity: 0.9;
        }
        
        .footer a {
            color: white;
            text-decoration: underline;
        }
        
        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive Design */
        @media (max-width: 968px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            
            .card {
                padding: 20px;
            }
            
            .expense-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .expense-amount {
                margin-right: 0;
            }
            
            .chart-container {
                height: 300px;
            }
            
            .export-buttons {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .btn-export {
                font-size: 0.85rem;
                padding: 8px 10px;
            }
        }
        
        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>💰 Expense Tracker</h1>
            <p>Track your expenses, manage your budget, analyze your spending</p>
        </div>
        
        <!-- Alert Messages -->
        <div id="alertSuccess" class="alert alert-success">
            <strong>✓ Success!</strong> <span id="successMessage"></span>
        </div>
        
        <div id="alertError" class="alert alert-error">
            <strong>✗ Error!</strong> <span id="errorMessage"></span>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">💵</div>
                <div class="label">Total Expenses</div>
                <div class="value">$<?php echo number_format($totalExpenses, 2); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="icon">📊</div>
                <div class="label">This Month</div>
                <div class="value">$<?php echo number_format($monthlyTotal, 2); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="icon">📝</div>
                <div class="label">Total Transactions</div>
                <div class="value"><?php echo $expenseCount; ?></div>
            </div>
            
            <div class="stat-card">
                <div class="icon">📈</div>
                <div class="label">Average Expense</div>
                <div class="value">$<?php echo number_format($avgExpense, 2); ?></div>
            </div>
        </div>
        
        <!-- Main Content Grid -->
        <div class="main-grid">
            <!-- Add Expense Form -->
            <div class="card">
                <h2>➕ Add New Expense</h2>
                
                <form id="expenseForm">
                    <div class="form-group">
                        <label for="amount">Amount ($)</label>
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01" required placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>">
                                    <?php echo $cat['icon'] . ' ' . $cat['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required placeholder="What did you spend on?"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <span>💾 Add Expense</span>
                    </button>
                </form>
            </div>
            
            <!-- Analytics Chart -->
            <div class="card">
                <h2>📊 Spending Analytics</h2>
                
                <div class="chart-container">
                    <canvas id="expenseChart"></canvas>
                </div>
                
                <div class="export-section">
                    <h3 style="margin-bottom: 15px; font-size: 1rem; color: var(--dark);">📤 Export Data</h3>
                    <div class="export-buttons">
                        <a href="?export=csv" class="btn btn-export btn-csv" title="Export as CSV file">
                            📊 CSV
                        </a>
                        <a href="?export=json" class="btn btn-export btn-json" title="Export as JSON file">
                            📋 JSON
                        </a>
                        <a href="?export=excel" class="btn btn-export btn-excel" title="Export as Excel file">
                            📗 Excel
                        </a>
                        <a href="?export=xml" class="btn btn-export btn-xml" title="Export as XML file">
                            📄 XML
                        </a>
                        <a href="?export=pdf" class="btn btn-export btn-pdf" title="Export as printable report" target="_blank">
                            📕 PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Expense List -->
        <div class="card expense-list-container">
            <h2>📋 Recent Expenses</h2>
            
            <div class="expense-list" id="expenseList">
                <?php if (empty($expenses)): ?>
                    <div class="empty-state">
                        <div class="icon">📭</div>
                        <h3>No expenses yet</h3>
                        <p>Start tracking your expenses by adding your first entry above!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($expenses as $expense): ?>
                        <?php
                            $category = array_filter($categories, function($cat) use ($expense) {
                                return $cat['id'] === $expense['category'];
                            });
                            $category = reset($category);
                        ?>
                        <div class="expense-item" data-id="<?php echo $expense['id']; ?>">
                            <div class="expense-info">
                                <div class="expense-header">
                                    <span class="category-badge" style="background-color: <?php echo $category['color']; ?>">
                                        <?php echo $category['icon'] . ' ' . $category['name']; ?>
                                    </span>
                                </div>
                                <div class="expense-description"><?php echo $expense['description']; ?></div>
                                <div class="expense-date">📅 <?php echo date('M d, Y', strtotime($expense['date'])); ?></div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="expense-amount">-$<?php echo number_format($expense['amount'], 2); ?></div>
                                <button class="btn btn-danger delete-btn" data-id="<?php echo $expense['id']; ?>">
                                    🗑️ Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Built with ❤️ for portfolio showcase | <a href="https://github.com/yourusername/expense-tracker-php" target="_blank">View on GitHub</a></p>
            <p>© <?php echo date('Y'); ?> Your Name. All rights reserved.</p>
        </div>
    </div>
    
    <script>
        // Category data for chart
        const categories = <?php echo json_encode($categories); ?>;
        const categoryTotals = <?php echo json_encode($categoryTotals); ?>;
        
        // Initialize Chart
        const ctx = document.getElementById('expenseChart').getContext('2d');
        
        const chartData = {
            labels: categories.map(cat => cat.name),
            datasets: [{
                label: 'Spending by Category',
                data: categories.map(cat => categoryTotals[cat.id]),
                backgroundColor: categories.map(cat => cat.color),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        };
        
        const chartConfig = {
            type: 'doughnut',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                family: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: $${value.toFixed(2)} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        };
        
        const expenseChart = new Chart(ctx, chartConfig);
        
        // Form submission
        document.getElementById('expenseForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'add_expense');
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', 'Expense added successfully!');
                    this.reset();
                    document.getElementById('date').value = new Date().toISOString().split('T')[0];
                    
                    // Reload page after short delay
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert('error', result.error || 'Failed to add expense');
                }
            } catch (error) {
                showAlert('error', 'Network error. Please try again.');
            }
        });
        
        // Delete expense
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (!confirm('Are you sure you want to delete this expense?')) {
                    return;
                }
                
                const id = this.dataset.id;
                const formData = new FormData();
                formData.append('action', 'delete_expense');
                formData.append('id', id);
                
                try {
                    const response = await fetch('', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        showAlert('success', 'Expense deleted successfully!');
                        
                        // Remove item with animation
                        const item = document.querySelector(`[data-id="${id}"]`);
                        item.style.animation = 'slideOutRight 0.3s ease-out';
                        
                        setTimeout(() => {
                            location.reload();
                        }, 300);
                    } else {
                        showAlert('error', result.error || 'Failed to delete expense');
                    }
                } catch (error) {
                    showAlert('error', 'Network error. Please try again.');
                }
            });
        });
        
        // Alert function
        function showAlert(type, message) {
            const alertId = type === 'success' ? 'alertSuccess' : 'alertError';
            const messageId = type === 'success' ? 'successMessage' : 'errorMessage';
            
            document.getElementById(messageId).textContent = message;
            document.getElementById(alertId).classList.add('show');
            
            setTimeout(() => {
                document.getElementById(alertId).classList.remove('show');
            }, 4000);
        }
        
        // Animation for slide out
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideOutRight {
                to {
                    opacity: 0;
                    transform: translateX(100%);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>


