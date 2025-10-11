<?php
/**
 * Base Model
 * 
 * Abstract base class for all models with common CRUD operations
 * 
 * @package ExpenseTracker\Models
 */

namespace ExpenseTracker\Models;

use ExpenseTracker\Services\Database;
use PDO;
use Exception;

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Find record by ID
     */
    public function find($id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            return $result ?: null;
        } catch (Exception $e) {
            error_log("Find failed: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Find all records
     */
    public function all(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table}");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Find all failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Find records with WHERE clause
     */
    public function where(string $column, $value): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ?");
            $stmt->execute([$value]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Where query failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create new record
     */
    public function create(array $data)
    {
        try {
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            
            $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array_values($data));
            
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            error_log("Create failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update record
     */
    public function update($id, array $data): bool
    {
        try {
            $sets = [];
            foreach (array_keys($data) as $column) {
                $sets[] = "{$column} = ?";
            }
            $setClause = implode(', ', $sets);
            
            $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
            $stmt = $this->db->prepare($sql);
            
            $values = array_values($data);
            $values[] = $id;
            
            return $stmt->execute($values);
        } catch (Exception $e) {
            error_log("Update failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete record
     */
    public function delete($id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            error_log("Delete failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Execute raw query
     */
    protected function query(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Query failed: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Execute raw query and return single value
     */
    protected function queryOne(string $sql, array $params = [])
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (Exception $e) {
            error_log("Query one failed: " . $e->getMessage());
            return null;
        }
    }
}

