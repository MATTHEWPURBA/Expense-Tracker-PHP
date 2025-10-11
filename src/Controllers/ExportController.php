<?php
/**
 * Export Controller
 * 
 * Handles data export in various formats (CSV, JSON, XML, Excel, PDF)
 * 
 * @package ExpenseTracker\Controllers
 */

namespace ExpenseTracker\Controllers;

use ExpenseTracker\Models\Expense;
use ExpenseTracker\Services\Auth;
use ExpenseTracker\Services\Currency;

class ExportController
{
    private $expenseModel;
    
    public function __construct()
    {
        $this->expenseModel = new Expense();
    }
    
    /**
     * Export data in specified format
     */
    public function export(string $format): void
    {
        $userId = Auth::id();
        $user = Auth::user();
        $expenses = $this->expenseModel->getByUser($userId);
        $filename = 'expenses_' . date('Y-m-d');
        $currency = $user['currency'] ?? 'USD';
        
        switch ($format) {
            case 'csv':
                $this->exportCSV($expenses, $filename, $currency);
                break;
            case 'json':
                $this->exportJSON($expenses, $filename, $currency);
                break;
            case 'xml':
                $this->exportXML($expenses, $filename, $currency);
                break;
            case 'excel':
                $this->exportExcel($expenses, $filename, $currency);
                break;
            case 'pdf':
                $this->exportPDF($expenses, $filename, $currency);
                break;
            default:
                http_response_code(400);
                echo 'Invalid export format';
                exit;
        }
    }
    
    /**
     * Export as CSV
     */
    private function exportCSV(array $expenses, string $filename, string $currency): void
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Category', 'Description', 'Amount', 'Currency', 'Created At']);
        
        foreach ($expenses as $expense) {
            fputcsv($output, [
                $expense['date'],
                $expense['category_name'] ?? $expense['category'],
                $expense['description'],
                Currency::format($expense['amount'], $currency),
                $currency,
                $expense['created_at']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Export as JSON
     */
    private function exportJSON(array $expenses, string $filename, string $currency): void
    {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '.json"');
        
        $exportData = [
            'export_date' => date('Y-m-d H:i:s'),
            'currency' => $currency,
            'currency_symbol' => Currency::getSymbol($currency),
            'total_expenses' => count($expenses),
            'total_amount' => array_sum(array_column($expenses, 'amount')),
            'total_amount_formatted' => Currency::format(array_sum(array_column($expenses, 'amount')), $currency),
            'expenses' => array_map(function($expense) use ($currency) {
                return [
                    'id' => $expense['id'],
                    'date' => $expense['date'],
                    'category' => $expense['category'],
                    'category_name' => $expense['category_name'] ?? $expense['category'],
                    'description' => $expense['description'],
                    'amount' => floatval($expense['amount']),
                    'amount_formatted' => Currency::format($expense['amount'], $currency),
                    'created_at' => $expense['created_at']
                ];
            }, $expenses)
        ];
        
        echo json_encode($exportData, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Export as XML
     */
    private function exportXML(array $expenses, string $filename, string $currency): void
    {
        header('Content-Type: application/xml');
        header('Content-Disposition: attachment; filename="' . $filename . '.xml"');
        
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><expenses></expenses>');
        $xml->addAttribute('export_date', date('Y-m-d H:i:s'));
        $xml->addAttribute('currency', $currency);
        $xml->addAttribute('total_count', count($expenses));
        $xml->addAttribute('total_amount', array_sum(array_column($expenses, 'amount')));
        
        foreach ($expenses as $expense) {
            $expenseNode = $xml->addChild('expense');
            $expenseNode->addChild('id', htmlspecialchars($expense['id']));
            $expenseNode->addChild('date', $expense['date']);
            $expenseNode->addChild('category', htmlspecialchars($expense['category']));
            $expenseNode->addChild('category_name', htmlspecialchars($expense['category_name'] ?? $expense['category']));
            $expenseNode->addChild('description', htmlspecialchars($expense['description']));
            $expenseNode->addChild('amount', $expense['amount']);
            $expenseNode->addChild('amount_formatted', htmlspecialchars(Currency::format($expense['amount'], $currency)));
            $expenseNode->addChild('created_at', $expense['created_at']);
        }
        
        echo $xml->asXML();
        exit;
    }
    
    /**
     * Export as Excel (XML SpreadsheetML format)
     */
    private function exportExcel(array $expenses, string $filename, string $currency): void
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
        
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        echo '    xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        echo '  <Worksheet ss:Name="Expenses">' . "\n";
        echo '    <Table>' . "\n";
        
        // Header row
        echo '      <Row>' . "\n";
        echo '        <Cell><Data ss:Type="String">Date</Data></Cell>' . "\n";
        echo '        <Cell><Data ss:Type="String">Category</Data></Cell>' . "\n";
        echo '        <Cell><Data ss:Type="String">Description</Data></Cell>' . "\n";
        echo '        <Cell><Data ss:Type="String">Amount (' . htmlspecialchars($currency) . ')</Data></Cell>' . "\n";
        echo '        <Cell><Data ss:Type="String">Created At</Data></Cell>' . "\n";
        echo '      </Row>' . "\n";
        
        // Data rows
        foreach ($expenses as $expense) {
            echo '      <Row>' . "\n";
            echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($expense['date']) . '</Data></Cell>' . "\n";
            echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($expense['category_name'] ?? $expense['category']) . '</Data></Cell>' . "\n";
            echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($expense['description']) . '</Data></Cell>' . "\n";
            echo '        <Cell><Data ss:Type="String">' . htmlspecialchars(Currency::format($expense['amount'], $currency)) . '</Data></Cell>' . "\n";
            echo '        <Cell><Data ss:Type="String">' . htmlspecialchars($expense['created_at']) . '</Data></Cell>' . "\n";
            echo '      </Row>' . "\n";
        }
        
        echo '    </Table>' . "\n";
        echo '  </Worksheet>' . "\n";
        echo '</Workbook>';
        exit;
    }
    
    /**
     * Export as printable HTML/PDF
     */
    private function exportPDF(array $expenses, string $filename, string $currency): void
    {
        $stats = $this->expenseModel->getStats(Auth::id());
        $user = Auth::user();
        
        header('Content-Type: text/html');
        header('Content-Disposition: inline; filename="' . $filename . '.html"');
        
        include VIEWS_PATH . '/exports/pdf.php';
        exit;
    }
}

