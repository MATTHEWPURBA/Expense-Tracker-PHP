<?php
/**
 * AI Controller - Handles AI-powered features
 * 
 * API Endpoints:
 * - POST /api/ai/categorize - Auto-categorize expense
 * - POST /api/ai/parse - Parse natural language expense
 * - GET /api/ai/insights - Get spending insights
 * - GET /api/ai/predict - Predict monthly budget
 * - GET /api/ai/recommendations - Get smart recommendations
 * 
 * @package ExpenseTracker\Controllers
 */

namespace ExpenseTracker\Controllers;

use ExpenseTracker\Services\AIService;
use ExpenseTracker\Models\Expense;
use ExpenseTracker\Services\Auth;

class AIController
{
    private $aiService;
    private $expenseModel;
    
    public function __construct()
    {
        // Initialize AI service with API key from environment or config
        $apiKey = getenv('GEMINI_API_KEY') ?: null;
        $this->aiService = new AIService($apiKey);
        $this->expenseModel = new Expense();
    }
    
    /**
     * Auto-categorize expense description
     * 
     * POST /api/ai/categorize
     * Body: {"description": "bought groceries at walmart"}
     * Response: {"category": "food", "success": true}
     */
    public function categorize(): void
    {
        header('Content-Type: application/json');
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['description'])) {
                echo json_encode(['error' => 'Description required', 'success' => false]);
                return;
            }
            
            $category = $this->aiService->categorizeExpense($input['description']);
            
            echo json_encode([
                'category' => $category,
                'success' => true
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'error' => $e->getMessage(),
                'success' => false
            ]);
        }
    }
    
    /**
     * Parse natural language expense entry
     * 
     * POST /api/ai/parse
     * Body: {"text": "I spent $50 on pizza last night"}
     * Response: {"amount": 50, "description": "pizza", "category": "food", "success": true}
     */
    public function parseNaturalLanguage(): void
    {
        header('Content-Type: application/json');
        
        try {
            error_log("=== AI Parse Request Received ===");
            
            $rawInput = file_get_contents('php://input');
            error_log("[AI Controller] Raw request body: $rawInput");
            
            $input = json_decode($rawInput, true);
            
            if (!isset($input['text'])) {
                error_log("[AI Controller] ❌ Missing 'text' field in request");
                echo json_encode(['error' => 'Text input required', 'success' => false]);
                return;
            }
            
            error_log("[AI Controller] Input text: '{$input['text']}'");
            
            // Get user's currency
            $user = Auth::user();
            $userCurrency = $user['currency'] ?? 'USD';
            
            error_log("[AI Controller] User ID: " . ($user['id'] ?? 'unknown'));
            error_log("[AI Controller] User currency: $userCurrency");
            
            $parsed = $this->aiService->parseNaturalLanguageExpense($input['text'], $userCurrency);
            
            if (!$parsed) {
                error_log("[AI Controller] ❌ AIService returned null - check logs above for details");
                echo json_encode([
                    'error' => 'Could not parse expense from text. Check server logs.',
                    'success' => false
                ]);
                return;
            }
            
            error_log("[AI Controller] ✅ Parse successful!");
            error_log("[AI Controller] Returning: " . json_encode($parsed));
            
            echo json_encode([
                'amount' => $parsed['amount'],
                'description' => $parsed['description'],
                'category' => $parsed['category'],
                'success' => true
            ]);
            
        } catch (\Exception $e) {
            error_log("[AI Controller] ❌ Exception: " . $e->getMessage());
            error_log("[AI Controller] Stack trace: " . $e->getTraceAsString());
            
            echo json_encode([
                'error' => $e->getMessage(),
                'success' => false
            ]);
        }
    }
    
    /**
     * Get AI-powered spending insights
     * 
     * GET /api/ai/insights
     * Response: {"insights": "...", "success": true}
     */
    public function getInsights(): void
    {
        header('Content-Type: application/json');
        
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                echo json_encode(['error' => 'Not authenticated', 'success' => false]);
                return;
            }
            
            // Get user's expenses and stats
            $expenses = $this->expenseModel->getByUser($userId);
            $stats = $this->expenseModel->getStats($userId);
            
            if (empty($expenses)) {
                echo json_encode([
                    'insights' => 'Start adding expenses to get personalized insights!',
                    'success' => true
                ]);
                return;
            }
            
            $insights = $this->aiService->generateSpendingInsights($expenses, $stats);
            
            echo json_encode([
                'insights' => $insights,
                'success' => true
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'error' => $e->getMessage(),
                'success' => false
            ]);
        }
    }
    
    /**
     * Predict monthly budget needs
     * 
     * GET /api/ai/predict
     * Response: {"predicted_amount": 1234.56, "confidence": "high", "reasoning": "...", "success": true}
     */
    public function predictBudget(): void
    {
        header('Content-Type: application/json');
        
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                echo json_encode(['error' => 'Not authenticated', 'success' => false]);
                return;
            }
            
            $expenses = $this->expenseModel->getByUser($userId);
            
            if (empty($expenses)) {
                echo json_encode([
                    'error' => 'Need more expense data to make predictions',
                    'success' => false
                ]);
                return;
            }
            
            $prediction = $this->aiService->predictMonthlyBudget($expenses);
            
            echo json_encode(array_merge($prediction, ['success' => true]));
            
        } catch (\Exception $e) {
            echo json_encode([
                'error' => $e->getMessage(),
                'success' => false
            ]);
        }
    }
    
    /**
     * Get smart spending recommendations
     * 
     * GET /api/ai/recommendations
     * Response: {"recommendations": ["...", "..."], "success": true}
     */
    public function getRecommendations(): void
    {
        header('Content-Type: application/json');
        
        try {
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                echo json_encode(['error' => 'Not authenticated', 'success' => false]);
                return;
            }
            
            $categoryBreakdown = $this->expenseModel->getCategoryBreakdown($userId);
            $stats = $this->expenseModel->getStats($userId);
            
            if (empty($categoryBreakdown)) {
                echo json_encode([
                    'recommendations' => ['Start tracking your expenses to get personalized recommendations!'],
                    'success' => true
                ]);
                return;
            }
            
            $recommendations = $this->aiService->getSmartRecommendations(
                $categoryBreakdown,
                $stats['total']
            );
            
            echo json_encode([
                'recommendations' => $recommendations,
                'success' => true
            ]);
            
        } catch (\Exception $e) {
            echo json_encode([
                'error' => $e->getMessage(),
                'success' => false
            ]);
        }
    }
    
    /**
     * Extract expense from receipt image (OCR)
     * 
     * POST /api/ai/receipt
     * Body: {"image": "base64_encoded_image", "mimeType": "image/jpeg"}
     * Response: {"amount": 50.99, "merchant": "Store", "date": "2025-10-12", "category": "food", "items": [...], "success": true}
     */
    public function scanReceipt(): void
    {
        header('Content-Type: application/json');
        
        try {
            error_log("=== AI Receipt OCR Request Received ===");
            
            $userId = $_SESSION['user_id'] ?? null;
            
            if (!$userId) {
                error_log("[AI Receipt] ❌ User not authenticated");
                echo json_encode(['error' => 'Not authenticated', 'success' => false]);
                return;
            }
            
            $rawInput = file_get_contents('php://input');
            error_log("[AI Receipt] Request body size: " . strlen($rawInput) . " bytes");
            
            $input = json_decode($rawInput, true);
            
            if (!isset($input['image'])) {
                error_log("[AI Receipt] ❌ Missing 'image' field in request");
                echo json_encode(['error' => 'Image data required', 'success' => false]);
                return;
            }
            
            // Get MIME type (default to jpeg)
            $mimeType = $input['mimeType'] ?? 'image/jpeg';
            
            // Validate MIME type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/heic', 'image/heif'];
            if (!in_array(strtolower($mimeType), $allowedTypes)) {
                error_log("[AI Receipt] ❌ Invalid MIME type: $mimeType");
                echo json_encode(['error' => 'Unsupported image format. Use JPEG, PNG, or WebP.', 'success' => false]);
                return;
            }
            
            // Remove data URL prefix if present
            $imageData = $input['image'];
            if (strpos($imageData, 'data:') === 0) {
                error_log("[AI Receipt] Removing data URL prefix...");
                $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
            }
            
            // Validate base64
            $decodedSize = strlen(base64_decode($imageData, true));
            error_log("[AI Receipt] Image size (decoded): " . number_format($decodedSize) . " bytes");
            
            if ($decodedSize === 0) {
                error_log("[AI Receipt] ❌ Invalid base64 data");
                echo json_encode(['error' => 'Invalid image data', 'success' => false]);
                return;
            }
            
            // Check size limit (10MB)
            if ($decodedSize > 10 * 1024 * 1024) {
                error_log("[AI Receipt] ❌ Image too large: " . number_format($decodedSize) . " bytes");
                echo json_encode(['error' => 'Image too large. Maximum size is 10MB.', 'success' => false]);
                return;
            }
            
            // Get user's currency
            $user = Auth::user();
            $userCurrency = $user['currency'] ?? 'USD';
            
            error_log("[AI Receipt] User ID: " . $userId);
            error_log("[AI Receipt] User currency: $userCurrency");
            error_log("[AI Receipt] MIME type: $mimeType");
            error_log("[AI Receipt] Processing receipt...");
            
            // Extract expense from receipt
            $result = $this->aiService->extractExpenseFromReceiptImage($imageData, $mimeType, $userCurrency);
            
            if (!$result) {
                error_log("[AI Receipt] ❌ AI service returned null");
                echo json_encode([
                    'error' => 'Could not extract information from receipt. Please ensure the image is clear and try again.',
                    'success' => false
                ]);
                return;
            }
            
            error_log("[AI Receipt] ✅ Successfully extracted receipt data!");
            error_log("[AI Receipt] Amount: {$result['amount']}, Merchant: {$result['merchant']}");
            
            echo json_encode(array_merge($result, ['success' => true]));
            
        } catch (\Exception $e) {
            error_log("[AI Receipt] ❌ Exception: " . $e->getMessage());
            error_log("[AI Receipt] Stack trace: " . $e->getTraceAsString());
            
            echo json_encode([
                'error' => 'Error processing receipt: ' . $e->getMessage(),
                'success' => false
            ]);
        }
    }
}

