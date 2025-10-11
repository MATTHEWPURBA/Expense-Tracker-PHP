<?php
/**
 * Dashboard View
 * 
 * Main dashboard interface
 */

// Calculate category totals for chart
$categoryTotals = [];
foreach ($categoryBreakdown as $cat) {
    $categoryTotals[$cat['id']] = floatval($cat['total']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Personal Expense Tracker - Track your expenses with beautiful analytics">
    <title>💰 Expense Tracker - Personal Finance Manager</title>
    
    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <style>
        <?php include __DIR__ . '/../layouts/dashboard-styles.php'; ?>
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>💰 Expense Tracker</h1>
            <p>Track your expenses, manage your budget, analyze your spending</p>
            <div style="margin-top: 15px; display: flex; align-items: center; justify-content: center; gap: 15px;">
                <span style="font-size: 1rem;">Welcome, <strong><?php echo htmlspecialchars($user['name'] ?? 'User'); ?></strong>!</span>
                <a href="/settings.php" style="background: rgba(255,255,255,0.2); color: white; padding: 8px 20px; border-radius: 20px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    ⚙️ Settings
                </a>
                <a href="/logout.php" style="background: rgba(255,255,255,0.2); color: white; padding: 8px 20px; border-radius: 20px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                    🚪 Logout
                </a>
            </div>
        </div>
        
        <!-- Alert Messages -->
        <div id="alertSuccess" class="alert alert-success">
            <strong>✓ Success!</strong> <span id="successMessage"></span>
        </div>
        
        <div id="alertError" class="alert alert-error">
            <strong>✗ Error!</strong> <span id="errorMessage"></span>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">💵</div>
                <div class="label">Total Expenses</div>
                <div class="value"><?php echo \ExpenseTracker\Services\Currency::format($stats['total'], $userCurrency); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="icon">📊</div>
                <div class="label">This Month</div>
                <div class="value"><?php echo \ExpenseTracker\Services\Currency::format($stats['monthly'], $userCurrency); ?></div>
            </div>
            
            <div class="stat-card">
                <div class="icon">📝</div>
                <div class="label">Total Transactions</div>
                <div class="value"><?php echo $stats['count']; ?></div>
            </div>
            
            <div class="stat-card">
                <div class="icon">📈</div>
                <div class="label">Average Expense</div>
                <div class="value"><?php echo \ExpenseTracker\Services\Currency::format($stats['average'], $userCurrency); ?></div>
            </div>
        </div>
        
        <!-- Main Content Grid -->
        <div class="main-grid">
            <!-- Add Expense Form -->
            <div class="card">
                <h2>➕ Add New Expense</h2>
                
                <form id="expenseForm">
                    <div class="form-group">
                        <label for="amount">Amount (<?php echo $currencySymbol; ?>)</label>
                        <input type="number" id="amount" name="amount" step="0.01" min="0.01" required placeholder="0.00">
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>">
                                    <?php echo $cat['icon'] . ' ' . $cat['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required placeholder="What did you spend on?"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <span>💾 Add Expense</span>
                    </button>
                </form>
            </div>
            
            <!-- Analytics Chart -->
            <div class="card">
                <h2>📊 Spending Analytics</h2>
                
                <div class="chart-container">
                    <canvas id="expenseChart"></canvas>
                </div>
                
                <div class="export-section">
                    <h3 style="margin-bottom: 15px; font-size: 1rem; color: var(--dark);">📤 Export Data</h3>
                    <div class="export-buttons">
                        <a href="?export=csv" class="btn btn-export btn-csv" title="Export as CSV file">📊 CSV</a>
                        <a href="?export=json" class="btn btn-export btn-json" title="Export as JSON file">📋 JSON</a>
                        <a href="?export=excel" class="btn btn-export btn-excel" title="Export as Excel file">📗 Excel</a>
                        <a href="?export=xml" class="btn btn-export btn-xml" title="Export as XML file">📄 XML</a>
                        <a href="?export=pdf" class="btn btn-export btn-pdf" title="Export as printable report" target="_blank">📕 PDF</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Expense List -->
        <div class="card expense-list-container">
            <h2>📋 Recent Expenses</h2>
            
            <div class="expense-list" id="expenseList">
                <?php if (empty($expenses)): ?>
                    <div class="empty-state">
                        <div class="icon">📭</div>
                        <h3>No expenses yet</h3>
                        <p>Start tracking your expenses by adding your first entry above!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($expenses as $expense): ?>
                        <div class="expense-item" data-id="<?php echo $expense['id']; ?>">
                            <div class="expense-info">
                                <div class="expense-header">
                                    <span class="category-badge" style="background-color: <?php echo $expense['color']; ?>">
                                        <?php echo $expense['icon'] . ' ' . $expense['category_name']; ?>
                                    </span>
                                </div>
                                <div class="expense-description"><?php echo htmlspecialchars($expense['description']); ?></div>
                                <div class="expense-date">📅 <?php echo date('M d, Y', strtotime($expense['date'])); ?></div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="expense-amount">-<?php echo \ExpenseTracker\Services\Currency::format($expense['amount'], $userCurrency); ?></div>
                                <button class="btn btn-danger delete-btn" data-id="<?php echo $expense['id']; ?>">
                                    🗑️ Delete
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Built with ❤️ for portfolio showcase</p>
            <p>© <?php echo date('Y'); ?> Expense Tracker. All rights reserved.</p>
        </div>
    </div>
    
    <script>
        // Category data for chart
        const categories = <?php echo json_encode($categories); ?>;
        const categoryTotals = <?php echo json_encode($categoryTotals); ?>;
        
        // Initialize Chart
        const ctx = document.getElementById('expenseChart').getContext('2d');
        
        const chartData = {
            labels: categories.map(cat => cat.name),
            datasets: [{
                label: 'Spending by Category',
                data: categories.map(cat => categoryTotals[cat.id] || 0),
                backgroundColor: categories.map(cat => cat.color),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        };
        
        const chartConfig = {
            type: 'doughnut',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                family: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: <?php echo $currencySymbol; ?>${value.toFixed(<?php echo \ExpenseTracker\Services\Currency::get($userCurrency)['decimal_places'] ?? 2; ?>)} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        };
        
        const expenseChart = new Chart(ctx, chartConfig);
        
        // Form submission
        document.getElementById('expenseForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'add_expense');
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', 'Expense added successfully!');
                    this.reset();
                    document.getElementById('date').value = new Date().toISOString().split('T')[0];
                    
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('error', result.error || 'Failed to add expense');
                }
            } catch (error) {
                showAlert('error', 'Network error. Please try again.');
            }
        });
        
        // Delete expense
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (!confirm('Are you sure you want to delete this expense?')) return;
                
                const id = this.dataset.id;
                const formData = new FormData();
                formData.append('action', 'delete_expense');
                formData.append('id', id);
                
                try {
                    const response = await fetch('', { method: 'POST', body: formData });
                    const result = await response.json();
                    
                    if (result.success) {
                        showAlert('success', 'Expense deleted successfully!');
                        const item = document.querySelector(`[data-id="${id}"]`);
                        item.style.animation = 'slideOutRight 0.3s ease-out';
                        setTimeout(() => location.reload(), 300);
                    } else {
                        showAlert('error', result.error || 'Failed to delete expense');
                    }
                } catch (error) {
                    showAlert('error', 'Network error. Please try again.');
                }
            });
        });
        
        // Alert function
        function showAlert(type, message) {
            const alertId = type === 'success' ? 'alertSuccess' : 'alertError';
            const messageId = type === 'success' ? 'successMessage' : 'errorMessage';
            
            document.getElementById(messageId).textContent = message;
            document.getElementById(alertId).classList.add('show');
            
            setTimeout(() => {
                document.getElementById(alertId).classList.remove('show');
            }, 4000);
        }
        
        // Animation for slide out
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideOutRight {
                to {
                    opacity: 0;
                    transform: translateX(100%);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>

