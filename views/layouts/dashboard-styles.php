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
    --warning: #ed8936;
    --info: #4299e1;
    --light: #f7fafc;
    --dark: #2d3748;
    --gray: #718096;
    --border: #e2e8f0;
    --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 20px;
    line-height: 1.6;
    color: var(--dark);
    /* Improve font rendering on mobile */
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    /* Prevent horizontal scroll */
    overflow-x: hidden;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
}

.header {
    text-align: center;
    color: white;
    margin-bottom: 30px;
    animation: fadeInDown 0.6s ease-out;
}

.header h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.header p {
    font-size: 1.1rem;
    opacity: 0.95;
}

/* Mobile navigation */
.mobile-nav-toggle {
    display: none;
    background: rgba(255,255,255,0.2);
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 1.5rem;
    cursor: pointer;
    margin: 10px auto;
    transition: all 0.3s ease;
}

.mobile-nav-toggle:active {
    transform: scale(0.95);
}

.nav-links {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}

.nav-links a {
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 10px 20px;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    /* Improve touch targets on mobile */
    min-height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.nav-links a:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-2px);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
    animation: fadeInUp 0.6s ease-out 0.2s both;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: var(--shadow);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.stat-card .icon {
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.stat-card .label {
    color: var(--gray);
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.stat-card .value {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary);
}

.main-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: var(--shadow);
    animation: fadeInUp 0.6s ease-out 0.4s both;
}

.card h2 {
    font-size: 1.5rem;
    margin-bottom: 20px;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 10px;
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
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 14px 15px;
    border: 2px solid var(--border);
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    font-family: inherit;
    /* Improve mobile input experience */
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    /* Better touch targets (min 44px height) */
    min-height: 44px;
}

.form-group select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23718096' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 15px center;
    padding-right: 40px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.btn {
    padding: 14px 30px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    /* Better touch targets */
    min-height: 44px;
    justify-content: center;
    /* Prevent text selection on tap */
    -webkit-user-select: none;
    user-select: none;
    /* Improve tap feedback */
    -webkit-tap-highlight-color: transparent;
}

.btn:active {
    transform: scale(0.98);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    width: 100%;
    justify-content: center;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.btn-primary:active {
    transform: scale(0.98);
}

.btn-danger {
    background: var(--danger);
    color: white;
    padding: 10px 16px;
    font-size: 0.9rem;
    min-height: 44px;
}

.btn-danger:hover {
    background: #e53e3e;
}

.expense-list {
    max-height: 500px;
    overflow-y: auto;
}

.expense-list::-webkit-scrollbar {
    width: 8px;
}

.expense-list::-webkit-scrollbar-track {
    background: var(--light);
    border-radius: 10px;
}

.expense-list::-webkit-scrollbar-thumb {
    background: var(--gray);
    border-radius: 10px;
}

.expense-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border: 2px solid var(--border);
    border-radius: 10px;
    margin-bottom: 12px;
    transition: all 0.3s ease;
    /* Improve touch interaction on mobile */
    min-height: 60px;
}

.expense-item:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow-sm);
    transform: translateX(-3px);
}

.expense-item:active {
    background: var(--light);
}

.expense-info {
    flex: 1;
}

.expense-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
}

.category-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    color: white;
}

.expense-description {
    color: var(--dark);
    font-weight: 600;
    margin-bottom: 3px;
}

.expense-date {
    color: var(--gray);
    font-size: 0.85rem;
}

.expense-amount {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--danger);
    margin-right: 15px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--gray);
}

.empty-state .icon {
    font-size: 5rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
}

.chart-container {
    position: relative;
    height: 400px;
    margin-top: 20px;
}

.export-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid var(--border);
}

.export-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 10px;
}

.btn-export {
    padding: 10px 15px;
    font-size: 0.9rem;
    text-align: center;
    transition: all 0.3s ease;
}

.btn-csv {
    background: linear-gradient(135deg, #48bb78, #38a169);
    color: white;
}

.btn-csv:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(72, 187, 120, 0.4);
}

.btn-json {
    background: linear-gradient(135deg, #4299e1, #3182ce);
    color: white;
}

.btn-json:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(66, 153, 225, 0.4);
}

.btn-excel {
    background: linear-gradient(135deg, #48bb78, #38a169);
    color: white;
}

.btn-excel:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(72, 187, 120, 0.4);
}

.btn-xml {
    background: linear-gradient(135deg, #ed8936, #dd6b20);
    color: white;
}

.btn-xml:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(237, 137, 54, 0.4);
}

.btn-pdf {
    background: linear-gradient(135deg, #f56565, #e53e3e);
    color: white;
}

.btn-pdf:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(245, 101, 101, 0.4);
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: none;
    animation: slideInDown 0.3s ease-out;
}

.alert.show {
    display: block;
}

.alert-success {
    background: #c6f6d5;
    color: #22543d;
    border-left: 4px solid var(--success);
}

.alert-error {
    background: #fed7d7;
    color: #742a2a;
    border-left: 4px solid var(--danger);
}

.footer {
    text-align: center;
    color: white;
    margin-top: 40px;
    padding: 20px;
    opacity: 0.9;
}

/* Animations */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 968px) {
    .main-grid {
        grid-template-columns: 1fr;
    }
    
    .header h1 {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
}

@media (max-width: 768px) {
    .mobile-nav-toggle {
        display: block;
    }
    
    .nav-links {
        flex-direction: column;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }
    
    .nav-links.active {
        max-height: 200px;
    }
    
    .nav-links a {
        width: 100%;
        max-width: 300px;
    }
    
    .stat-card .value {
        font-size: 1.5rem;
    }
    
    .stat-card .icon {
        font-size: 2rem;
    }
}

@media (max-width: 600px) {
    body {
        padding: 10px;
    }
    
    .header h1 {
        font-size: 1.8rem;
    }
    
    .header p {
        font-size: 0.95rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .stat-card {
        padding: 20px;
    }
    
    .card {
        padding: 20px;
        border-radius: 12px;
    }
    
    .card h2 {
        font-size: 1.3rem;
    }
    
    .expense-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        padding: 15px;
    }
    
    .expense-amount {
        margin-right: 0;
        font-size: 1.2rem;
    }
    
    .expense-header {
        flex-wrap: wrap;
    }
    
    .category-badge {
        font-size: 0.8rem;
        padding: 3px 10px;
    }
    
    .expense-description {
        font-size: 0.95rem;
    }
    
    .chart-container {
        height: 280px;
    }
    
    .export-buttons {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    
    .btn-export {
        padding: 12px 10px;
        font-size: 0.85rem;
    }
    
    /* AI Features Mobile Optimization */
    .ai-features-container {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .ai-feature {
        padding: 20px;
    }
    
    .ai-feature h3 {
        font-size: 1.2em;
    }
    
    .nl-input {
        font-size: 14px;
        padding: 12px;
    }
    
    .ai-btn {
        width: 100%;
        padding: 12px 20px;
        font-size: 14px;
    }
    
    .predicted-amount {
        font-size: 2em;
    }
}

@media (max-width: 400px) {
    .header h1 {
        font-size: 1.5rem;
    }
    
    .stat-card .value {
        font-size: 1.3rem;
    }
    
    .expense-list {
        max-height: 400px;
    }
    
    .export-buttons {
        grid-template-columns: 1fr;
    }
}

