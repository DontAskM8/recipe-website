<?php
    include "./components/session.php";
    include "./db/connect_db.php";

    date_default_timezone_set('Asia/Kuala_Lumpur');

//Select all post into query
$sql = "SELECT p.id, p.title, p.content, p.user_id, u.username, 
               (SELECT AVG(rating) FROM posts_ratings WHERE post_id = p.id) AS avg_rating 
        FROM posts p JOIN users u ON p.user_id = u.id";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html>
<head>
    <?php include "./components/styling.php" ?>
    <title>Community Engagement</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include "./components/navbar.php" ?>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Community Discussions</h1>
        <div class="text-center mb-4">
            <a href='community_post.php' class="btn btn-primary">Create New Post</a>
        </div>

        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card mb-4 shadow">
                <div class="card-body">
                    <h2><?php echo $row['title']; ?></h2>
                    <p><?php echo $row['content']; ?></p>
                    <p>Posted by: <strong><?php echo $row['username']; ?></strong></p>
                    <p>Average Rating: <strong><?php echo number_format($row['avg_rating'], 1) ?: 'No ratings yet'; ?>/5</strong></p>

                    <div>
                        <a href='community_comment.php?post_id=<?php echo $row['id']; ?>' class="btn btn-info">View Comments</a>
                        <a href='community_rate.php?post_id=<?php echo $row['id']; ?>' class="btn btn-success">Rate This Post</a>
                    
                        <?php if ($_SESSION["id"] == $row["user_id"]): ?>

                            <a href='community_edit_post.php?post_id=<?php echo $row["id"]; ?>' class="btn btn-warning">Edit</a>
                                <form action="community_delete_post.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                    <input type="hidden" name="post_id" value="<?php echo $row["id"]; ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                        <?php endif; ?>
                    
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include "./components/footer.php"; ?>
</body>
</html>








