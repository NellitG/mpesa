<?php
// Database credentials
$host = "localhost";
$username = "root";
$password = "";
$database = "feedback";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    // SQL query to insert data into the table
    $sql = "INSERT INTO feedback (name, email, phone, message) VALUES (?, ?, ?, ?)";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $phone, $message);

    if ($stmt->execute()) {
        echo "Feedback submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
