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
    
    // Get all faculty members
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, faculty_id, department, contact_number, created_at FROM faculty ORDER BY created_at DESC");
    $stmt->execute();
    $faculty = $stmt->fetchAll();
    
    // Get faculty statistics
    $statsStmt = $pdo->prepare("SELECT COUNT(*) as total FROM faculty");
    $statsStmt->execute();
    $totalFaculty = $statsStmt->fetch()['total'];
    
    // Get student count
    $studentStmt = $pdo->prepare("SELECT COUNT(*) as total FROM students");
    $studentStmt->execute();
    $totalStudents = $studentStmt->fetch()['total'];
    
    // Get today's registrations (both faculty and students)
    $todayStmt = $pdo->prepare("
        SELECT COUNT(*) as total FROM (
            SELECT created_at FROM faculty WHERE DATE(created_at) = CURDATE()
            UNION ALL
            SELECT created_at FROM students WHERE DATE(created_at) = CURDATE()
        ) as today_registrations
    ");
    $todayStmt->execute();
    $todayRegistrations = $todayStmt->fetch()['total'];
    
    // Get seminar count
    $seminarStmt = $pdo->prepare("SELECT COUNT(*) as total FROM seminars");
    $seminarStmt->execute();
    $totalSeminars = $seminarStmt->fetch()['total'];
    
    // Get message statistics
    $messageStmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_messages,
            SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_messages
        FROM contact_messages
    ");
    $messageStmt->execute();
    $messageStats = $messageStmt->fetch();
    
    echo json_encode([
        'success' => true,
        'data' => $faculty,
        'statistics' => [
            'total_faculty' => $totalFaculty,
            'total_students' => $totalStudents,
            'total_seminars' => $totalSeminars,
            'today_registrations' => $todayRegistrations,
            'total_messages' => (int)$messageStats['total_messages'],
            'unread_messages' => (int)$messageStats['unread_messages']
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
