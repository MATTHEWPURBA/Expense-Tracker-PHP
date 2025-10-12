<?php
/**
 * AI Service using Google Gemini API (FREE TIER)
 * 
 * Features:
 * - Smart expense categorization
 * - Natural language expense parsing
 * - Spending insights and recommendations
 * - Budget predictions
 * 
 * FREE TIER LIMITS:
 * - 1,500 requests per day
 * - 60 requests per minute
 * - No credit card required
 * 
 * Setup: Get your FREE API key from https://makersuite.google.com/app/apikey
 * 
 * @package ExpenseTracker\Services
 */

namespace ExpenseTracker\Services;

use ExpenseTracker\Services\Currency;

class AIService
{
    private $apiKey;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    
    /**
     * Initialize AI Service
     * 
     * Get your FREE API key from: https://makersuite.google.com/app/apikey
     */
    public function __construct(?string $apiKey = null)
    {
        // You can pass API key or set it in environment
        $this->apiKey = $apiKey ?? getenv('GEMINI_API_KEY');
        
        if (!$this->apiKey) {
            throw new \Exception('Gemini API key not set. Get your FREE key from https://makersuite.google.com/app/apikey');
        }
    }
    
    /**
     * Send request to Gemini API
     */
    private function makeRequest(string $prompt): ?string
    {
        $url = $this->apiUrl;
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 1024,
            ]
        ];
        
        error_log("[AI] Making request to Gemini API...");
        error_log("[AI] Model: gemini-2.5-flash");
        error_log("[AI] Prompt length: " . strlen($prompt) . " characters");
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-goog-api-key: ' . $this->apiKey
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        error_log("[AI] Response HTTP code: $httpCode");
        
        if ($curlError) {
            error_log("[AI] ❌ CURL Error: $curlError");
            return null;
        }
        
        if ($httpCode !== 200) {
            error_log("[AI] ❌ API Error Response: $response");
            return null;
        }
        
        $result = json_decode($response, true);
        
        if (!$result) {
            error_log("[AI] ❌ Failed to decode JSON response");
            error_log("[AI] Raw response: " . substr($response, 0, 500));
            return null;
        }
        
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
        
        if (!$text) {
            error_log("[AI] ❌ No text found in response structure");
            error_log("[AI] Response keys: " . json_encode(array_keys($result)));
            return null;
        }
        
        error_log("[AI] ✅ Success! Response: " . substr($text, 0, 200) . (strlen($text) > 200 ? '...' : ''));
        
        return $text;
    }
    
    /**
     * 🤖 Smart Expense Categorization
     * 
     * Automatically categorize an expense based on description
     * 
     * @param string $description The expense description
     * @return string Category ID (food, transport, utilities, etc.)
     */
    public function categorizeExpense(string $description): string
    {
        $prompt = "You are an expense categorization AI. Categorize the following expense into ONE of these categories:
        
Categories (respond with ONLY the ID):
- food: Food & Dining (restaurants, groceries, snacks)
- transport: Transportation (gas, uber, public transit, parking)
- utilities: Utilities (electricity, water, internet, phone bills)
- entertainment: Entertainment (movies, games, subscriptions, hobbies)
- healthcare: Healthcare (doctor visits, medicine, gym, wellness)
- shopping: Shopping (clothes, electronics, home goods)
- other: Other (anything else)

Expense description: \"$description\"

Respond with ONLY the category ID (e.g., 'food' or 'transport'). No explanation needed.";

        $response = $this->makeRequest($prompt);
        
        if (!$response) {
            return 'other'; // Default fallback
        }
        
        // Clean response and extract category ID
        $category = strtolower(trim($response));
        $validCategories = ['food', 'transport', 'utilities', 'entertainment', 'healthcare', 'shopping', 'other'];
        
        // Find matching category
        foreach ($validCategories as $valid) {
            if (strpos($category, $valid) !== false) {
                return $valid;
            }
        }
        
        return 'other';
    }
    
    /**
     * 💬 Parse Natural Language Expense Entry
     * 
     * Examples:
     * - "I spent $50 on groceries at Walmart"
     * - "Paid 25 dollars for uber ride to airport"
     * - "$15.99 for Netflix subscription"
     * - "beli pizza 50 ribu rupiah" (Indonesian)
     * - "買了100元的咖啡" (Chinese)
     * 
     * @param string $text Natural language input
     * @param string $userCurrency User's currency code (e.g., 'USD', 'IDR', 'EUR')
     * @return array ['amount' => float, 'description' => string, 'category' => string] or null
     */
    public function parseNaturalLanguageExpense(string $text, string $userCurrency = 'USD'): ?array
    {
        // Get currency info
        $currencyInfo = Currency::get($userCurrency);
        $currencyName = $currencyInfo['name'] ?? $userCurrency;
        $currencySymbol = $currencyInfo['symbol'] ?? '$';
        
        $prompt = "You are a multilingual expense parser. Extract expense information from ANY language.

Text: \"$text\"

User's currency: {$userCurrency} ({$currencyName}, symbol: {$currencySymbol})

IMPORTANT PARSING RULES:
1. Accept ANY language (English, Indonesian, Chinese, Spanish, etc.)
2. Recognize amounts in ANY format:
   - \"50\" or \"50000\" = exact number
   - \"50 ribu\" or \"50k\" or \"50 thousand\" = 50,000
   - \"1 juta\" or \"1m\" or \"1 million\" = 1,000,000
   - Handle decimals: \"50.5\" or \"50,5\" = 50.5
3. Currency keywords to recognize (ignore in amount):
   - USD: dollar, dollars, USD, \$
   - IDR: rupiah, ribu, juta, IDR, Rp
   - EUR: euro, euros, EUR, €
   - GBP: pound, pounds, GBP, £
4. Extract the numeric amount ONLY

Examples:
- \"beli pizza 500 ribu\" → amount: 500000, description: \"pizza\"
- \"I spent \$50 on coffee\" → amount: 50, description: \"coffee\"
- \"買了100元的咖啡\" → amount: 100, description: \"咖啡/coffee\"
- \"uber ke kantor 35k\" → amount: 35000, description: \"uber ke kantor\"

Parse and return ONLY a JSON object with this exact format:
{
  \"amount\": <number>,
  \"description\": \"<short description>\",
  \"category\": \"<category_id>\"
}

Categories (use ID only):
- food: Food & Dining (restaurants, groceries, snacks, pizza, coffee)
- transport: Transportation (uber, taxi, gas, parking, bus)
- utilities: Utilities (electricity, water, internet, phone)
- entertainment: Entertainment (movies, games, subscriptions)
- healthcare: Healthcare (doctor, medicine, gym)
- shopping: Shopping (clothes, electronics, general shopping)
- other: Other (anything else)

If you cannot parse the expense, return: {\"error\": \"Could not parse expense\"}

Respond with ONLY the JSON, no other text.";

        error_log("[AI Parse] Input text: '$text'");
        error_log("[AI Parse] User currency: $userCurrency");
        
        $response = $this->makeRequest($prompt);
        
        if (!$response) {
            error_log("[AI Parse] ❌ AI returned no response");
            return null;
        }
        
        error_log("[AI Parse] Raw AI response: " . substr($response, 0, 300));
        
        // Extract JSON from response
        $originalResponse = $response;
        $response = trim($response);
        
        if (strpos($response, '```json') !== false) {
            error_log("[AI Parse] Extracting JSON from code block...");
            preg_match('/```json\s*(.*?)\s*```/s', $response, $matches);
            $response = $matches[1] ?? $response;
        }
        
        error_log("[AI Parse] JSON to parse: $response");
        
        $data = json_decode($response, true);
        
        if (!$data) {
            error_log("[AI Parse] ❌ Failed to decode JSON");
            error_log("[AI Parse] JSON error: " . json_last_error_msg());
            error_log("[AI Parse] Original response: " . substr($originalResponse, 0, 500));
            return null;
        }
        
        if (isset($data['error'])) {
            error_log("[AI Parse] ❌ AI returned error: " . $data['error']);
            return null;
        }
        
        if (!isset($data['amount'])) {
            error_log("[AI Parse] ❌ No amount field in parsed data");
            error_log("[AI Parse] Available fields: " . json_encode(array_keys($data)));
            return null;
        }
        
        $result = [
            'amount' => floatval($data['amount']),
            'description' => $data['description'] ?? '',
            'category' => $data['category'] ?? 'other'
        ];
        
        error_log("[AI Parse] ✅ Successfully parsed! Amount: {$result['amount']}, Description: '{$result['description']}', Category: {$result['category']}");
        
        return $result;
    }
    
    /**
     * 📊 Generate Spending Insights
     * 
     * Analyze user's expenses and provide personalized insights
     * 
     * @param array $expenses Array of expense records
     * @param array $stats Expense statistics
     * @return string AI-generated insights
     */
    public function generateSpendingInsights(array $expenses, array $stats): string
    {
        // Prepare expense summary for AI
        $expenseSummary = [];
        foreach ($expenses as $expense) {
            $expenseSummary[] = [
                'amount' => $expense['amount'],
                'category' => $expense['category_name'] ?? $expense['category'],
                'date' => $expense['date']
            ];
        }
        
        $expenseJson = json_encode(array_slice($expenseSummary, 0, 50)); // Last 50 expenses
        $statsJson = json_encode($stats);
        
        $prompt = "You are a personal finance advisor. Analyze these expenses and provide 3-4 actionable insights.

Expense Statistics:
$statsJson

Recent Expenses:
$expenseJson

Provide:
1. Spending patterns you notice
2. Categories where they're spending the most
3. Practical money-saving suggestions
4. Any concerning trends

Keep it friendly, concise (3-4 bullet points), and actionable. Use emojis for visual appeal.";

        $response = $this->makeRequest($prompt);
        
        return $response ?? "Unable to generate insights at this time.";
    }
    
    /**
     * 🎯 Predict Monthly Budget Needs
     * 
     * Based on past expenses, predict how much user will need
     * 
     * @param array $expenses Historical expenses
     * @return array ['predicted_amount' => float, 'confidence' => string, 'reasoning' => string]
     */
    public function predictMonthlyBudget(array $expenses): array
    {
        // Calculate monthly averages per category
        $categoryTotals = [];
        foreach ($expenses as $expense) {
            $category = $expense['category_name'] ?? $expense['category'];
            if (!isset($categoryTotals[$category])) {
                $categoryTotals[$category] = 0;
            }
            $categoryTotals[$category] += floatval($expense['amount']);
        }
        
        $categoryJson = json_encode($categoryTotals);
        
        $prompt = "You are a financial forecasting AI. Based on these spending patterns, predict next month's budget needs.

Category Spending (recent data):
$categoryJson

Provide a JSON response with:
{
  \"predicted_amount\": <number>,
  \"confidence\": \"high/medium/low\",
  \"reasoning\": \"<brief explanation>\"
}

Respond with ONLY the JSON, no other text.";

        $response = $this->makeRequest($prompt);
        
        if (!$response) {
            return [
                'predicted_amount' => array_sum($categoryTotals),
                'confidence' => 'low',
                'reasoning' => 'Based on historical average'
            ];
        }
        
        // Parse JSON response
        $response = trim($response);
        if (strpos($response, '```json') !== false) {
            preg_match('/```json\s*(.*?)\s*```/s', $response, $matches);
            $response = $matches[1] ?? $response;
        }
        
        $data = json_decode($response, true);
        
        return $data ?: [
            'predicted_amount' => array_sum($categoryTotals),
            'confidence' => 'low',
            'reasoning' => 'Based on historical average'
        ];
    }
    
    /**
     * 🧾 Extract Expense from Receipt Text
     * 
     * For future OCR integration - parse receipt text
     * 
     * @param string $receiptText Text extracted from receipt
     * @return array Extracted expense details
     */
    public function extractExpenseFromReceipt(string $receiptText): ?array
    {
        $prompt = "Extract expense information from this receipt:

$receiptText

Return ONLY a JSON object:
{
  \"amount\": <total_amount>,
  \"merchant\": \"<store_name>\",
  \"date\": \"YYYY-MM-DD\",
  \"category\": \"<category_id>\",
  \"items\": [\"item1\", \"item2\"]
}

Categories: food, transport, utilities, entertainment, healthcare, shopping, other

If you cannot parse, return: {\"error\": \"Could not parse receipt\"}

Respond with ONLY the JSON.";

        $response = $this->makeRequest($prompt);
        
        if (!$response) {
            return null;
        }
        
        // Parse JSON
        $response = trim($response);
        if (strpos($response, '```json') !== false) {
            preg_match('/```json\s*(.*?)\s*```/s', $response, $matches);
            $response = $matches[1] ?? $response;
        }
        
        $data = json_decode($response, true);
        
        if (!$data || isset($data['error'])) {
            return null;
        }
        
        return $data;
    }
    
    /**
     * 💡 Get Smart Recommendations
     * 
     * Based on user's spending, suggest ways to save money
     * 
     * @param array $categoryBreakdown Spending by category
     * @param float $totalSpent Total amount spent
     * @return array List of recommendations
     */
    public function getSmartRecommendations(array $categoryBreakdown, float $totalSpent): array
    {
        $breakdownJson = json_encode($categoryBreakdown);
        
        $prompt = "Analyze this spending breakdown and provide 3-5 specific, actionable money-saving recommendations:

Category Breakdown:
$breakdownJson

Total Spent: $$totalSpent

Return ONLY a JSON array of recommendations:
[
  \"🍽️ Recommendation 1\",
  \"🚗 Recommendation 2\",
  \"💡 Recommendation 3\"
]

Make them practical, specific, and include emojis. Focus on the highest spending categories.

Respond with ONLY the JSON array.";

        $response = $this->makeRequest($prompt);
        
        if (!$response) {
            return ["Unable to generate recommendations at this time."];
        }
        
        // Parse JSON array
        $response = trim($response);
        if (strpos($response, '```json') !== false) {
            preg_match('/```json\s*(.*?)\s*```/s', $response, $matches);
            $response = $matches[1] ?? $response;
        }
        
        $recommendations = json_decode($response, true);
        
        return is_array($recommendations) ? $recommendations : ["Focus on tracking your expenses regularly!"];
    }
}

