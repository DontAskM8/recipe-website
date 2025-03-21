<?php 
    
    try {
        $conn = new mysqli("localhost", "root", "", "recipes_db");
        if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
    }catch(mysqli_sql_exception $err){
        echo "<script type=\"text/javascript\">alert(\"Failed to connect to database: ". $err->getMessage() ."\");</script>";
    }
?>