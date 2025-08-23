<?php
/**
 * Contact Form Submission Handler
 * Processes contact form data and saves to database
 */

// Set content type to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration
require_once 'config.php';

// Function to sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate phone number (basic validation)
function validatePhone($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    // Check if length is reasonable (10-15 digits)
    return strlen($phone) >= 10 && strlen($phone) <= 15;
}

// Response function
function sendResponse($success, $message, $data = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Invalid request method. Only POST requests are allowed.');
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // If JSON input is empty, try regular POST data
    if (empty($input)) {
        $input = $_POST;
    }
    
    // Validate required fields
    $required_fields = ['firstName', 'lastName', 'email', 'subject', 'message'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        sendResponse(false, 'Missing required fields: ' . implode(', ', $missing_fields));
    }
    
    // Sanitize input data
    $firstName = sanitizeInput($input['firstName']);
    $lastName = sanitizeInput($input['lastName']);
    $email = sanitizeInput($input['email']);
    $phone = isset($input['phone']) ? sanitizeInput($input['phone']) : '';
    $subject = sanitizeInput($input['subject']);
    $message = sanitizeInput($input['message']);
    
    // Validate data
    if (strlen($firstName) < 2 || strlen($firstName) > 50) {
        sendResponse(false, 'First name must be between 2 and 50 characters.');
    }
    
    if (strlen($lastName) < 2 || strlen($lastName) > 50) {
        sendResponse(false, 'Last name must be between 2 and 50 characters.');
    }
    
    if (!validateEmail($email)) {
        sendResponse(false, 'Please provide a valid email address.');
    }
    
    if (!empty($phone) && !validatePhone($phone)) {
        sendResponse(false, 'Please provide a valid phone number.');
    }
    
    if (strlen($subject) < 5 || strlen($subject) > 200) {
        sendResponse(false, 'Subject must be between 5 and 200 characters.');
    }
    
    if (strlen($message) < 10 || strlen($message) > 1000) {
        sendResponse(false, 'Message must be between 10 and 1000 characters.');
    }
    
    // Get database connection
    $pdo = getDBConnection();
    if (!$pdo) {
        sendResponse(false, 'Database connection failed. Please try again later.');
    }
    
    // Check for duplicate submissions (same email and subject within last 5 minutes)
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as count 
        FROM contact_messages 
        WHERE email = ? AND subject = ? AND submitted_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)
    ");
    $stmt->execute([$email, $subject]);
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        sendResponse(false, 'You have already submitted a similar message recently. Please wait before submitting again.');
    }
    
    // Get client IP address
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_address = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        $ip_address = $_SERVER['HTTP_X_REAL_IP'];
    }
    
    // Insert into database
    $stmt = $pdo->prepare("
        INSERT INTO contact_messages (first_name, last_name, email, phone, subject, message, ip_address) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $firstName,
        $lastName, 
        $email,
        $phone,
        $subject,
        $message,
        $ip_address
    ]);
    
    if ($result) {
        $insertId = $pdo->lastInsertId();
        
        // Optional: Send email notification to admin
        // You can uncomment and configure this section if you want email notifications
        /*
        $to = 'admin@symposium.com';
        $email_subject = 'New Contact Form Submission';
        $email_message = "
            New contact form submission received:
            
            Name: $firstName $lastName
            Email: $email
            Phone: $phone
            Subject: $subject
            Message: $message
            
            Submitted at: " . date('Y-m-d H:i:s') . "
        ";
        $headers = "From: noreply@symposium.com\r\nReply-To: $email";
        mail($to, $email_subject, $email_message, $headers);
        */
        
        sendResponse(true, 'Thank you for your message! We will get back to you soon.', [
            'id' => $insertId,
            'submitted_at' => date('Y-m-d H:i:s')
        ]);
    } else {
        sendResponse(false, 'Failed to submit your message. Please try again.');
    }
    
} catch (PDOException $e) {
    // Log the error (don't expose database errors to users)
    error_log("Database error in submit_contact.php: " . $e->getMessage());
    sendResponse(false, 'A database error occurred. Please try again later.');
} catch (Exception $e) {
    // Log general errors
    error_log("General error in submit_contact.php: " . $e->getMessage());
    sendResponse(false, 'An unexpected error occurred. Please try again later.');
}
?>
