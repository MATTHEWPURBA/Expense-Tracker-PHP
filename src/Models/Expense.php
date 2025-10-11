<?php
/**
 * Expense Model
 * 
 * Handles expense data operations
 * 
 * @package ExpenseTracker\Models
 */

namespace ExpenseTracker\Models;

class Expense extends Model
{
    protected $table = 'expenses';
    protected $primaryKey = 'id';
    
    /**
     * Get expenses for a user
     */
    public function getByUser(int $userId): array
    {
        $sql = "
            SELECT 
                e.*,
                c.name as category_name,
                c.color,
                c.icon
            FROM {$this->table} e
            LEFT JOIN categories c ON e.category = c.id
            WHERE e.user_id = ?
            ORDER BY e.date DESC, e.created_at DESC
        ";
        
        return $this->query($sql, [$userId]);
    }
    
    /**
     * Get expense statistics for a user
     */
    public function getStats(int $userId): array
    {
        // Total expenses
        $totalSql = "SELECT COALESCE(SUM(amount), 0) as total FROM {$this->table} WHERE user_id = ?";
        $total = $this->queryOne($totalSql, [$userId])['total'] ?? 0;
        
        // This month expenses
        $monthlySql = "
            SELECT COALESCE(SUM(amount), 0) as monthly
            FROM {$this->table}
            WHERE user_id = ?
            AND EXTRACT(YEAR FROM date) = EXTRACT(YEAR FROM CURRENT_DATE)
            AND EXTRACT(MONTH FROM date) = EXTRACT(MONTH FROM CURRENT_DATE)
        ";
        $monthly = $this->queryOne($monthlySql, [$userId])['monthly'] ?? 0;
        
        // Average expense
        $avgSql = "SELECT COALESCE(AVG(amount), 0) as average FROM {$this->table} WHERE user_id = ?";
        $average = $this->queryOne($avgSql, [$userId])['average'] ?? 0;
        
        // Total count
        $countSql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ?";
        $count = $this->queryOne($countSql, [$userId])['count'] ?? 0;
        
        return [
            'total' => floatval($total),
            'monthly' => floatval($monthly),
            'average' => floatval($average),
            'count' => intval($count)
        ];
    }
    
    /**
     * Get category breakdown for a user
     */
    public function getCategoryBreakdown(int $userId): array
    {
        $sql = "
            SELECT
                c.id,
                c.name,
                c.color,
                c.icon,
                COALESCE(SUM(e.amount), 0) as total
            FROM categories c
            LEFT JOIN {$this->table} e ON c.id = e.category AND e.user_id = ?
            GROUP BY c.id, c.name, c.color, c.icon
            ORDER BY total DESC
        ";
        
        return $this->query($sql, [$userId]);
    }
    
    /**
     * Delete expense (only if it belongs to user)
     */
    public function deleteByUser(string $expenseId, int $userId): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ? AND user_id = ?");
            return $stmt->execute([$expenseId, $userId]);
        } catch (\Exception $e) {
            error_log("Delete expense failed: " . $e->getMessage());
            return false;
        }
    }
}
