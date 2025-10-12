<?php
/**
 * Login Diagnostics Page
 * 
 * Comprehensive diagnostics for login issues
 * 
 * @package ExpenseTracker
 */

require_once __DIR__ . '/bootstrap.php';

use ExpenseTracker\Services\Auth;
use ExpenseTracker\Models\User;
use ExpenseTracker\Services\Database;
use ExpenseTracker\Services\LoginLogger;

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Diagnostics - Expense Tracker</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .btn { padding: 10px 20px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .status-indicator { display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 8px; }
        .status-success { background: #28a745; }
        .status-error { background: #dc3545; }
        .status-warning { background: #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Login Diagnostics Dashboard</h1>
        
        <!-- Quick Actions -->
        <div class="card">
            <h2>🚀 Quick Actions</h2>
            <button class="btn btn-primary" onclick="location.reload()">Refresh Diagnostics</button>
            <button class="btn btn-success" onclick="window.open('/debug_login.php', '_blank')">Open Debug Tool</button>
            <button class="btn btn-danger" onclick="clearLogs()">Clear Login Logs</button>
        </div>
        
        <!-- System Status -->
        <div class="card">
            <h2>📊 System Status</h2>
            <?php
            $issues = [];
            $warnings = [];
            
            // Check database connection
            try {
                $db = Database::getInstance()->getConnection();
                echo '<p><span class="status-indicator status-success"></span>Database Connection: <span class="success">OK</span></p>';
            } catch (Exception $e) {
                echo '<p><span class="status-indicator status-error"></span>Database Connection: <span class="error">FAILED</span> - ' . htmlspecialchars($e->getMessage()) . '</p>';
                $issues[] = 'Database connection failed';
            }
            
            // Check users table
            try {
                $stmt = $db->query("SELECT COUNT(*) FROM users");
                $userCount = $stmt->fetchColumn();
                echo '<p><span class="status-indicator status-success"></span>Users Table: <span class="success">OK</span> (' . $userCount . ' users)</p>';
            } catch (Exception $e) {
                echo '<p><span class="status-indicator status-error"></span>Users Table: <span class="error">FAILED</span> - ' . htmlspecialchars($e->getMessage()) . '</p>';
                $issues[] = 'Users table issue';
            }
            
            // Check specific user
            try {
                $testEmail = "robhertomatt@gmail.com";
                $userModel = new User();
                $user = $userModel->findByEmail($testEmail);
                if ($user) {
                    echo '<p><span class="status-indicator status-success"></span>Test User Found: <span class="success">OK</span> (' . htmlspecialchars($user['name']) . ')</p>';
                } else {
                    echo '<p><span class="status-indicator status-warning"></span>Test User: <span class="warning">NOT FOUND</span> (' . $testEmail . ')</p>';
                    $warnings[] = 'Test user not found';
                }
            } catch (Exception $e) {
                echo '<p><span class="status-indicator status-error"></span>Test User Check: <span class="error">FAILED</span> - ' . htmlspecialchars($e->getMessage()) . '</p>';
                $issues[] = 'User lookup failed';
            }
            
            // Check logs directory
            $logsDir = __DIR__ . '/logs';
            if (is_dir($logsDir) && is_writable($logsDir)) {
                echo '<p><span class="status-indicator status-success"></span>Logs Directory: <span class="success">OK</span></p>';
            } else {
                echo '<p><span class="status-indicator status-error"></span>Logs Directory: <span class="error">NOT WRITABLE</span></p>';
                $issues[] = 'Logs directory not writable';
            }
            
            // Summary
            if (empty($issues)) {
                echo '<p><strong>Overall Status: <span class="success">✅ HEALTHY</span></strong></p>';
            } else {
                echo '<p><strong>Overall Status: <span class="error">❌ ISSUES DETECTED</span></strong></p>';
            }
            ?>
        </div>
        
        <!-- Recent Login Attempts -->
        <div class="card">
            <h2>📝 Recent Login Attempts</h2>
            <?php
            try {
                $attempts = LoginLogger::getRecentAttempts(20);
                if (!empty($attempts)) {
                    echo '<table>';
                    echo '<tr><th>Timestamp</th><th>Status</th><th>Username</th><th>IP</th><th>Error</th></tr>';
                    foreach ($attempts as $attempt) {
                        $statusClass = $attempt['status'] === 'SUCCESS' ? 'success' : 'error';
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($attempt['timestamp']) . '</td>';
                        echo '<td><span class="' . $statusClass . '">' . htmlspecialchars($attempt['status']) . '</span></td>';
                        echo '<td>' . htmlspecialchars($attempt['username']) . '</td>';
                        echo '<td>' . htmlspecialchars($attempt['ip']) . '</td>';
                        echo '<td>' . htmlspecialchars($attempt['error'] ?? '') . '</td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p class="info">No login attempts logged yet.</p>';
                }
            } catch (Exception $e) {
                echo '<p class="error">Failed to load login attempts: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            ?>
        </div>
        
        <!-- Database Users -->
        <div class="card">
            <h2>👥 Database Users</h2>
            <?php
            try {
                $stmt = $db->query("SELECT id, name, email, created_at FROM users ORDER BY id LIMIT 10");
                $users = $stmt->fetchAll();
                
                if (!empty($users)) {
                    echo '<table>';
                    echo '<tr><th>ID</th><th>Name</th><th>Email</th><th>Created</th><th>Test Login</th></tr>';
                    foreach ($users as $user) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($user['id']) . '</td>';
                        echo '<td>' . htmlspecialchars($user['name']) . '</td>';
                        echo '<td>' . htmlspecialchars($user['email']) . '</td>';
                        echo '<td>' . htmlspecialchars($user['created_at']) . '</td>';
                        echo '<td><button class="btn btn-primary" onclick="testLogin(\'' . htmlspecialchars($user['email']) . '\')">Test</button></td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                } else {
                    echo '<p class="warning">No users found in database.</p>';
                }
            } catch (Exception $e) {
                echo '<p class="error">Failed to load users: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            ?>
        </div>
        
        <!-- Error Logs -->
        <div class="card">
            <h2>🚨 Recent Error Logs</h2>
            <?php
            $errorLogFile = __DIR__ . '/logs/error.log';
            if (file_exists($errorLogFile)) {
                $logs = file_get_contents($errorLogFile);
                $lines = explode("\n", $logs);
                $recentLines = array_slice($lines, -15); // Last 15 lines
                
                echo '<pre>';
                foreach ($recentLines as $line) {
                    if (trim($line)) {
                        echo htmlspecialchars($line) . "\n";
                    }
                }
                echo '</pre>';
            } else {
                echo '<p class="info">No error log file found.</p>';
            }
            ?>
        </div>
        
        <!-- Login Test Form -->
        <div class="card">
            <h2>🧪 Login Test</h2>
            <form id="testLoginForm" onsubmit="return false;">
                <label>Email/Username: <input type="text" id="testUsername" placeholder="robhertomatt@gmail.com" style="margin: 5px;"></label><br><br>
                <label>Password: <input type="password" id="testPassword" placeholder="Enter password" style="margin: 5px;"></label><br><br>
                <button type="button" class="btn btn-primary" onclick="performLoginTest()">Test Login</button>
                <button type="button" class="btn btn-success" onclick="testApiLogin()">Test API Login</button>
            </form>
            <div id="testResults" style="margin-top: 15px;"></div>
        </div>
        
        <!-- Troubleshooting Guide -->
        <div class="card">
            <h2>🔧 Troubleshooting Guide</h2>
            <div>
                <h3>Common Issues & Solutions:</h3>
                <ul>
                    <li><strong>Database Connection Failed:</strong> Check your config.php database settings</li>
                    <li><strong>User Not Found:</strong> Verify the user exists in the database</li>
                    <li><strong>Invalid Password:</strong> Check if password was hashed correctly during registration</li>
                    <li><strong>API Not Working:</strong> Ensure /api/auth/login endpoint is accessible</li>
                    <li><strong>Session Issues:</strong> Check PHP session configuration</li>
                </ul>
                
                <h3>Quick Fixes:</h3>
                <ul>
                    <li>Try creating a new user account</li>
                    <li>Check browser console for JavaScript errors</li>
                    <li>Verify server error logs</li>
                    <li>Test database connection manually</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
    function testLogin(email) {
        document.getElementById('testUsername').value = email;
        document.getElementById('testPassword').focus();
    }
    
    function performLoginTest() {
        const username = document.getElementById('testUsername').value;
        const password = document.getElementById('testPassword').value;
        const results = document.getElementById('testResults');
        
        if (!username || !password) {
            results.innerHTML = '<p class="error">Please enter both username and password</p>';
            return;
        }
        
        results.innerHTML = '<p class="info">Testing login...</p>';
        
        // Create form data
        const formData = new FormData();
        formData.append('username', username);
        formData.append('password', password);
        
        fetch('/login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.includes('Invalid username/email or password')) {
                results.innerHTML = '<p class="error">❌ Login failed: Invalid credentials</p>';
            } else if (data.includes('index.php') || data.includes('dashboard')) {
                results.innerHTML = '<p class="success">✅ Login successful!</p>';
            } else {
                results.innerHTML = '<p class="warning">⚠️ Unexpected response</p>';
            }
        })
        .catch(error => {
            results.innerHTML = '<p class="error">❌ Network error: ' + error.message + '</p>';
        });
    }
    
    function testApiLogin() {
        const username = document.getElementById('testUsername').value;
        const password = document.getElementById('testPassword').value;
        const results = document.getElementById('testResults');
        
        if (!username || !password) {
            results.innerHTML = '<p class="error">Please enter both username and password</p>';
            return;
        }
        
        results.innerHTML = '<p class="info">Testing API login...</p>';
        
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
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                results.innerHTML = '<p class="success">✅ API Login successful!</p>';
            } else {
                results.innerHTML = '<p class="error">❌ API Login failed: ' + (data.error || 'Unknown error') + '</p>';
            }
        })
        .catch(error => {
            results.innerHTML = '<p class="error">❌ API Error: ' + error.message + '</p>';
        });
    }
    
    function clearLogs() {
        if (confirm('Are you sure you want to clear all login logs?')) {
            fetch('/api/clear-logs', { method: 'POST' })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Logs cleared successfully');
                    location.reload();
                } else {
                    alert('Failed to clear logs');
                }
            })
            .catch(error => {
                alert('Error clearing logs: ' + error.message);
            });
        }
    }
    </script>
</body>
</html>
