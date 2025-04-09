<?php
    require "../components/session.php";
    require "connect_db.php";


    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $competitionId = $_POST["competition_id"] ?? null;
        $recipeId = $_POST["recipe_id"] ?? null;
        $userId = $_SESSION["id"];

        //Missing form details
        if (!$competitionId || !$recipeId) {
            echo json_encode(["status" => "error", "message" => "Missing competition or recipe ID"]);
            exit();
        }

        //check if the competition is active
        $checkComp = $conn->prepare("SELECT * FROM competitions WHERE id = ? AND isActive = 1");
        $checkComp->bind_param("i", $competitionId);
        $checkComp->execute();
        $compResult = $checkComp->get_result();
        if($compResult->num_rows == 0){
            echo json_encode(["status" => "error", "message" => "The competition is no longer active"]);
            exit();
        }
        $checkComp->close();

        // Check if the user has already joined this competition
        $stmt = $conn->prepare("SELECT id FROM competition_entries WHERE competition_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $competitionId, $userId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // User already entered → Update their recipe_id
            if($recipeId == -1){ //uuserwant to exit
                $stmt->close();
                $deleteStmt = $conn->prepare("DELETE FROM competition_entries WHERE competition_id = ? AND user_id = ?");
                $deleteStmt->bind_param("ii", $competitionId, $userId);
            
                if ($deleteStmt->execute()) {
                    echo json_encode(["status" => "success", "message" => "Exited competition"]);
                } else {
                    echo json_encode(["status" => "error", "message" => $conn->error]);
                }
            
                $deleteStmt->close();
            }else{
                $stmt->close();
                $updateStmt = $conn->prepare("UPDATE competition_entries SET recipe_id = ? WHERE competition_id = ? AND user_id = ?");
                $updateStmt->bind_param("iii", $recipeId, $competitionId, $userId);
    
                if ($updateStmt->execute()) {
                    echo json_encode(["status" => "success", "message" => "Entry updated!"]);
                } else {
                    echo json_encode(["status" => "error", "message" => $conn->error]);
                }
    
                $updateStmt->close();
            }
        } else {
            // User has not entered → Insert a new entry
            $stmt->close();
            $insertStmt = $conn->prepare("INSERT INTO competition_entries (competition_id, user_id, recipe_id) VALUES (?, ?, ?)");
            $insertStmt->bind_param("iii", $competitionId, $userId, $recipeId);

            if ($insertStmt->execute()) {
                echo json_encode(["status" => "success", "message" => "Joined competition!"]);
            } else {
                echo json_encode(["status" => "error", "message" => $conn->error]);
            }

            $insertStmt->close();
        }

        $conn->close();
    }
?>