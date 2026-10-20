<?php
/**
 * Configuration file for B2B Phishing Simulator
 * 
 * SETUP INSTRUCTIONS:
 * 1. Create a MySQL database in cPanel
 * 2. Note the database name, username, and password
 * 3. Update the values below
 */

// ========== DATABASE CONFIGURATION ==========

// Option 1: MySQL (Recommended for cPanel)
define('DB_TYPE', 'mysql'); // 'mysql' or 'mongodb'

// MySQL Settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');

// ========== MONGODB CONFIGURATION (Alternative) ==========
// Only needed if using MongoDB
define('MONGODB_URI', 'mongodb://localhost:27017');
// Or MongoDB Atlas:
// define('MONGODB_URI', 'mongodb+srv://user:pass@cluster.mongodb.net/');

// ========== APPLICATION SETTINGS ==========
define('APP_NAME', 'Adobe Document Cloud');
define('TIMEZONE', 'America/New_York');

// Set timezone
date_default_timezone_set(TIMEZONE);

// Error reporting (set to 0 for production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// ========== DATABASE CONNECTION ==========

function getDB() {
    static $db = null;
    
    if ($db !== null) {
        return $db;
    }
    
    if (DB_TYPE === 'mysql') {
        try {
            $db = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            return $db;
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            http_response_code(500);
            die(json_encode(['success' => false, 'error' => 'Database connection failed']));
        }
    } elseif (DB_TYPE === 'mongodb') {
        try {
            if (!class_exists('MongoDB\Client')) {
                throw new Exception('MongoDB library not installed');
            }
            $db = new MongoDB\Client(MONGODB_URI);
            return $db;
        } catch (Exception $e) {
            error_log("MongoDB connection failed: " . $e->getMessage());
            http_response_code(500);
            die(json_encode(['success' => false, 'error' => 'MongoDB connection failed']));
        }
    }
    
    die(json_encode(['success' => false, 'error' => 'Invalid database type']));
}

// ========== INITIALIZE DATABASE ==========

function initializeDatabase() {
    if (DB_TYPE !== 'mysql') {
        return; // MongoDB doesn't need initialization
    }
    
    $db = getDB();
    
    // Create campaigns table
    $db->exec("
        CREATE TABLE IF NOT EXISTS campaigns (
            id VARCHAR(255) PRIMARY KEY,
            sender VARCHAR(255) NOT NULL,
            title VARCHAR(255) NOT NULL,
            url TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    
    // Create visits table
    $db->exec("
        CREATE TABLE IF NOT EXISTS visits (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip VARCHAR(45) NOT NULL,
            email VARCHAR(255) NOT NULL,
            device VARCHAR(500) NOT NULL,
            mx_type VARCHAR(50) NOT NULL,
            document_key VARCHAR(255) NOT NULL,
            timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_document_key (document_key),
            INDEX idx_email (email),
            INDEX idx_timestamp (timestamp)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}
?>
