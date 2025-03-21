<?php
    include "./components/session.php";

    session_destroy();

    // Redirect to login or home page
    header("Location: login.php");
    exit();
?>