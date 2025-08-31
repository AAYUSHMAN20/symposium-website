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
    
    $firstName = trim($data['first_name'] ?? '');
    $lastName = trim($data['last_name'] ?? '');
    $email = trim($data['email'] ?? '');
    $contactNumber = trim($data['contact_number'] ?? '');
    $rollNumber = trim($data['roll_number'] ?? '');
    $password = $data['password'] ?? '';
    $confirmPassword = $data['confirm_password'] ?? '';
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($contactNumber) || 
        empty($rollNumber) || empty($password) || empty($confirmPassword)) {
        throw new Exception('All fields are required');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    
    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters long');
    }
    
    if ($password !== $confirmPassword) {
        throw new Exception('Passwords do not match');
    }
    
    // Validate phone number (basic validation)
    if (!preg_match('/^[0-9+\-\s()]+$/', $contactNumber)) {
        throw new Exception('Invalid contact number format');
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM students WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new Exception('Email already registered');
    }
    
    // Check if roll number already exists
    $stmt = $pdo->prepare("SELECT id FROM students WHERE roll_number = ?");
    $stmt->execute([$rollNumber]);
    if ($stmt->fetch()) {
        throw new Exception('Roll number already registered');
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert student data without seminar topic (will be added later)
    $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, email, contact_number, roll_number, password) VALUES (?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([$firstName, $lastName, $email, $contactNumber, $rollNumber, $hashedPassword]);
    
    if (!$result) {
        throw new Exception('Registration failed');
    }
    
    $studentId = $pdo->lastInsertId();
    
    // Set session
    $_SESSION['student_id'] = $studentId;
    $_SESSION['student_name'] = $firstName . ' ' . $lastName;
    $_SESSION['student_email'] = $email;
    $_SESSION['student_roll'] = $rollNumber;
    $_SESSION['user_type'] = 'student';
    
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful',
        'redirect' => 'event.html',
        'data' => [
            'id' => $studentId,
            'name' => $firstName . ' ' . $lastName
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
