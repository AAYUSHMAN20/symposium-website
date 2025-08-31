<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Modify the seminar_topic column to allow NULL values
    $sql = "ALTER TABLE students MODIFY COLUMN seminar_topic VARCHAR(500) NULL";
    $pdo->exec($sql);
    
    echo "Database table updated successfully! The seminar_topic column now allows NULL values.\n";
    echo "Students can now register without selecting a seminar topic initially.\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
