<?php 
    require "../components/session.php";
    require "connect_db.php";

    if(!isset($_SESSION["role"]) || $_SESSION["role"] != "admin"){
        die("Unauthorized!"); // End the connection if is not admin;
    }

    if($_SERVER["REQUEST_METHOD"] == "DELETE"){
        $id =  $_GET["id"];

        // Delete the competition
        $query = "DELETE FROM competitions WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();

        echo json_encode(["success" => $success]);
        exit();
    }
?>