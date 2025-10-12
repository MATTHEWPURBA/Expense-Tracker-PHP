<?php
/**
 * Settings Controller
 * 
 * Handles user settings operations with API responses
 * 
 * @package ExpenseTracker\Controllers
 */

namespace ExpenseTracker\Controllers;

use ExpenseTracker\Models\User;
use ExpenseTracker\Models\Model;
use ExpenseTracker\Services\Auth;
use ExpenseTracker\Services\Currency;
use ExpenseTracker\Router\ApiResponse;

class SettingsController
{
    private $userModel;
    
    public function __construct()
    {
        $this->userModel = new User();
    }
    
    /**
     * Get settings data
     * GET /api/settings
     */
    public function index(): ApiResponse
    {
        $user = Auth::user();
        $currencies = Currency::getAll();
        
        return ApiResponse::success([
            'user' => $user,
            'currencies' => $currencies,
            'currentCurrency' => $user['currency'] ?? 'USD'
        ])->setQueries(Model::getQueries());
    }
    
    /**
     * Update user currency
     * PATCH /api/settings/currency
     */
    public function updateCurrency(): ApiResponse
    {
        $userId = Auth::id();
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $currency = $input['currency'] ?? '';
        
        if (empty($currency)) {
            return ApiResponse::error('Currency is required', 400);
        }
        
        if (!Currency::isValid($currency)) {
            return ApiResponse::error('Invalid currency code', 400);
        }
        
        $result = $this->userModel->updateCurrency($userId, $currency);
        
        if ($result) {
            // Update session
            $_SESSION['user']['currency'] = strtoupper($currency);
            
            return ApiResponse::success([
                'currency' => strtoupper($currency),
                'symbol' => Currency::getSymbol(strtoupper($currency))
            ], 'Currency updated successfully')->setQueries(Model::getQueries());
        }
        
        return ApiResponse::error('Failed to update currency', 500);
    }
    
    /**
     * Update user profile
     * PATCH /api/settings/profile
     */
    public function updateProfile(): ApiResponse
    {
        $userId = Auth::id();
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        
        $updateData = [];
        $errors = [];
        
        if (isset($input['name'])) {
            if (empty($input['name'])) {
                $errors['name'] = 'Name cannot be empty';
            } else {
                $updateData['name'] = Auth::sanitize($input['name']);
            }
        }
        
        if (isset($input['email'])) {
            if (empty($input['email'])) {
                $errors['email'] = 'Email cannot be empty';
            } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Invalid email format';
            } else {
                $updateData['email'] = Auth::sanitize($input['email']);
            }
        }
        
        if (!empty($errors)) {
            return ApiResponse::error('Validation failed', 422, $errors);
        }
        
        if (empty($updateData)) {
            return ApiResponse::error('No data to update', 400);
        }
        
        $result = $this->userModel->update($userId, $updateData);
        
        if ($result) {
            // Update session
            if (isset($updateData['name'])) {
                $_SESSION['user']['name'] = $updateData['name'];
            }
            if (isset($updateData['email'])) {
                $_SESSION['user']['email'] = $updateData['email'];
            }
            
            return ApiResponse::success([
                'user' => Auth::user()
            ], 'Profile updated successfully')->setQueries(Model::getQueries());
        }
        
        return ApiResponse::error('Failed to update profile', 500);
    }
    
    // =============================================================================
    // LEGACY METHODS (for backward compatibility)
    // =============================================================================
    
    /**
     * Get settings data (legacy method)
     */
    public function getSettingsData(): array
    {
        $user = Auth::user();
        $currencies = Currency::getAll();
        
        return [
            'user' => $user,
            'currencies' => $currencies,
            'currentCurrency' => $user['currency'] ?? 'USD'
        ];
    }
}
