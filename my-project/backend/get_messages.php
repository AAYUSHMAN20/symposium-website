<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if user is admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Get statistics
    $stats_stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_messages,
            SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_messages,
            SUM(CASE WHEN submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as this_week
        FROM contact_messages
    ");
    $stats = $stats_stmt->fetch();
    
    // Get messages with pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
    $offset = ($page - 1) * $per_page;
    
    $stmt = $pdo->prepare("
        SELECT * FROM contact_messages 
        ORDER BY submitted_at DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$per_page, $offset]);
    $messages = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'messages' => $messages,
            'statistics' => [
                'total_messages' => (int)$stats['total_messages'],
                'unread_messages' => (int)$stats['unread_messages'],
                'this_week' => (int)$stats['this_week']
            ],
            'pagination' => [
                'current_page' => $page,
                'per_page' => $per_page,
                'total_messages' => (int)$stats['total_messages']
            ]
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
