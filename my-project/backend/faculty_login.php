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
    $department = trim($data['department'] ?? '');
    
    // Validation
    if (empty($email) || empty($password) || empty($department)) {
        throw new Exception('All fields are required');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Check if faculty exists
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, faculty_id, department, password FROM faculty WHERE email = ? AND department = ?");
    $stmt->execute([$email, $department]);
    $faculty = $stmt->fetch();
    
    if (!$faculty) {
        throw new Exception('Invalid email or department');
    }
    
    // Verify password
    if (!password_verify($password, $faculty['password'])) {
        throw new Exception('Invalid password');
    }
    
    // Set session
    $_SESSION['faculty_id'] = $faculty['id'];
    $_SESSION['faculty_name'] = $faculty['first_name'] . ' ' . $faculty['last_name'];
    $_SESSION['faculty_email'] = $faculty['email'];
    $_SESSION['faculty_department'] = $faculty['department'];
    $_SESSION['user_type'] = 'faculty';
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'redirect' => 'faculty_dashboard.html',
        'data' => [
            'name' => $faculty['first_name'] . ' ' . $faculty['last_name'],
            'email' => $faculty['email'],
            'department' => $faculty['department'],
            'faculty_id' => $faculty['faculty_id']
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
