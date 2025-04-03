<?php
session_start();

// If the user is already logged in, we dont let them access login and register page and redirect to index.php
$require_login = ["add_recipe.php"];
$restricted_pages = ['login.php', 'register.php'];
$current_page = basename($_SERVER['PHP_SELF']); // Get the filename of the current script

if (isset($_SESSION["username"]) && in_array($current_page, $restricted_pages)) {
    header("Location: index.php");
    exit();
}

if(!isset($_SESSION["username"]) && in_array($current_page, $require_login)){
    header("Location: login.php");
    exit();
}
?>