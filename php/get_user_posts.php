<?php
// Start session and include the database connection
session_start();
include('../includes/db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "Please log in to see your posts.";
    exit();
}

// Get the logged-in user's username
$username = $_SESSION['username'];

// Fetch the user_id from the database
$sql = "SELECT user_id FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Fetch posts made by this user
$sql = "SELECT title, description, image, created_at FROM posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($title, $description, $image, $created_at);

if ($stmt->num_rows > 0) {
    while ($stmt->fetch()) {
        echo "<div class='post'>";
        echo "<h4>" . htmlspecialchars($title) . "</h4>";
        echo "<p>" . htmlspecialchars($description) . "</p>";
        if ($image) {
            echo "<img src='../uploads/" . htmlspecialchars($image) . "' alt='Post Image' style='width: 100px; height: 100px;'>";
        }
        echo "<p><small>Posted on: " . htmlspecialchars($created_at) . "</small></p>";
        echo "</div>";
    }
} else {
    echo "No posts available.";
}

$stmt->close();
$conn->close();
?>
