<?php
session_start();
include '../includes/db_connection.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $bio = $_POST['bio'];

    $stmt = $conn->prepare("UPDATE users SET bio = ? WHERE id = ?");
    $stmt->bind_param("si", $bio, $user_id);
    $stmt->execute();

    header("Location: ../pages/profile.php");
}
?>
