<?php
/**
 * Database Setup Script
 * Run this script to set up the database, tables, and initial data
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'symposium_db';

try {
    // Create connection without database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Setup Progress:</h2>";
    
    // Create database
    echo "<p>1. Creating database '$database'...</p>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $database");
    echo "<p style='color: green;'>✓ Database created successfully!</p>";
    
    // Use the database
    $pdo->exec("USE $database");
    
    // Create tables
    echo "<p>2. Creating tables...</p>";
    
    // Faculty table
    $pdo->exec("CREATE TABLE IF NOT EXISTS faculty (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        contact_number VARCHAR(15) NOT NULL,
        faculty_id VARCHAR(20) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        department VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "<p style='color: green;'>✓ Faculty table created!</p>";
    
    // Students table
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        contact_number VARCHAR(15) NOT NULL,
        roll_number VARCHAR(20) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        seminar_topic VARCHAR(500) NOT NULL,
        seminar_link VARCHAR(500) DEFAULT NULL,
        qr_code VARCHAR(500) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "<p style='color: green;'>✓ Students table created!</p>";
    
    // Admin table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p style='color: green;'>✓ Admin table created!</p>";
    
    // Seminars table
    $pdo->exec("CREATE TABLE IF NOT EXISTS seminars (
        id INT AUTO_INCREMENT PRIMARY KEY,
        topic VARCHAR(500) NOT NULL,
        category VARCHAR(100) NOT NULL,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p style='color: green;'>✓ Seminars table created!</p>";
    
    // Insert default admin user
    echo "<p>3. Setting up default admin user...</p>";
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admin (username, password, email) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE username=username");
    $stmt->execute(['admin', $hashedPassword, 'admin@symposium.com']);
    echo "<p style='color: green;'>✓ Default admin user created (username: admin, password: admin123)</p>";
    
    // Check if seminars table is empty
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM seminars");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        echo "<p>4. Populating seminars table...</p>";
        
        // Read seminar topics from the text file
        $seminarFile = '../Seminar Topics List.txt';
        if (file_exists($seminarFile)) {
            $content = file_get_contents($seminarFile);
            $lines = explode("\n", $content);
            
            $currentCategory = '';
            $insertedCount = 0;
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                // Check if this is a category header (ends with ':')
                if (strpos($line, ':') !== false && !strpos($line, 'Case Study') && !strpos($line, 'Application')) {
                    $currentCategory = trim($line, ':');
                    continue;
                }
                
                // If we have a category and a topic, insert it
                if (!empty($currentCategory) && !empty($line)) {
                    $stmt = $pdo->prepare("INSERT INTO seminars (topic, category) VALUES (?, ?)");
                    $stmt->execute([$line, $currentCategory]);
                    $insertedCount++;
                }
            }
            
            echo "<p style='color: green;'>✓ Inserted $insertedCount seminar topics!</p>";
        } else {
            echo "<p style='color: red;'>✗ Seminar Topics List.txt file not found!</p>";
        }
    } else {
        echo "<p style='color: green;'>✓ Seminars table already has data!</p>";
    }
    
    echo "<h3 style='color: green;'>🎉 Database setup completed successfully!</h3>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ul>";
    echo "<li>Test the admin login at: <a href='../admin_login.html'>admin_login.html</a></li>";
    echo "<li>Test student registration at: <a href='../index.html'>index.html</a></li>";
    echo "<li>Test faculty login at: <a href='../index.html'>index.html</a></li>";
    echo "</ul>";
    
} catch(PDOException $e) {
    echo "<h3 style='color: red;'>❌ Database setup failed!</h3>";
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p><strong>Please check:</strong></p>";
    echo "<ul>";
    echo "<li>MySQL server is running</li>";
    echo "<li>Database credentials are correct in config.php</li>";
    echo "<li>MySQL user has CREATE DATABASE privileges</li>";
    echo "</ul>";
}
?>
