<?php
/**
 * API Logs Viewer
 * 
 * View and analyze API request logs
 * 
 * @package ExpenseTracker
 */

require_once __DIR__ . '/bootstrap.php';

use ExpenseTracker\Services\RequestLogger;
use ExpenseTracker\Middleware\AuthMiddleware;

// Require authentication
AuthMiddleware::requireAuth();

$logger = RequestLogger::getInstance();
$logs = $logger->getRecentLogs(100);
$stats = $logger->getStatistics();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Request Logs - Expense Tracker</title>
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
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        h1 {
            color: #667eea;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        button, .btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        button:hover, .btn:hover {
            background: #5568d3;
        }
        
        .btn-danger {
            background: #ef4444;
        }
        
        .btn-danger:hover {
            background: #dc2626;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6b7280;
            font-size: 0.95rem;
        }
        
        .panel {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .log-entry {
            background: #f9fafb;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }
        
        .log-entry.error {
            border-left-color: #ef4444;
            background: #fef2f2;
        }
        
        .log-entry.response {
            border-left-color: #10b981;
        }
        
        .log-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .log-type {
            padding: 3px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .log-type.request {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .log-type.response {
            background: #d1fae5;
            color: #065f46;
        }
        
        .log-type.error {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .log-body {
            color: #374151;
            line-height: 1.6;
        }
        
        .method-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-right: 5px;
        }
        
        .method-GET {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .method-POST {
            background: #d1fae5;
            color: #065f46;
        }
        
        .method-PUT {
            background: #fef3c7;
            color: #92400e;
        }
        
        .method-DELETE {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .filter-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        select, input {
            padding: 8px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📊 API Request Logs</h1>
            <div class="actions">
                <a href="/api-docs.php" class="btn">📖 API Docs</a>
                <a href="/index.php" class="btn">🏠 Dashboard</a>
                <button onclick="if(confirm('Clear all logs?')) location.href='?clear=1'" class="btn btn-danger">🗑️ Clear Logs</button>
            </div>
        </header>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $stats['total_requests'] ?></div>
                <div class="stat-label">Total Requests</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $stats['total_errors'] ?></div>
                <div class="stat-label">Errors</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= count($stats['endpoints']) ?></div>
                <div class="stat-label">Unique Endpoints</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= count($stats['users']) ?></div>
                <div class="stat-label">Active Users</div>
            </div>
        </div>
        
        <div class="panel">
            <h2 style="margin-bottom: 20px; color: #667eea;">Recent Requests</h2>
            
            <div class="filter-bar">
                <select id="typeFilter" onchange="filterLogs()">
                    <option value="">All Types</option>
                    <option value="REQUEST">Requests</option>
                    <option value="RESPONSE">Responses</option>
                    <option value="ERROR">Errors</option>
                </select>
                
                <select id="methodFilter" onchange="filterLogs()">
                    <option value="">All Methods</option>
                    <?php foreach (array_keys($stats['methods']) as $method): ?>
                        <option value="<?= $method ?>"><?= $method ?></option>
                    <?php endforeach; ?>
                </select>
                
                <input type="text" id="searchFilter" placeholder="Search..." onkeyup="filterLogs()">
            </div>
            
            <div id="logsList">
                <?php if (empty($logs)): ?>
                    <p style="text-align: center; color: #6b7280; padding: 40px;">
                        No logs available yet. Make some API requests to see logs here.
                    </p>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <div class="log-entry <?= strtolower($log['type']) ?>" 
                             data-type="<?= $log['type'] ?>" 
                             data-method="<?= $log['method'] ?? '' ?>">
                            <div class="log-header">
                                <div>
                                    <span class="log-type <?= strtolower($log['type']) ?>">
                                        <?= $log['type'] ?>
                                    </span>
                                    <?php if (isset($log['method'])): ?>
                                        <span class="method-badge method-<?= $log['method'] ?>">
                                            <?= $log['method'] ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (isset($log['path'])): ?>
                                        <span><?= htmlspecialchars($log['path']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <span style="color: #6b7280;"><?= $log['timestamp'] ?></span>
                                </div>
                            </div>
                            <div class="log-body">
                                <?php if ($log['type'] === 'REQUEST'): ?>
                                    <div><strong>IP:</strong> <?= $log['ip'] ?? 'unknown' ?></div>
                                    <?php if (isset($log['user_id'])): ?>
                                        <div><strong>User ID:</strong> <?= $log['user_id'] ?></div>
                                    <?php endif; ?>
                                <?php elseif ($log['type'] === 'RESPONSE'): ?>
                                    <div><strong>Status:</strong> <?= $log['status_code'] ?? 'unknown' ?></div>
                                    <div><strong>Request ID:</strong> <?= $log['request_id'] ?? 'unknown' ?></div>
                                <?php elseif ($log['type'] === 'ERROR'): ?>
                                    <div style="color: #ef4444;"><strong>Error:</strong> <?= htmlspecialchars($log['message'] ?? 'Unknown error') ?></div>
                                    <?php if (isset($log['context']) && !empty($log['context'])): ?>
                                        <details style="margin-top: 10px;">
                                            <summary style="cursor: pointer; color: #667eea;">Show Context</summary>
                                            <pre style="margin-top: 10px; background: white; padding: 10px; border-radius: 5px; overflow-x: auto;"><?= json_encode($log['context'], JSON_PRETTY_PRINT) ?></pre>
                                        </details>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if (isset($log['data']) && !empty($log['data'])): ?>
                                    <details style="margin-top: 10px;">
                                        <summary style="cursor: pointer; color: #667eea;">Show Data</summary>
                                        <pre style="margin-top: 10px; background: white; padding: 10px; border-radius: 5px; overflow-x: auto;"><?= json_encode($log['data'], JSON_PRETTY_PRINT) ?></pre>
                                    </details>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        function filterLogs() {
            const typeFilter = document.getElementById('typeFilter').value;
            const methodFilter = document.getElementById('methodFilter').value;
            const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
            
            const logs = document.querySelectorAll('.log-entry');
            
            logs.forEach(log => {
                let show = true;
                
                if (typeFilter && log.dataset.type !== typeFilter) {
                    show = false;
                }
                
                if (methodFilter && log.dataset.method !== methodFilter) {
                    show = false;
                }
                
                if (searchFilter && !log.textContent.toLowerCase().includes(searchFilter)) {
                    show = false;
                }
                
                log.style.display = show ? 'block' : 'none';
            });
        }
    </script>
</body>
</html>

<?php
// Handle clear logs action
if (isset($_GET['clear']) && $_GET['clear'] == '1') {
    $logger->clearLogs();
    header('Location: /api-logs.php');
    exit;
}
?>

