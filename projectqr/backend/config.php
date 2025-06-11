<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Replace with your database username
define('DB_PASSWORD', '');   // Replace with your database password
define('DB_NAME', 'event_manager');

// Error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS Headers for API access
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// QR Code configuration
define('QR_SAVE_PATH', __DIR__ . '/qrcodes/');
define('QR_BASE_URL', 'http://localhost:8000/backend/qrcodes/'); // Update with your server URL
