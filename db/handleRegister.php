<?php
    require "connect_db.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];
        $username = $_POST["username"];
        $password = $_POST["password"];

        //Check if exists first
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? or username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        //Username or email already exissts
        if($result){
            header("Location: register.php?status=-1"); // exists
        }else{
            // Hash the password before storing
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (email, username, password_hash, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $email, $username, $password_hash);

            if ($stmt->execute()) {
                
                header("Location: register.php?status=1"); // Success
            } else {
                header("Location: register.php?status=0"); // Fail
            }
            $stmt->close();
        }
        
        $conn->close();
    }
?>