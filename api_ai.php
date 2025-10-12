<?php
/**
 * AI API Router
 * 
 * Routes AI-related API requests to the AIController
 * 
 * Endpoints:
 * POST /api_ai.php?action=categorize
 * POST /api_ai.php?action=parse
 * POST /api_ai.php?action=receipt
 * GET  /api_ai.php?action=insights
 * GET  /api_ai.php?action=predict
 * GET  /api_ai.php?action=recommendations
 */

// Start session
session_start();

// Load bootstrap
require_once __DIR__ . '/bootstrap.php';

use ExpenseTracker\Controllers\AIController;

// Enable CORS for API requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get action from query parameter
$action = $_GET['action'] ?? '';

// Validate action
$validActions = ['categorize', 'parse', 'receipt', 'insights', 'predict', 'recommendations'];

if (!in_array($action, $validActions)) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Invalid action',
        'valid_actions' => $validActions,
        'success' => false
    ]);
    exit;
}

// Initialize controller
try {
    $controller = new AIController();
    
    // Route to appropriate method
    switch ($action) {
        case 'categorize':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed. Use POST.');
            }
            $controller->categorize();
            break;
            
        case 'parse':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed. Use POST.');
            }
            $controller->parseNaturalLanguage();
            break;
            
        case 'receipt':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed. Use POST.');
            }
            $controller->scanReceipt();
            break;
            
        case 'insights':
            $controller->getInsights();
            break;
            
        case 'predict':
            $controller->predictBudget();
            break;
            
        case 'recommendations':
            $controller->getRecommendations();
            break;
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => $e->getMessage(),
        'success' => false
    ]);
}

