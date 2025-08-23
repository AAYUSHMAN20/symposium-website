<?php
/**
 * Database Configuration File for InfinityFree Hosting
 * Configure your database connection settings here
 */

// Database configuration for InfinityFree
// Replace these values with your actual InfinityFree database details
define('DB_HOST', 'sql123.infinityfree.com');     // Your database server (check InfinityFree control panel)
define('DB_USERNAME', 'if0_12345678');            // Your database username (check InfinityFree control panel)
define('DB_PASSWORD', 'your_database_password');  // Your database password (check InfinityFree control panel)
define('DB_NAME', 'if0_12345678_symposium_db');   // Your database name (check InfinityFree control panel)

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

// Production settings (disable error reporting for security)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Enable error logging to file instead
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/error.log');
?>
