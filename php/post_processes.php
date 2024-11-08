<?php
session_start();
include '../includes/db_connection.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $content);
    $stmt->execute();

    header("Location: ../pages/home.php");
} else {
    echo "You must be logged in to post.";
}
?>
