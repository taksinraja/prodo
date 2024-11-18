<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Start PHP session to store email or user ID temporarily during verification
session_start();
include 'db_connect.php'; // Make sure this file contains your database connection code

//Check if the email is set in the URL (or store email in session during registration process for better security)
if (isset($_GET['email'])) {
    $_SESSION['email'] = $_GET['email'];
}

if (isset($_POST['otp'])) {
    // Get the entered OTP
    $enteredOtp = $_POST['otp'];
    $email = $_SESSION['email'];

    // Query the database for the stored OTP for this email
    $result = mysqli_query($conn, "SELECT otp FROM users WHERE email = '$email'");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $storedOtp = $row['otp'];

        // Check if the entered OTP matches the stored OTP
        if ($enteredOtp == $storedOtp) {
            // Update the status to 'active'
            $updateQuery = "UPDATE users SET status = 'active', otp = NULL WHERE email = '$email'";
            if (mysqli_query($conn, $updateQuery)) {
            // Send the alert and redirect using JavaScript
            echo '<script>
                    alert("Account verified successfully. You can now log in.");
                    window.location.href = "signin.php";
                </script>';
            exit; // Ensure the script stops executing after sending the response
            } else {
                echo "<script>alert('Error updating account status. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Invalid OTP. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Error fetching OTP. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prodo - Organize Your Daily Life</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #ffffff;

        }
        .container {
            max-width: 375px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
            box-sizing: border-box;
            height: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: column;
        }
        .illustration {
            width: 100%;
            max-width: 300px;
            height: auto;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 31px;
            margin-bottom: 10px;
        }
        p {
            font-size: 14px;
            color: #a0a0a0;
            margin-bottom: 30px;
        }

        .register-link {
            font-size: 14px;
            color: #a0a0a0;
        }
        .register-link a {
            color: #3168E0;
            text-decoration: none;
        }

        .otp-code{
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
            width: 100%;
            height: 100%;

        }
        .input-group {
            display: flex;
            flex-direction: row;
            gap: 10px;
            width: 100%;
            align-items: center;
        }
        .input-group label {
            font-size: 18px;
            color: #a0a0a0;
            margin-bottom: 10px;
            align-items: flex-start;
        }
        .form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
            width: 100%;
            height: 100%;
            justify-content: space-between;
        }
        .form input {
            width: -webkit-fill-available;
            padding: 15px;
            background-color: #313131;
            border: none;
            border-radius: 5px;
            color: #FFFFFF;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .otp-code {
            text-align: center;
        }
        .otp-code label {
            font-size: 24px;
        }
        
        .otp-input {
            width: 100%;
            font-size: 24px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin: 5px;
        }
        
        .otp-input:focus {
            outline: none;
            border-color: #007bff;
        }
        .button{
            display: flex;
            width: 100%;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .sign-in-btn {
            display: flex;
            width: 100%;
            padding: 15px;
            background-color: #3168E0;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 15px;
            border: none;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
<div class="container">
        <div style="display: flex; flex-direction: column; align-items: center; height: 100%;">
            <img src="images/verify.png" alt="Illustration of person working on laptop" class="illustration">
            <div>
                <h1>Verification Code</h1>
                <p>We have sent the code verification to your email ID.</p>

                <!-- print user email here if (isset($_GET['email'])) {
                                                $_SESSION['email'] = $_GET['email'];
                                            }
            -->
                <p>Email: <strong><?= $_SESSION['email'] ?? '' ?></strong></p>
            </div>

            <div class="otp-code">
                <label for="otp">Enter OTP</label>
                <form class="form" method="POST" action="">
                    <input type="number" maxlength="4" id="otp" name="otp" class="otp-input" required>
                    <div class="button">
                        <button type="submit" class="sign-in-btn" id="verifyBtn">Verify</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- <script>
        // // Move to the next input automatically
        // function moveToNext(currentInput, nextInputId) {
        //     if (currentInput.value.length === 1) {
        //         if (nextInputId) {
        //             document.getElementById(nextInputId).focus(); // Move to next input
        //         }
        //     }
        //     checkOTPInputs(); // Check if all OTP inputs are filled
        // }

        // // Check if all OTP inputs are filled
        // function checkOTPInputs() {
        //     const otp1 = document.getElementById('otp').value;

        //     // Enable button if all inputs are filled
        //     const verifyBtn = document.getElementById('verifyBtn');
        //     if (otp1 && otp2 && otp3 && otp4) {
        //         verifyBtn.classList.add('active'); // Make button clickable
        //     } else {
        //         verifyBtn.classList.remove('active'); // Disable button
        //     }
        // }

        // Verify OTP
        function verifyOTP() {
            const otp = document.getElementById('otp').value;
            const email = "user's email"; // Pass user’s email dynamically

            // Check if all OTP fields are filled
            if (otp) {
                const otp = otp;

                // Send AJAX request to verify_otp.php
                fetch('verify_otp.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `otp=${otp}&email=${encodeURIComponent(email)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        window.location.href = 'signin.php';
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            } else {
                alert('Please enter the complete OTP.');
            }
        }
    </script> -->
    
</body>
</html>