<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Check if admin is logged in (you'll need to implement admin login)
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

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
    
    $firstName = trim($data['first_name'] ?? '');
    $lastName = trim($data['last_name'] ?? '');
    $email = trim($data['email'] ?? '');
    $contactNumber = trim($data['contact_number'] ?? '');
    $facultyId = trim($data['faculty_id'] ?? '');
    $password = $data['password'] ?? '';
    $department = trim($data['department'] ?? '');
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($contactNumber) || 
        empty($facultyId) || empty($password) || empty($department)) {
        throw new Exception('All fields are required');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters long');
    }
    
    // Validate phone number
    if (!preg_match('/^[0-9+\-\s()]+$/', $contactNumber)) {
        throw new Exception('Invalid contact number format');
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM faculty WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new Exception('Email already registered');
    }
    
    // Check if faculty ID already exists
    $stmt = $pdo->prepare("SELECT id FROM faculty WHERE faculty_id = ?");
    $stmt->execute([$facultyId]);
    if ($stmt->fetch()) {
        throw new Exception('Faculty ID already exists');
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert faculty data
    $stmt = $pdo->prepare("INSERT INTO faculty (first_name, last_name, email, contact_number, faculty_id, password, department) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([$firstName, $lastName, $email, $contactNumber, $facultyId, $hashedPassword, $department]);
    
    if (!$result) {
        throw new Exception('Failed to add faculty member');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Faculty member added successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
