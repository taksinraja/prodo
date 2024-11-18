<?php
session_start();
session_destroy(); // Destroy the session
header("Location: signin.php"); // Redirect to the login page
exit();
?>