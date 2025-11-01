<?php
/**
 * API Health Check Endpoint
 * 
 * Physical file endpoint for /api/health
 * Routes to main api.php router
 * 
 * @package ExpenseTracker
 */

// Set headers before including
header('Content-Type: application/json');

// Store original REQUEST_URI and modify it to match route pattern
$_SERVER['ORIGINAL_REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '';
$_SERVER['REQUEST_URI'] = '/api/health';

// Modify SCRIPT_NAME so Router knows we're using a physical file
$_SERVER['ORIGINAL_SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'] ?? '';
$_SERVER['SCRIPT_NAME'] = '/api.php';

// Include the main API router
require_once dirname(__DIR__) . '/api.php';

