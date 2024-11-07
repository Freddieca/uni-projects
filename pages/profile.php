<?php
// Start the session
session_start();

// Include the database connection file
include('../includes/db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: login.html");
    exit();
}

// Fetch the logged-in user's details
$username = $_SESSION['username'];

// Initialize variables for bio and update status
$bio = '';

// Check if the form is submitted to update the bio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bio'])) {
    // Get the updated bio from the form
    $new_bio = trim($_POST['bio']);

    // Update the user's bio in the database
    $sql = "UPDATE users SET bio = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("ss", $new_bio, $username);
    $stmt->execute();

    if ($stmt->error) {
        die('Execute failed: ' . htmlspecialchars($stmt->error));
    }

    // Set a session variable to indicate bio was updated
    $_SESSION['bio_updated'] = true;
    $stmt->close();
}

// Fetch the current bio from the database
$sql = "SELECT bio FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($bio);
$stmt->fetch();

if ($stmt->error) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Social Media</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Initially hide the bio edit form */
        #bio-edit-form {
            display: none;
        }
        /* Edit Bio Button (with arrow) */
        #edit-bio-btn {
            background-color: navy;
            color: white;
            padding: 5px 10px; /* Adjust padding to fit text size */
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            display: inline-block; /* Ensure button only takes up the width it needs */
        }
        /* Arrow styling */
        #edit-bio-btn::after {
            content: " ▼"; /* Downward arrow */
            font-size: 10px;
        }
        /* Add some space when editing bio */
        #bio-edit-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        #bio-edit-form button {
            margin-top: 10px;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>Fred's Free Speech</h1>
        <h3>Profile</h3>
        <nav class="navbar">
            <a href="home.html">Home</a> |
            <a href="profile.php">Profile</a> |
            <a href="messages.html">Messages</a> |
            <a href="report.html">Report</a> |
            <a href="../pages/login.html">Logout</a>
        </nav>
    </header>
    <main>
        <h2>User Profile</h2>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
        <p><strong>Bio:</strong> <?php echo htmlspecialchars($bio); ?></p>

        <!-- Edit Bio Button (Dropdown) -->
        <button id="edit-bio-btn" onclick="toggleEditForm()">Edit Bio</button>

        <!-- Bio Edit Form (Initially hidden) -->
        <div id="bio-edit-form">
            <h3>Edit Bio</h3>
            <form action="profile.php" method="POST">
                <textarea name="bio" rows="4" cols="50"><?php echo htmlspecialchars($bio); ?></textarea><br>
                <button type="submit">Update Bio</button>
            </form>
        </div>

        <h3>Posts</h3>
        <div class="post">
            <p>This is a sample post.</p>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Social Media Platform</p>
    </footer>

    <script>
        // Function to toggle the visibility of the edit bio form
        function toggleEditForm() {
            var form = document.getElementById('bio-edit-form');
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }

        // Display alert message only if bio was updated successfully
        <?php if (isset($_SESSION['bio_updated']) && $_SESSION['bio_updated'] === true): ?>
            alert("Bio updated successfully!");
            // Clear the session flag after showing the alert
            <?php 
                unset($_SESSION['bio_updated']); 
            ?>
        <?php endif; ?>
    </script>
</body>
</html>
