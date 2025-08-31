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
    
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    
    // Validation
    if (empty($email) || empty($password)) {
        throw new Exception('Email and password are required');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Check if student exists
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, roll_number, seminar_topic, seminar_link, qr_code, password FROM students WHERE email = ?");
    $stmt->execute([$email]);
    $student = $stmt->fetch();
    
    if (!$student) {
        throw new Exception('Invalid email');
    }
    
    // Verify password
    if (!password_verify($password, $student['password'])) {
        throw new Exception('Invalid password');
    }
    
    // Set session
    $_SESSION['student_id'] = $student['id'];
    $_SESSION['student_name'] = $student['first_name'] . ' ' . $student['last_name'];
    $_SESSION['student_email'] = $student['email'];
    $_SESSION['student_roll'] = $student['roll_number'];
    $_SESSION['user_type'] = 'student';
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'redirect' => 'student_dashboard.html',
        'data' => [
            'student_id' => $student['id'],
            'name' => $student['first_name'] . ' ' . $student['last_name'],
            'seminar_topic' => $student['seminar_topic'],
            'seminar_link' => $student['seminar_link'],
            'qr_code' => $student['qr_code']
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
