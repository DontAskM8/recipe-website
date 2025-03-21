<?php
    require "connect_db.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];
        $password = $_POST["password"];

        $stmt = $conn->prepare("SELECT username, password_hash, role FROM users WHERE email = ? or username = ?");
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result && password_verify($password, $result["password_hash"])) {
            $_SESSION["username"] = $result["username"];
            $_SESSION["role"] = $result["role"];
            header("Location: login.php?status=1"); // Success
        } else {
            header("Location: login.php?status=0"); // fail
        }


        $stmt->close();
        $conn->close();
    }
?>