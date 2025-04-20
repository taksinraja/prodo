<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['uid'])) {
    // Redirect to the login page if not logged in
    header("Location: signin.php");
    exit();
}

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
include 'db_connect.php';

// Check if the 'id' is set in the URL (to identify the task)
if (isset($_GET['id'])) {
    $taskId = intval($_GET['id']);  // Convert 'id' to an integer to prevent SQL injection

    // Fetch the task details based on the provided task ID
    $sql = "SELECT * FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $taskId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the task was found
    if ($result->num_rows > 0) {
        // Fetch task details
        $task = $result->fetch_assoc();
    } else {
        echo "<script>alert('Task not found'); window.location='app.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('No task ID provided'); window.location='app.php';</script>";
    exit();
}

// Delete task
if (isset($_POST['delete'])) {
    $deleteSql = "DELETE FROM tasks WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("i", $taskId);
    if ($deleteStmt->execute()) {
        echo "<script>alert('Task deleted successfully'); window.location='schedule.php';</script>";
    } else {
        echo "Error deleting task: " . $conn->error;
    }
}

// Mark task as complete (this could involve moving to a 'completed_tasks' table or adding a 'status' column)
if (isset($_POST['complete'])) {
    // Option 1: Update the status to "complete"
    $completeSql = "UPDATE tasks SET status = 'complete' WHERE id = ?";
    $completeStmt = $conn->prepare($completeSql);
    $completeStmt->bind_param("i", $taskId);
    if ($completeStmt->execute()) {
        echo "<script>alert('Task marked as complete'); window.location='schedule.php';</script>";
    } else {
        echo "Error updating task: " . $conn->error;
    }
}

// Update task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {

    // Get the task ID and updated details from the form
    $taskId = $_POST['task_id'];  // Ensure task_id is passed in your form as hidden input or query param
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Prepare the SQL query to update the task (use prepared statements for security)
    $updateQuery = "UPDATE tasks SET title = ?, description = ?, date = ?, time = ?, status = 'pending', viewed = '0' WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ssssi", $title, $description, $date, $time, $taskId);

    // Execute the query
    if ($stmt->execute()) {
        // If the query is successful, redirect or display a success message
        echo "<script>alert('Task updated successfully!'); window.location='task_details.php';</script>";
        exit();  // Stop further script execution after redirection
    } else {
        // If the query fails, display an error message
        echo "Error updating task: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Details</title>
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
            padding: 20px 20px 30px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            margin: 0;
        }

        .menu-container {
            position: relative;
            display: inline-block;
        }

        .dropdown {
            display: none;
            position: absolute;
            background-color: #1e1e1e;
            color: white;
            min-width: 100px;
            border-radius: 8px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            right: 0;
        }

        .dropdown a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }



        /* Modal container (hidden by default) */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: -10;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            -webkit-backdrop-filter: blur(4px); /* For Safari */
            backdrop-filter: blur(4px); /* For other browsers */
            justify-content: center;
            align-items: end;
            padding: 0;
            margin: 0;
            margin-bottom: 10px;
        }
        .modal-header{
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            color: #ffffff;
            font-size: large;
            margin-bottom: 5px;
            margin-top: 10px;
        }

        /* Close button */
        .close {
            color: #ffffff;
            float: right;
            font-size: 28px;
            cursor: pointer;
        }
        /* Modal content box */
        .modal-content {
            background-color: #262626;
            padding: 20px;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
            /* height: 500px; */
            width: 430px;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            position: relative;
            padding-bottom: 30px;
            animation: slide-up 0.4s ease-in-out forwards; /* Trigger the slide-up animation */
        }
        @keyframes slide-up {
            from {
                transform: translateY(100%);
            }
            to {
                transform: translateY(0);
            }
        }


        .modal form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .date-time-container{
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 100%;
            align-items: center;
            gap: 10px;
        }
        /* Form styling */
        input[type="text"],
        input[type="date"],
        input[type="time"],
        textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 6px;
            border: none;
            background-color: #333;
            color: white;
            resize: none;
        }

        .update-btn {
            background-color: #3084FE;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 30px;
        }

        .update-btn:hover {
            background-color: #4084FE;
        }

        /* Style for the task details container */
        .details{
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 0px;
            margin-top: 10px;
        }

        .task-details-container {
            background-color: #161616; /* Darker background for better contrast */
            border-radius: 12px;
            padding: 30px;
            width: 100%;
            height: auto; /* Remove fixed height, let content adjust it */
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: start;
        }

        .task-details-container .title-task {
            color: #ffffff;
            border-bottom: 2px solid #252525;
            padding-bottom: 8px;
            width: 100%;
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            gap: 10px;
        }
        .title-task h2{
            color: #ffffff;
            font-size: 30px;
            margin-bottom: 5px;
            font-weight: 400;
            flex-basis: 30%;
            display: flex;
            align-items: center;
            justify-content: space-between;

        }
        .title-task p{
            color: #ffffff;
            font-size: 26px;
            margin-bottom: 5px;
            font-weight: 400;
            flex-basis: 70%;

        }

        .description {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .task-info{
            display: flex;
            justify-content: start;
            width: 100%;
            color: #b3b3b3;
            font-size: 14px;
            line-height: 1.6;
            align-items: flex-start;
            padding: 20px 0;
            border-bottom: 2px solid #252525;
            gap: 10px;

        }

        .task-info h3{
            color: #9e9e9e;
            flex-basis: 30%;
            font-size: 16px;
            font-weight: 400;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .task-info p{
            color: #ffffff;
            flex-basis: 70%;
            margin-top: 2px;
        }

        /* Button styling */

        .buttons {
            margin-top: 40px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth transitions */
            width: 100%; /* Equal button width */
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .buttons:hover {
            transform: translateY(-2px); /* Hover lift effect */
        }

        .complete-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            background-color: #3084FE;
            color: white;
        }

        .complete-btn:hover {
            background-color: #4084FE;
        }

        .delete-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            background-color: #e53935;
            color: white;
        }
        .delete-btn img {
            width: 20px;
            height: 20px;
        }

        .delete-btn:hover {
            background-color: #e53935;
        }

        .complete-btn[disabled] {
            background-color: #9e9e9e; /* Grey color for disabled state */
            cursor: not-allowed; /* Show not-allowed cursor */
        }
    </style>
</head>
<body>
    <!-- Update Task Modal -->
    <div id="updateTaskModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Update Task</h2>
                <span class="close">&times;</span>
            </div>
            <form id="updateTaskForm" method="POST">
                <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                
                <div>
                    <label for="taskTitle">Title:</label>
                    <input type="text" id="taskTitle" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
                </div>

                <div>
                    <label for="taskDescription">Description:</label>
                    <textarea id="taskDescription" name="description" required><?= htmlspecialchars($task['description']) ?></textarea>
                </div>

                <div class="date-time-container">
                    <div>
                        <label for="taskDate">Due Date:</label>
                        <input type="date" id="taskDate" name="date" value="<?= $task['date'] ?>" required>
                    </div>
                    <div>
                        <label for="taskTime">Time:</label>
                        <input type="time" id="taskTime" name="time" value="<?= $task['time'] ?>" required>
                    </div>

                </div>
                <button type="submit" name="update" class="update-btn">Update Task</button>
            </form>
        </div>
    </div>
    <div class="header">
        <a href="schedule.php">
            <button class="back-button">
                <!-- back icon svg from favicon -->
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 19L9 13L15 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </a>
        <h1>Task Details</h1>
        <div class="menu-container">
            <button class="menu-button">
                <svg width="35" height="35" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8C12.5523 8 13 8.44772 13 9C13 9.55228 12.5523 10 12 10C11.4477 10 11 9.55228 11 9C11 8.44772 11.4477 8 12 8Z" fill="white" />
                    <path d="M12 12C12.5523 12 13 12.4477 13 13C13 13.5523 12.5523 14 12 14C11.4477 14 11 13.5523 11 13C11 12.4477 11.4477 12 12 12Z" fill="white" />
                    <path d="M12 16C12.5523 16 13 16.4477 13 17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17C11 16.4477 11.4477 16 12 16Z" fill="white" />
                </svg>
            </button>

            <div class="dropdown" id="dropdownMenu">
                <a href="#" class="update-option">Update</a>
            </div>
        </div>
    </div>
    <div class="details">
        <div class="task-details-container">
            <div class="title-task">
                <h2>Title <span>:</span></h2>
                <p><?= htmlspecialchars($task['title']) ?></p>
            </div>

            <div class="task-info">
                <h3>Description <span>:</span></h3>
                <p><?= htmlspecialchars($task['description']) ?></p>
            </div>

            <div class="task-info">
                <h3>Due Date <span>:</span></h3>
                <p><?= date('F j, Y', strtotime($task['date'])) ?></p>
            </div>

            <div class="task-info">
                <h3>Time <span>:</span></h3>
                <p><?= date('h:i A', strtotime($task['time'])) ?></p>
            </div>
            <div class="task-info">
                <h3>Stutas <span>:</span></h3>
                <p><?= htmlspecialchars($task['status']) ?></p>
            </div>


            <div class="buttons">
                <form method="POST" style="display:inline;">
                    <?php if ($task['status'] === 'complete'): ?>
                        <!-- If task is already complete, show the 'Completed' button -->
                        <button type="button" class="complete-btn" disabled>Completed</button>
                    <?php else: ?>
                        <!-- If task is not complete, show the 'Mark as Complete' button -->
                        <button type="submit" name="complete" class="complete-btn">Mark as Complete</button>
                    <?php endif; ?>
                </form>
                
                <form method="POST" style="display:inline;">
                    <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Are you sure you want to delete this task?');">
                        <img src="images/trash-icon.png" alt="">
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
            document.querySelector('.menu-button').addEventListener('click', function() {
                const dropdownMenu = document.getElementById('dropdownMenu');
                dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
            });

            // Close the dropdown if the user clicks outside of it
            window.onclick = function(event) {
                if (!event.target.matches('.menu-button') && !event.target.closest('.menu-container')) {
                    const dropdowns = document.querySelectorAll('.dropdown');
                    dropdowns.forEach(dropdown => {
                        dropdown.style.display = 'none';
                    });
                }
            }

            // Get the modal
            var modal = document.getElementById('updateTaskModal');

            // Get the button that opens the modal
            var updateButton = document.querySelector('.update-option');

            // Get the <span> element that closes the modal
            var closeBtn = document.querySelector('.close');

            // Open the modal when update is clicked
            updateButton.addEventListener('click', function(event) {
                event.preventDefault();
                modal.style.display = 'flex';
            });

            // Close the modal when the close button is clicked
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            // Close the modal when clicking outside the modal content
            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            });
    </script>
</body>


</html>

<?php
// Close the database connection
$conn->close();
?>