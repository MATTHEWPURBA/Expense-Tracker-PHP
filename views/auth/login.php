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
        
        <form id="loginForm" method="POST" action="">
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
            
            <button type="submit" class="btn" id="loginBtn">
                <span id="loginBtnText">🔓 Sign In</span>
                <span id="loginBtnSpinner" style="display: none;">⏳ Signing in...</span>
            </button>
        </form>
        
        <!-- Debug Mode Toggle -->
        <div style="margin-top: 20px; text-align: center;">
            <label style="font-size: 12px; color: #666;">
                <input type="checkbox" id="debugMode" style="margin-right: 5px;">
                Enable Debug Mode (shows API calls in console)
            </label>
        </div>
        
        <div class="auth-footer">
            Don't have an account? <a href="/signup.php">Sign Up</a>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const loginBtnText = document.getElementById('loginBtnText');
        const loginBtnSpinner = document.getElementById('loginBtnSpinner');
        const debugMode = document.getElementById('debugMode');
        
        form.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Please enter both username/email and password');
                return;
            }
            
            // Show loading state
            loginBtn.disabled = true;
            loginBtnText.style.display = 'none';
            loginBtnSpinner.style.display = 'inline';
            
            // If debug mode is enabled, try API first
            if (debugMode.checked) {
                e.preventDefault();
                attemptApiLogin(username, password);
            }
            // Otherwise, proceed with normal form submission
        });
        
        function attemptApiLogin(username, password) {
            console.log('🌐 Attempting API login...');
            
            fetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Debug': 'true'
                },
                body: JSON.stringify({
                    username: username,
                    password: password
                })
            })
            .then(response => {
                console.log('📡 API Response Status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('📊 API Response Data:', data);
                
                if (data.success) {
                    console.log('✅ API Login successful!');
                    window.location.href = '/index.php';
                } else {
                    console.log('❌ API Login failed:', data.error);
                    showError(data.error || 'Login failed');
                    resetButton();
                }
            })
            .catch(error => {
                console.error('❌ API Request failed:', error);
                console.log('🔄 Falling back to form submission...');
                
                // Fallback to traditional form submission
                const formData = new FormData();
                formData.append('username', username);
                formData.append('password', password);
                
                fetch('/login.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = '/index.php';
                    } else {
                        window.location.reload();
                    }
                })
                .catch(fallbackError => {
                    console.error('❌ Fallback also failed:', fallbackError);
                    showError('Network error. Please try again.');
                    resetButton();
                });
            });
        }
        
        function showError(message) {
            // Remove existing error alerts
            const existingError = document.querySelector('.alert-error');
            if (existingError) {
                existingError.remove();
            }
            
            // Create new error alert
            const errorAlert = document.createElement('div');
            errorAlert.className = 'alert alert-error';
            errorAlert.innerHTML = '<strong>✗</strong> ' + message;
            
            // Insert after the logo
            const logo = document.querySelector('.logo');
            logo.parentNode.insertBefore(errorAlert, logo.nextSibling);
            
            // Scroll to error
            errorAlert.scrollIntoView({ behavior: 'smooth' });
        }
        
        function resetButton() {
            loginBtn.disabled = false;
            loginBtnText.style.display = 'inline';
            loginBtnSpinner.style.display = 'none';
        }
        
        // Debug info
        console.log('🔧 Login page loaded');
        console.log('📋 Available endpoints:');
        console.log('  - Traditional: POST /login.php');
        console.log('  - API: POST /api/auth/login');
        console.log('💡 Enable debug mode to see API calls in Network tab');
    });
    </script>
</body>
</html>

