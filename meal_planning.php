<?php include "./components/session.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./components/styling.php" ?>
    <title>Meal Planning</title>
</head>
<body>
    <?php include "./components/navbar.php" ?>
    <div class="container mt-5">
        <h2 class="text-center">Meal Planning</h2>
        <form action="meal_planning.php" method="post">
            <div class="mb-3">
                <label for="date" class="form-label">Select Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="mb-3">
                <label for="meal" class="form-label">Meal Name</label>
                <input type="text" class="form-control" id="meal" name="meal" required>
            </div>
            <div class="mb-3">
                <label for="recipe_id" class="form-label">Select Recipe</label>
                <select class="form-control" id="recipe_id" name="recipe_id" required>
                    <option value="">-- Select Recipe --</option>
                    <?php
                    $conn = new mysqli("localhost", "root", "", "recipes_db");
                    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
                    
                    $sql = "SELECT id, title FROM recipes";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["id"] . "'>" . $row["title"] . "</option>";
                    }
                    $conn->close();
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add to Plan</button>
        </form>
        
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $conn = new mysqli("localhost", "root", "", "recipes_db");
            if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
            
            $date = $conn->real_escape_string($_POST['date']);
            $meal = $conn->real_escape_string($_POST['meal']);
            $recipe_id = $conn->real_escape_string($_POST['recipe_id']);
            
            $sql = "INSERT INTO meal_plans (date, meal, recipe_id) VALUES ('$date', '$meal', '$recipe_id')";
            
            if ($conn->query($sql) === TRUE) {
                echo "<div class='alert alert-success mt-3'>Meal added to plan successfully!</div>";
            } else {
                echo "<div class='alert alert-danger mt-3'>Error: " . $conn->error . "</div>";
            }
            
            $conn->close();
        }
        ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
