<?php
/**
 * Settings View
 * 
 * User settings interface
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>⚙️ Settings - Expense Tracker</title>
    <style>
        <?php include __DIR__ . '/../layouts/dashboard-styles.php'; ?>
        
        .settings-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .settings-section {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .settings-section h2 {
            margin: 0 0 20px 0;
            color: var(--dark);
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .currency-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .currency-option {
            position: relative;
        }
        
        .currency-option input[type="radio"] {
            display: none;
        }
        
        .currency-label {
            display: block;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            /* Better touch target */
            min-height: 80px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            /* Prevent text selection on tap */
            -webkit-user-select: none;
            user-select: none;
            /* Improve tap feedback */
            -webkit-tap-highlight-color: transparent;
        }
        
        .currency-label:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .currency-label:active {
            transform: scale(0.98);
        }
        
        .currency-option input[type="radio"]:checked + .currency-label {
            border-color: var(--primary);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.1));
        }
        
        .currency-symbol {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .currency-name {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 3px;
        }
        
        .currency-code {
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background: var(--light);
            color: var(--dark);
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        
        .back-button:hover {
            background: var(--primary);
            color: white;
        }
        
        .save-button {
            margin-top: 20px;
            width: 100%;
            max-width: 200px;
        }
        
        .search-currency {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            margin-bottom: 20px;
            transition: border-color 0.3s ease;
        }
        
        .search-currency:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        /* Mobile responsive styles */
        @media (max-width: 768px) {
            .settings-container {
                padding: 0;
            }
            
            .currency-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            
            .currency-label {
                padding: 12px;
            }
            
            .currency-symbol {
                font-size: 1.2rem;
            }
            
            .currency-name {
                font-size: 0.9rem;
            }
            
            .currency-code {
                font-size: 0.75rem;
            }
        }
        
        @media (max-width: 600px) {
            .settings-section {
                padding: 20px;
                border-radius: 10px;
            }
            
            .settings-section h2 {
                font-size: 1.3rem;
            }
            
            .back-button {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
            
            .save-button {
                max-width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .currency-grid {
                grid-template-columns: 1fr;
            }
            
            .settings-section {
                padding: 15px;
            }
            
            .search-currency {
                padding: 10px 12px;
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <div class="container settings-container">
        <a href="/index.php" class="back-button">← Back to Dashboard</a>
        
        <div class="header">
            <h1>⚙️ Settings</h1>
            <p>Manage your account preferences</p>
        </div>
        
        <!-- Alert Messages -->
        <div id="alertSuccess" class="alert alert-success">
            <strong>✓ Success!</strong> <span id="successMessage"></span>
        </div>
        
        <div id="alertError" class="alert alert-error">
            <strong>✗ Error!</strong> <span id="errorMessage"></span>
        </div>
        
        <!-- Currency Settings -->
        <div class="settings-section">
            <h2>💱 Currency Preference</h2>
            <p style="color: var(--text-muted); margin-bottom: 20px;">
                Select your preferred currency for displaying expenses. This will be used throughout the application.
            </p>
            
            <input 
                type="text" 
                class="search-currency" 
                id="currencySearch" 
                placeholder="🔍 Search currencies..."
            >
            
            <form id="currencyForm">
                <div class="currency-grid" id="currencyGrid">
                    <?php foreach ($currencies as $code => $currency): ?>
                        <div class="currency-option" data-currency-name="<?php echo strtolower($currency['name']); ?>" data-currency-code="<?php echo strtolower($code); ?>">
                            <input 
                                type="radio" 
                                name="currency" 
                                id="currency_<?php echo $code; ?>" 
                                value="<?php echo $code; ?>"
                                <?php echo ($currentCurrency === $code) ? 'checked' : ''; ?>
                            >
                            <label for="currency_<?php echo $code; ?>" class="currency-label">
                                <div class="currency-symbol"><?php echo $currency['symbol']; ?></div>
                                <div class="currency-name"><?php echo htmlspecialchars($currency['name']); ?></div>
                                <div class="currency-code"><?php echo $code; ?></div>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <button type="submit" class="btn btn-primary save-button">
                    <span>💾 Save Currency</span>
                </button>
            </form>
        </div>
        
        <!-- Account Info -->
        <div class="settings-section">
            <h2>👤 Account Information</h2>
            <div style="color: var(--text-muted);">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p><strong>Current Currency:</strong> <?php echo $currentCurrency; ?> (<?php echo $currencies[$currentCurrency]['symbol']; ?>)</p>
                <p><strong>Member Since:</strong> <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>© <?php echo date('Y'); ?> Expense Tracker. All rights reserved.</p>
        </div>
    </div>
    
    <script>
        // Currency form submission
        document.getElementById('currencyForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'update_currency');
            formData.append('currency', document.querySelector('input[name="currency"]:checked').value);
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', 'Currency updated successfully! Redirecting...');
                    setTimeout(() => window.location.href = '/index.php', 1500);
                } else {
                    showAlert('error', result.error || 'Failed to update currency');
                }
            } catch (error) {
                showAlert('error', 'Network error. Please try again.');
            }
        });
        
        // Currency search
        document.getElementById('currencySearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const currencyOptions = document.querySelectorAll('.currency-option');
            
            currencyOptions.forEach(option => {
                const name = option.dataset.currencyName;
                const code = option.dataset.currencyCode;
                
                if (name.includes(searchTerm) || code.includes(searchTerm)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
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
    </script>
</body>
</html>

