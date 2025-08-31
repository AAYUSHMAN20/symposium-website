<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Check if faculty is logged in
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'faculty') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Get all students with their seminar details
    $stmt = $pdo->prepare("
        SELECT 
            s.id,
            s.first_name,
            s.last_name,
            s.email,
            s.contact_number,
            s.roll_number,
            s.seminar_topic,
            s.seminar_link,
            s.qr_code,
            s.created_at
        FROM students s
        ORDER BY s.created_at DESC
    ");
    $stmt->execute();
    $students = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $students,
        'count' => count($students)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
