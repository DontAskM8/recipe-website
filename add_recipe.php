<?php include "./components/session.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./components/styling.php" ?>
    <title>Add Recipe</title>
</head>
<body>
    <?php include "./components/navbar.php" ?>
    <div class="container mt-5">
        <h2 class="text-center">Add a New Recipe</h2>
        <form action="add_recipe.php" method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Recipe Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="cuisine" class="form-label">Cuisine Type</label>
                <input type="text" class="form-control" id="cuisine" name="cuisine" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="ingredients" class="form-label">Ingredients</label>
                <textarea class="form-control" id="ingredients" name="ingredients" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label for="steps" class="form-label">Preparation Steps</label>
                <textarea class="form-control" id="steps" name="steps" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Recipe</button>
        </form>
        
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $conn = new mysqli("localhost", "root", "", "recipes_db");
            if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
            
            $title = $conn->real_escape_string($_POST['title']);
            $cuisine = $conn->real_escape_string($_POST['cuisine']);
            $description = $conn->real_escape_string($_POST['description']);
            $ingredients = $conn->real_escape_string($_POST['ingredients']);
            $steps = $conn->real_escape_string($_POST['steps']);
            
            $sql = "INSERT INTO recipes (title, cuisine, description, ingredients, steps) VALUES ('$title', '$cuisine', '$description', '$ingredients', '$steps')";
            
            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Recipe added successfully!'); window.location.href='add_recipe.php';</script>";
            } else {
                echo "<div class='alert alert-danger mt-3'>Error: " . $conn->error . "</div>";
            }
            
            $conn->close();
        }
        ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include "./components/footer.php"; ?>
</body>
</html>
