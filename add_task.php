<?php
// Enable error reporting (development mode)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session to access user `uid`
session_start();

// Database connection file
include 'db_connect.php';


// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data via POST
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Convert date to MySQL format
    $date = date('Y-m-d', strtotime($date));

    // Get the logged-in user's `uid` from the session
    $uid = $_SESSION['uid'];

    // Prepare and bind the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO tasks (title, description, date, time, uid) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $title, $description, $date, $time, $uid);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<script>alert('Task added successfully'); window.location='app.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>