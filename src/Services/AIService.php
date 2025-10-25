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
                'maxOutputTokens' => 2048,
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
            error_log("[AI] Full response structure: " . json_encode($result, JSON_PRETTY_PRINT));
            
            // Try alternative response structure
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $text = $result['candidates'][0]['content']['parts'][0]['text'];
            } elseif (isset($result['candidates'][0]['text'])) {
                $text = $result['candidates'][0]['text'];
            } elseif (isset($result['text'])) {
                $text = $result['text'];
            }
            
            if (!$text) {
                return null;
            }
        }
        
        error_log("[AI] ✅ Success! Response: " . substr($text, 0, 200) . (strlen($text) > 200 ? '...' : ''));
        
        return $text;
    }
    
    /**
     * Send request to Gemini API with image (Vision API)
     * 
     * @param string $prompt Text prompt
     * @param string $imageBase64 Base64 encoded image
     * @param string $mimeType Image MIME type (e.g., 'image/jpeg', 'image/png')
     * @return string|null AI response
     */
    private function makeVisionRequest(string $prompt, string $imageBase64, string $mimeType): ?string
    {
        $url = $this->apiUrl;
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $imageBase64
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.4,
                'topK' => 32,
                'topP' => 0.95,
                'maxOutputTokens' => 2048,
            ]
        ];
        
        error_log("[AI Vision] Making vision request to Gemini API...");
        error_log("[AI Vision] Model: gemini-2.5-flash");
        error_log("[AI Vision] Image size: " . strlen($imageBase64) . " bytes (base64)");
        error_log("[AI Vision] MIME type: $mimeType");
        
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
        
        error_log("[AI Vision] Response HTTP code: $httpCode");
        
        if ($curlError) {
            error_log("[AI Vision] ❌ CURL Error: $curlError");
            return null;
        }
        
        if ($httpCode !== 200) {
            error_log("[AI Vision] ❌ API Error Response: " . substr($response, 0, 500));
            return null;
        }
        
        $result = json_decode($response, true);
        
        if (!$result) {
            error_log("[AI Vision] ❌ Failed to decode JSON response");
            return null;
        }
        
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
        
        if (!$text) {
            error_log("[AI Vision] ❌ No text found in response structure");
            return null;
        }
        
        error_log("[AI Vision] ✅ Success! Response: " . substr($text, 0, 200) . (strlen($text) > 200 ? '...' : ''));
        
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
   - \"50 ribu\" = 50,000 (multiply by 1000)
   - \"487494 ribu\" = 487,494 (use number as-is, ribu is just currency indicator)
   - \"1 juta\" or \"1m\" or \"1 million\" = 1,000,000
   - Handle decimals: \"50.5\" or \"50,5\" = 50.5
   - For large numbers with \"ribu\", use the number directly (ribu = thousand indicator)
3. Currency keywords to recognize (ignore in amount):
   - USD: dollar, dollars, USD, \$
   - IDR: rupiah, ribu, juta, IDR, Rp
   - EUR: euro, euros, EUR, €
   - GBP: pound, pounds, GBP, £
4. Extract the numeric amount ONLY

Examples:
- \"beli pizza 50 ribu\" → amount: 50000, description: \"pizza\"
- \"I spent \$50 on coffee\" → amount: 50, description: \"coffee\"
- \"買了100元的咖啡\" → amount: 100, description: \"咖啡/coffee\"
- \"uber ke kantor 35k\" → amount: 35000, description: \"uber ke kantor\"
- \"487494 ribu\" → amount: 487494, description: \"expense\"
- \"bayar tagihan di bulan oktober dan november\" → amount: [amount], description: \"[description] (paid twice)\"
- \"beli cakwe 2 kali sejumlah 20000\" → amount: 40000, description: \"cakwe (2 times)\"

Parse and return ONLY a JSON object with this EXACT format (no extra text, no explanations):
{
  \"amount\": <number>,
  \"description\": \"<short description>\",
  \"category\": \"<category_id>\"
}

Categories (use ID only):
- food: Food & Dining (restaurants, groceries, snacks, pizza, coffee)
- transport: Transportation (uber, taxi, gas, parking, bus)
- utilities: Utilities (electricity, water, internet, phone, payment services, bills)
- entertainment: Entertainment (movies, games, subscriptions)
- healthcare: Healthcare (doctor, medicine, gym)
- shopping: Shopping (clothes, electronics, general shopping)
- other: Other (anything else)

IMPORTANT: 
- Return ONLY the JSON object
- Do not include any explanations or additional text
- Ensure the JSON is complete with all required fields
- Use proper JSON syntax with double quotes
- If text mentions multiple months/periods (like \"di bulan oktober dan november\"), indicate this in the description
- For \"ribu\" amounts, use the number as-is (487494 ribu = 487494, not 487494000)

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
        
        // Handle incomplete JSON responses
        if (strpos($response, '```json') !== false) {
            // If we still have code block markers, extract everything after the first one
            $parts = explode('```json', $response);
            if (count($parts) > 1) {
                $response = trim($parts[1]);
                // Remove any trailing ``` if present
                $response = rtrim($response, '`');
            }
        }
        
        // Try to fix incomplete JSON by adding missing closing brace
        if (substr_count($response, '{') > substr_count($response, '}')) {
            $response .= '}';
            error_log("[AI Parse] Added missing closing brace to JSON");
        }
        
        error_log("[AI Parse] JSON to parse: $response");
        
        $data = json_decode($response, true);
        
        if (!$data) {
            error_log("[AI Parse] ❌ Failed to decode JSON");
            error_log("[AI Parse] JSON error: " . json_last_error_msg());
            error_log("[AI Parse] Original response: " . substr($originalResponse, 0, 500));
            
            // Try to extract partial data from incomplete JSON
            if (preg_match('/"amount"\s*:\s*(\d+)/', $response, $amountMatches)) {
                $amount = floatval($amountMatches[1]);
                $description = 'Unknown expense';
                $category = 'other';
                
                if (preg_match('/"description"\s*:\s*"([^"]*)/', $response, $descMatches)) {
                    $description = $descMatches[1];
                }
                
                if (preg_match('/"category"\s*:\s*"([^"]*)/', $response, $catMatches)) {
                    $category = $catMatches[1];
                }
                
                error_log("[AI Parse] ✅ Extracted partial data from incomplete JSON");
                return [
                    'amount' => $amount,
                    'description' => $description,
                    'category' => $category
                ];
            }
            
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
        
        // Check if the original text indicates multiple payments
        $originalText = strtolower($text);
        $hasMultiplePayments = false;
        $multiplier = 1;
        
        // Check if AI already handled the multiple payments in description
        $aiAlreadyHandled = false;
        if (preg_match('/\((\d+)\s+times?\)/', $result['description'], $descMatches)) {
            $aiAlreadyHandled = true;
            error_log("[AI Parse] ✅ AI already handled multiple payments in description: {$descMatches[1]} times");
        }
        
        // Only apply additional logic if AI hasn't already handled it
        if (!$aiAlreadyHandled) {
            // Check for "kali" (times) patterns like "2 kali", "3 kali", etc.
            if (preg_match('/(\d+)\s+kali/', $originalText, $matches)) {
                $multiplier = intval($matches[1]);
                $hasMultiplePayments = true;
                error_log("[AI Parse] ✅ Detected '{$matches[1]} kali' pattern - multiplier: $multiplier");
            }
            // Check for "times" patterns like "2 times", "3 times", etc.
            elseif (preg_match('/(\d+)\s+times/', $originalText, $matches)) {
                $multiplier = intval($matches[1]);
                $hasMultiplePayments = true;
                error_log("[AI Parse] ✅ Detected '{$matches[1]} times' pattern - multiplier: $multiplier");
            }
            // Check for month/period patterns
            elseif (preg_match('/di\s+bulan\s+\w+\s+dan\s+\w+/', $originalText) || 
                    preg_match('/bulan\s+\w+\s+dan\s+\w+/', $originalText) ||
                    preg_match('/\w+\s+dan\s+\w+\s+bulan/', $originalText)) {
                $multiplier = 2; // Default to 2 for month patterns
                $hasMultiplePayments = true;
                error_log("[AI Parse] ✅ Detected month/period pattern - multiplier: $multiplier");
            }
        }
        
        if ($hasMultiplePayments) {
            // Multiply the amount by the detected multiplier
            $result['amount'] = $result['amount'] * $multiplier;
            
            // Add appropriate description based on multiplier
            if ($multiplier == 2) {
                if (strpos($result['description'], '(paid twice)') === false && 
                    strpos($result['description'], '(2 times)') === false) {
                    $result['description'] = $result['description'] . ' (paid twice)';
                }
            } else {
                if (strpos($result['description'], "($multiplier times)") === false && 
                    strpos($result['description'], '(paid twice)') === false) {
                    $result['description'] = $result['description'] . " ($multiplier times)";
                }
            }
            
            error_log("[AI Parse] ✅ Detected multiple payments - multiplied amount by $multiplier to: {$result['amount']}");
        }
        
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
    
    /**
     * 🧾 Extract Expense from Receipt Image (OCR)
     * 
     * Upload a receipt photo and AI extracts all expense details
     * 
     * Supports: JPEG, PNG, WebP, HEIC/HEIF
     * 
     * @param string $imageData Base64 encoded image data
     * @param string $mimeType Image MIME type (e.g., 'image/jpeg')
     * @param string $userCurrency User's currency code
     * @return array|null Extracted expense details
     */
    public function extractExpenseFromReceiptImage(string $imageData, string $mimeType, string $userCurrency = 'USD'): ?array
    {
        // Get currency info
        $currencyInfo = Currency::get($userCurrency);
        $currencyName = $currencyInfo['name'] ?? $userCurrency;
        $currencySymbol = $currencyInfo['symbol'] ?? '$';
        
        $prompt = "You are a receipt OCR and expense extraction AI. Analyze this receipt image and extract ALL expense information.

User's Currency: {$userCurrency} ({$currencyName}, symbol: {$currencySymbol})

EXTRACTION RULES:
1. Find the TOTAL amount (not individual items unless there's only one item)
2. Extract merchant/store name
3. Extract date (convert to YYYY-MM-DD format)
4. List all items purchased (up to 10 items)
5. Determine the most appropriate category
6. Handle ANY language on the receipt (English, Indonesian, Chinese, etc.)
7. Convert amounts properly:
   - \"50 ribu\" or \"50,000\" = 50000
   - \"1 juta\" or \"1,000,000\" = 1000000
8. If multiple currencies shown, use the one matching user's currency or the dominant one

Return ONLY a JSON object with this EXACT format:
{
  \"amount\": <number>,
  \"merchant\": \"<store_name>\",
  \"date\": \"YYYY-MM-DD\",
  \"category\": \"<category_id>\",
  \"items\": [\"item1\", \"item2\", \"item3\"],
  \"confidence\": \"high|medium|low\"
}

Categories (use ID only):
- food: Food, restaurants, groceries, cafes, dining
- transport: Transportation, gas stations, parking, uber, taxi
- utilities: Utilities, electricity, water, internet, phone bills
- entertainment: Entertainment, movies, games, subscriptions, events
- healthcare: Healthcare, pharmacy, doctor, medicine, gym
- shopping: Shopping, retail, clothes, electronics, general merchandise
- other: Other (if unsure)

If you cannot extract information, return: {\"error\": \"Could not read receipt\"}

Respond with ONLY the JSON, no other text.";

        error_log("[AI OCR] Processing receipt image...");
        error_log("[AI OCR] MIME type: $mimeType");
        error_log("[AI OCR] User currency: $userCurrency");
        
        $response = $this->makeVisionRequest($prompt, $imageData, $mimeType);
        
        if (!$response) {
            error_log("[AI OCR] ❌ Vision API returned no response");
            return null;
        }
        
        error_log("[AI OCR] Raw response: " . substr($response, 0, 300));
        
        // Extract JSON from response
        $response = trim($response);
        if (strpos($response, '```json') !== false) {
            preg_match('/```json\s*(.*?)\s*```/s', $response, $matches);
            $response = $matches[1] ?? $response;
        } elseif (strpos($response, '```') !== false) {
            preg_match('/```\s*(.*?)\s*```/s', $response, $matches);
            $response = $matches[1] ?? $response;
        }
        
        $data = json_decode($response, true);
        
        if (!$data) {
            error_log("[AI OCR] ❌ Failed to decode JSON");
            error_log("[AI OCR] JSON error: " . json_last_error_msg());
            return null;
        }
        
        if (isset($data['error'])) {
            error_log("[AI OCR] ❌ AI returned error: " . $data['error']);
            return null;
        }
        
        if (!isset($data['amount'])) {
            error_log("[AI OCR] ❌ No amount field in response");
            return null;
        }
        
        // Validate and clean data
        $result = [
            'amount' => floatval($data['amount']),
            'merchant' => $data['merchant'] ?? 'Unknown merchant',
            'date' => $data['date'] ?? date('Y-m-d'),
            'category' => $data['category'] ?? 'other',
            'items' => $data['items'] ?? [],
            'confidence' => $data['confidence'] ?? 'medium'
        ];
        
        // Generate description from merchant and items
        $description = $result['merchant'];
        if (!empty($result['items'])) {
            $itemList = implode(', ', array_slice($result['items'], 0, 3));
            $description .= ' (' . $itemList;
            if (count($result['items']) > 3) {
                $description .= ', +' . (count($result['items']) - 3) . ' more';
            }
            $description .= ')';
        }
        $result['description'] = $description;
        
        error_log("[AI OCR] ✅ Successfully extracted receipt!");
        error_log("[AI OCR] Amount: {$result['amount']}, Merchant: {$result['merchant']}, Items: " . count($result['items']));
        
        return $result;
    }
}

