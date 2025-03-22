<?php 
    include "./components/session.php";
    include "./db/connect_db.php";
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./components/styling.php" ?>
    <title>Cooking Competition</title>
</head>
<body>
    <?php include "./components/navbar.php" ?>
    
    <div class="container mt-5">
        <h2 class="text-center">Cooking Competition</h2>
        <!-- Join competition -->
        <form action="competition_submit.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="competition_id" class="form-label">Select the competition</label>
                <select class="form-control" id="recipe" name="competition_id" required>
                    <option value="">-- Select a competition --</option>
                    <?php 
                        // Fetch active competitions
                        $stmt_competitions = $conn->prepare("SELECT id, name FROM competitions WHERE isActive = TRUE");
                        $stmt_competitions->execute();
                        $competitions = $stmt_competitions->get_result();

                        while ($row = $competitions->fetch_assoc()): 
                    ?>
                        <option value="<?= htmlspecialchars($row['id']) ?>">
                            <?= htmlspecialchars($row['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="recipe_id" class="form-label">Select your recipe</label>
                <select class="form-control" id="recipe" name="recipe_id" required>
                    <option value="">-- Select a Recipe --</option>
                    <?php 
                        $username = $_SESSION["username"];

                        // Fetch recipes by the logged-in user
                        $stmt = $conn->prepare("SELECT id, name FROM recipes WHERE username = ?");
                        $stmt->bind_param("s", $username);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        while ($row = $result->fetch_assoc()): 
                    ?>
                        <option value="<?= htmlspecialchars($row['id']) ?>">
                            <?= htmlspecialchars($row['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="recipe_description" class="form-label">Description</label>
                <textarea class="form-control" id="recipe_description" name="recipe_description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="recipe_image" class="form-label">Upload Image</label>
                <input type="file" class="form-control" id="recipe_image" name="recipe_image" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Recipe</button>
        </form>
    </div>
    
    <div class="container mt-5">
        <h2 class="text-center">Competition Entries</h2>
        <div class="row">
            <?php
            $conn = new mysqli("localhost", "root", "", "recipes_db");
            if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
            
            $sql = "SELECT id, recipe_title, recipe_description, recipe_image FROM competition_entries ORDER BY id DESC";
            $result = $conn->query($sql);
            
            while ($row = $result->fetch_assoc()) {
                echo "<div class='col-md-4'>";
                echo "<div class='card mb-3'>";
                echo "<img src='uploads/" . $row["recipe_image"] . "' class='card-img-top' alt='Recipe Image'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . $row["recipe_title"] . "</h5>";
                echo "<p class='card-text'>" . $row["recipe_description"] . "</p>";
                echo "<a href='vote.php?id=" . $row["id"] . "' class='btn btn-success'>Vote</a>";
                echo "</div></div></div>";
            }
            $conn->close();
            ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php include "./components/footer.php"; ?>
</body>
</html>
