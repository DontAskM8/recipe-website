<?php
    include "./components/session.php";
    include "./db/connect_db.php";

    date_default_timezone_set('Asia/Kuala_Lumpur');

    if (!isset($_GET['post_id'])) {
        header("Location: community_engagement.php"); // Redirect if no post ID is provided
        exit();
    }

    $post_id = intval($_GET['post_id']);
    $user_id = $_SESSION["id"];

    // Fetch the post selected
    $sql = "SELECT p.id, p.title, p.content, u.username, 
                   (SELECT AVG(rating) FROM posts_ratings WHERE post_id = p.id) AS avg_rating 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.id = $post_id";
    $post_result = $conn->query($sql);
    $post = $post_result->fetch_assoc();

    if (!$post) { //if post not exist
        echo "<p class='text-center text-danger'>Post not found.</p>";
        exit();
    }

    // Check if the user has already rated this post
    $rating_sql = "SELECT rating FROM posts_ratings WHERE post_id = $post_id AND user_id = $user_id";
    $rating_result = $conn->query($rating_sql);
    $user_rating = $rating_result->fetch_assoc();

    // Handle new rating submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating'])) {
        $rating = intval($_POST['rating']);

        if ($rating >= 1 && $rating <= 5) {
            if ($user_rating) {//if user rating exist
                // update existing rating of the user
                $update_sql = "UPDATE posts_ratings SET rating = $rating WHERE post_id = $post_id AND user_id = $user_id";
                $conn->query($update_sql);
            } else {
                // insert new rating
                $insert_sql = "INSERT INTO posts_ratings (post_id, rating, user_id) VALUES ($post_id, $rating, $user_id)";
                $conn->query($insert_sql);
            }

            header("Location: community_engagement.php?post_id=$post_id");
            exit();
        } else {
            echo "<p class='text-center text-danger'>Invalid rating. Please enter a value between 1 and 5.</p>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./components/styling.php"; ?>
    <title>Rate Post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

    <?php include "./components/navbar.php"; ?>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Rate a Post</h1>
        
        <div class="card mb-4 shadow">
            <div class="card-body">
                <h2><?php echo $post['title']; ?></h2>
                <p><?php echo $post['content']; ?></p>
                <p>Posted by: <strong><?php echo $post['username']; ?></strong></p>
                <p>Average Rating: <?php echo number_format($post['avg_rating'], 1) ?: 'No ratings yet'; ?>/5</p>
            </div>
        </div>

        <!-- Rating Form -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5>Submit Your Rating:</h5>
                <form method="POST">
                    <label for="rating" class="form-label">Rate this post (1-5):</label>
                    <input type="number" name="rating" id="rating" min="1" max="5" required >
                    <button type="submit" class="btn btn-success">Submit Rating</button>
                </form>
            </div>
        </div>

        <a href="community_engagement.php" class="btn btn-secondary">Back to Discussions</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include "./components/footer.php"; ?>

</body>
</html>
