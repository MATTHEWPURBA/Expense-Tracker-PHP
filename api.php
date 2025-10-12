<?php
/**
 * API Entry Point
 * 
 * RESTful API with full debugging and route tracking
 * All CRUD operations go through this file for easy debugging
 * 
 * @package ExpenseTracker
 */

require_once __DIR__ . '/bootstrap.php';

use ExpenseTracker\Router\Router;
use ExpenseTracker\Router\ApiResponse;
use ExpenseTracker\Controllers\ExpenseController;
use ExpenseTracker\Controllers\SettingsController;
use ExpenseTracker\Controllers\AuthController;
use ExpenseTracker\Middleware\AuthMiddleware;
use ExpenseTracker\Models\Model;
use ExpenseTracker\Services\Auth;

// Enable debug mode (can be disabled in production)
define('DEBUG_MODE', true);

// Set CORS headers for development
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Debug');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Clear query tracking for each request
Model::clearQueries();

// Create router instance
$router = new Router();

// =============================================================================
// PUBLIC ROUTES (No authentication required)
// =============================================================================

/**
 * API Health Check
 * GET /api/health
 */
$router->get('/api/health', function() {
    return ApiResponse::success([
        'status' => 'healthy',
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => '2.0.0'
    ], 'API is running');
});

/**
 * Get all available routes
 * GET /api/routes
 */
$router->get('/api/routes', function() use ($router) {
    return ApiResponse::success([
        'routes' => $router->getRoutes()
    ], 'Available API routes');
});

// =============================================================================
// AUTHENTICATION ROUTES
// =============================================================================

/**
 * User Login
 * POST /api/auth/login
 */
$router->post('/api/auth/login', [AuthController::class, 'login']);

/**
 * User Register
 * POST /api/auth/register
 */
$router->post('/api/auth/register', [AuthController::class, 'register']);

/**
 * User Logout
 * POST /api/auth/logout
 */
$router->post('/api/auth/logout', [AuthController::class, 'logout']);

// =============================================================================
// PROTECTED ROUTES (Authentication required)
// =============================================================================

// Apply authentication middleware to all routes below
$router->middleware(function() {
    $authMiddleware = new AuthMiddleware();
    $authMiddleware->handleApi();
});

/**
 * Get current user
 * GET /api/auth/me
 */
$router->get('/api/auth/me', function() {
    return ApiResponse::success([
        'user' => Auth::user()
    ]);
});

// =============================================================================
// EXPENSE ROUTES
// =============================================================================

/**
 * Get all expenses for current user
 * GET /api/expenses
 */
$router->get('/api/expenses', [ExpenseController::class, 'index']);

/**
 * Get single expense
 * GET /api/expenses/{id}
 */
$router->get('/api/expenses/{id}', [ExpenseController::class, 'show']);

/**
 * Create new expense
 * POST /api/expenses
 */
$router->post('/api/expenses', [ExpenseController::class, 'store']);

/**
 * Update expense
 * PUT /api/expenses/{id}
 */
$router->put('/api/expenses/{id}', [ExpenseController::class, 'update']);

/**
 * Delete expense
 * DELETE /api/expenses/{id}
 */
$router->delete('/api/expenses/{id}', [ExpenseController::class, 'destroy']);

/**
 * Get expense statistics
 * GET /api/expenses/stats/summary
 */
$router->get('/api/expenses/stats/summary', [ExpenseController::class, 'getStats']);

/**
 * Get category breakdown
 * GET /api/expenses/stats/categories
 */
$router->get('/api/expenses/stats/categories', [ExpenseController::class, 'getCategoryBreakdown']);

/**
 * Get dashboard data (all in one)
 * GET /api/dashboard
 */
$router->get('/api/dashboard', [ExpenseController::class, 'dashboard']);

// =============================================================================
// CATEGORY ROUTES
// =============================================================================

/**
 * Get all categories
 * GET /api/categories
 */
$router->get('/api/categories', function() {
    $categoryModel = new \ExpenseTracker\Models\Category();
    $categories = $categoryModel->getAllOrdered();
    
    return ApiResponse::success([
        'categories' => $categories,
        'count' => count($categories)
    ])->setQueries(Model::getQueries());
});

// =============================================================================
// SETTINGS ROUTES
// =============================================================================

/**
 * Get user settings
 * GET /api/settings
 */
$router->get('/api/settings', [SettingsController::class, 'index']);

/**
 * Update currency
 * PATCH /api/settings/currency
 */
$router->patch('/api/settings/currency', [SettingsController::class, 'updateCurrency']);

/**
 * Update user profile
 * PATCH /api/settings/profile
 */
$router->patch('/api/settings/profile', [SettingsController::class, 'updateProfile']);

// =============================================================================
// DISPATCH ROUTER
// =============================================================================

$router->dispatch();
