<header class="bg-dark text-white p-3 text-center">
    <h1>My Recipe Website</h1>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <?php if(isset($_SESSION["username"])): ?> 
                        <li class="nav-item"><a class="nav-link" href="add_recipe.php">Add Recipe</a></li>
                    <?php endif ?>
                    <li class="nav-item"><a class="nav-link" href="meal_planning.php">Meal Planning</a></li>
                    <li class="nav-item"><a class="nav-link" href="community_engagement.php">Community</a></li>
                    <li class="nav-item"><a class="nav-link" href="competition.php">Cooking Competition</a></li>
                    <li class="nav-item rounded-1"><a class="nav-link" 
                        style="<?php echo isset($_SESSION["username"]) ? "color: red" : "color: skyblue"; ?>"
                        href="<?php
                            if(isset($_SESSION["username"])){
                                echo "logout.php";
                            }else{
                                echo "login.php";
                            }
                        ?>">
                        <?php 
                            if(!isset($_SESSION["username"])){
                                echo "Login";
                            }else{
                                // echo $_SESSION["username"];
                                echo "Logout";
                            }
                        ?>
                    </a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</header>