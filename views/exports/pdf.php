<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Expense Report - <?php echo date('Y-m-d'); ?></title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 40px; 
            max-width: 1000px; 
            margin: 0 auto; 
        }
        h1 { 
            color: #667eea; 
            border-bottom: 3px solid #667eea; 
            padding-bottom: 10px; 
        }
        .summary { 
            background: #f7fafc; 
            padding: 20px; 
            border-radius: 8px; 
            margin: 20px 0; 
        }
        .summary-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 15px; 
        }
        .summary-item { 
            background: white; 
            padding: 15px; 
            border-radius: 5px; 
            border-left: 4px solid #667eea; 
        }
        .summary-label { 
            color: #718096; 
            font-size: 0.9em; 
        }
        .summary-value { 
            font-size: 1.5em; 
            font-weight: bold; 
            color: #2d3748; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th { 
            background: #667eea; 
            color: white; 
            padding: 12px; 
            text-align: left; 
        }
        td { 
            padding: 10px; 
            border-bottom: 1px solid #e2e8f0; 
        }
        tr:hover { 
            background: #f7fafc; 
        }
        .amount { 
            color: #f56565; 
            font-weight: bold; 
        }
        .category { 
            display: inline-block; 
            padding: 4px 10px; 
            border-radius: 15px; 
            font-size: 0.85em; 
        }
        .footer { 
            margin-top: 40px; 
            padding-top: 20px; 
            border-top: 2px solid #e2e8f0; 
            color: #718096; 
            text-align: center; 
        }
        @media print { 
            body { 
                padding: 20px; 
            } 
        }
    </style>
</head>
<body>
    <h1>💰 Expense Report</h1>
    <p><strong>Generated:</strong> <?php echo date('F d, Y h:i A'); ?></p>
    
    <div class="summary">
        <h2>Summary Statistics (<?php echo $currency; ?>)</h2>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Expenses</div>
                <div class="summary-value"><?php echo \ExpenseTracker\Services\Currency::format($stats['total'], $currency); ?></div>
            </div>
            <div class="summary-item">
                <div class="summary-label">This Month</div>
                <div class="summary-value"><?php echo \ExpenseTracker\Services\Currency::format($stats['monthly'], $currency); ?></div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Transactions</div>
                <div class="summary-value"><?php echo $stats['count']; ?></div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Average</div>
                <div class="summary-value"><?php echo \ExpenseTracker\Services\Currency::format($stats['average'], $currency); ?></div>
            </div>
        </div>
    </div>
    
    <h2>Expense Details</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($expenses as $expense): ?>
                <tr>
                    <td><?php echo date('M d, Y', strtotime($expense['date'])); ?></td>
                    <td>
                        <span class="category">
                            <?php echo htmlspecialchars($expense['category_name'] ?? $expense['category']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($expense['description']); ?></td>
                    <td class="amount"><?php echo \ExpenseTracker\Services\Currency::format($expense['amount'], $currency); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="footer">
        <p>This report contains <?php echo count($expenses); ?> expense entries.</p>
        <p><em>Tip: Use your browser's print function (Ctrl+P / Cmd+P) to save as PDF.</em></p>
    </div>
</body>
</html>

