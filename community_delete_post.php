<?php
include "./components/session.php";
include "./db/connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION["id"];

    $check = "SELECT * FROM posts WHERE id = $post_id AND user_id = $user_id";
    $check_result = $conn->query($check);

    if ($check_result->num_rows === 1) {
        // Delete posts comments & ratings first
        $conn->query("DELETE FROM posts_comments WHERE post_id = $post_id");
        $conn->query("DELETE FROM posts_ratings WHERE post_id = $post_id");

        // Delete post
        $conn->query("DELETE FROM posts WHERE id = $post_id");

        header("Location: community_engagement.php");
        exit();
    } else {
        echo "Post not found.";
    }
} else {
    echo "Invalid request.";
}
?>