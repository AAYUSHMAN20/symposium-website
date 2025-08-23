<?php
/**
 * Database Connection Test
 * Run this file to test if your database connection is working
 */

require_once 'config.php';

echo "<!DOCTYPE html>";
echo "<html><head><title>Database Connection Test</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:40px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";
echo "</head><body>";

echo "<h2>Database Connection Test</h2>";

try {
    // Test database connection
    $pdo = getDBConnection();
    
    if ($pdo) {
        echo "<p class='success'>✅ Database connection successful!</p>";
        
        // Test if table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'contact_messages'");
        if ($stmt->rowCount() > 0) {
            echo "<p class='success'>✅ Table 'contact_messages' exists!</p>";
            
            // Test table structure
            $stmt = $pdo->query("DESCRIBE contact_messages");
            $columns = $stmt->fetchAll();
            
            echo "<h3>Table Structure:</h3>";
            echo "<table border='1' style='border-collapse:collapse; padding:5px;'>";
            echo "<tr style='background:#f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            
            foreach ($columns as $column) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Count existing records
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_messages");
            $count = $stmt->fetch()['count'];
            echo "<p class='info'>📊 Current messages in database: <strong>$count</strong></p>";
            
        } else {
            echo "<p class='error'>❌ Table 'contact_messages' does not exist!</p>";
            echo "<p class='info'>💡 Please run the SQL script from 'create_table.sql' to create the table.</p>";
        }
        
    } else {
        echo "<p class='error'>❌ Database connection failed!</p>";
        echo "<p class='info'>💡 Please check your database configuration in config.php</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p class='info'>💡 Please check your database configuration and ensure MySQL is running.</p>";
}

echo "<hr>";
echo "<h3>Configuration Details:</h3>";
echo "<p><strong>Database Host:</strong> " . DB_HOST . "</p>";
echo "<p><strong>Database Name:</strong> " . DB_NAME . "</p>";
echo "<p><strong>Database Username:</strong> " . DB_USERNAME . "</p>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>PDO Available:</strong> " . (extension_loaded('pdo') ? 'Yes' : 'No') . "</p>";
echo "<p><strong>PDO MySQL Available:</strong> " . (extension_loaded('pdo_mysql') ? 'Yes' : 'No') . "</p>";

echo "<hr>";
echo "<p><a href='admin.php'>Go to Admin Dashboard</a> | <a href='../contact.html'>Go to Contact Form</a></p>";
echo "<p style='color:#888; font-size:12px;'>Delete this file after testing for security reasons.</p>";

echo "</body></html>";
?>
