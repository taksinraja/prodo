<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Generate OTP and activation code
$otp_str = str_shuffle('0123456789');
$otp = substr($otp_str, 0, 4);
$act_str = rand(100000, 999999);
$activation_code = str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ" . $act_str);

// Handle form submission
if (isset($_POST['register'])) {

    // Receive OTP and activation code
    $otp = $_POST['otp'];
    $activation_code = $_POST['activation_code'];

    // Escape user inputs
    $username = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));
    
    // Check if email already exists
    $selectDatabase = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($selectDatabase) > 0) {
        $selectRow = mysqli_fetch_assoc($selectDatabase);
        $status = $selectRow['status'];
    
        if ($status == 'active') {
            echo "<script>alert('Email already registered.');</script>";
        } else {
            // Update user details if they exist but are inactive
            $update = mysqli_query($conn, "UPDATE users SET username = '$username', password = '$password', otp = '$otp', activation_code = '' WHERE email = '$email'");
    
            if ($update) {
                sendEmail($email, $otp); // Reuse function to send email
            }
        }
    } else {
        // Insert new user if email doesn't exist
        $sql = "INSERT INTO users (username, email, password, otp, activation_code) VALUES ('$username', '$email', '$password', '$otp', '$activation_code')";
        
        if ($conn->query($sql) === TRUE) {
            sendEmail($email, $otp); // Reuse function to send email
        } else {
            echo '<script>alert("Something went wrong. Please try again later.");</script>';
        }
    }
}

// Function to send email using PHPMailer
function sendEmail($email, $otp) {
    $mail = new PHPMailer(true);

    try {
        // SMTP server configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'taskinraja01@gmail.com'; // Replace with your email
        $mail->Password   = 'oibynqbzrfcsrbnv'; // Use App Password for Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Set up the email headers and body
        $mail->setFrom('taskinraja01@gmail.com', 'Prodo Support');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Verification Code for your Prodo Account';
        $mail->Body    = '<p>For verifying your email address, use this code: <strong>'.$otp.'</strong></p><p>Sincerely,</p>';
        $mail->AltBody = 'For verifying your email address, use this code: '.$otp;

        // Send the email
        $mail->send();
            // Send the alert and redirect using JavaScript
            echo '<script>
                    alert("Email sent successfully. Please check your inbox.");
                    window.location.href = "verify.php?email=' . urlencode($email) . '";
                </script>';
            exit; // Ensure the script stops executing after sending the response
    } catch (Exception $e) {
        echo '<script>alert("Failed to send verification email. Mailer Error: '.$mail->ErrorInfo.'");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In Page</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: white;
            height: 100%;
        }
        .container {
            padding: 20px;
            box-sizing: border-box;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .first-section {
            display: flex;
            flex-direction: column;
            gap: 15px;
            height: 100%;
        }
        .back-arrow {
            width: 40px;
            height: 40px;
            background-color: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .back-arrow::before {
            font-size: 20px;
            color: white;
        }
        h1 {
            font-size: 36px;
            margin-bottom: 10px;
            margin-top: 10px;

        }
        p {
            font-size: 24px;
            margin-top: 0;
            margin-bottom: 30px;
        }
        .input-group {
            margin-bottom: 20px;
            width: 100%;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-size: 18px;
        }
        input {
            width: -webkit-fill-available;
            padding: 15px 10px;
            background-color: #2a2a2a;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 18px;
        }
        input::placeholder {
            color: #888;
        }
        .submit-btn {
            background-color: #3168E0;
            color: white;
            border: none;
            padding: 15px;
            width:  100%;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            margin-top: auto;
        }
        .form{
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="first-section" style="text-decoration: none; color: white">
            <a href="signin_welcome.php">
                <div class="back-arrow">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 19L9 13L15 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </a>
    
            <div>
                <h1>Let’s Register yourself.</h1>
            </div>
    
            <form action="" method="POST" class="form">
                <div>
                    <div class="input-group">
                        <input type="hidden" name="otp" value="<?= $otp; ?>">
                        <input type="hidden" name="activation_code" value="<?= $activation_code; ?>">
                    </div>

                    <div class="input-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" placeholder="Your name" required>
                    </div>

                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Your email" required>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>

                    <!-- <div class="input-group">
                        <label for="confirm-password">Confirm Password</label>
                        <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password" required>
                    </div> -->
                </div>

                <div class="input-group">
                    <!-- <a href="verify.php" class="sign-in-button"> -->
                        <button type="submit" class="submit-btn" name="register">Register</button>
                    <!-- </a> -->
                </div>
            </form>
        </div>
   
<!--    <div style="margin-bottom: 20px">
            <a href="verify.php" class="sign-in-button">
                <button id="continue-btn" class="sign-in-button" disabled>Continue</button>
            </a>
        </div> -->
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const nameField = document.getElementById('name');
            const emailField = document.getElementById('email');
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm-password');
            const continueButton = document.getElementById('continue-btn');
        
            function checkFields() {
                if (nameField.value && emailField.value && passwordField.value && confirmPasswordField.value) {
                    continueButton.disabled = false;
                } else {
                    continueButton.disabled = true;
                }
            }
        
            // Attach event listeners to each field to check when they are updated
            nameField.addEventListener('input', checkFields);
            emailField.addEventListener('input', checkFields);
            passwordField.addEventListener('input', checkFields);
            confirmPasswordField.addEventListener('input', checkFields);
        });
    </script>
</body>
</html>