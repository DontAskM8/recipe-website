<?php
    include "./components/session.php";
    include "./db/connect_db.php";

    date_default_timezone_set('Asia/Kuala_Lumpur');

    if (!isset($_GET['post_id'])) {
        header("Location: community_engagement.php"); // Redirect if no post ID is provided
        exit();
    }

    $post_id = $_GET['post_id'];

    // Fetch the post selected
    $sql = "SELECT p.id, p.title, p.content, u.username 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.id = $post_id";
    $post_result = $conn->query($sql);
    $post = $post_result->fetch_assoc();

    if (!$post) { //if post not exist
        echo "<p class='text-center text-danger'>Post not found.</p>";
        exit();
    }

    // Handle new comment submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
        $comment = mysqli_real_escape_string($conn, $_POST['comment']);
        $user_id = $_SESSION["id"];

        $sql = "INSERT INTO posts_comments (post_id, comment, user_id) VALUES ($post_id, '$comment', $user_id)";
        if ($conn->query($sql)) {
            header("Location: community_comment.php?post_id=$post_id");
            exit();
        }
    }

    // Fetch all comments for the post
    $comment_sql = "SELECT c.comment, u.username, c.created_at 
                    FROM posts_comments c 
                    JOIN users u ON c.user_id = u.id 
                    WHERE c.post_id = $post_id 
                    ORDER BY c.created_at DESC";
    $comments = $conn->query($comment_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./components/styling.php"; ?>
    <title>Community Comments</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <?php include "./components/navbar.php"; ?>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Post Discussion</h1>
        
        <div class="card mb-4 shadow">
            <div class="card-body">
                <h2><?php echo $post['title']; ?></h2>
                <p><?php echo $post['content']; ?></p>
                <p>Posted by: <strong><?php echo $post['username']; ?></strong></p>
            </div>
        </div>

        <!-- List all Comments -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5>Comments:</h5>
                <?php if ($comments->num_rows > 0): ?>
                    <?php while ($comment = $comments->fetch_assoc()): ?>
                        <div class="card mb-2">
                            <div class="card-body">
                                <p><?php echo $comment['comment']; ?></p>
                                <small >- <?php echo $comment['username']; ?> on <?php echo date('d M Y, H:i', strtotime($comment['created_at'])); ?></small>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No comments yet....</p>
                <?php endif; ?>

                <!-- New Comment Form -->
                <form method="POST" class="mt-3">
                    <div class="mb-3">
                        <textarea class="form-control" name="comment" placeholder="Add a comment..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Comment</button>
                </form>
            </div>
        </div>

        <a href="community_engagement.php" class="btn btn-secondary">Back to Discussions</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include "./components/footer.php"; ?>

</body>
</html>
