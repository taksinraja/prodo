<?php
// Enable error reporting (development mode)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db_connect.php';

if (!isset($_SESSION['uid'])) {
    echo "<script>alert('Please log in first.');</script>";
    header("Location: signin.php");
    exit;
}

$uid = $_SESSION['uid'];
date_default_timezone_set('Asia/Kolkata');

// Current date and time
$currentDate = date('Y-m-d');
$currentTime = date('H:i');
$currentTimeTimestamp = strtotime($currentTime);

// Prepared statement to fetch tasks due within the past day
$stmt = $conn->prepare("SELECT * FROM tasks WHERE date = ? AND uid = ? AND time >= DATE_SUB(NOW(), INTERVAL 1 DAY)");
$stmt->bind_param("si", $currentDate, $uid);
$stmt->execute();
$result = $stmt->get_result();

$notifications = '';

if ($result && $result->num_rows > 0) {
    while ($task = $result->fetch_assoc()) {
        $taskTimeTimestamp = strtotime($task['time']);
        // Display notification if within 1 day after the task time
        if ($currentTimeTimestamp >= $taskTimeTimestamp && $currentTimeTimestamp < ($taskTimeTimestamp + 86400)) { // 86400 seconds = 1 day
            $notificationId = 'notification_' . uniqid();
            $notifications .= "<div class='notification' id='{$notificationId}'>
                                   <div class='notification-content'>It's time to do: " . htmlspecialchars($task['title']) . 
                                   "<br><span class='task-time'>Task time: " . htmlspecialchars($task['time']) . "</span>
                                   </div>
                               </div>";
        }
    }
} else {
    $notifications = "<p>No tasks are due right now.</p>";
}

// Mark notifications as viewed only for tasks that are due now or in the past
$updateStmt = $conn->prepare("UPDATE tasks SET viewed = 1 
    WHERE uid = ? 
    AND viewed = 0 
    AND CONCAT(date, ' ', time) <= NOW()");
$updateStmt->bind_param("i", $uid);
$updateStmt->execute();

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
// Return JSON response and exit
    echo json_encode([
        'hasNewNotifications' => $hasNewNotifications,
        'notifications' => $notifications
    ]);
    exit;
}

$stmt->close();
$updateStmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <style>
        /* Overall layout enhancements */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #141414;
            color: #333;
            font-family: 'Encode Sans', sans-serif;
            /* background-color: var(--background); */
            /* color: var(--text-primary); */
            padding: 12px;
            max-width: 450px;
            margin: auto auto;
            display: flex;
            flex-direction: column;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
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
            font-size: 20px;
            font-weight: normal;
            margin: 0;
        }
        /* Headings for a cleaner look */
        h1 {
            font-size: 28px;
            color: #ffffff;
            margin-bottom: 20px;
            text-align: start;
        }

        /* Responsive grid system */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            justify-items: center;
            align-items: start;
        }

        /* Mobile-first media query to ensure responsive design */
        @media (max-width: 768px) {
            .wide-card {
                grid-column: span 1;
            }
        }

        .notification {
            background-color: #d7be95; /* Highlight color for notifications */
            border: 1px solid #d6a824;
            margin: 10px 0;
            border-radius: 5px;
            width: 100%;
            position: relative;
            opacity: 1;
            transition: opacity 0.5s ease;
        }
        .notification-content{
            padding: 10px;
        }
        .notification-container {
            display: flex;
            flex-direction: column-reverse; /* Stacks new notifications on top */
            width: 100%;
        }
        .task-time{
            margin-top: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <a href="app.php">
            <button class="back-button">
                <!-- back icon svg from favicon -->
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 19L9 13L15 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </a>
        <h1>Notifications</h1>
        <button class="menu-button">
            <svg width="35" height="35" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 8C12.5523 8 13 8.44772 13 9C13 9.55228 12.5523 10 12 10C11.4477 10 11 9.55228 11 9C11 8.44772 11.4477 8 12 8Z" fill="white" />
                <path d="M12 12C12.5523 12 13 12.4477 13 13C13 13.5523 12.5523 14 12 14C11.4477 14 11 13.5523 11 13C11 12.4477 11.4477 12 12 12Z" fill="white" />
                <path d="M12 16C12.5523 16 13 16.4477 13 17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17C11 16.4477 11.4477 16 12 16Z" fill="white" />
            </svg>
        </button>
    </div>


    <div class="grid-container">
        <!-- Display notifications for tasks -->
        <div class="notification-container">
            <?php
            if ($notifications) {
                echo $notifications; // Show notifications for tasks
            } else {
                echo "<p>No tasks are due right now.</p>";
            }
            ?>
        </div>
    </div>

    <script>
function checkNewNotifications() {
    // Add custom header to identify AJAX request
    fetch('notification.php', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.hasNewNotifications) {
            playNotificationSound();
            // Update notification container with new notifications
            document.querySelector('.notification-container').innerHTML = data.notifications;
        }
    })
    .catch(error => console.error('Error:', error));
}

function playNotificationSound() {
    const audio = new Audio('images/Iphone Notification Tone Mp3 Download.mp3');
    audio.volume = 1; // Set volume to 50%
    audio.play().catch(function(error) {
        console.log('Sound play failed:', error);
    });
}

// Check for new notifications every minute
setInterval(checkNewNotifications, 60000);
</script>
</body>
</html>