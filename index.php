<?php include "./components/session.php"; ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include "./components/styling.php" ?>
        <title>Recipe Website</title>
    </head>
    <body>
        <?php include "./components/navbar.php" ?>
        <?php include "./db/connect_db.php" ?>
        <div class="container mt-4">
            <h2 class="text-center">Latest Recipes</h2>
            <div class="row">
                <?php
                
                echo "<p class='text-center'>No recipes found.</p>";

                ?>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <?php include "./components/footer.php"; ?>
    </body>
</html>
