<?php
// Enable CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Check for POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method.']);
    http_response_code(405);
    exit;
}

$postData = file_get_contents("php://input");


parse_str($postData, $parsedData);


$amount = isset($parsedData['amount']) ? $parsedData['amount'] : null;
$phone = isset($parsedData['phone']) ? $parsedData['phone'] : null;

if (empty($amount) || empty($phone)) {
    echo json_encode(['error' => 'Invalid input: Missing or empty "amount" or "phone".']);
    http_response_code(400);
    exit;
}

file_put_contents('debug_log.txt', "Amount: $amount, Phone: $phone" . PHP_EOL, FILE_APPEND);

// Return success (temporary for testing)
echo json_encode(['success' => 'Input received successfully.']);
