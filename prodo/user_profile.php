<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location: signin.php");
    exit();
}

$uid = $_SESSION['uid'];

// Fetch user details
$sql = "SELECT * FROM users WHERE uid = '$uid'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Profile picture upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = $_FILES['profile_pic']['name'];
        $fileSize = $_FILES['profile_pic']['size'];
        $fileType = $_FILES['profile_pic']['type'];
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if ($fileSize < 2000000 && in_array($fileType, $allowedTypes)) {
            $newFileName = uniqid() . '_' . $fileName;
            $uploadFileDir = './uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $sql = "UPDATE users SET username = '$username', email = '$email', profile_pic = '$newFileName' WHERE uid = '$uid'";
                mysqli_query($conn, $sql);
                header("Location: user_profile.php");
                exit();
            }
        }
    } else {
        $sql = "UPDATE users SET username = '$username', email = '$email' WHERE uid = '$uid'";
        mysqli_query($conn, $sql);
        header("Location: user_profile.php");
        exit();
    }



}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <style>
        :root {
            --background: #141414;
            --card-bg: #313131;
            --primary: #3168E0;
            --calendar-bg: #C8A2D7;
            --task-bg: #d7be95;
            --text-primary: #FFFFFF;
            --text-secondary: #868686;
            --text-dark: #505050;
        }

        body {
            background-color: var(--background);
            color: var(--text-primary);
            font-family: Arial, sans-serif;
            /* margin: 0; */
            height: 100%;
            font-family: 'Encode Sans', sans-serif;
            /* background-color: var(--background); */
            /* color: var(--text-primary); */
            padding: 12px;
            max-width: 450px;
            margin: auto auto;
            display: flex;
            flex-direction: column;
        }

        /* header */

        .header1 {
            display: flex;
            justify-content: flex-start;
            margin: 10px;
        }

        .back-button svg,
        .menu-button svg {
            margin-right: 2px;
            padding: 0;
            color: #ffffff;
        }

        .back-button,
        .menu-button {
            background-color: #121212;
            width: 40px;
            height: 40px;
            border-radius: 50px;
            border: 1px solid gray;
            color: #ffffff;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            margin: 0;
        }

        .header h1 {
            margin: 10px auto;
            font-size: 28px;
            font-weight: normal;
        }

        /* profile */
        .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: column;
            background-color: var(--background-bg);
            /* max-width: 500px; */
            /* margin: auto; */
            /* height: 100%; */
            gap: 20px;
            /* padding: 0px 20px 20px 20px; */

        }
        .header{
            display: flex;
            flex-direction: column;
            align-items: center;
            /* gap: 15px; */
            /* height: 100%; */
        }

        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }

        .form {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .form-group{
            display: flex;
            flex-direction: column;
            /* width: 100%; */
            /* padding: 15px; */
            border-radius: 8px;
            background-color: var(--background); 
            gap: 15px;

        }
        .form-group2{
            display: flex;
            flex-direction: column;
            /* width: 100%; */
            padding: 15px;
            border-radius: 8px;
            background-color: var(--card-bg); ;
        }

        label {
            font-size: 18px;
            margin: 15px 0 4px 0;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"] {
            padding: 15px 10px;
            border: none;
            border-radius: 5px;
            background-color: var(--text-dark);
            color: var(--text-primary);
            margin-top: 5px;
            font-size: 14px;
        }

        button {
            padding: 10px;
            margin-top: 25px;
            border: none;
            border-radius: 5px;
            background-color: var(--primary);
            color: var(--text-primary);
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 16px;
            width: 100%;
        }

        .acc-delete{
            display: flex;
            flex-direction: column;
            width: 100%;
            border-radius: 8px;
            background-color: var(--card-bg); ;
        }

        .delete-form{
            width: 100%;
        }
        .acc-logout{
            display: flex;
            flex-direction: column;
            width: 100%;
            border-radius: 8px;
            background-color: var(--card-bg); ;
        }

        .delete-button {
            margin-top: 10px;
            background-color: red;
        }
        .logout-button {
            margin-top: 10px;
            background-color: var(--primary);
        }

        .delete-button:hover {
            background-color: darkred; /* Darker shade for delete button hover effect */
        }
    </style>
</head>
<body>
<div class="header1">
        <a href="app.php">
            <button class="back-button">
                <!-- back icon svg from favicon -->
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 19L9 13L15 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </a>
    </div>

    <div class="container">
        <div class="header">
            <h1>User Profile</h1>
            <img src="./uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" class="profile-pic" />
        </div>

        <!-- Update Profile Form -->
        <form method="POST" enctype="multipart/form-data" class="form">
            <div class="form-group">
                <div class="form-group2">
                    <label for="username">Username:</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required />
                    
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly
                    style="cursor: not-allowed; outline: none;"/>

                    <label for="profile_pic">Profile Picture <span style="color:#868686; font-size: 16px;">(less than 2MB)</span>:</label>
                    <input type="file" name="profile_pic" accept="image/*" />

                    <button type="submit">Update Profile</button>
                </div>
            </div>
        </form>

        <!-- Logout Button - Separate Form -->
        <div class="acc-logout">
            <div class="form-group2">
                <label for="logout">Logout Account</label>
                <form action="logout.php" method="POST" onsubmit="return confirm('Are you sure you want to logout?');">
                    <button type="submit" name="logout" class="logout-button">Logout</button>
                </form>
            </div>
        </div>

        <!-- Delete Account Button - Separate Form -->
        <div class="acc-delete">
            <div class="form-group2">
                <label for="delete">Delete Account</label>
                <form action="delete_account.php" method="POST" onsubmit="return confirm('Are you sure you want to delete your account?');">
                    <button type="submit" name="delete" class="delete-button">Delete Account</button>
                </form>
            </div>
        </div>
        
    </div>

</body>
</html>