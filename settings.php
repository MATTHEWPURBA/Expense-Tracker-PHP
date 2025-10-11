<?php
/**
 * Settings Page
 * 
 * User settings management
 */

require_once __DIR__ . '/bootstrap.php';

use ExpenseTracker\Middleware\AuthMiddleware;
use ExpenseTracker\Controllers\SettingsController;

// Require authentication
AuthMiddleware::requireAuth();

$controller = new SettingsController();

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'update_currency':
            $controller->updateCurrency();
            break;
        default:
            jsonResponse(['success' => false, 'error' => 'Invalid action'], 400);
    }
    exit;
}

// Get settings data
$data = $controller->getSettingsData();
extract($data);

// Load view
require_once __DIR__ . '/views/settings/index.php';

