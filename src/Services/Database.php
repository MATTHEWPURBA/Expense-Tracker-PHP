<?php
/**
 * Database Service
 * 
 * Singleton pattern for database connection and initialization
 * 
 * @package ExpenseTracker\Services
 */

namespace ExpenseTracker\Services;

use PDO;
use PDOException;
use Exception;

class Database
{
    private static $instance = null;
    private $connection = null;
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct()
    {
        $this->connect();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Get PDO connection
     */
    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connect();
        }
        
        return $this->connection;
    }
    
    /**
     * Establish database connection
     */
    private function connect(): void
    {
        try {
            $dsn = sprintf(
                "pgsql:host=%s;port=%s;dbname=%s;sslmode=%s",
                config('db_host'),
                config('db_port'),
                config('db_name'),
                config('db_ssl', 'require')
            );
            
            $this->connection = new PDO(
                $dsn,
                config('db_user'),
                config('db_pass'),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_PERSISTENT => false, // Disable persistent connections to avoid locks
                ]
            );
            
            // Set PostgreSQL statement timeout to 5 seconds
            $this->connection->exec("SET statement_timeout = 5000");
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    /**
     * Initialize database tables
     */
    public function initializeTables(): void
    {
        try {
            $pdo = $this->getConnection();
            
            // Check if users table exists with current structure
            $stmt = $pdo->query("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_schema = 'public' 
                AND table_name = 'users'
            ");
            $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // If users table doesn't exist or has old structure, create/update it
            if (empty($existingColumns)) {
                // Create fresh users table
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS users (
                        id SERIAL PRIMARY KEY,
                        name VARCHAR(255) NOT NULL,
                        email VARCHAR(255) UNIQUE NOT NULL,
                        password VARCHAR(255) NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )
                ");
            }
            
            // Create categories table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS categories (
                    id VARCHAR(50) PRIMARY KEY,
                    name VARCHAR(100) NOT NULL,
                    color VARCHAR(7) NOT NULL,
                    icon VARCHAR(10) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Create expenses table
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS expenses (
                    id VARCHAR(50) PRIMARY KEY,
                    user_id INTEGER NOT NULL,
                    amount DECIMAL(10,2) NOT NULL,
                    category VARCHAR(50) NOT NULL,
                    description TEXT,
                    date DATE NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (category) REFERENCES categories(id) ON DELETE CASCADE
                )
            ");
            
            // Insert default categories if none exist
            $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
            $count = $stmt->fetchColumn();
            
            if ($count == 0) {
                $this->insertDefaultCategories();
            }
            
        } catch (Exception $e) {
            error_log("Database initialization failed: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Insert default expense categories
     */
    private function insertDefaultCategories(): void
    {
        $categories = [
            ['id' => 'food', 'name' => 'Food & Dining', 'color' => '#FF6384', 'icon' => '🍔'],
            ['id' => 'transport', 'name' => 'Transportation', 'color' => '#36A2EB', 'icon' => '🚗'],
            ['id' => 'utilities', 'name' => 'Utilities', 'color' => '#FFCE56', 'icon' => '💡'],
            ['id' => 'entertainment', 'name' => 'Entertainment', 'color' => '#4BC0C0', 'icon' => '🎮'],
            ['id' => 'healthcare', 'name' => 'Healthcare', 'color' => '#9966FF', 'icon' => '🏥'],
            ['id' => 'shopping', 'name' => 'Shopping', 'color' => '#FF9F40', 'icon' => '🛍️'],
            ['id' => 'other', 'name' => 'Other', 'color' => '#C9CBCF', 'icon' => '📦']
        ];
        
        $pdo = $this->getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO categories (id, name, color, icon) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($categories as $category) {
            $stmt->execute([
                $category['id'],
                $category['name'],
                $category['color'],
                $category['icon']
            ]);
        }
    }
    
    /**
     * Prevent cloning of the instance
     */
    private function __clone() {}
    
    /**
     * Prevent unserializing of the instance
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}

