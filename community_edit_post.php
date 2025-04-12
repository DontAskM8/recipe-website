<?php
include "./components/session.php";
include "./db/connect_db.php";

if (!isset($_GET['post_id'])) {
    header("Location: community_engagement.php"); // Redirect if no post ID is provided
    exit();
}

$post_id = intval($_GET['post_id']);
$user_id = $_SESSION["id"];

// Get post information
$sql = "SELECT * FROM posts WHERE id = $post_id AND user_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows !== 1) {
    echo "<p>Post not found</p>";
    exit();
}

$post = $result->fetch_assoc();

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_title = mysqli_real_escape_string($conn, $_POST['title']);
    $new_content = mysqli_real_escape_string($conn, $_POST['content']);

    $update = "UPDATE posts SET title = '$new_title', content = '$new_content' WHERE id = $post_id AND user_id = $user_id";
    if ($conn->query($update)) {
        header("Location: community_engagement.php");
        exit();
    } else {
        echo "Failed to update post.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include "./components/navbar.php"; ?>

    <div class="container mt-4">
        <h2>Edit Your Post</h2>
        <form method="POST">
            <div class="mb-2">
                <label class="form-label">Title:</label>
                <input type="text" name="title" class="form-control" value="<?php echo $post['title']; ?>" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Content:</label>
                <textarea name="content" rows="5" class="form-control" required><?php echo $post['content']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Post</button>
        </form>
    </div>

    <?php include "./components/footer.php"; ?>
</body>
</html>