<?php
require_once 'db.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['qrCode'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'QR code is required']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Decode QR data (assuming it's JSON encoded)
    $qrData = json_decode($data['qrCode'], true);
    
    if (!$qrData || !isset($qrData['code'])) {
        throw new Exception('Invalid QR code format');
    }

    $qrCode = $qrData['code'];

    // Get user information
    $user = $db->queryOne(
        "SELECT id, name, email, created_at FROM users WHERE qr_code = ?",
        [$qrCode]
    );

    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Invalid QR code or user not found']);
        exit;
    }

    // Get current active event (you might want to modify this logic based on your needs)
    $currentEvent = $db->queryOne(
        "SELECT id, name FROM events WHERE event_date = CURRENT_DATE() LIMIT 1"
    );

    if (!$currentEvent) {
        // Create a default event if none exists
        $db->query(
            "INSERT INTO events (name, description, event_date) VALUES (?, ?, CURRENT_DATE())",
            ['Daily Event', 'Auto-generated daily event']
        );
        $eventId = $db->getConnection()->lastInsertId();
        $eventName = 'Daily Event';
    } else {
        $eventId = $currentEvent['id'];
        $eventName = $currentEvent['name'];
    }

    // Record attendance
    $db->query(
        "INSERT INTO attendances (user_id, event_id) VALUES (?, ?)",
        [$user['id'], $eventId]
    );

    // Get attendance history
    $attendanceCount = $db->queryOne(
        "SELECT COUNT(*) as count FROM attendances WHERE user_id = ?",
        [$user['id']]
    )['count'];

    // Get last attendance (excluding current one)
    $lastAttendance = $db->queryOne(
        "SELECT e.name as event_name, a.scan_time 
         FROM attendances a 
         JOIN events e ON a.event_id = e.id 
         WHERE a.user_id = ? 
         ORDER BY a.scan_time DESC 
         LIMIT 1 OFFSET 1",
        [$user['id']]
    );

    // Format response data
    $responseData = [
        'success' => true,
        'message' => 'Access verified successfully',
        'data' => [
            'userName' => $user['name'],
            'email' => $user['email'],
            'currentEvent' => $eventName,
            'scanTime' => date('Y-m-d H:i:s'),
            'totalAttendance' => $attendanceCount,
            'lastAttendance' => $lastAttendance ? [
                'eventName' => $lastAttendance['event_name'],
                'scanTime' => $lastAttendance['scan_time']
            ] : null
        ]
    ];

    echo json_encode($responseData);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Verification failed',
        'error' => $e->getMessage()
    ]);
}
