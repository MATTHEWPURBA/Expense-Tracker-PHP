<?php
/**
 * Expense Controller
 * 
 * Handles expense-related operations
 * 
 * @package ExpenseTracker\Controllers
 */

namespace ExpenseTracker\Controllers;

use ExpenseTracker\Models\Expense;
use ExpenseTracker\Models\Category;
use ExpenseTracker\Services\Auth;

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
     * Get dashboard data
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
     * Add new expense
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
     * Delete expense
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
     * Get expenses (for AJAX)
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
