<?php
// Database connection file: db_connect.php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "productivity_app";


// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
