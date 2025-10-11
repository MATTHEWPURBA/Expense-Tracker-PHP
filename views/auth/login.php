<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Expense Tracker</title>
    
    <?php include __DIR__ . '/../layouts/auth-styles.php'; ?>
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <h1>💰</h1>
            <h2 style="color: var(--dark); margin-bottom: 5px;">Welcome Back</h2>
            <p>Sign in to your account</p>
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
                <label for="username">Full Name or Email</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    autocomplete="username"
                    placeholder="Enter your username or email"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                >
                <div class="password-requirements">You can login with either your full name or email address</div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="Enter your password"
                >
            </div>
            
            <button type="submit" class="btn">
                🔓 Sign In
            </button>
        </form>
        
        <div class="auth-footer">
            Don't have an account? <a href="/signup.php">Sign Up</a>
        </div>
    </div>
</body>
</html>

