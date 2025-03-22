<?php 
    include "./components/session.php";
    include "./db/connect_db.php";
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
                    <button class="btn btn-primary">Create Competition</button>
                </div>
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

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 shadow">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                    <p class="card-text text-muted"><?php echo htmlspecialchars($row['description']); ?></p>
                                    <p class="card-text">
                                        <strong>Start:</strong> <?php echo $row['start_time']; ?><br>
                                        <strong>End:</strong> <?php echo $row['end_time']; ?>
                                    </p>
                                    <div class="d-flex align-items-center">
                                        <!-- Dont show join competition button if not logged in -->
                                        <?php if(isset($_SESSION["username"])): ?>
                                            <a href="competition_details.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-primary me-2 <?php if(!$row['isActive']) echo "disabled opacity-50" ?>" >Join Competition</a>
                                        <?php endif ?>
                                        
                                        <!-- Competition buttons can only be seen by admin -->
                                        <?php if(isset($_SESSION["role"]) && $_SESSION["role"] == "admin"): ?>
                                            <a href="competition_details.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-<?php echo $row['isActive'] ? "warning" : "success" ?>" ><?php echo $row['isActive'] ? "Disable" : "Enable" ?></a>
                                            <a href="competition_details.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-danger ms-auto">Delete</a>
                                        <?php endif ?>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }else {
                    echo "<p class='text-muted'>No active competitions.</p>";
                }
                $stmt->close();
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
        

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>