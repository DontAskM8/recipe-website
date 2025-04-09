<?php 
    include "./components/session.php";
    include "./db/connect_db.php";

    date_default_timezone_set('Asia/Kuala_Lumpur'); //Set to malaysia time zone
 ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include "./components/styling.php" ?>
        <title>Cooking Competitions</title>
    </head>
    <body>
        <?php include 'components/navbar.php'; ?>
        
        <div class="container mt-5">
            <h2 class="text-center mb-5">Cooking Competitions</h2>
            
            
            <!-- only admins can create competitions -->
            <?php if(isset($_SESSION["role"]) && $_SESSION["role"] == "admin"): ?>
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCompetitionModal">Create Competition</button>
                </div>

                <script>
                    function toggleCompetition(button, id) {
                        fetch("./db/toggleCompetition.php?id=" + id, {
                            method: "POST"
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                button.textContent = data.isActive ? "Disable" : "Enable";
                                button.classList.toggle("btn-warning");
                                button.classList.toggle("btn-success");
                            } else {
                                alert("Failed to update status!");
                            }
                        })
                        .catch(error => console.error("Error:", error));
                    }

                    function deleteCompetition(id){
                        if(!confirm("Are you sure u want to delete competition id " + id)) return; //Stop the execution of the function if cancel
                        
                        fetch("./db/deleteCompetition.php?id=" + id, {
                            method: "DELETE"
                        })
                        .then(response => response.json())
                        .then(data => {
                            document.querySelector(`[data-competition-id="${id}"]`).remove();
                        })
                        .catch(error => console.error("Error:", error));
                    }
                </script>
            <?php endif; ?>

            <!-- If not logged in, let the user know how to join the competition -->
            <?php if(!isset($_SESSION["username"])): ?>
                <div class="alert alert-info">You must login to enter the competition!</div>
            <?php endif; ?>

            <div class="d-flex justify-content-center flex-wrap gap-3 w-100">
                <?php
                 // Fetch active competitions
                $stmt = $conn->prepare("SELECT id, name, description, start_time, end_time, isActive FROM competitions ORDER BY end_time DESC");
                $stmt->execute();
                $result = $stmt->get_result();
                $competitions = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();

                $userId = $_SESSION["id"] ?? null;
                $userEntries = [];

                if ($userId) {
                    // Get all competitions the user has joined
                    $entryStmt = $conn->prepare("SELECT competition_id FROM competition_entries WHERE user_id = ?");
                    $entryStmt->bind_param("i", $userId);
                    $entryStmt->execute();
                    $entryResult = $entryStmt->get_result();

                    while ($entryRow = $entryResult->fetch_assoc()) {
                        $userEntries[$entryRow["competition_id"]] = true; // Mark joined competitions
                    }

                    $entryStmt->close();
                }

                if (count($competitions) > 0) {
                    foreach ($competitions as $row) {
                        $competitionId = $row["id"];
                        $isUserJoined = isset($userEntries[$competitionId]); // Check if the user has joined
                ?>
                        <div class="col-md-4 mb-3" data-competition-id="<?php echo $competitionId ?>">
                            <div class="card h-100 shadow">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                    <p class="card-text text-muted"><?php echo htmlspecialchars($row['description']); ?></p>
                                    <p class="card-text">
                                        <strong>Start:</strong> <?php echo (new DateTime($row['start_time']))->format('d/m/Y H:i'); ?><br>
                                        <strong>End:</strong> <?php echo (new DateTime($row['end_time']))->format('d/m/Y H:i'); ?>
                                    </p>
                                    <div class="d-flex align-items-center flex-wrap">
                                        <a class="btn btn-primary me-2" href="view_competition.php?id=<?php echo $competitionId ?>">View</a>
                                        <?php 
                                            if(isset($_SESSION["username"])):
                                                $startTime = new DateTime($row["start_time"]);
                                                $endTime = new DateTime($row["end_time"]);
                                                $currentTime = new DateTime();
                
                                                $isCompetitionError = ($currentTime < $startTime) || ($currentTime > $endTime);
                                        ?>
                                            <a data-bs-toggle="modal" data-bs-target="#joinCompetitionModal" onclick="updateJoinCompetition(<?php echo $competitionId ?>, '<?php echo $row["name"] ?>')" class="btn btn-primary me-2 <?php if(!$row['isActive'] || $isCompetitionError) echo "disabled opacity-50" ?>">
                                                <?php 
                                                    if($currentTime < $startTime){
                                                        echo "Not started";
                                                    } else if($currentTime > $endTime){
                                                        echo "Ended";
                                                    } else {
                                                        echo $isUserJoined ? "Update Entry" : "Join";
                                                    }
                                                ?>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <!-- Admin Controls: Enable/Disable/Delete -->
                                        <?php if(isset($_SESSION["role"]) && $_SESSION["role"] == "admin"): ?>
                                            <button onClick="toggleCompetition(this, '<?php echo $competitionId ?>')" class="btn btn-<?php echo $row['isActive'] ? "warning" : "success" ?>">
                                                <?php echo $row['isActive'] ? "Disable" : "Enable" ?>
                                            </button>
                                            <button onClick="deleteCompetition('<?php echo $competitionId ?>')" class="btn btn-danger ms-auto">
                                                Delete
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php
                    }
                    } else {
                        echo "<p class='text-muted'>There are currently no competitions, check back later!</p>";
                    }
                ?>
            </div>
        </div>

        
        <!-- Create Competition Modal, only generate if its admin -->
        <?php if(isset($_SESSION["role"]) && $_SESSION["role"] == "admin"): ?>
            <div class="modal fade" id="createCompetitionModal" tabindex="-1" aria-labelledby="createCompetitionModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="createCompetitionModalLabel">Create a New Competition</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" id="addCompetitionForm">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea type="text" class="form-control" id="description" name="description" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="start_time" class="form-label">Start Date</label>
                                    <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                                </div>
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">End Date</label>
                                    <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
                                </div>
                                <div id="addCompetitionMessage">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Create Competition</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                document.getElementById("addCompetitionForm").addEventListener("submit", function(event) {
                    event.preventDefault(); // Prevent default form submission

                    let formData = new FormData(this);

                    fetch("db/handleAddCompetition.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        let messageDiv = document.getElementById("addCompetitionMessage");
                        if (data.status === "success") {
                            messageDiv.innerHTML = `<div class="alert alert-success">Competition created!</div>`;
                            setTimeout(() => location.reload(), 500)//Refresh the page after success.
                        } else {
                            messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                        }
                    })
                    .catch(error => console.error("Error:", error));
                });
            </script>
        <?php endif; ?>

        <div class="modal fade" id="joinCompetitionModal" tabindex="-1" aria-labelledby="joinCompetitionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="joinCompetitionModalLabel">Join Competition</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" id="joinCompetitionForm">
                        <div class="modal-body">
                            <div class="mb-3">
                                You are joining the competition - <span id="joinCompetitionName"></span>
                            </div>
                            <div class="mb-3">
                                <label for="recipe" class="form-label">Select your recipe</label>
                                <select name="recipe_id" class="form-select" required>
                                    <option value="-1">Quit competition</option>
                                    <?php
                                        $query = "SELECT id, title, cuisine, description FROM recipes WHERE username = ? ORDER BY id DESC";
                                        $stmt = $conn->prepare($query);
                                        $stmt->bind_param("s", $_SESSION["username"]);
                                        $stmt->execute();
                                        $result = $stmt->get_result();

                                        if ($result && $result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option value=".$row["id"].">".$row["title"]."</option>";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <input class="d-none" name="competition_id" id="competitionId" />
                            </div>
                            <div id="joinCompetitionMessage">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Join Competition</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.getElementById("joinCompetitionForm").addEventListener("submit", function(event) {
                event.preventDefault(); // Prevent default form submission
                
                let formData = new FormData(this);

                fetch("db/handleJoinCompetition.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    let messageDiv = document.getElementById("joinCompetitionMessage");
                    if (data.status === "success") {
                        messageDiv.innerHTML = `<div class="alert alert-success">${data.message}!</div>`;
                        setTimeout(() => location.reload(), 500)//Refresh the page after success.
                    } else {
                        messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                    }
                })
                .catch(error => console.error("Error:", error));
            });

            function updateJoinCompetition(id, name){
                document.getElementById("joinCompetitionName").innerText = name;
                document.getElementById("competitionId").value = id;
            }
        </script>
        

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <?php include "./components/footer.php"; ?>
    </body>
</html>