<?php
// Start the session
session_start();

// Enable error reporting (development mode)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['uid'])) {
    // Redirect to the sign-in page if `uid` is not set
    header('Location: signin.php');
    exit();
}

include 'db_connect.php'; // Database connection file

// Retrieve `uid` from the session
$uid = $_SESSION['uid'];

// Set the time zone to your local time zone
date_default_timezone_set('Asia/Kolkata');

// Get current date in 'Y-m-d' format
$currentDate = date('Y-m-d');

// Fetch username and profile picture based on the logged-in `uid`
$stmt = $conn->prepare("SELECT username, profile_pic FROM users WHERE uid = ?");
$stmt->bind_param("i", $uid); // Assuming `uid` corresponds to `id` in the database
$stmt->execute();
$result = $stmt->get_result();

// Check if user data was found
if ($result->num_rows > 0) {
    // Fetch the user data
    $user = $result->fetch_assoc();
    $username = htmlspecialchars($user['username']); // Sanitize username for output
    // Set the profile picture path, using a default if none is set
    $profilePic = !empty($user['profile_pic']) ? htmlspecialchars($user['profile_pic']) : 'uploads/default_profile_pic.jpg';
} else {
    echo "User not found.";
    exit();
}

// Fetch total tasks for today for the logged-in user
$totalTasksSql = "SELECT COUNT(*) AS total_tasks FROM tasks WHERE date = ? AND uid = ?";
$totalTasksStmt = $conn->prepare($totalTasksSql);
$totalTasksStmt->bind_param("si", $currentDate, $uid);
$totalTasksStmt->execute();
$totalTasksResult = $totalTasksStmt->get_result();
$totalTasksRow = $totalTasksResult->fetch_assoc();
$totalTasks = $totalTasksRow['total_tasks'];
$totalTasksStmt->close();

// Fetch completed tasks for today for the logged-in user
$completedTasksSql = "SELECT COUNT(*) AS completed_tasks FROM tasks WHERE date = ? AND status = 'complete' AND uid = ?";
$completedTasksStmt = $conn->prepare($completedTasksSql);
$completedTasksStmt->bind_param("si", $currentDate, $uid);
$completedTasksStmt->execute();
$completedTasksResult = $completedTasksStmt->get_result();
$completedTasksRow = $completedTasksResult->fetch_assoc();
$completedTasks = $completedTasksRow['completed_tasks'];
$completedTasksStmt->close();

// Fetch pending tasks for today for the logged-in user
$pendingTasksSql = "SELECT COUNT(*) AS pending_tasks FROM tasks WHERE date = ? AND status = 'pending' AND uid = ?";
$pendingTasksStmt = $conn->prepare($pendingTasksSql);
$pendingTasksStmt->bind_param("si", $currentDate, $uid);
$pendingTasksStmt->execute();
$pendingTasksResult = $pendingTasksStmt->get_result();
$pendingTasksRow = $pendingTasksResult->fetch_assoc();
$pendingTasks = $pendingTasksRow['pending_tasks'];
$pendingTasksStmt->close();

// Get current time
date_default_timezone_set('Asia/Kolkata');
$currentTime = date('H:i');
$currentTimeTimestamp = strtotime($currentTime);

// Check for unread tasks that match current time
$sql = "SELECT * FROM tasks WHERE uid = '$uid' AND viewed = 0 AND time = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $currentTime);
$stmt->execute();
$result = $stmt->get_result();  

$hasUnread = false;
if ($result && mysqli_num_rows($result) > 0) {
    $hasUnread = true;  // Set to true if any unread tasks exist for current time
}

$stmt->close();


// Close the database connection
$conn->close();

// Output the task progress (e.g., "5/7 tasks done")
$progressText = $totalTasks > 0 ? "$completedTasks/$totalTasks tasks done" : "No tasks for today.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prodo</title>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="images/logo.png" type="image/png">
</head>
<!-- Background blur container (initially hidden) -->
<div class="blur-background" id="background" style="display: none;"></div>

<div class="popup">
    <h2>Add New Task</h2>
    <form id="taskForm" action="add_task.php" method="POST">
        <label for="title">Title</label>
        <input type="text" name="title" id="title" placeholder="Add task name" required>

        <label for="description">Description</label>
        <textarea name="description" id="description" placeholder="Add description" style="resize: none;"></textarea>

        <div class="date-time">
            <div>
                <label for="date">Date</label>
                <input type="date" name="date" id="date" placeholder="dd/mm/yy" value="<?= $currentDate?>" required>
            </div>
            <div>
                <label for="time">Time</label>
                <input type="time" name="time" id="time" placeholder="hh : mm" value="<?= date('H:i')?>" required>
            </div>
        </div>
        <div class="buttons">
            <button type="button" id="cancelBtn">Cancel</button>
            <button type="submit" id="createBtn">Create</button>
        </div>
    </form>
</div>
<body>
    <div class="header">
        <div class="user-info">
            <img src="<?php echo 'uploads/'.$profilePic; ?>" alt="User Avatar" class="avatar" id="profileImage">

            <!-- print user name here using php -->
            <span style="margin-left: 10px">Hi, <?php echo htmlspecialchars($username); ?></span>
        </div>

        <!-- Notification -->
        <a href="notification.php" style="text-decoration: none; color: #fff;">
            <div class="notification">
                <div class="notification-icon">
                     <?php if ($hasUnread): ?>
                        <!-- Red dot for unread notifications -->
                        <div style="width: 8px; height: 8px; background-color: red; border-radius: 50%; position: relative; top: 2px; right: -10px; z-index: 1000;"></div>
                    <?php endif; ?>
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.4583 5.66667C13.4583 4.47383 12.9853 3.32951 12.1445 2.48875C11.3038 1.64799 10.1595 1.175 8.96667 1.175C7.77383 1.175 6.62951 1.64799 5.78875 2.48875C4.94799 3.32951 4.475 4.47383 4.475 5.66667C4.475 10.625 2.125 12.0417 2.125 12.0417H15.8083C15.8083 12.0417 13.4583 10.625 13.4583 5.66667Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9.91208 15.0166C9.7992 15.2083 9.64013 15.3673 9.44846 15.4801C9.25679 15.5929 9.0382 15.6561 8.81458 15.6646C8.59097 15.6731 8.36873 15.6267 8.16936 15.5293C7.96999 15.4319 7.79916 15.2864 7.67041 15.1058" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <h1>Be productive today</h1>

    <div class="search-bar">
        <input type="text" placeholder="Search task">
        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12.8333 22.1667C17.988 22.1667 22.1667 17.988 22.1667 12.8333C22.1667 7.67868 17.988 3.5 12.8333 3.5C7.67868 3.5 3.5 7.67868 3.5 12.8333C3.5 17.988 7.67868 22.1667 12.8333 22.1667Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M24.5 24.5L19.425 19.425" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

    <div class="progress-card">
        <div class="progress-header">
            <div>
                <div>Task Progress</div>
                <div style="color: #C5C5C5; font-size: 14px;">
                    <?php echo $progressText; ?>
                </div>
            </div>
            <div class="progress-date">
                <p>

                </p>
            </div>
        </div>
    </div>

    <div class="widgets">
        <div class="weather-calendar">
            <div class="calendar">
                <div class="date" id="date"></div>
                <div class="day" id="day"></div>
                
                <div class="event-message" style="margin-top: auto; font-size: 14px;">No events today</div>
            </div>
            <div class="weather" onclick="window.location.href='weather.php'" style="cursor: pointer;">
                <div id="location">Islampur</div>
                <div id="temperature" style="font-size: 40px; font-weight: 500;">23°</div>
                <div style="margin-top: auto;" class="weather-details">
                    <img id="weather-icon" src="" alt="Weather icon" class="white-icon">
                    <div id="weather-description">Mostly Clear</div> <!-- Updated ID here -->
                    <div id="high-low">H:30° L:21°</div>
                </div>
            </div>
        </div>
        <div class="tasks">
            <div>
                <h3 style=" margin-bottom: 10px;">Today's task</h3>
                <div class="task-list-ul">
                <?php
                    // Database connection file
                    include 'db_connect.php'; // Ensure you have a separate file for database connection

                    // Retrieve `uid` from the session to filter tasks for the logged-in user
                    $uid = $_SESSION['uid'];

                    // Set the time zone to your local time zone
                    date_default_timezone_set('Asia/Kolkata'); 

                    // Get current date in 'Y-m-d' format
                    $currentDate = date('Y-m-d');
                    
                    // Fetch tasks for today and the logged-in user
                    $sql = "SELECT title FROM tasks WHERE date = '$currentDate' AND uid = '$uid'";
                    $result = $conn->query($sql);

                    // Initialize an array to hold task titles
                    $tasks = [];

                    // Check if any tasks are found
                    if ($result->num_rows > 0) {
                        // Loop through the tasks and store the titles in the array
                        while ($row = $result->fetch_assoc()) {
                            $tasks[] = htmlspecialchars($row['title']);
                        }
                    }

                    // Close the database connection
                    $conn->close();

                    // Output the task list
                    echo '<ul class="task-list">';
                    if (count($tasks) > 0) {
                        // Display each task in a list item
                        foreach ($tasks as $task) {
                            echo '<li class="task-item">' . $task . '</li>';
                        }
                    } else {
                        // Display a message if no tasks are found
                        echo '<li class="task-item no-task-message">No tasks for today.</li>';
                    }
                    echo '</ul>';
                ?>
                </div>
            </div>
            <div class="see-all-button">
                <a href="schedule.php" style="text-decoration: none;">
                    <button>
                        <img src="images/fluent-mdl2_go.png" alt="">
                        <!-- <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 18L15 12L9 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg> -->
                    </button>
                </a>
            </div>
        </div>
    </div>



    <!-- <div class="urgent-tasks">
        <h2 style="margin-bottom: 10px;">Urgent tasks</h2>
        <div class="urgent-task">
            <div class="urgent-task-header">
                <div style="font-weight: 500;">Payment</div>
                <div style="font-size: small; font-weight: 300; color: #878787">I have to pay for my shoes</div>
            </div>
            <div class="see-all-button">
                <button>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 18L15 12L9 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="urgent-task">
            <div>
                <div style="font-weight: 500;">Payment</div>
                <div>I have to pay for my shoes</div>
            </div>
            <div class="see-all-button">
                <button>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 18L15 12L9 6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
    </div> -->
    <div class="clock-container">
        <div class="digital-clock">
            <div class="time">
                <span id="hours">00</span>
                <span class="colon">:</span>
                <span id="minutes">00</span>
                <span class="colon">:</span>
                <span id="seconds">00</span>
            </div>
            <!-- <div class="date" id="current-date"></div> -->
        </div>
    </div>

    <div class="notes">
        <div class="notes-header">
            <h2>Notes</h2>
            <div class="see-all-button">
                <a href="notes.php">
                    <button style="background-color: #d7be95;">
                        <svg width="24" height="24" style="color: black" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 18L15 12L9 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </a>
            </div>
        </div>

        <?php
            
            // Include the database connection file
            include 'db_connect.php';

            // Retrieve `uid` from the session
            $uid = $_SESSION['uid'];
            
            // Fetch the first 3 notes from the database for the logged-in user
            $sql = "SELECT * FROM notes WHERE uid = '$uid' ORDER BY created_at DESC LIMIT 3";
            $result = mysqli_query($conn, $sql);
        ?>

        <div class="note-cards">
            <?php
            // Display the first 3 notes if available
            if ($result->num_rows > 0) {
                while ($note = $result->fetch_assoc()) {
                    echo '<div class="note-card style="overflow: hidden">';
                    echo  '<div style="font-weight: 500; margin-bottom: 5px;">'. htmlspecialchars($note['title']) . '</div>';
                    echo '<div class="note-description" style="font-size: 12px;">'  . htmlspecialchars($note['description']) . '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>No notes available.</p>";
            }
            ?>
        </div>
    </div>
    <div class="add-button" >
        <button class="add-task">Add new task +</button>
    </div>

    <script>
        // Fetch the events from the JSON file
        fetch('events.json')
            .then(response => response.json())
            .then(events => {
                // Update current date
                const today = new Date();
                const options = { weekday: 'long', day: 'numeric' };
                const dateString = today.toLocaleDateString('en-US', options);
                const [dayName, date] = dateString.split(' ');
                
                document.querySelector('.calendar #day').textContent = dayName.toUpperCase();
                document.querySelector('.calendar #date').textContent = date;

                // Update the progress date
                const monthOptions = { month: 'long', day: 'numeric' };
                const progressDateString = today.toLocaleDateString('en-US', monthOptions);
                document.querySelector('.progress-date p').textContent = progressDateString;

                // Format today's date as 'YYYY-MM-DD'
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const formattedDate = `${year}-${month}-${day}`;

                // Check if there's an event for today
                const eventMessageElement = document.querySelector('.calendar .event-message');
                if (events[formattedDate]) {
                    // If there's an event, display it
                    eventMessageElement.textContent = events[formattedDate];
                } else {
                    // If no event, show the default message
                    eventMessageElement.textContent = "No events today";
                }

                // Debug log to check dates
                console.log('Today\'s date:', formattedDate);
                console.log('Available events:', events);
            })
            .catch(error => {
                console.error('Error fetching events:', error);
            });



        // Search functionality
        const searchInput = document.querySelector('.search-bar input');
        const taskItems = document.querySelectorAll('.task-item');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            taskItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });


        // Popup for adding new task
        const addTaskButton = document.querySelector('.add-task');
        const popup = document.querySelector('.popup');
        const cancelBtn = document.querySelector('#cancelBtn');
        const taskForm = document.querySelector('#taskForm');
        const background = document.querySelector('#background');  // Reference to blur background div
        
        // Show popup and blur background
        addTaskButton.addEventListener('click', () => {
            popup.style.display = 'flex';  // Ensure popup is displayed
            background.style.display = 'block';  // Show the blur background
        
            setTimeout(() => {
                popup.classList.add('show');  // Add 'show' class for animation
                background.classList.add('show');  // Show the blur background
            }, 10);
        });
        
        // Function to close the popup and remove blur
        function closePopup() {
            popup.classList.remove('show');  // Remove 'show' class to hide popup
            setTimeout(() => {
                popup.style.display = 'none';  // Set display to none after animation completes
                background.style.display = 'none';  // Hide the blur background
            }, 300);  // Match CSS transition duration
        }
        
        // Hide popup when "Cancel" button is clicked
        cancelBtn.addEventListener('click', closePopup);   
        

        // Fetch the weather data
        const apiKey = 'a2e9c4e128c0d0a65b3c008f12186ec7';

        // Function to get user's current location
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showWeatherData, showError);
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        }

        // Function to fetch weather data from OpenWeather API
        function showWeatherData(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&appid=${apiKey}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    updateWeatherUI(data);
                })
                .catch(error => console.error('Error fetching the weather data:', error));
        }

        // Function to update the UI with weather data
        function updateWeatherUI(data) {
            const location = document.getElementById('location');
            const temperature = document.getElementById('temperature');
            const weatherDescriptionElement = document.getElementById('weather-description');
            const highLow = document.getElementById('high-low');
            const weatherIcon = document.getElementById('weather-icon');

            // Log the API response for debugging
            console.log('Weather API Data:', data);

            // Get weather details from API response
            const temp = Math.round(data.main.temp);
            const high = Math.round(data.main.temp_max);
            const low = Math.round(data.main.temp_min);
            const weatherDescription = data.weather[0].description;
            const icon = data.weather[0].icon;
            const isDay = icon.includes('d');  // Check if it's day or night from icon code

            // Update UI with the fetched weather data
            location.innerHTML = data.name;
            temperature.innerHTML = `${temp}°`;
            weatherDescriptionElement.innerHTML = weatherDescription.charAt(0).toUpperCase() + weatherDescription.slice(1);

            // Display High/Low temperatures or a fallback message if they are the same
            // if (high !== low) {
            highLow.innerHTML = `H: ${high}° L: ${low}°`;
            // } else {
            //     highLow.innerHTML = `Temp: ${temp}°`;  // Show just the current temperature as a fallback
            // }

            // Update the weather icon based on day or night
            if (isDay) {
                weatherIcon.src = 'images/sun-icon.png';  // Daytime sun icon
            } else {
                weatherIcon.src = 'images/night-icon.png';  // Nighttime moon icon
            }
        }

        // Function to handle errors if location access is denied
        function showError(error) {
            console.error('Error getting location:', error);
            alert('Unable to retrieve your location.');
        }

        // Call the function to get location and show weather data
        getCurrentLocation();


        // clock-container

        var hoursContainer = document.querySelector('.hours')
        var minutesContainer = document.querySelector('.minutes')
        var secondsContainer = document.querySelector('.seconds')
        var tickElements = Array.from(document.querySelectorAll('.tick'))

        var last = new Date(0)
        last.setUTCHours(-1)

        var tickState = true

        function updateTime () {
        var now = new Date
        
        var lastHours = last.getHours().toString()
        var nowHours = now.getHours().toString()
        if (lastHours !== nowHours) {
            updateContainer(hoursContainer, nowHours)
        }
        
        var lastMinutes = last.getMinutes().toString()
        var nowMinutes = now.getMinutes().toString()
        if (lastMinutes !== nowMinutes) {
            updateContainer(minutesContainer, nowMinutes)
        }
        
        var lastSeconds = last.getSeconds().toString()
        var nowSeconds = now.getSeconds().toString()
        if (lastSeconds !== nowSeconds) {
            //tick()
            updateContainer(secondsContainer, nowSeconds)
        }
        
        last = now
        }

        function tick () {
        tickElements.forEach(t => t.classList.toggle('tick-hidden'))
        }

        function updateContainer (container, newTime) {
        var time = newTime.split('')
        
        if (time.length === 1) {
            time.unshift('0')
        }
        
        
        var first = container.firstElementChild
        if (first.lastElementChild.textContent !== time[0]) {
            updateNumber(first, time[0])
        }
        
        var last = container.lastElementChild
        if (last.lastElementChild.textContent !== time[1]) {
            updateNumber(last, time[1])
        }
        }

        function updateNumber (element, number) {
        //element.lastElementChild.textContent = number
        var second = element.lastElementChild.cloneNode(true)
        second.textContent = number
        
        element.appendChild(second)
        element.classList.add('move')

        setTimeout(function () {
            element.classList.remove('move')
        }, 990)
        setTimeout(function () {
            element.removeChild(element.firstElementChild)
        }, 990)
        }

        setInterval(updateTime, 100)

                // JavaScript to handle image click
        document.getElementById('profileImage').onclick = function() {
            window.location.href = 'user_profile.php'; // Redirect to the desired URL
        };

        // Add this to your existing script section
        function updateClock() {
            const now = new Date();
            let hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            
            // Convert 24h to 12h format
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            hours = hours.toString().padStart(2, '0');
            
            // Update time
            document.getElementById('hours').textContent = hours;
            document.getElementById('minutes').textContent = minutes;
            document.getElementById('seconds').textContent = seconds;
            
            // Add AM/PM indicator
            const timeElements = document.querySelectorAll('.time span');
            if (!document.getElementById('ampm')) {
                const ampmSpan = document.createElement('span');
                ampmSpan.id = 'ampm';
                ampmSpan.style.fontSize = '1rem';
                ampmSpan.style.marginLeft = '5px';
                timeElements[timeElements.length - 1].parentNode.appendChild(ampmSpan);
            }
            document.getElementById('ampm').textContent = ampm;
            
            // Update date
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('current-date').textContent = now.toLocaleDateString('en-US', options);
        }

        // Update clock every second
        setInterval(updateClock, 1000);
        updateClock(); // Initial call
    </script>
</body>
</html>

