<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function sendResponse($success, $message, $data = null) {
    $response = array('success' => $success, 'message' => $message);
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method. Only POST requests are allowed.');
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input)) {
        $input = $_POST;
    }
    
    $required_fields = array('firstName', 'lastName', 'email', 'subject', 'message');
    $missing_fields = array();
    
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        sendResponse(false, 'Missing required fields: ' . implode(', ', $missing_fields));
    }
    
    $firstName = sanitizeInput($input['firstName']);
    $lastName = sanitizeInput($input['lastName']);
    $email = sanitizeInput($input['email']);
    $phone = isset($input['phone']) ? sanitizeInput($input['phone']) : '';
    $subject = sanitizeInput($input['subject']);
    $message = sanitizeInput($input['message']);
    
    if (strlen($firstName) < 2 || strlen($firstName) > 50) {
        sendResponse(false, 'First name must be between 2 and 50 characters.');
    }
    
    if (strlen($lastName) < 2 || strlen($lastName) > 50) {
        sendResponse(false, 'Last name must be between 2 and 50 characters.');
    }
    
    if (!validateEmail($email)) {
        sendResponse(false, 'Please provide a valid email address.');
    }
    
    if (strlen($subject) < 5 || strlen($subject) > 200) {
        sendResponse(false, 'Subject must be between 5 and 200 characters.');
    }
    
    if (strlen($message) < 10 || strlen($message) > 1000) {
        sendResponse(false, 'Message must be between 10 and 1000 characters.');
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        sendResponse(false, 'Database connection failed. Please try again later.');
    }
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM contact_messages WHERE email = ? AND subject = ? AND submitted_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
    $stmt->execute(array($email, $subject));
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        sendResponse(false, 'You have already submitted a similar message recently. Please wait before submitting again.');
    }
    
    $ip_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    
    $stmt = $pdo->prepare("INSERT INTO contact_messages (first_name, last_name, email, phone, subject, message, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $result = $stmt->execute(array($firstName, $lastName, $email, $phone, $subject, $message, $ip_address));
    
    if ($result) {
        $insertId = $pdo->lastInsertId();
        sendResponse(true, 'Thank you for your message! We will get back to you soon.', array('id' => $insertId, 'submitted_at' => date('Y-m-d H:i:s')));
    } else {
        sendResponse(false, 'Failed to submit your message. Please try again.');
    }
    
} catch (PDOException $e) {
    sendResponse(false, 'A database error occurred. Please try again later.');
} catch (Exception $e) {
    sendResponse(false, 'An unexpected error occurred. Please try again later.');
}
?>
