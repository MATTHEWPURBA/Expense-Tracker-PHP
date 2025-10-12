/**
 * AI Features Integration for Expense Tracker Dashboard
 * 
 * Features:
 * 1. Smart categorization button
 * 2. Natural language expense entry
 * 3. AI insights panel
 * 4. Budget predictions
 * 5. Smart recommendations
 */

// =============================================================================
// AI UTILITY FUNCTIONS
// =============================================================================

/**
 * Make API request to AI endpoints
 */
async function aiRequest(action, data = null, method = 'GET') {
    const url = `/api_ai.php?action=${action}`;
    
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (data && method === 'POST') {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(url, options);
        return await response.json();
    } catch (error) {
        console.error('AI Request failed:', error);
        return { success: false, error: error.message };
    }
}

/**
 * Show loading indicator
 */
function showAILoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '<div class="ai-loading">🤖 AI is thinking...</div>';
    }
}

/**
 * Show error message
 */
function showAIError(elementId, message) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = `<div class="ai-error">❌ ${message}</div>`;
    }
}

// =============================================================================
// FEATURE 1: SMART EXPENSE CATEGORIZATION
// =============================================================================

/**
 * Auto-categorize expense when description is entered
 * Add this to your expense form's description input
 */
async function smartCategorize() {
    const descriptionInput = document.getElementById('expense-description');
    const categorySelect = document.getElementById('expense-category');
    const smartButton = document.getElementById('smart-categorize-btn');
    
    if (!descriptionInput || !categorySelect) return;
    
    const description = descriptionInput.value.trim();
    
    if (!description) {
        alert('Please enter a description first');
        return;
    }
    
    // Show loading
    smartButton.disabled = true;
    smartButton.innerHTML = '🤖 Thinking...';
    
    // Call AI
    const result = await aiRequest('categorize', { description }, 'POST');
    
    if (result.success) {
        categorySelect.value = result.category;
        smartButton.innerHTML = '✨ Auto-categorize';
        
        // Show success feedback
        const feedback = document.createElement('span');
        feedback.className = 'ai-feedback';
        feedback.innerHTML = '✅ Categorized!';
        smartButton.parentElement.appendChild(feedback);
        
        setTimeout(() => feedback.remove(), 2000);
    } else {
        alert('AI categorization failed: ' + (result.error || 'Unknown error'));
        smartButton.innerHTML = '✨ Auto-categorize';
    }
    
    smartButton.disabled = false;
}

// =============================================================================
// FEATURE 2: NATURAL LANGUAGE EXPENSE ENTRY
// =============================================================================

/**
 * Parse natural language and create expense
 * e.g., "I spent $50 on groceries" → auto-fills form
 */
async function parseNaturalLanguage() {
    const nlInput = document.getElementById('nl-expense-input');
    const parseButton = document.getElementById('parse-nl-btn');
    
    if (!nlInput) return;
    
    const text = nlInput.value.trim();
    
    if (!text) {
        alert('Please enter an expense description (e.g., "I spent $50 on pizza")');
        return;
    }
    
    // Show loading
    parseButton.disabled = true;
    parseButton.innerHTML = '🤖 Parsing...';
    
    // Call AI
    const result = await aiRequest('parse', { text }, 'POST');
    
    if (result.success) {
        // Fill in the form
        document.getElementById('expense-amount').value = result.amount;
        document.getElementById('expense-description').value = result.description;
        document.getElementById('expense-category').value = result.category;
        
        // Clear NL input
        nlInput.value = '';
        
        // Show success
        alert('✅ Expense parsed! Review and submit.');
        parseButton.innerHTML = '💬 Parse Expense';
    } else {
        alert('Could not parse expense. Please try rephrasing (e.g., "I spent $50 on pizza")');
        parseButton.innerHTML = '💬 Parse Expense';
    }
    
    parseButton.disabled = false;
}

// =============================================================================
// FEATURE 3: AI SPENDING INSIGHTS
// =============================================================================

/**
 * Load and display AI-generated spending insights
 */
async function loadAIInsights() {
    const insightsPanel = document.getElementById('ai-insights');
    
    if (!insightsPanel) return;
    
    showAILoading('ai-insights');
    
    const result = await aiRequest('insights');
    
    if (result.success) {
        insightsPanel.innerHTML = `
            <div class="ai-insights-content">
                <h3>💡 AI Insights</h3>
                <div class="insights-text">
                    ${result.insights.replace(/\n/g, '<br>')}
                </div>
                <button onclick="loadAIInsights()" class="refresh-btn">🔄 Refresh</button>
            </div>
        `;
    } else {
        showAIError('ai-insights', result.error || 'Could not load insights');
    }
}

// =============================================================================
// FEATURE 4: BUDGET PREDICTIONS
// =============================================================================

/**
 * Show AI budget prediction for next month
 */
async function loadBudgetPrediction() {
    const predictionPanel = document.getElementById('budget-prediction');
    
    if (!predictionPanel) return;
    
    showAILoading('budget-prediction');
    
    const result = await aiRequest('predict');
    
    if (result.success) {
        const confidenceColor = {
            'high': '#4CAF50',
            'medium': '#FF9800',
            'low': '#f44336'
        }[result.confidence] || '#999';
        
        predictionPanel.innerHTML = `
            <div class="prediction-content">
                <h3>🎯 Budget Prediction</h3>
                <div class="predicted-amount">
                    $${result.predicted_amount.toFixed(2)}
                </div>
                <div class="confidence-badge" style="background: ${confidenceColor}">
                    Confidence: ${result.confidence}
                </div>
                <div class="reasoning">
                    ${result.reasoning}
                </div>
                <button onclick="loadBudgetPrediction()" class="refresh-btn">🔄 Refresh</button>
            </div>
        `;
    } else {
        showAIError('budget-prediction', result.error || 'Need more data for predictions');
    }
}

// =============================================================================
// FEATURE 5: SMART RECOMMENDATIONS
// =============================================================================

/**
 * Get AI-powered saving recommendations
 */
async function loadRecommendations() {
    const recommendationsPanel = document.getElementById('ai-recommendations');
    
    if (!recommendationsPanel) return;
    
    showAILoading('ai-recommendations');
    
    const result = await aiRequest('recommendations');
    
    if (result.success) {
        const recommendationsHTML = result.recommendations
            .map(rec => `<li>${rec}</li>`)
            .join('');
        
        recommendationsPanel.innerHTML = `
            <div class="recommendations-content">
                <h3>💰 Smart Recommendations</h3>
                <ul class="recommendations-list">
                    ${recommendationsHTML}
                </ul>
                <button onclick="loadRecommendations()" class="refresh-btn">🔄 Refresh</button>
            </div>
        `;
    } else {
        showAIError('ai-recommendations', result.error || 'Could not load recommendations');
    }
}

// =============================================================================
// AUTO-LOAD AI FEATURES ON PAGE LOAD
// =============================================================================

document.addEventListener('DOMContentLoaded', function() {
    // Auto-load insights if panel exists
    if (document.getElementById('ai-insights')) {
        loadAIInsights();
    }
    
    // Auto-load predictions if panel exists
    if (document.getElementById('budget-prediction')) {
        loadBudgetPrediction();
    }
    
    // Auto-load recommendations if panel exists
    if (document.getElementById('ai-recommendations')) {
        loadRecommendations();
    }
    
    console.log('✅ AI features loaded successfully!');
});

// =============================================================================
// EXPORT FUNCTIONS FOR GLOBAL ACCESS
// =============================================================================

window.smartCategorize = smartCategorize;
window.parseNaturalLanguage = parseNaturalLanguage;
window.loadAIInsights = loadAIInsights;
window.loadBudgetPrediction = loadBudgetPrediction;
window.loadRecommendations = loadRecommendations;

