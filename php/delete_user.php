<?php
include '../includes/db_connection.php';

if ($_POST['admin_action'] === 'delete_user') {
    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    echo "User deleted successfully.";
}
?>
