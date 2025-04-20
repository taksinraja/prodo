<?php
// Start session to access `uid`
session_start();

// Include the database connection file
include 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['uid'])) {
    echo "<script>alert('Please log in first.');</script>";
    exit;
}

// Retrieve `uid` from the session
$uid = $_SESSION['uid'];

// Handle form submission for adding a new note
if (isset($_POST['submit'])) {
    // Escape user inputs for security
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Insert new note into the database, including the `uid`
    $sql = "INSERT INTO notes (title, description, uid) VALUES ('$title', '$description', '$uid')";
    if (mysqli_query($conn, $sql)) {
        // Redirect to the same page to prevent form resubmission
        header("Location: notes.php");
        exit(); // Always call exit after redirection
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Fetch all notes from the database for the logged-in user only
$sql = "SELECT * FROM notes WHERE uid = '$uid' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <style>
        /* Overall layout enhancements */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #141414;
            color: #333;
            /* margin: 0; */
            font-family: 'Encode Sans', sans-serif;
            /* background-color: var(--background); */
            color: var(--text-primary);
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

        /* Card styling */
        .card {
            border-radius: 10px;
            padding: 20px;
            box-sizing: border-box;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            width: 100%;
        }

        /* Add card button */
        .add-card {
            background-color: #333333;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 36px;
            color: white;
            cursor: pointer;
            /* aspect-ratio: 1 / 1; */
            transition: background-color 0.3s ease;
        }

        .add-card:hover {
            background-color: #555;
        }

        /* Note card with subtle hover effect */
        /* Note card with alternating background colors */
        .note-card {
            color: #000;
            display: flex;
            flex-direction: column;
            justify-content: start;
            align-items: start;
            font-size: 16px;
            cursor: pointer;
            border-left: 5px solid #3168E0;
        }

        .note-card h2 {
            font-size: 18px;
            margin: 0;
            color: #333;
        }
        .note-card-link {
            display: block;
            text-decoration: none;
            width: 100%;
        }
        .note-card p {
            font-size: 14px;
            margin: 8px 0 0;
            width: 100%;
        }

        /* Hover animation for note cards */
        .note-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            border-left: 5px solid #007BFF;
        }

        /* Responsive grid for wide cards */
        .wide-card {
            grid-column: span 2;
        }

        /* Color variations for cards */
        .purple-card {
            background-color: #c8a2c8;
        }

        /* Mobile-first media query to ensure responsive design */
        @media (max-width: 768px) {
            .wide-card {
                grid-column: span 1;
            }
        }


        /* Modal container (hidden by default) */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            -webkit-backdrop-filter: blur(4px); /* For Safari */
            backdrop-filter: blur(4px); /* For other browsers */
            justify-content: center;
            align-items: flex-end;
            padding: 0;
            margin: 0;
            margin-bottom: 10px;
        }

        /* Modal header */
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 99%;
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

        .date-time-container {
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
            width: auto;
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
        <h1>Notes</h1>
        <button class="menu-button">
            <svg width="35" height="35" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 8C12.5523 8 13 8.44772 13 9C13 9.55228 12.5523 10 12 10C11.4477 10 11 9.55228 11 9C11 8.44772 11.4477 8 12 8Z" fill="white" />
                <path d="M12 12C12.5523 12 13 12.4477 13 13C13 13.5523 12.5523 14 12 14C11.4477 14 11 13.5523 11 13C11 12.4477 11.4477 12 12 12Z" fill="white" />
                <path d="M12 16C12.5523 16 13 16.4477 13 17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17C11 16.4477 11.4477 16 12 16Z" fill="white" />
            </svg>
        </button>
    </div>

    <!-- <h1>Notes</h1> -->
    <div class="grid-container">
        <!-- Add card button that opens the modal -->
        <div class="card add-card" id="addNoteBtn">+</div>

        <!-- Display notes from the database -->
        <?php
        if ($result->num_rows > 0) {
            $counter = 1; // Initialize a counter to alternate background colors
            while ($note = $result->fetch_assoc()) {
                $className = "";
                if (strlen($note['description']) > 50) {
                    $className = 'wide-card';
                }

                // Determine background color based on the counter (odd or even)
                $backgroundColor = ($counter % 2 == 0) ? '#d7be95' : '#C8A2D7';

                // Make the note-card clickable by wrapping it in a link
                echo '<a href="note-details.php?id=' . $note['id'] . '" class="note-card-link">';
                echo '<div class="card note-card ' . $className . '" style="background-color: ' . $backgroundColor . ';">';
                echo '<h2>' . htmlspecialchars($note['title']) . '</h2>';
                echo '<p>' . htmlspecialchars($note['description']) . '</p>';
                echo '</div>';
                echo '</a>';

                $counter++; // Increment the counter after each iteration
            }
        } else {
            echo "<p>No notes found.</p>";
        }
        ?>
    </div>

    <!-- Modal for adding a new note -->
    <div class="modal" id="noteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add a New Note</h2>
                <span class="close-modal" id="closeModal">&times;</span>
            </div>
            <form action="notes.php" method="POST">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>

                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required></textarea>

                <button type="submit" name="submit" class="update-btn">Add Note</button>
            </form>
        </div>
    </div>

    <script>
        // Get modal elements
        var modal = document.getElementById("noteModal");
        var addNoteBtn = document.getElementById("addNoteBtn");
        var closeModal = document.getElementById("closeModal");

        // Open modal when the add note button is clicked
        addNoteBtn.onclick = function() {
            modal.style.display = "flex";
            document.body.style.overflow = "hidden";  // Disable scrolling
        }

        // Close modal when the close button is clicked
        closeModal.onclick = function() {
            modal.style.display = "none";
            document.body.style.overflow = "auto";  // Enable scrolling
        }

        // Close modal when clicking outside of the modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                document.body.style.overflow = "auto";  // Enable scrolling
            }
        }
    </script>
</body>
</html>