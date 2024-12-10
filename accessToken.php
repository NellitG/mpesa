<?php

header("Access-Control-Allow-Origin: *");

// Allow specific HTTP methods (GET, POST, OPTIONS)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Allow headers (Content-Type and Authorization)
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle OPTIONS request (preflight request)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// M-Pesa API credentials
$consumerKey = 'AX06ehmo29PhAfElAeBJ0VHnD5FS2uyjMgZcZun1dA4zYAuZ';
$consumerSecret = 'beAnShEvwImJQ0UihPODAbW9C6IuKmVkX3AbClJN69AzA6YlrgnLaEnW47pU8R0s';

// Generate Access Token URL
$accessTokenUrl = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

// Initialize cURL session
$curl = curl_init();

// Set cURL options for getting the access token
curl_setopt($curl, CURLOPT_URL, $accessTokenUrl);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . base64_encode($consumerKey . ':' . $consumerSecret)]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// Execute cURL session and get response
$response = curl_exec($curl);

// Check for cURL errors
if (curl_errno($curl)) {
    echo json_encode(['error' => curl_error($curl)]);
    http_response_code(500);
    exit;
}

// Check if the response is empty
if (!$response) {
    echo json_encode(['error' => 'No response from API']);
    http_response_code(500);
    exit;
}

// Close cURL session
curl_close($curl);

// Decode the JSON response from Safaricom API
$responseData = json_decode($response, true);

// Check if access token was returned
if (isset($responseData['access_token'])) {
    // Send back the access token
    echo json_encode(['access_token' => $responseData['access_token']]);
} else {
    // Send error if no access token found
    echo json_encode(['error' => 'Failed to retrieve access token', 'details' => $responseData]);
    http_response_code(400);
}
?>
