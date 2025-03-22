<?php 
    include "./components/session.php"; 
    include "./db/handleRegister.php";

    if (isset($_GET["status"])) {
        if ($_GET["status"] == "-1") {
            $message = "<div class='alert alert-danger'>The username/email is already in use!</div>";
        } elseif ($_GET["status"] == "1") {
            $message = "<div class='alert alert-success'>Registration successful!</div>";
        } elseif ($_GET["status"] == "0") {
            $message = "<div class='alert alert-danger'>Registration error!</div>";
        }else {
            $message = "<div class='alert alert-danger'>Unknown error!</div>";
        }
    }
?>

<!DOCTYPE HTML>
<html>
    <head>
        <?php include "./components/styling.php" ?>
        <style>
            body {
                background-color: #f8f9fa;
            }
            .register-container {
                max-width: 400px;
                margin: 80px auto;
            }
            .card {
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            }
    </style>
    </head>
    <body>
        <?php include "./components/navbar.php" ?>
        <!-- Basic Login Form -->
        <div class="container">
            <div class="register-container">
                <div class="card">
                    <h4 class="text-center">Register</h4>
                    <?php if(isset($message)) echo $message; ?>
                    <form action="#" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                    <div class="text-center mt-3">
                        <small>Already have an account? <a href="login.php">Login</a></small>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

