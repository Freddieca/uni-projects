<?php
// Start the session and include the database connection
session_start();
include('../includes/db_connection.php');

// Fetch all posts from the database, ordered by created_at (most recent first)
$sql = "SELECT post_id, user_id, title, image, description, created_at FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Fred's Free Speech</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <h1>Fred's Free Speech</h1>
        <nav class="navbar">
            <a href="home.php">Home</a> |
            <a href="profile.php">Profile</a> |
            <a href="messages.html">Messages</a> |
            <a href="report.html">Report</a> |
            <a href="../pages/login.html">Logout</a>
        </nav>
    </header>
    <main>
        <h2>Feed</h2>

        <?php
        // Check if there are any posts to display
        if ($result->num_rows > 0) {
            // Loop through each post
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="post">
                    <p><strong><?php echo htmlspecialchars($row['title']); ?></strong></p>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <?php if (!empty($row['image'])): ?>
                        <img src="/path/to/uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Post Image">
                    <?php endif; ?>
                    <p><em>Posted on: <?php echo htmlspecialchars($row['created_at']); ?></em></p>
                    <button>Like</button>
                    <button>Comment</button>
                </div>
                <?php
            }
        } else {
            echo "<p>No posts to display.</p>";
        }
        // Close the database connection
        $conn->close();
        ?>
    </main>
    <footer>
        <p>&copy; 2024 Fred's Free Speech Platform</p>
    </footer>
</body>
</html>
