<?php
/**
 * Expense Controller
 * 
 * Handles expense-related operations with API responses
 * 
 * @package ExpenseTracker\Controllers
 */

namespace ExpenseTracker\Controllers;

use ExpenseTracker\Models\Expense;
use ExpenseTracker\Models\Category;
use ExpenseTracker\Models\Model;
use ExpenseTracker\Services\Auth;
use ExpenseTracker\Router\ApiResponse;

class ExpenseController
{
    private $expenseModel;
    private $categoryModel;
    
    public function __construct()
    {
        $this->expenseModel = new Expense();
        $this->categoryModel = new Category();
    }
    
    /**
     * Get all expenses for current user
     * GET /api/expenses
     */
    public function index(): ApiResponse
    {
        $userId = Auth::id();
        $expenses = $this->expenseModel->getByUser($userId);
        
        return ApiResponse::success([
            'expenses' => $expenses,
            'count' => count($expenses)
        ])->setQueries(Model::getQueries());
    }
    
    /**
     * Get single expense
     * GET /api/expenses/{id}
     */
    public function show(array $params): ApiResponse
    {
        $userId = Auth::id();
        $expenseId = $params['id'] ?? null;
        
        if (!$expenseId) {
            return ApiResponse::error('Expense ID is required', 400);
        }
        
        $expense = $this->expenseModel->find($expenseId);
        
        if (!$expense || $expense['user_id'] != $userId) {
            return ApiResponse::error('Expense not found', 404);
        }
        
        return ApiResponse::success([
            'expense' => $expense
        ])->setQueries(Model::getQueries());
    }
    
    /**
     * Create new expense
     * POST /api/expenses
     */
    public function store(): ApiResponse
    {
        $userId = Auth::id();
        
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Validate
        $errors = [];
        
        if (!isset($input['amount']) || $input['amount'] <= 0) {
            $errors['amount'] = 'Amount must be greater than 0';
        }
        
        if (empty($input['category'])) {
            $errors['category'] = 'Category is required';
        }
        
        if (!empty($errors)) {
            return ApiResponse::error('Validation failed', 422, $errors);
        }
        
        // Create expense
        $newExpense = [
            'id' => uniqid('exp_', true),
            'user_id' => $userId,
            'amount' => floatval($input['amount']),
            'category' => $input['category'],
            'description' => htmlspecialchars($input['description'] ?? ''),
            'date' => $input['date'] ?? date('Y-m-d')
        ];
        
        $result = $this->expenseModel->create($newExpense);
        
        if ($result) {
            $newExpense['created_at'] = date('Y-m-d H:i:s');
            
            return ApiResponse::created([
                'expense' => $newExpense
            ])->setQueries(Model::getQueries());
        }
        
        return ApiResponse::error('Failed to create expense', 500);
    }
    
    /**
     * Update expense
     * PUT /api/expenses/{id}
     */
    public function update(array $params): ApiResponse
    {
        $userId = Auth::id();
        $expenseId = $params['id'] ?? null;
        
        if (!$expenseId) {
            return ApiResponse::error('Expense ID is required', 400);
        }
        
        // Check if expense exists and belongs to user
        $expense = $this->expenseModel->find($expenseId);
        
        if (!$expense || $expense['user_id'] != $userId) {
            return ApiResponse::error('Expense not found', 404);
        }
        
        // Get JSON input
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        // Build update data
        $updateData = [];
        
        if (isset($input['amount'])) {
            if ($input['amount'] <= 0) {
                return ApiResponse::error('Amount must be greater than 0', 422);
            }
            $updateData['amount'] = floatval($input['amount']);
        }
        
        if (isset($input['category'])) {
            if (empty($input['category'])) {
                return ApiResponse::error('Category cannot be empty', 422);
            }
            $updateData['category'] = $input['category'];
        }
        
        if (isset($input['description'])) {
            $updateData['description'] = htmlspecialchars($input['description']);
        }
        
        if (isset($input['date'])) {
            $updateData['date'] = $input['date'];
        }
        
        if (empty($updateData)) {
            return ApiResponse::error('No data to update', 400);
        }
        
        $result = $this->expenseModel->update($expenseId, $updateData);
        
        if ($result) {
            $updatedExpense = $this->expenseModel->find($expenseId);
            
            return ApiResponse::success([
                'expense' => $updatedExpense
            ], 'Expense updated successfully')->setQueries(Model::getQueries());
        }
        
        return ApiResponse::error('Failed to update expense', 500);
    }
    
    /**
     * Delete expense
     * DELETE /api/expenses/{id}
     */
    public function destroy(array $params): ApiResponse
    {
        $userId = Auth::id();
        $expenseId = $params['id'] ?? null;
        
        if (!$expenseId) {
            return ApiResponse::error('Expense ID is required', 400);
        }
        
        $result = $this->expenseModel->deleteByUser($expenseId, $userId);
        
        if ($result) {
            return ApiResponse::success([], 'Expense deleted successfully')
                ->setQueries(Model::getQueries());
        }
        
        return ApiResponse::error('Failed to delete expense', 500);
    }
    
    /**
     * Get expense statistics
     * GET /api/expenses/stats/summary
     */
    public function getStats(): ApiResponse
    {
        $userId = Auth::id();
        $stats = $this->expenseModel->getStats($userId);
        
        return ApiResponse::success([
            'stats' => $stats
        ])->setQueries(Model::getQueries());
    }
    
    /**
     * Get category breakdown
     * GET /api/expenses/stats/categories
     */
    public function getCategoryBreakdown(): ApiResponse
    {
        $userId = Auth::id();
        $breakdown = $this->expenseModel->getCategoryBreakdown($userId);
        
        return ApiResponse::success([
            'breakdown' => $breakdown
        ])->setQueries(Model::getQueries());
    }
    
    /**
     * Get full dashboard data
     * GET /api/dashboard
     */
    public function dashboard(): ApiResponse
    {
        $userId = Auth::id();
        
        return ApiResponse::success([
            'expenses' => $this->expenseModel->getByUser($userId),
            'categories' => $this->categoryModel->getAllOrdered(),
            'stats' => $this->expenseModel->getStats($userId),
            'categoryBreakdown' => $this->expenseModel->getCategoryBreakdown($userId),
            'user' => Auth::user()
        ])->setQueries(Model::getQueries());
    }
    
    // =============================================================================
    // LEGACY METHODS (for backward compatibility)
    // =============================================================================
    
    /**
     * Get dashboard data (legacy method)
     */
    public function getDashboardData(): array
    {
        $userId = Auth::id();
        
        return [
            'expenses' => $this->expenseModel->getByUser($userId),
            'categories' => $this->categoryModel->getAllOrdered(),
            'stats' => $this->expenseModel->getStats($userId),
            'categoryBreakdown' => $this->expenseModel->getCategoryBreakdown($userId),
            'user' => Auth::user()
        ];
    }
    
    /**
     * Add new expense (legacy method)
     */
    public function addExpense(): void
    {
        $userId = Auth::id();
        
        $newExpense = [
            'id' => uniqid('exp_', true),
            'user_id' => $userId,
            'amount' => floatval($_POST['amount'] ?? 0),
            'category' => $_POST['category'] ?? '',
            'description' => htmlspecialchars($_POST['description'] ?? ''),
            'date' => $_POST['date'] ?? date('Y-m-d')
        ];
        
        // Validate
        if ($newExpense['amount'] <= 0) {
            jsonResponse([
                'success' => false,
                'error' => 'Invalid amount'
            ], 400);
        }
        
        if (empty($newExpense['category'])) {
            jsonResponse([
                'success' => false,
                'error' => 'Category is required'
            ], 400);
        }
        
        $result = $this->expenseModel->create($newExpense);
        
        if ($result) {
            $newExpense['created_at'] = date('Y-m-d H:i:s');
            jsonResponse([
                'success' => true,
                'expense' => $newExpense
            ]);
        } else {
            jsonResponse([
                'success' => false,
                'error' => 'Failed to save expense'
            ], 500);
        }
    }
    
    /**
     * Delete expense (legacy method)
     */
    public function deleteExpense(): void
    {
        $userId = Auth::id();
        $expenseId = $_POST['id'] ?? '';
        
        if (empty($expenseId)) {
            jsonResponse([
                'success' => false,
                'error' => 'Expense ID is required'
            ], 400);
        }
        
        $result = $this->expenseModel->deleteByUser($expenseId, $userId);
        
        if ($result) {
            jsonResponse([
                'success' => true
            ]);
        } else {
            jsonResponse([
                'success' => false,
                'error' => 'Failed to delete expense'
            ], 500);
        }
    }
    
    /**
     * Get expenses (legacy method)
     */
    public function getExpenses(): void
    {
        $userId = Auth::id();
        $expenses = $this->expenseModel->getByUser($userId);
        
        jsonResponse([
            'success' => true,
            'expenses' => $expenses
        ]);
    }
}
