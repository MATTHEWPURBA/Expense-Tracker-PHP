/**
 * Dashboard API Integration
 * 
 * Uses the API client for all dashboard operations
 * All CRUD operations go through REST API endpoints
 * 
 * @package ExpenseTracker
 */

class DashboardAPI {
    constructor() {
        this.expenses = [];
        this.categories = [];
        this.stats = {};
        this.categoryBreakdown = [];
        this.user = {};
        this.currencySymbol = '$';
    }

    /**
     * Initialize dashboard - load all data
     */
    async init() {
        try {
            console.log('🚀 Initializing dashboard with API...');
            
            // Load all dashboard data in one request
            const response = await api.get('/api/dashboard');
            
            if (response.success) {
                this.expenses = response.data.expenses || [];
                this.categories = response.data.categories || [];
                this.stats = response.data.stats || {};
                this.categoryBreakdown = response.data.categoryBreakdown || [];
                this.user = response.data.user || {};
                
                // Set currency symbol
                this.setCurrencySymbol(this.user.currency || 'USD');
                
                console.log('✅ Dashboard data loaded:', {
                    expenses: this.expenses.length,
                    categories: this.categories.length,
                    stats: this.stats
                });
                
                // Render dashboard
                this.render();
            }
        } catch (error) {
            console.error('❌ Failed to initialize dashboard:', error);
            this.showError('Failed to load dashboard data. Please refresh the page.');
        }
    }

    /**
     * Load expenses from API
     */
    async loadExpenses() {
        try {
            const response = await api.get('/api/expenses');
            
            if (response.success) {
                this.expenses = response.data.expenses || [];
                this.renderExpensesList();
                console.log(`📊 Loaded ${this.expenses.length} expenses`);
            }
        } catch (error) {
            console.error('❌ Failed to load expenses:', error);
            this.showError('Failed to load expenses');
        }
    }

    /**
     * Create new expense
     */
    async createExpense(expenseData) {
        try {
            console.log('➕ Creating expense:', expenseData);
            
            const response = await api.post('/api/expenses', expenseData);
            
            if (response.success) {
                console.log('✅ Expense created:', response.data.expense);
                
                // Add to local array
                this.expenses.unshift(response.data.expense);
                
                // Refresh data
                await this.refreshStats();
                this.render();
                
                this.showSuccess('Expense added successfully!');
                return response.data.expense;
            }
        } catch (error) {
            console.error('❌ Failed to create expense:', error);
            
            if (error instanceof ApiError && error.data?.errors) {
                // Show validation errors
                const errorMsg = Object.values(error.data.errors).join(', ');
                this.showError(errorMsg);
            } else {
                this.showError(error.message || 'Failed to add expense');
            }
            
            throw error;
        }
    }

    /**
     * Update expense
     */
    async updateExpense(expenseId, updateData) {
        try {
            console.log(`✏️ Updating expense ${expenseId}:`, updateData);
            
            const response = await api.put(`/api/expenses/${expenseId}`, updateData);
            
            if (response.success) {
                console.log('✅ Expense updated:', response.data.expense);
                
                // Update local array
                const index = this.expenses.findIndex(e => e.id === expenseId);
                if (index !== -1) {
                    this.expenses[index] = response.data.expense;
                }
                
                // Refresh data
                await this.refreshStats();
                this.render();
                
                this.showSuccess('Expense updated successfully!');
                return response.data.expense;
            }
        } catch (error) {
            console.error('❌ Failed to update expense:', error);
            this.showError(error.message || 'Failed to update expense');
            throw error;
        }
    }

    /**
     * Delete expense
     */
    async deleteExpense(expenseId) {
        try {
            console.log(`🗑️ Deleting expense ${expenseId}`);
            
            const response = await api.delete(`/api/expenses/${expenseId}`);
            
            if (response.success) {
                console.log('✅ Expense deleted');
                
                // Remove from local array
                this.expenses = this.expenses.filter(e => e.id !== expenseId);
                
                // Refresh data
                await this.refreshStats();
                this.render();
                
                this.showSuccess('Expense deleted successfully!');
                return true;
            }
        } catch (error) {
            console.error('❌ Failed to delete expense:', error);
            this.showError(error.message || 'Failed to delete expense');
            throw error;
        }
    }

    /**
     * Refresh statistics
     */
    async refreshStats() {
        try {
            const response = await api.get('/api/expenses/stats/summary');
            
            if (response.success) {
                this.stats = response.data.stats || {};
                this.renderStats();
            }
        } catch (error) {
            console.error('❌ Failed to refresh stats:', error);
        }
    }

    /**
     * Load categories
     */
    async loadCategories() {
        try {
            const response = await api.get('/api/categories');
            
            if (response.success) {
                this.categories = response.data.categories || [];
                this.renderCategoriesDropdown();
                console.log(`📁 Loaded ${this.categories.length} categories`);
            }
        } catch (error) {
            console.error('❌ Failed to load categories:', error);
        }
    }

    /**
     * Set currency symbol
     */
    setCurrencySymbol(currency) {
        const symbols = {
            'USD': '$', 'EUR': '€', 'GBP': '£', 'JPY': '¥',
            'CAD': 'C$', 'AUD': 'A$', 'CHF': 'CHF', 'CNY': '¥',
            'INR': '₹', 'BRL': 'R$', 'MXN': 'MX$'
        };
        this.currencySymbol = symbols[currency] || currency + ' ';
    }

    /**
     * Format currency
     */
    formatCurrency(amount) {
        return this.currencySymbol + parseFloat(amount || 0).toFixed(2);
    }

    /**
     * Render everything
     */
    render() {
        this.renderStats();
        this.renderExpensesList();
        this.renderCategoryBreakdown();
        this.renderCategoriesDropdown();
    }

    /**
     * Render statistics cards
     */
    renderStats() {
        // Total expenses
        const totalEl = document.getElementById('totalExpenses');
        if (totalEl) {
            totalEl.textContent = this.formatCurrency(this.stats.total || 0);
        }

        // Monthly expenses
        const monthlyEl = document.getElementById('monthlyExpenses');
        if (monthlyEl) {
            monthlyEl.textContent = this.formatCurrency(this.stats.monthly || 0);
        }

        // Average expense
        const avgEl = document.getElementById('averageExpense');
        if (avgEl) {
            avgEl.textContent = this.formatCurrency(this.stats.average || 0);
        }

        // Count
        const countEl = document.getElementById('expenseCount');
        if (countEl) {
            countEl.textContent = this.stats.count || 0;
        }
    }

    /**
     * Render expenses list
     */
    renderExpensesList() {
        const container = document.getElementById('expensesList');
        if (!container) return;

        if (this.expenses.length === 0) {
            container.innerHTML = `
                <div style="text-align: center; padding: 40px; color: #6b7280;">
                    <p style="font-size: 1.2rem; margin-bottom: 10px;">📝 No expenses yet</p>
                    <p>Add your first expense using the form above</p>
                </div>
            `;
            return;
        }

        container.innerHTML = this.expenses.map(expense => `
            <div class="expense-item" data-id="${expense.id}">
                <div class="expense-info">
                    <div class="expense-category">
                        <span class="category-icon" style="background: ${expense.color || '#667eea'};">
                            ${expense.icon || '💰'}
                        </span>
                        <span class="category-name">${expense.category_name || expense.category}</span>
                    </div>
                    <div class="expense-details">
                        <p class="expense-description">${expense.description || 'No description'}</p>
                        <span class="expense-date">${new Date(expense.date).toLocaleDateString()}</span>
                    </div>
                </div>
                <div class="expense-actions">
                    <span class="expense-amount">${this.formatCurrency(expense.amount)}</span>
                    <button onclick="dashboard.deleteExpense('${expense.id}')" 
                            class="delete-btn" 
                            title="Delete expense">
                        🗑️
                    </button>
                </div>
            </div>
        `).join('');
    }

    /**
     * Render category breakdown
     */
    renderCategoryBreakdown() {
        const container = document.getElementById('categoryBreakdown');
        if (!container) return;

        if (this.categoryBreakdown.length === 0) {
            container.innerHTML = '<p style="color: #6b7280; text-align: center;">No data available</p>';
            return;
        }

        container.innerHTML = this.categoryBreakdown
            .filter(cat => cat.total > 0)
            .map(cat => `
                <div class="category-item">
                    <div class="category-info">
                        <span class="category-icon" style="background: ${cat.color};">${cat.icon}</span>
                        <span class="category-name">${cat.name}</span>
                    </div>
                    <span class="category-amount">${this.formatCurrency(cat.total)}</span>
                </div>
            `).join('');
    }

    /**
     * Render categories dropdown
     */
    renderCategoriesDropdown() {
        const dropdown = document.getElementById('categorySelect');
        if (!dropdown) return;

        dropdown.innerHTML = this.categories.map(cat => `
            <option value="${cat.id}">${cat.icon} ${cat.name}</option>
        `).join('');
    }

    /**
     * Show success message
     */
    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    /**
     * Show error message
     */
    showError(message) {
        this.showNotification(message, 'error');
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        // Remove existing notification
        const existing = document.getElementById('notification');
        if (existing) {
            existing.remove();
        }

        const notification = document.createElement('div');
        notification.id = 'notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 10001;
            animation: slideIn 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        `;

        const colors = {
            success: '#10b981',
            error: '#ef4444',
            info: '#3b82f6'
        };

        notification.style.background = colors[type] || colors.info;
        notification.textContent = message;

        document.body.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Add CSS animations
if (!document.getElementById('notificationStyles')) {
    const style = document.createElement('style');
    style.id = 'notificationStyles';
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
}

// Create global instance
const dashboard = new DashboardAPI();
window.dashboard = dashboard;

console.log('✅ Dashboard API loaded. Access via: window.dashboard');

