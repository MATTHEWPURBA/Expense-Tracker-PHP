<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --primary: #667eea;
    --primary-dark: #5a67d8;
    --secondary: #764ba2;
    --success: #48bb78;
    --danger: #f56565;
    --light: #f7fafc;
    --dark: #2d3748;
    --gray: #718096;
    --border: #e2e8f0;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    /* Improve mobile font rendering */
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    /* Prevent horizontal scroll */
    overflow-x: hidden;
}

.auth-container {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    width: 100%;
    max-width: 450px;
    padding: 40px;
    animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.logo {
    text-align: center;
    margin-bottom: 30px;
}

.logo h1 {
    font-size: 2.5rem;
    color: var(--primary);
    margin-bottom: 10px;
}

.logo p {
    color: var(--gray);
    font-size: 1.1rem;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--dark);
    font-size: 0.9rem;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 14px 15px;
    border: 2px solid var(--border);
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    font-family: inherit;
    /* Better touch targets (min 44px height) */
    min-height: 44px;
    /* Improve mobile input experience */
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

.form-group select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23718096' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 15px center;
    padding-right: 40px;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.btn {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    /* Better touch target */
    min-height: 48px;
    /* Prevent text selection on tap */
    -webkit-user-select: none;
    user-select: none;
    /* Improve tap feedback */
    -webkit-tap-highlight-color: transparent;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.btn:active {
    transform: scale(0.98);
}

.alert {
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.9rem;
}

.alert-error {
    background: #fed7d7;
    color: #742a2a;
    border-left: 4px solid var(--danger);
}

.alert-success {
    background: #c6f6d5;
    color: #22543d;
    border-left: 4px solid var(--success);
}

.auth-footer {
    text-align: center;
    margin-top: 20px;
    color: var(--gray);
}

.auth-footer a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
}

.auth-footer a:hover {
    text-decoration: underline;
}

.password-requirements {
    font-size: 0.85rem;
    color: var(--gray);
    margin-top: 5px;
}

@media (max-width: 600px) {
    body {
        padding: 15px;
    }
    
    .auth-container {
        padding: 30px 20px;
        border-radius: 15px;
    }
    
    .logo h1 {
        font-size: 2rem;
    }
    
    .logo p {
        font-size: 1rem;
    }
    
    .form-group label {
        font-size: 0.85rem;
    }
    
    .password-requirements {
        font-size: 0.8rem;
    }
}

@media (max-width: 400px) {
    body {
        padding: 10px;
    }
    
    .auth-container {
        padding: 25px 15px;
    }
    
    .logo h1 {
        font-size: 1.8rem;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
}
</style>

