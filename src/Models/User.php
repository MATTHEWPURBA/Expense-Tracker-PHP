<?php
/**
 * User Model
 * 
 * Handles user data operations
 * 
 * @package ExpenseTracker\Models
 */

namespace ExpenseTracker\Models;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    /**
     * Find user by email (case-insensitive)
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE LOWER(email) = LOWER(?) LIMIT 1";
        return $this->queryOne($sql, [$email]);
    }
    
    /**
     * Find user by username (name field, case-insensitive)
     */
    public function findByUsername(string $username): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE LOWER(name) = LOWER(?) LIMIT 1";
        return $this->queryOne($sql, [$username]);
    }
    
    /**
     * Find user by username or email (case-insensitive)
     */
    public function findByUsernameOrEmail(string $usernameOrEmail): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE LOWER(name) = LOWER(?) OR LOWER(email) = LOWER(?) LIMIT 1";
        return $this->queryOne($sql, [$usernameOrEmail, $usernameOrEmail]);
    }
    
    /**
     * Update last login timestamp
     */
    public function updateLastLogin(int $userId): bool
    {
        return $this->update($userId, ['updated_at' => date('Y-m-d H:i:s')]);
    }
    
    /**
     * Get user with expense count
     */
    public function getUserWithStats(int $userId): ?array
    {
        $sql = "
            SELECT 
                u.*,
                COUNT(e.id) as expense_count,
                COALESCE(SUM(e.amount), 0) as total_spent
            FROM users u
            LEFT JOIN expenses e ON u.id = e.user_id
            WHERE u.id = ?
            GROUP BY u.id, u.name, u.email, u.created_at, u.updated_at, u.password, u.currency
        ";
        
        return $this->queryOne($sql, [$userId]);
    }
    
    /**
     * Get user's currency preference
     */
    public function getCurrency(int $userId): string
    {
        $user = $this->find($userId);
        return $user['currency'] ?? 'USD';
    }
    
    /**
     * Update user's currency preference
     */
    public function updateCurrency(int $userId, string $currency): bool
    {
        return $this->update($userId, ['currency' => strtoupper($currency)]);
    }
}
