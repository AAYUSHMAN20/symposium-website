<?php
/**
 * Database Configuration File
 * Configure your database connection settings here
 */

// Database configuration for local development
define('DB_HOST', 'localhost');        // Database host (usually localhost)
define('DB_USERNAME', 'root');         // Your MySQL username (default for XAMPP is 'root')
define('DB_PASSWORD', 'ayush');             // Your MySQL password (default for XAMPP is empty)
define('DB_NAME', 'symposium_db');     // Database name

// Create connection
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USERNAME,
            DB_PASSWORD,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch(PDOException $e) {
        // Log error (don't show sensitive info to users)
        error_log("Database connection failed: " . $e->getMessage());
        return false;
    }
}

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Enable error reporting for development (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
