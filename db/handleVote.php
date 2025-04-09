<?php 
    require "connect_db.php";
    require "../components/session.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $competitionId = $_POST["competition_id"];
        $recipeId = $_POST["recipe_id"];
        $userId = $_SESSION["id"];
    }

    $stmtCheck = $conn->prepare("SELECT id FROM competition_votes WHERE user_id = ? AND competition_id = ?");
    $stmtCheck->bind_param("ii", $userId, $competitionId);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // User has already voted
        echo "<script>
            alert('You have already voted for this recipe in this competition.');
            window.location = '" . $_SERVER['HTTP_REFERER'] . "';
        </script>";
    } else {
        // Insert the vote
        $stmtInsert = $conn->prepare("INSERT INTO competition_votes (user_id, recipe_id, competition_id) VALUES (?, ?, ?)");
        $stmtInsert->bind_param("iii", $userId, $recipeId, $competitionId);
        
        if ($stmtInsert->execute()) {
            echo "<script>
                alert('Your vote has been accepted.');
                window.location = '" . $_SERVER['HTTP_REFERER'] . "';
            </script>";
        } else {
            echo "<script>
                alert('Error submitting vote.');
                window.location = '" . $_SERVER['HTTP_REFERER'] . "';
            </script>";
        }
        $stmtInsert->close();
    }

    $stmtCheck->close();
?>