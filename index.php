<?php include "./components/session.php"; ?>
<?php include "./db/connect_db.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "./components/styling.php"; ?>
    <title>Recipe Website</title>
</head>
<body>
    <?php include "./components/navbar.php"; ?>

    <div class="container mt-4">
        <?php
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $recipe_id = intval($_GET['id']);
            $query = "SELECT title, cuisine, description, ingredients, steps FROM recipes WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $recipe_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $recipe = $result->fetch_assoc();
                ?>
                <!-- Display Full Recipe -->
                <h2 class="text-center"><?php echo htmlspecialchars($recipe['title']); ?></h2>
                <p class="text-center"><strong>Cuisine:</strong> <?php echo htmlspecialchars($recipe['cuisine']); ?></p>
                <div class="card">
                    <div class="card-body">
                        <p><strong>Description:</strong></p>
                        <p class="mb-3"><?php echo nl2br(htmlspecialchars($recipe['description'])); ?></p>
                        
                        <p><strong>Ingredients:</strong></p>
                        <pre class="bg-light p-3"><?php echo nl2br(htmlspecialchars($recipe['ingredients'])); ?></pre>
                        
                        <p><strong>Preparation Steps:</strong></p>
                        <pre class="bg-light p-3"><?php echo nl2br(htmlspecialchars($recipe['steps'])); ?></pre>

                        <a href="index.php" class="btn btn-secondary mt-3">Back to Recipes</a>
                    </div>
                </div>
                <?php
            } else {
                echo "<p class='text-center text-danger'>Recipe not found.</p>";
            }
            $stmt->close();
        } else {
            ?>
            <!-- Show Latest Recipes -->
            <h2 class="text-center">Latest Recipes</h2>

            <!-- Search Form -->
            <form method="GET" action="index.php" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search by title or cuisine..." 
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>

            <div class="row">
                <?php
                // Search functionality
                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $search = "%" . $conn->real_escape_string($_GET['search']) . "%";
                    $query = "SELECT id, title, cuisine, description FROM recipes WHERE title LIKE ? OR cuisine LIKE ? ORDER BY id DESC";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ss", $search, $search);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } else {
                    // Default: Show latest recipes
                    $query = "SELECT id, title, cuisine, description FROM recipes ORDER BY id DESC LIMIT 6";
                    $result = $conn->query($query);
                }

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                                    <p><strong>Cuisine:</strong> <?php echo htmlspecialchars($row['cuisine']); ?></p>
                                    <p class="card-text">
                                        <?php echo nl2br(htmlspecialchars(substr($row['description'], 0, 50))) . '...'; ?>
                                    </p>
                                    <a href="index.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Recipe</a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p class='text-center text-muted'>No recipes found.</p>";
                }
                ?>
            </div>
            <?php
        }
        $conn->close();
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include "./components/footer.php"; ?>
</body>
</html>
