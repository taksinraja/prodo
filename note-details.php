<?php
// Start session to access `uid`
session_start();
// Include the database connection file
include 'db_connect.php';

// Example user ID; replace this with the actual session user ID after implementing user login
$uid = $_SESSION['uid'] ?? null;

if (!$uid) {
    echo "<script>alert('Please log in first.');</script>";
    exit;
}

// Check if the note ID is provided in the URL
if (isset($_GET['id'])) {
    $note_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Fetch the note details from the database for the specified user
    $sql = "SELECT * FROM notes WHERE id = $note_id AND uid = $uid";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $note = mysqli_fetch_assoc($result);
    } else {
        echo "Note not found or access denied.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}

// Handle the update form submission
if (isset($_POST['update_note'])) {
    $updated_title = mysqli_real_escape_string($conn, $_POST['title']);
    $updated_description = mysqli_real_escape_string($conn, $_POST['description']);

    $sql = "UPDATE notes SET title='$updated_title', description='$updated_description' WHERE id=$note_id AND uid=$uid";
    
    if (mysqli_query($conn, $sql)) {
        // echo "<script>alert('Note updated successfully.');</script>";
        // Refresh note data after update
        $result = mysqli_query($conn, "SELECT * FROM notes WHERE id = $note_id AND uid = $uid");
        $note = mysqli_fetch_assoc($result);
    } else {
        echo "Error updating note: " . mysqli_error($conn);
    }
}

// Handle the delete form submission
if (isset($_POST['delete_note'])) {
    $sql = "DELETE FROM notes WHERE id=$note_id AND uid=$uid";

    if (mysqli_query($conn, $sql)) {
        // Redirect to the main notes page after deletion
        header("Location: notes.php");
        exit;
    } else {
        echo "Error deleting note: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note Details</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="images/logo.png" type="image/png">
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
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
            margin-bottom: 10px;
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
            color: #ffffff;
            margin: 0;
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
            backdrop-filter: blur(4px); /* Background blur */
            justify-content: center;
            align-items: end; /* Align to the bottom */
            padding: 0;
            margin: 0;
            margin-bottom: 10px;
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
            gap: 14px;
            position: relative;
            padding-bottom: 30px;
            animation: slide-up 0.4s ease-in-out forwards; /* Slide-up animation */
        }

        /* Slide-up animation */
        @keyframes slide-up {
            from {
                transform: translateY(100%);
            }
            to {
                transform: translateY(0);
            }
        }

        /* Modal header with close button */
        .modal-header {
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
            font-size: 28px;
            cursor: pointer;
            padding: 5px;
            border: none;
            background: transparent;
        }

        /* Form inside the modal */
        .modal form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        /* Form input fields */
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
            outline: none;
            resize: none;
        }
        textarea {
            height: 100px;
        }

        /* Update button */
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

        /* Date and time container (if needed) */
        .date-time-container {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 100%;
            align-items: center;
            gap: 10px;
        }

        /* details  */
        .note-details {
            display: flex;
            flex-direction: column;
            margin-top: 50px;
            padding: 10px;
        }
        .note-details-container{
            border-radius: 12px;
            height: auto; /* Remove fixed height, let content adjust it */
            display: flex;
            flex-direction: column;
            align-items: start;
            justify-content: start;
            gap: 20px;
        }

        .note-details-container .title-note {
            color: #ffffff;
            border-bottom: 2px solid #252525;
            padding-bottom: 20px;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .title-note h2{
            color: #ffffff;
            font-size: 30px;
            font-weight: 400;
            flex-basis: 30%;
            display: flex;
            align-items: center;
            justify-content: space-between;

        }

        .title-note p{
            color: #ffffff;
            font-size: 30px;
            font-weight: 400;
            flex-basis: 70%;
        }

        .description {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .note-info{
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

        .note-info h3{
            color: #9e9e9e;
            flex-basis: 30%;
            font-size: 16px;
            font-weight: 400;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .note-info p{
            color: #ffffff;
            flex-basis: 70%;
            margin-top: 2px;
        }

        /* Buttons */
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

        .btn {
            display: inline-block;
            padding: 10px 15px;
            margin: 10px 0;
            text-decoration: none;
            border-radius: 5px;
            background-color: #6c757d;
            color: white;
            border: none;
        }

        .btn-update {
            background-color: #3084FE;
        }

        .btn-delete {
            background-color: red;
        }

        .btn-back {
            background-color: #17a2b8;
        }

    </style>
</head>
<body>

    <div class="header">
        <a href="notes.php">
            <button class="back-button">
                <!-- back icon svg from favicon -->
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 19L9 13L15 7" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </a>
        <h1>Note Details</h1>
        <button class="menu-button">
            <svg width="35" height="35" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 8C12.5523 8 13 8.44772 13 9C13 9.55228 12.5523 10 12 10C11.4477 10 11 9.55228 11 9C11 8.44772 11.4477 8 12 8Z" fill="white" />
                <path d="M12 12C12.5523 12 13 12.4477 13 13C13 13.5523 12.5523 14 12 14C11.4477 14 11 13.5523 11 13C11 12.4477 11.4477 12 12 12Z" fill="white" />
                <path d="M12 16C12.5523 16 13 16.4477 13 17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17C11 16.4477 11.4477 16 12 16Z" fill="white" />
            </svg>
        </button>
    </div>
    <div class="note-details">
        <div class="note-details-container">
            <div class="title-note">
                <h2>Title <span>:</span></h2>
                <p><?php echo htmlspecialchars($note['title']); ?></p>
            </div>

            <div class="note-info">
                <h3>Description <span>:</span></h3>
                <p><?php echo htmlspecialchars($note['description']); ?></p>
            </div>

            <div class="note-info">
                <h3>Created at <span>:</span></h3>
                <p><?php echo htmlspecialchars($note['created_at']); ?></p>
            </div>
        </div>


        <!-- Buttons for update and delete -->
        <div class="buttons">
            <button class="btn btn-update" id="openUpdateModal">Update Note</button>
            <form method="POST" action="" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this note?');">
                <button type="submit" name="delete_note" class="btn btn-delete">Delete Note</button>
            </form>
        </div>
    </div>

    <!-- Modal for updating the note -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Update Note</h2>
                <span class="close" id="closeUpdateModal">&times;</span>
            </div>

            <form method="POST" action="">
                <div>
                    <label for="title">Title:</label>
                    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($note['title']); ?>" required>
                </div>

                <div>
                    <label for="description">Description:</label>
                    <textarea name="description" id="description" required><?php echo htmlspecialchars($note['description']); ?></textarea>
                </div>
                
                <input type="submit" name="update_note" value="Update Note" class="btn btn-update">
            </form>
        </div>
    </div>

    <!-- JavaScript for modal functionality -->
    <script>
        var modal = document.getElementById("updateModal");
        var openModalBtn = document.getElementById("openUpdateModal");
        var closeModalBtn = document.getElementById("closeUpdateModal");

        // Open the modal when the "Update Note" button is clicked
        openModalBtn.onclick = function() {
            modal.style.display = "flex";
        }

        // Close the modal when the "X" is clicked
        closeModalBtn.onclick = function() {
            modal.style.display = "none";
        }

        // Close the modal when clicking outside of the modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>