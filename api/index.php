<?php
/**
 * API Directory Entry Point (Fallback)
 * 
 * This file routes API requests when accessed via /api/ or /api/index.php
 * 
 * @package ExpenseTracker
 */

// Set headers
header('Content-Type: application/json');

// Get the path from REQUEST_URI
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);

// Extract the API path
// e.g., /api/auth/login -> /api/auth/login
if (strpos($path, '/api/') === 0) {
    // Store original and set for router
    $_SERVER['ORIGINAL_REQUEST_URI'] = $requestUri;
    $_SERVER['REQUEST_URI'] = $path;
    $_SERVER['ORIGINAL_SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'] ?? '';
    $_SERVER['SCRIPT_NAME'] = '/api.php';
    
    // Route to main API router
    require_once dirname(__DIR__) . '/api.php';
} else {
    // Return 404 for invalid paths
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'API endpoint not found'
    ]);
}

