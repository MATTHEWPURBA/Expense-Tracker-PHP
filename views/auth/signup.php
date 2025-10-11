<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Expense Tracker</title>
    
    <?php include __DIR__ . '/../layouts/auth-styles.php'; ?>
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <h1>💰</h1>
            <h2 style="color: var(--dark); margin-bottom: 5px;">Create Account</h2>
            <p>Join Expense Tracker today</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <strong>✗</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <strong>✓</strong> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Full Name</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    autocomplete="name"
                    placeholder="Enter your full name"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                >
                <div class="password-requirements">3-255 characters, letters, numbers, spaces, dots, underscores, and hyphens</div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required 
                    autocomplete="email"
                    placeholder="your@email.com"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                >
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="new-password"
                    placeholder="Enter your password"
                >
                <div class="password-requirements">Minimum 6 characters</div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    required 
                    autocomplete="new-password"
                    placeholder="Confirm your password"
                >
            </div>
            
            <div class="form-group">
                <label for="currency">💱 Preferred Currency</label>
                <select 
                    id="currency" 
                    name="currency" 
                    required
                >
                    <?php foreach ($currencies as $code => $currency): ?>
                        <option 
                            value="<?php echo $code; ?>"
                            <?php echo ($defaultCurrency === $code) ? 'selected' : ''; ?>
                        >
                            <?php echo $currency['symbol'] . ' - ' . $currency['name'] . ' (' . $code . ')'; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="password-requirements">Choose your preferred currency for tracking expenses</div>
            </div>
            
            <button type="submit" class="btn">
                🚀 Create Account
            </button>
        </form>
        
        <div class="auth-footer">
            Already have an account? <a href="/login.php">Sign In</a>
        </div>
    </div>
</body>
</html>

