<?php
/**
 * Category Model
 * 
 * Handles category data operations
 * 
 * @package ExpenseTracker\Models
 */

namespace ExpenseTracker\Models;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    
    /**
     * Get all categories ordered by name
     */
    public function getAllOrdered(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY name");
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            error_log("Get categories failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Find category by ID
     */
    public function findById(string $categoryId): ?array
    {
        return $this->find($categoryId);
    }
}
