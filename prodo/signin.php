<!-- PHP Authentication Logic -->
<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php'; // Database connection file

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists with active status
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $uid = $user['uid'];

        
        // Verify password using md5
        if (md5($password) === $user['password']) {
            session_start();
            header('Location: app.php');
            $_SESSION['uid'] = $uid;

            // header("Location: app.php"); 
            exit();
        } else {
            echo "<script>alert('Incorrect password. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('User does not exist or is not active. Please register.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In Page</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: white;
            height: 100%;
            font-family: 'Encode Sans', sans-serif;
            /* background-color: var(--background); */
            /* color: var(--text-primary); */
            padding: 12px;
            max-width: 450px;
            margin: auto auto;
            /* display: flex; */
            flex-direction: column;
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
            font-size: 32px;
            margin-bottom: 10px;
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
        .sign-in-button {
            background-color: #3168E0;
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            margin-top: auto;
        }
        .register-link {
            text-align: center;
            margin-top: 15px;
        }
        .register-link a {
            color: #3d5afe;
            text-decoration: none;
        }
        .error-message {
            color: red;
            font-size: 16px;
            text-align: center;
        }
        .form{
            height: 100%;
            display: flex;
            flex-direction: column;
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
                <h1>Let's Sign you in.</h1>
                <p>Welcome back.<br>You've been missed!</p>
            </div>
    
            <!-- Sign-In Form -->
            <form method="POST" action="" class="form">
                <div>
                    <div class="input-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Your email" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                    </div>
                </div>

                <div>
                    <button type="submit" class="sign-in-button">Sign in</button>
                </div>
            </form>
        </div>
        
        <div class="register-link">
            Don't have an account? <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html>