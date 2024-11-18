<?php
include 'db_connect.php'; // Database connection

// Check if OTP is provided
if (isset($_POST['otp']) && isset($_POST['email'])) {
    $otp = mysqli_real_escape_string($conn, $_POST['otp']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Query to check if OTP and email match
    $query = "SELECT * FROM users WHERE email = '$email' AND otp = '$otp'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // OTP matches, so update status to 'active'
        $updateQuery = "UPDATE users SET status = 'active', otp = NULL WHERE email = '$email'";
        if (mysqli_query($conn, $updateQuery)) {
            echo json_encode(['status' => 'success', 'message' => 'OTP verified successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update status.']);
        }
    } else {
        // OTP or email do not match
        echo json_encode(['status' => 'error', 'message' => 'Invalid OTP.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>