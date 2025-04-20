<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Josefin Sans', sans-serif;

        }

        body {
            background-color: #121212;
            color: #ffffff;
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
            margin-bottom: 40px;
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

        h1 {
            font-size: 20px;
            font-weight: normal;
        }

        .month-container {
            display: flex;
            align-items: center;
            font-size: 24px;
            margin-bottom: 20px;
            position: relative;
        }

        .month {
            margin-right: 10px;
        }

        .dropdown-icon {
            cursor: pointer;
        }

        .month-picker {
            display: none;
            /* Initially hidden */
            position: absolute;
            top: 30px;
            left: 0;
            z-index: 1;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px;
            font-size: 16px;
        }

        .month-container:hover .month-picker {
            display: block;
            /* Show dropdown on hover */
        }

        .date-picker {
            display: flex;
            overflow-x: auto;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .month {
            font-size: 24px;
            font-family: 'Josefin Sans', sans-serif;

        }

        .date-picker {
            display: flex;
            overflow-x: auto;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .date-item {
            min-width: 60px;
            height: 70px;
            background-color: #1e1e1e;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-right: 10px;
            transition: background-color 0.3s;
            text-decoration: none;
        }

        .date-item:visited{
            color: rgb(0, 0, 238);
        }

        
        /* Active date styles */
        .date-item.active {
            background-color: #3168E0;
            color: #ffffff;
            font-weight: bold;
        }
        
        .date.active {
            background-color: #3168E0;
            color: #ffffff;
        }
        .date-item.active .day {
            color: #ffffff;
        }
        .date-item.active .date {
            color: #ffffff;
        }

        a.date-item {
            text-decoration: none;
        }

        /* Current date styles (always highlighted) */
        .current-date  {
            border: 2px solid #3168E0; 
        }

        /* Date styling */
        .date {
            font-family: 'Josefin Sans', sans-serif;
            font-size: 20px;
            color: #888;
        }

        .date a {
            text-decoration: none;

        }

        /* Day styling */
        .day {
            font-family: 'Josefin Sans', sans-serif;
            font-size: 14px;
            color: #888;
            
        }

        .today-task {
            font-size: 20px;
            margin-bottom: 15px;
        }

        .timeline {
            position: relative;
        }

        .time-slot {
            display: flex;
            margin-bottom: 20px;
        }

        .time {
            width: 50px;
            font-size: 14px;
            color: #888;
        }

        .task {
            flex-grow: 1;
            margin-left: 20px;
            padding: 10px;

            border-radius: 5px;
            font-size: 14px;
            position: relative;
            font-weight: bold;
        }

        .task::after {
            content: '';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .task-time {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.7);
        }

        .timeline {
            position: relative;
            padding: 20px 0;
            margin: 20px 0;
            /* border-left: 3px solid #3498db; */
        }

        .time-slot {
            position: relative;
            padding: 20px;
            margin-bottom: 10px;
            background: #1e1e1e;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* .time-slot:before {
            content: '';
            position: absolute;
            top: 0;
            left: -6px;
            width: 12px;
            height: 12px;
            background-color: #3498db;
            border-radius: 50%;
        } */

        .time {
            font-size: 16px;
            font-weight: 400;
        }

        .task {
            background-color: #C8A2D7;
            padding: 6px;
            border-radius: 4px;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
        }

        .no-task {
            color: #aaa;
        }

        .task-time-div {
            display: flex;
            flex-direction: column;
            align-items: start;
            gap: 4px;
        }

        .task-time {
            font-size: 12px;
            font-weight: 400;
            color: #505050;
            display: flex;
            justify-content: start;
            align-items: stretch;
            gap: 4px;
        }

        .task-time img {
            width: 12px;
            height: 12px;
        }
        .task-onetime{
            width: 100%;
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            gap: 10px;
        }

        a button {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #888;
            border-radius: 50px;
            width: 30px;
            height: 30px;
            cursor: pointer;
            background-color: #1e1e1e;
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
        <h1>Schedule</h1>
        <button class="menu-button">
            <svg width="35" height="35" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 8C12.5523 8 13 8.44772 13 9C13 9.55228 12.5523 10 12 10C11.4477 10 11 9.55228 11 9C11 8.44772 11.4477 8 12 8Z" fill="white" />
                <path d="M12 12C12.5523 12 13 12.4477 13 13C13 13.5523 12.5523 14 12 14C11.4477 14 11 13.5523 11 13C11 12.4477 11.4477 12 12 12Z" fill="white" />
                <path d="M12 16C12.5523 16 13 16.4477 13 17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17C11 16.4477 11.4477 16 12 16Z" fill="white" />
            </svg>
        </button>
    </div>

    <div class="month-container">
        <div class="month" id="currentMonth"></div>
        <div class="dropdown-icon">&#9662;</div>
        <select id="month-picker" class="month-picker" onchange="changeMonthYear()">
            <!-- Months and Years will be populated here -->
        </select>
    </div>

    <div class="date-picker">
        <!-- date appers here -->
    </div>

    
    <div class="today-task">Today's task</div>

    <div class="timeline">
        <?php
        include 'fetch_tasks.php';
        // Sort tasks by time
        ksort($tasksByTime);
        // Only display time slots where tasks exist
        ?>

        <?php foreach ($tasksByTime as $time => $tasks) : ?>
            <div class="time-slot">
                <div class="time"><?= $time ?></div>

                <!--Display each task for this specific time slot -->
                <div class="task-onetime">
                    <?php foreach ($tasks as $task): ?>
                        <a href="task_details.php?id=<?= urlencode($task['id']); ?>" style="text-decoration: none; color: white">
                            <div class="task">
                                <div class="task-time-div">
                                    <?= $task['title'] ?> <!-- Task title -->
                                    <div class="task-time">
                                        <img src="images/clock-line-icon.png" alt="">
                                        <?= $time ?>
                                    </div>
                                </div>

                                <button>
                                    <img src="images/fluent-mdl2_go.png" alt="Go">
                                </button>
                            </div>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        <?php endforeach ?>
    </div>


    <script src="schedule.js"></script>
    <script>
    // Function to get and display current month
    function setCurrentMonth() {
        const months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        
        const now = new Date();
        const currentMonth = months[now.getMonth()];
        const currentYear = now.getFullYear();
        
        document.getElementById('currentMonth').textContent = `${currentMonth} ${currentYear}`;
    }

    // Call the function when page loads
    setCurrentMonth();
    </script>
</body>

</html>
