<?php 
    require "../components/session.php";
    require "connect_db.php";

    if(!isset($_SESSION["role"]) || $_SESSION["role"] != "admin"){
        die("Unauthorized!"); // End the connection if is not admin;
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $id =  $_GET["id"];

        // Get current status
        $query = "SELECT isActive FROM competitions WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($currentStatus);
        $stmt->fetch();
        $stmt->close();

        // Toggle the status
        $newStatus = $currentStatus ? 0 : 1;

        // Update status in database
        $updateQuery = "UPDATE competitions SET isActive = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $newStatus, $id);
        $success = $updateStmt->execute();
        $updateStmt->close();

        echo json_encode(["success" => $success, "isActive" => $newStatus]);
        exit();
    }
?>