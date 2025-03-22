<?php
    require "../components/session.php";
    require "connect_db.php";


    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
            echo json_encode(["status" => "error", "message" => "Unauthorized access"]);
            exit();
        }

        $name = htmlspecialchars($_POST["name"]);
        $description = htmlspecialchars($_POST["description"]);
        $starttime = $_POST["start_time"];
        $endtime = $_POST["end_time"];

        $stmt = $conn->prepare("INSERT INTO competitions (name, description, start_time, end_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $description, $starttime, $endtime);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
    }
?>