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
    protected static $queries = [];
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Track SQL query for debugging
     */
    protected function trackQuery(string $sql, array $params = [], float $executionTime = 0): void
    {
        self::$queries[] = [
            'model' => get_class($this),
            'table' => $this->table,
            'query' => $sql,
            'params' => $params,
            'execution_time' => round($executionTime * 1000, 2) . 'ms',
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Get all tracked queries
     */
    public static function getQueries(): array
    {
        return self::$queries;
    }
    
    /**
     * Clear tracked queries
     */
    public static function clearQueries(): void
    {
        self::$queries = [];
    }
    
    /**
     * Find record by ID
     */
    public function find($id): ?array
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
            $startTime = microtime(true);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            $this->trackQuery($sql, [$id], microtime(true) - $startTime);
            
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
            $sql = "SELECT * FROM {$this->table}";
            $startTime = microtime(true);
            
            $stmt = $this->db->query($sql);
            $result = $stmt->fetchAll();
            
            $this->trackQuery($sql, [], microtime(true) - $startTime);
            
            return $result;
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
            $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
            $startTime = microtime(true);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$value]);
            $result = $stmt->fetchAll();
            
            $this->trackQuery($sql, [$value], microtime(true) - $startTime);
            
            return $result;
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
            $startTime = microtime(true);
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(array_values($data));
            
            $this->trackQuery($sql, array_values($data), microtime(true) - $startTime);
            
            // If an ID was provided in the data, return it
            // Otherwise, try to get the last insert ID (for auto-increment columns)
            if (isset($data[$this->primaryKey])) {
                return $data[$this->primaryKey];
            }
            
            // For auto-increment columns, get the last inserted ID
            try {
                return $this->db->lastInsertId();
            } catch (Exception $e) {
                // If lastInsertId fails (e.g., no sequence used), just return success
                return $result;
            }
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
            $values = array_values($data);
            $values[] = $id;
            
            $startTime = microtime(true);
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($values);
            
            $this->trackQuery($sql, $values, microtime(true) - $startTime);
            
            return $result;
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
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
            $startTime = microtime(true);
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$id]);
            
            $this->trackQuery($sql, [$id], microtime(true) - $startTime);
            
            return $result;
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
            $startTime = microtime(true);
            
            $stmt = $this->db->prepare($sql);
            
            // Set a query timeout to prevent hanging
            $stmt->setAttribute(\PDO::ATTR_TIMEOUT, 3);
            
            $stmt->execute($params);
            $result = $stmt->fetchAll();
            
            $this->trackQuery($sql, $params, microtime(true) - $startTime);
            
            return $result;
        } catch (\PDOException $e) {
            $executionTime = microtime(true) - $startTime;
            error_log("Query failed after {$executionTime}s: " . $e->getMessage());
            error_log("SQL: $sql");
            error_log("Params: " . json_encode($params));
            return [];
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
            $startTime = microtime(true);
            
            $stmt = $this->db->prepare($sql);
            
            // Set a query timeout to prevent hanging
            $stmt->setAttribute(\PDO::ATTR_TIMEOUT, 3);
            
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            $this->trackQuery($sql, $params, microtime(true) - $startTime);
            
            return $result ?: null;
        } catch (\PDOException $e) {
            $executionTime = microtime(true) - $startTime;
            error_log("Query one failed after {$executionTime}s: " . $e->getMessage());
            error_log("SQL: $sql");
            error_log("Params: " . json_encode($params));
            return null;
        } catch (Exception $e) {
            error_log("Query one failed: " . $e->getMessage());
            return null;
        }
    }
}

