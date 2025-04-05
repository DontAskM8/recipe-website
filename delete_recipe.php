<?php
include "./components/session.php";
include "./db/connect_db.php";

if (!isset($_SESSION["username"])) {
    die("Unauthorized access.");
}

$username = $_SESSION["username"];
$admin_username = "admin"; // Set your actual admin username

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $recipe_id = intval($_GET['id']);

    if ($username === $admin_username) {
        // Admin can delete any recipe
        $query = "DELETE FROM recipes WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $recipe_id);
    } else {
        // Regular users can only delete their own recipes
        $query = "DELETE FROM recipes WHERE id = ? AND username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $recipe_id, $username);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Recipe deleted successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error deleting recipe.'); window.location.href='index.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request.'); window.location.href='index.php';</script>";
}

$conn->close();
?>
