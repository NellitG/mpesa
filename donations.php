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

// M-Pesa STK Push Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $phone = $_POST['phone'];

    // Validate inputs
    if (!$amount || !$phone) {
        echo json_encode(['error' => 'Invalid input']);
        http_response_code(400);
        exit;
    }

    // M-Pesa API credentials
    $consumerKey = 'AX06ehmo29PhAfElAeBJ0VHnD5FS2uyjMgZcZun1dA4zYAuZ';
    $consumerSecret = 'beAnShEvwImJQ0UihPODAbW9C6IuKmVkX3AbClJN69AzA6YlrgnLaEnW47pU8R0s';
    $shortCode = '174379';
    $passKey = 'bfb279f9aa9bdbcf158e97dd71a467cd2c6972f0c60f080f58c78e5c2b2219';

    // Generate Timestamp
    $timestamp = date('YmdHis');

    // Generate Password
    $password = base64_encode($shortCode . $passKey . $timestamp);

    // Access Token
    $accessTokenUrl = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $stkPushUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

    // Get access token
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $accessTokenUrl);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . base64_encode($consumerKey . ':' . $consumerSecret)]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = json_decode(curl_exec($curl));
    curl_close($curl);

    $accessToken = $response->access_token;

    // Initiate STK Push
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $stkPushUrl);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
        'BusinessShortCode' => $shortCode,
        'Password' => $password,
        'Timestamp' => $timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $amount,
        'PartyA' => $phone,
        'PartyB' => $shortCode,
        'PhoneNumber' => $phone,
        'CallBackURL' => 'http://your_callback_url',
        'AccountReference' => 'Donation',
        'TransactionDesc' => 'Donation Payment',
    ]));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = json_decode(curl_exec($curl));
    curl_close($curl);

    if ($response->ResponseCode == '0') {
        echo json_encode(['success' => 'STK Push initiated successfully']);
    } else {
        echo json_encode(['error' => $response->errorMessage]);
        http_response_code(400);
    }
}
