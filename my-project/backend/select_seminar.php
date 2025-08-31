<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }
    
    $studentId = $data['student_id'] ?? null;
    $seminarTopic = trim($data['seminar_topic'] ?? '');
    $category = trim($data['category'] ?? '');
    
    // Validation
    if (empty($studentId) || empty($seminarTopic)) {
        throw new Exception('Student ID and seminar topic are required');
    }
    
    // Verify the student exists and hasn't already selected a seminar
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Check if student exists and get current data
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, seminar_topic FROM students WHERE id = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch();
    
    if (!$student) {
        throw new Exception('Student not found');
    }
    
    // Check if student has already selected a seminar
    if (!empty($student['seminar_topic'])) {
        throw new Exception('You have already selected a seminar topic. Please contact administration to change it.');
    }
    
    // Verify the seminar topic exists in the seminars table
    $stmt = $pdo->prepare("SELECT id, topic FROM seminars WHERE topic = ?");
    $stmt->execute([$seminarTopic]);
    $seminar = $stmt->fetch();
    
    if (!$seminar) {
        throw new Exception('Invalid seminar topic selected');
    }
    
    // Generate seminar link and QR code
    $seminarLink = "https://symposium.example.com/seminar/" . urlencode(str_replace(' ', '-', strtolower($seminarTopic))) . "?student=" . $studentId;
    $qrCode = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($seminarLink);
    
    // Update student record with seminar selection
    $stmt = $pdo->prepare("UPDATE students SET seminar_topic = ?, seminar_link = ?, qr_code = ?, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$seminarTopic, $seminarLink, $qrCode, $studentId]);
    
    if (!$result) {
        throw new Exception('Failed to update seminar selection');
    }
    
    // Update session if it's the same student
    if (isset($_SESSION['student_id']) && $_SESSION['student_id'] == $studentId) {
        $_SESSION['seminar_topic'] = $seminarTopic;
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Seminar selected successfully',
        'data' => [
            'seminar_topic' => $seminarTopic,
            'category' => $category,
            'seminar_link' => $seminarLink,
            'qr_code' => $qrCode,
            'student_name' => $student['first_name'] . ' ' . $student['last_name']
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
