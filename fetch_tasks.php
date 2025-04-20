<?php
    // Start the session to access `uid`
    session_start();

    // Database connection file
    include 'db_connect.php';

    // Set the time zone to your local time zone
    date_default_timezone_set('Asia/Kolkata');

    // Get current date in 'Y-m-d' format
    if (isset($_GET['date']) && isset($_GET['month']) && isset($_GET['year'])) {
        $currentDate = date('Y-m-d', strtotime($_GET['year'].'-'.$_GET['month'].'-'.$_GET['date']));
    } else {
        $currentDate = date('Y-m-d');
    }

    // Retrieve `uid` from the session
    $uid = $_SESSION['uid'];

    // Fetch tasks with today's date for the logged-in user
    $sql = "SELECT title, time, id FROM tasks WHERE date = '$currentDate' AND uid = '$uid'";
    $result = $conn->query($sql);

    // Initialize an array to hold tasks by time
    $tasksByTime = [];

    // Check if any tasks are found and store them in the array, grouped by time
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $time = date('hA', strtotime($row['time'])); // Format time to 12-hour format with AM/PM
            $tasksByTime[$time][] = [
                'title' => htmlspecialchars($row['title']),
                'id'    => $row['id'],
            ]; // Store both title and id
        }
    }

    // Close the database connection
    $conn->close();
?>
