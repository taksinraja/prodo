<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prodo</title>
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
        .sign-in-btn {
            display: block;
            width: auto;
            padding: 15px;
            background-color: #3168E0;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .register-link {
            font-size: 14px;
            color: #a0a0a0;
        }
        .register-link a {
            color: #3168E0;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div>
            <img src="images/signin_welcome.png" alt="Illustration of person working on laptop" class="illustration">
            <div>
                <h1>Organize your daily life with Prodo</h1>
                <p>Prodo helps you streamline your tasks, notes, and weather updates in one place. Stay organized!</p>
            </div>
        </div>

        <div style="width: 100%; margin-bottom: 10px">
            <a href="signin.php" class="sign-in-btn">Sign in</a>
            <div class="register-link">
                Don't have an account? <a href="register.php">Register</a>
            </div>
        </div>
    </div>
</body>
</html>