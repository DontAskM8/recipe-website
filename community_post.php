<?php
include "./components/session.php";
include "./db/connect_db.php";

// Handle New Post Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION["id"];
    $sql = "INSERT INTO posts (title, content, user_id) VALUES ('$title', '$content', $user_id)";
    if ($conn->query($sql)) {
        header("Location: community_engagement.php");
    }
}
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

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-lg p-4 w-75">
            <h1 class="text-center mb-4">Community Post</h1>

            <form method="POST">
                <div class="mb-3">
                    <input type="text" name="title" class="form-control form-control-lg" placeholder="Title" required>
                </div>

                <div class="mb-3">
                    <textarea name="content" class="form-control form-control-lg" rows="5" placeholder="Share your thoughts" required></textarea>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success">Post</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include "./components/footer.php"; ?>
</body>
</html>