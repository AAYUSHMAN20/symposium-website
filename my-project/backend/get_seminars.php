<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // First, check if seminars table exists and has data
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM seminars");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        throw new Exception('No seminar topics found in database. Please run setup_database.php first.');
    }
    
    // Get all active seminars grouped by category
    $stmt = $pdo->prepare("SELECT id, topic, category FROM seminars WHERE is_active = 1 ORDER BY category, topic");
    $stmt->execute();
    $seminars = $stmt->fetchAll();
    
    // Group seminars by category
    $groupedSeminars = [];
    foreach ($seminars as $seminar) {
        $category = $seminar['category'];
        if (!isset($groupedSeminars[$category])) {
            $groupedSeminars[$category] = [];
        }
        $groupedSeminars[$category][] = [
            'id' => $seminar['id'],
            'topic' => $seminar['topic']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $groupedSeminars,
        'total_seminars' => count($seminars),
        'categories' => array_keys($groupedSeminars)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug_info' => [
            'database' => DB_NAME,
            'host' => DB_HOST
        ]
    ]);
}
?>
