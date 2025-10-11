<?php
/**
 * Settings Controller
 * 
 * Handles user settings operations
 * 
 * @package ExpenseTracker\Controllers
 */

namespace ExpenseTracker\Controllers;

use ExpenseTracker\Models\User;
use ExpenseTracker\Services\Auth;
use ExpenseTracker\Services\Currency;

class SettingsController
{
    private $userModel;
    
    public function __construct()
    {
        $this->userModel = new User();
    }
    
    /**
     * Get settings data
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
    
    /**
     * Update user currency
     */
    public function updateCurrency(): void
    {
        $userId = Auth::id();
        $currency = $_POST['currency'] ?? '';
        
        if (empty($currency)) {
            jsonResponse([
                'success' => false,
                'error' => 'Currency is required'
            ], 400);
        }
        
        if (!Currency::isValid($currency)) {
            jsonResponse([
                'success' => false,
                'error' => 'Invalid currency code'
            ], 400);
        }
        
        $result = $this->userModel->updateCurrency($userId, $currency);
        
        if ($result) {
            // Update session
            $_SESSION['user']['currency'] = strtoupper($currency);
            
            jsonResponse([
                'success' => true,
                'message' => 'Currency updated successfully',
                'currency' => strtoupper($currency)
            ]);
        } else {
            jsonResponse([
                'success' => false,
                'error' => 'Failed to update currency'
            ], 500);
        }
    }
}

