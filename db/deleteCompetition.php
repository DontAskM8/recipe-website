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
        
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            // Check if it's a foreign key constraint failure
            if ($conn->errno == 1451) {
                echo json_encode(["success" => false, "error" => "Cannot delete competition: Entries or votes exist"]);
            } else {
                echo json_encode(["success" => false, "error" => $conn->error]);
            }
        }
    }
?>