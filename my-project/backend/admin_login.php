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
    
    $username = trim($data['username'] ?? '');
    $password = $data['password'] ?? '';
    
    // Validation
    if (empty($username) || empty($password)) {
        throw new Exception('Username and password are required');
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }
    
    // Check admin credentials
    $stmt = $pdo->prepare("SELECT id, username, password FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if (!$admin || !password_verify($password, $admin['password'])) {
        throw new Exception('Invalid username or password.' );
    }
    
    // Set session variables
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['user_type'] = 'admin';
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'redirect' => 'admin_panel.html'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
