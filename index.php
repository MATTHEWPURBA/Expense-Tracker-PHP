<?php
/**
 * Dashboard Entry Point
 * 
 * Main application dashboard using the new micro-architecture
 * 
 * @package ExpenseTracker
 */

require_once __DIR__ . '/bootstrap.php';

use ExpenseTracker\Controllers\ExpenseController;
use ExpenseTracker\Controllers\ExportController;
use ExpenseTracker\Middleware\AuthMiddleware;
use ExpenseTracker\Services\Currency;

// Ensure user is authenticated
$authMiddleware = new AuthMiddleware();
$authMiddleware->handle();

// Handle export requests
if (isset($_GET['export'])) {
    $exportController = new ExportController();
    $exportController->export($_GET['export']);
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expenseController = new ExpenseController();
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_expense':
            $expenseController->addExpense();
            break;
        case 'delete_expense':
            $expenseController->deleteExpense();
            break;
        case 'get_expenses':
            $expenseController->getExpenses();
            break;
    }
}

// Get dashboard data
$expenseController = new ExpenseController();
$data = $expenseController->getDashboardData();

// Extract data for view
$expenses = $data['expenses'] ?? [];
$categories = $data['categories'] ?? [];
$stats = $data['stats'] ?? [];
$categoryBreakdown = $data['categoryBreakdown'] ?? [];
$user = $data['user'] ?? [];
$userCurrency = $user['currency'] ?? 'USD';
$currencySymbol = Currency::getSymbol($userCurrency);

// Sort expenses by date (newest first)
usort($expenses, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// Render dashboard view
require_once __DIR__ . '/views/dashboard/index.php';

