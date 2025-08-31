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
    $currentStatus = $data['current_status'] ?? null;
    
    if (empty($messageId)) {
        throw new Exception('Message ID is required');
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Toggle the read status
    $newStatus = $currentStatus ? 0 : 1;
    $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = ? WHERE id = ?");
    $result = $stmt->execute([$newStatus, $messageId]);
    
    if (!$result) {
        throw new Exception('Failed to update message status');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Message status updated successfully',
        'data' => [
            'message_id' => $messageId,
            'new_status' => $newStatus
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
