<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }
    
    $messageId = $data['message_id'] ?? null;
    
    if (empty($messageId)) {
        throw new Exception('Message ID is required');
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Delete the message
    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    $result = $stmt->execute([$messageId]);
    
    if (!$result) {
        throw new Exception('Failed to delete message');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Message deleted successfully',
        'data' => [
            'message_id' => $messageId
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
