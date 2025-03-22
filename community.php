<?php include "./components/session.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./components/styling.php" ?>
    <title>Community Page</title>
</head>
<body>
    <?php include "./components/navbar.php" ?>
    
    <div class="container mt-5">
        <h2 class="text-center">Community Discussions</h2>
        <form action="community_submit.php" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Your Name</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="comment" class="form-label">Your Comment</label>
                <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Post Comment</button>
        </form>
    </div>
    
    <div class="container mt-5">
        <h2 class="text-center">Community Posts</h2>
        <div class="list-group">
            <?php
            $conn = new mysqli("localhost", "root", "", "recipes_db");
            if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
            
            $sql = "SELECT username, comment, created_at FROM community_posts ORDER BY created_at DESC";
            $result = $conn->query($sql);
            
            while ($row = $result->fetch_assoc()) {
                echo "<div class='list-group-item'>";
                echo "<h5 class='mb-1'>" . htmlspecialchars($row["username"]) . "</h5>";
                echo "<p class='mb-1'>" . htmlspecialchars($row["comment"]) . "</p>";
                echo "<small class='text-muted'>Posted on " . $row["created_at"] . "</small>";
                echo "</div>";
            }
            $conn->close();
            ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include "./components/footer.php"; ?>
</body>
</html>
