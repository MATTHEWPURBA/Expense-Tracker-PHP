<?php
/**
 * API Documentation & Debug Viewer
 * 
 * Interactive documentation for all API endpoints
 * Allows testing and viewing responses with full debug info
 * 
 * @package ExpenseTracker
 */

require_once __DIR__ . '/bootstrap.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - Expense Tracker</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #666;
            font-size: 1.1rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            background: #10b981;
            color: white;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-left: 10px;
        }
        
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 1200px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
        
        .panel {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .panel h2 {
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .endpoint {
            background: #f9fafb;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .endpoint:hover {
            background: #f3f4f6;
            transform: translateX(5px);
        }
        
        .endpoint-header {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .method {
            padding: 5px 12px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 0.85rem;
            min-width: 70px;
            text-align: center;
        }
        
        .method.GET { background: #dbeafe; color: #1e40af; }
        .method.POST { background: #d1fae5; color: #065f46; }
        .method.PUT { background: #fef3c7; color: #92400e; }
        .method.PATCH { background: #e0e7ff; color: #3730a3; }
        .method.DELETE { background: #fee2e2; color: #991b1b; }
        
        .endpoint-path {
            font-family: 'Courier New', monospace;
            font-size: 0.95rem;
            color: #374151;
        }
        
        .endpoint-desc {
            color: #6b7280;
            font-size: 0.9rem;
            margin-top: 8px;
            padding-left: 85px;
        }
        
        .test-form {
            background: #f9fafb;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #374151;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.3s;
        }
        
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea {
            font-family: 'Courier New', monospace;
            min-height: 120px;
        }
        
        button {
            background: #667eea;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        button:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .response-viewer {
            background: #1f2937;
            color: #e5e7eb;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            overflow-x: auto;
            max-height: 600px;
            overflow-y: auto;
        }
        
        .response-viewer pre {
            margin: 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .debug-info {
            background: #fef3c7;
            border: 2px solid #fbbf24;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .debug-info h3 {
            color: #92400e;
            margin-bottom: 10px;
        }
        
        .debug-section {
            background: white;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            font-family: 'Courier New', monospace;
            font-size: 0.85rem;
        }
        
        .query-list {
            list-style: none;
        }
        
        .query-item {
            background: #f9fafb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            border-left: 3px solid #667eea;
        }
        
        .query-sql {
            color: #059669;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .query-time {
            color: #dc2626;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🚀 Expense Tracker API Documentation
                <span class="status-badge" id="apiStatus">Checking...</span>
            </h1>
            <p class="subtitle">Interactive API testing and debugging tool</p>
        </header>
        
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card">
                <div class="stat-value" id="totalRoutes">0</div>
                <div class="stat-label">Total Routes</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="publicRoutes">0</div>
                <div class="stat-label">Public Routes</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="protectedRoutes">0</div>
                <div class="stat-label">Protected Routes</div>
            </div>
        </div>
        
        <div class="grid">
            <!-- Left Panel: Routes -->
            <div class="panel">
                <h2>📍 Available Endpoints</h2>
                <div id="routesList"></div>
            </div>
            
            <!-- Right Panel: Testing -->
            <div class="panel">
                <h2>🧪 API Tester</h2>
                <div class="test-form">
                    <div class="form-group">
                        <label>Method</label>
                        <select id="testMethod">
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="PATCH">PATCH</option>
                            <option value="DELETE">DELETE</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Endpoint URL</label>
                        <input type="text" id="testUrl" placeholder="/api/expenses">
                    </div>
                    
                    <div class="form-group">
                        <label>Request Body (JSON)</label>
                        <textarea id="testBody" placeholder='{"amount": 100, "category": "food"}'></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="debugMode" checked>
                            Enable Debug Mode (Show detailed info)
                        </label>
                    </div>
                    
                    <button onclick="testEndpoint()">Send Request</button>
                </div>
                
                <div style="margin-top: 20px;">
                    <h3 style="margin-bottom: 10px; color: #374151;">Response</h3>
                    <div class="response-viewer" id="responseViewer">
                        <pre>Click "Send Request" to test an endpoint...</pre>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Full Width Panel: Documentation -->
        <div class="panel">
            <h2>📖 Quick Start Guide</h2>
            
            <h3 style="margin-top: 20px; color: #374151;">Authentication</h3>
            <p style="margin-bottom: 10px;">Protected routes require authentication. The API uses session-based authentication.</p>
            
            <div class="debug-section">
                <strong>Login First:</strong><br>
                POST /api/auth/login<br>
                Body: {"username": "your_username", "password": "your_password"}
            </div>
            
            <h3 style="margin-top: 20px; color: #374151;">Debug Mode</h3>
            <p style="margin-bottom: 10px;">All API responses include debug information when:</p>
            <ul style="margin-left: 30px; margin-bottom: 10px;">
                <li>Debug mode is enabled (DEBUG_MODE = true)</li>
                <li>Request includes <code>?debug=true</code> query parameter</li>
                <li>Request includes <code>X-Debug: true</code> header</li>
            </ul>
            
            <div class="debug-section">
                <strong>Debug info includes:</strong><br>
                • Route and controller information<br>
                • All SQL queries executed<br>
                • Query execution times<br>
                • Memory usage<br>
                • Request metadata
            </div>
            
            <h3 style="margin-top: 20px; color: #374151;">Browser Network Tab</h3>
            <p>Open your browser's Developer Tools (F12) and go to the Network tab to see:</p>
            <ul style="margin-left: 30px;">
                <li>All API requests with their methods (GET, POST, etc.)</li>
                <li>Complete request and response data</li>
                <li>Response times and status codes</li>
                <li>SQL queries executed (in debug mode)</li>
            </ul>
        </div>
    </div>
    
    <script>
        // Check API health
        async function checkHealth() {
            try {
                const response = await fetch('/api.php/api/health');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('apiStatus').textContent = 'API Online';
                    document.getElementById('apiStatus').style.background = '#10b981';
                } else {
                    document.getElementById('apiStatus').textContent = 'API Error';
                    document.getElementById('apiStatus').style.background = '#ef4444';
                }
            } catch (error) {
                document.getElementById('apiStatus').textContent = 'API Offline';
                document.getElementById('apiStatus').style.background = '#ef4444';
            }
        }
        
        // Load routes
        async function loadRoutes() {
            try {
                const response = await fetch('/api.php/api/routes');
                const data = await response.json();
                
                if (data.success && data.data.routes) {
                    const routes = data.data.routes;
                    displayRoutes(routes);
                    updateStats(routes);
                }
            } catch (error) {
                console.error('Failed to load routes:', error);
            }
        }
        
        // Display routes
        function displayRoutes(routes) {
            const container = document.getElementById('routesList');
            container.innerHTML = '';
            
            routes.forEach(route => {
                const endpoint = document.createElement('div');
                endpoint.className = 'endpoint';
                endpoint.onclick = () => selectEndpoint(route);
                
                endpoint.innerHTML = `
                    <div class="endpoint-header">
                        <span class="method ${route.method}">${route.method}</span>
                        <span class="endpoint-path">${route.path}</span>
                    </div>
                    <div class="endpoint-desc">${route.handler}</div>
                `;
                
                container.appendChild(endpoint);
            });
        }
        
        // Update statistics
        function updateStats(routes) {
            document.getElementById('totalRoutes').textContent = routes.length;
            
            const protectedPaths = ['/api/expenses', '/api/dashboard', '/api/settings'];
            const protectedCount = routes.filter(r => 
                protectedPaths.some(p => r.path.startsWith(p))
            ).length;
            
            document.getElementById('protectedRoutes').textContent = protectedCount;
            document.getElementById('publicRoutes').textContent = routes.length - protectedCount;
        }
        
        // Select endpoint for testing
        function selectEndpoint(route) {
            document.getElementById('testMethod').value = route.method;
            document.getElementById('testUrl').value = route.path;
            
            // Clear body for GET requests
            if (route.method === 'GET') {
                document.getElementById('testBody').value = '';
            } else if (route.method === 'POST' && route.path.includes('/expenses')) {
                document.getElementById('testBody').value = JSON.stringify({
                    amount: 100,
                    category: "food",
                    description: "Test expense",
                    date: new Date().toISOString().split('T')[0]
                }, null, 2);
            }
        }
        
        // Test endpoint
        async function testEndpoint() {
            const method = document.getElementById('testMethod').value;
            const url = document.getElementById('testUrl').value;
            const body = document.getElementById('testBody').value;
            const debug = document.getElementById('debugMode').checked;
            
            const viewer = document.getElementById('responseViewer');
            viewer.innerHTML = '<pre>Loading...</pre>';
            
            try {
                const options = {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json'
                    }
                };
                
                if (method !== 'GET' && body.trim()) {
                    options.body = body;
                }
                
                if (debug) {
                    options.headers['X-Debug'] = 'true';
                }
                
                const fullUrl = '/api.php' + url + (debug ? '?debug=true' : '');
                const startTime = performance.now();
                const response = await fetch(fullUrl, options);
                const endTime = performance.now();
                
                const data = await response.json();
                
                // Format response
                const formatted = {
                    status: response.status,
                    statusText: response.statusText,
                    responseTime: (endTime - startTime).toFixed(2) + 'ms',
                    data: data
                };
                
                viewer.innerHTML = '<pre>' + JSON.stringify(formatted, null, 2) + '</pre>';
                
                // Highlight debug info
                if (data._debug) {
                    viewer.style.borderLeft = '5px solid #fbbf24';
                } else {
                    viewer.style.borderLeft = 'none';
                }
            } catch (error) {
                viewer.innerHTML = '<pre style="color: #ef4444;">Error: ' + error.message + '</pre>';
            }
        }
        
        // Initialize
        checkHealth();
        loadRoutes();
        
        // Refresh health status every 30 seconds
        setInterval(checkHealth, 30000);
    </script>
</body>
</html>

