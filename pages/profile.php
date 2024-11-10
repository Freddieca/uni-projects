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
    <title>Profile - Fred's Free Speech</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Initially hide the bio edit form */
        #bio-edit-form, #new-post-form {
            display: none;
        }
        #edit-bio-btn, #create-post-btn {
            background-color: navy;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }
        /* Arrow styling */
        #edit-bio-btn::after, #create-post-btn::after {
            content: " ▼";
            font-size: 10px;
        }
        /* Styling for textarea and buttons */
        #bio-edit-form textarea, #new-post-form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        #bio-edit-form button, #new-post-form button {
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
            <a href="home.php">Home</a> |
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
                <textarea name="bio" rows="4"><?php echo htmlspecialchars($bio); ?></textarea><br>
                <button type="submit">Update Bio</button>
            </form>
        </div>

        <!-- Create New Post Section -->
        <button id="create-post-btn" onclick="togglePostForm()">Create New Post</button>
        <div id="new-post-form">
            <h3>Create New Post</h3>
            <form id="submit-post-form" enctype="multipart/form-data">
                <label for="post-title">Title:</label>
                <input type="text" id="post-title" name="title" required><br>

                <label for="post-description">Description:</label>
                <textarea id="post-description" name="description" rows="4" required></textarea><br>

                <label for="post-image">Image:</label>
                <input type="file" id="post-image" name="image" accept="image/*"><br>

                <button type="button" onclick="submitPost()">Submit Post</button>
            </form>
        </div>

        <!-- Display User's Posts -->
        <h3>Your Posts</h3>
        <div id="user-posts"></div>
    </main>
    <footer>
        <p>&copy; 2024 Fred's Free Speech Platform</p>
    </footer>

    <script>
        // Function to toggle the visibility of the edit bio form
        function toggleEditForm() {
            var form = document.getElementById('bio-edit-form');
            form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
        }

        // Function to toggle the visibility of the new post form
        function togglePostForm() {
            var form = document.getElementById('new-post-form');
            form.style.display = form.style.display === 'none' || form.style.display === '' ? 'block' : 'none';
        }

        // AJAX request to submit the new post
        function submitPost() {
            var formData = new FormData(document.getElementById("submit-post-form"));

            fetch("post_processes.php", {
                method: "POST",
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Post created successfully!");
                    document.getElementById("submit-post-form").reset();
                    togglePostForm();
                    loadUserPosts(); // Reload posts
                } else {
                    alert("Failed to create post: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while creating the post.");
            });
        }

        // Load user's posts via AJAX
        function loadUserPosts() {
            fetch("get_user_posts.php")
                .then(response => response.text())
                .then(data => {
                    document.getElementById("user-posts").innerHTML = data;
                })
                .catch(error => {
                    console.error("Error:", error);
                });
        }

        // Load posts when page loads
        document.addEventListener("DOMContentLoaded", loadUserPosts);

        // Display alert if bio was updated
        <?php if (isset($_SESSION['bio_updated']) && $_SESSION['bio_updated'] === true): ?>
            alert("Bio updated successfully!");
            <?php unset($_SESSION['bio_updated']); ?>
        <?php endif; ?>
    </script>
</body>
</html>
