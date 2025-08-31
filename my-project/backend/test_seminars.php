<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Test if seminars table exists and has data
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM seminars");
    $stmt->execute();
    $result = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'message' => 'Database connection successful',
        'seminars_count' => $result['count'],
        'database_name' => DB_NAME
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'database_name' => DB_NAME
    ]);
}
?>
