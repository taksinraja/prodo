<?php
// Function to send notification
function sendNotification($deviceToken, $title, $body) {
    $serverKey = 'YOUR_SERVER_KEY'; // Replace with your actual Server Key
    $url = 'https://fcm.googleapis.com/fcm/send';

    // Notification payload
    $notification = [
        'title' => $title,
        'body' => $body,
        'icon' => 'images/logo.png', // Optional: Replace with your icon URL
    ];

    $data = [
        'to' => $deviceToken,
        'notification' => $notification,
        'priority' => 'high',
    ];

    // Set headers for the request
    $headers = [
        'Authorization: key=' . $serverKey,
        'Content-Type: application/json'
    ];

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute the request
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
    }

    // Close cURL
    curl_close($ch);
    return $result;
}

// Example Usage
$deviceToken = 'USER_DEVICE_TOKEN'; // Replace with the actual user device token
$title = 'Task Reminder';
$body = 'It’s time to do your task!';

// Call this function to send notification
sendNotification($deviceToken, $title, $body);
?>