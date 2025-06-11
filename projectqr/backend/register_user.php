<?php
require_once 'db.php';
require_once 'vendor/autoload.php';
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['name']) || !isset($data['email']) || !isset($data['phone'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Sanitize input
$name = filter_var($data['name'], FILTER_SANITIZE_STRING);
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$phone = filter_var($data['phone'], FILTER_SANITIZE_STRING);

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Check if email already exists
    $existingUser = $db->queryOne(
        "SELECT id FROM users WHERE email = ?",
        [$email]
    );
    
    if ($existingUser) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }

    // Generate unique QR code
    $qrCode = uniqid('QR_') . '_' . time();
    
    // Create QR code directory if it doesn't exist
    if (!file_exists(QR_SAVE_PATH)) {
        mkdir(QR_SAVE_PATH, 0777, true);
    }

    // Generate and save QR code
    $qrData = json_encode([
        'code' => $qrCode,
        'name' => $name,
        'email' => $email
    ]);

    $qr = QrCode::create($qrData);
    $writer = new PngWriter();
    $result = $writer->write($qr);
    
    $qrImagePath = QR_SAVE_PATH . $qrCode . '.png';
    file_put_contents($qrImagePath, $result->getString());

    // Insert user into database
    $db->query(
        "INSERT INTO users (name, email, phone, qr_code) VALUES (?, ?, ?, ?)",
        [$name, $email, $phone, $qrCode]
    );

    // Get the user ID
    $userId = $db->getConnection()->lastInsertId();

    // Return success response with QR code URL
    echo json_encode([
        'success' => true,
        'message' => 'User registered successfully',
        'data' => [
            'userId' => $userId,
            'name' => $name,
            'email' => $email,
            'qrCode' => QR_BASE_URL . $qrCode . '.png'
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Registration failed',
        'error' => $e->getMessage()
    ]);
}
