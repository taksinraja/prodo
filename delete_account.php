<?php
session_start();
include 'db_connect.php'; // Include database connection file

if (isset($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];

    // Delete the user account
    $sql = "DELETE FROM users WHERE uid = '$uid'";
    if (mysqli_query($conn, $sql)) {
        session_destroy(); // Destroy the session
        header("Location: signin.php"); // Redirect to login page
        exit();
    } else {
        echo "Error deleting account: " . mysqli_error($conn);
    }
} else {
    header("Location: signin.php"); // Redirect if not logged in
    exit();
}
?>