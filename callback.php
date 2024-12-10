<?php
// Enable CORS (if needed)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Get the JSON payload from Safaricom
$data = json_decode(file_get_contents("php://input"), true);

// Log the data for debugging (optional)
file_put_contents('callback_log.txt', json_encode($data, JSON_PRETTY_PRINT), FILE_APPEND);

// Check if the callback data is valid
if (isset($data['Body']['stkCallback'])) {
    $callback = $data['Body']['stkCallback'];

    // Extract relevant information
    $resultCode = $callback['ResultCode']; 
    $resultDesc = $callback['ResultDesc'];
    $metadata = isset($callback['CallbackMetadata']) ? $callback['CallbackMetadata'] : [];

    if ($resultCode == 0) {
        // Transaction was successful
        $amount = null;
        $phone = null;

        // Extract data from metadata
        if (isset($metadata['Item'])) {
            foreach ($metadata['Item'] as $item) {
                if ($item['Name'] === 'Amount') $amount = $item['Value'];
                if ($item['Name'] === 'PhoneNumber') $phone = $item['Value'];
            }
        }

        // Process the successful transaction (e.g., save to database)
        file_put_contents('callback_success.txt', "Amount: $amount, Phone: $phone" . PHP_EOL, FILE_APPEND);

    } else {
        // Transaction failed or was cancelled
        file_put_contents('callback_failed.txt', "Result Description: $resultDesc" . PHP_EOL, FILE_APPEND);
    }
} else {
    // Invalid callback
    file_put_contents('callback_invalid.txt', "Invalid callback received" . PHP_EOL, FILE_APPEND);
}

// Respond to Safaricom (important)
http_response_code(200);
echo json_encode(['status' => 'success']);
?>
