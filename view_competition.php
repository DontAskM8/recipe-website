<?php
include "./components/session.php";
include "./db/connect_db.php";

date_default_timezone_set('Asia/Kuala_Lumpur'); //Set to malaysia time zone

// Check if the competition ID is passed in the URL
if (isset($_GET['id'])) {
    $competitionId = $_GET['id'];

    // Fetch competition details from the database
    $stmt = $conn->prepare("SELECT * FROM competitions WHERE id = ?");
    $stmt->bind_param("i", $competitionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $competition = $result->fetch_assoc();

    // Check if the competition exists
    if (!$competition) {
        echo "Competition not found!";
        exit;
    }

    // Fetch entries for the competition
    $isJoined = false; // Default to not joined
    $recipeName = ""; // Default to empty
    if (isset($_SESSION['id'])) {
        $userId = $_SESSION['id']; // Assuming user_id is stored in session
        // Fetch the user's entry for the competition along with the recipe name
        $stmtJoined = $conn->prepare("SELECT ce.recipe_id, r.title AS recipe_name
                                      FROM competition_entries ce
                                      JOIN recipes r ON ce.recipe_id = r.id
                                      WHERE ce.competition_id = ? AND ce.user_id = ?");
        $stmtJoined->bind_param("ii", $competitionId, $userId);
        $stmtJoined->execute();
        $entryResult = $stmtJoined->get_result();

        if ($entryResult->num_rows > 0) {
            $isJoined = true; // User has already joined
            $entry = $entryResult->fetch_assoc();
            $recipeName = $entry['recipe_name']; // Fetch the associated recipe name
        }
    }

    // Fetch recipes for users in the competition
    $recipesStmt = $conn->prepare("SELECT * FROM recipes WHERE username = ?");
    $recipesStmt->bind_param("s", $_SESSION["username"]);
    $recipesStmt->execute();
    $recipesResult = $recipesStmt->get_result();

} else {
    echo "Invalid competition ID!";
    exit;
}
?>

<?php
// Assuming the previous code is already loaded, e.g., competition info, etc.

// Fetch all competition entries, join with recipes and users
$stmtEntries = $conn->prepare("SELECT 
    ce.recipe_id,
    u.id AS user_id,
    r.title AS recipe_name, 
    u.username AS user_name, 
    COUNT(cv.id) AS votes,
    EXISTS (
        SELECT 1 FROM competition_votes cv2 
        WHERE cv2.recipe_id = ce.recipe_id 
        AND cv2.user_id = ?
    ) AS hasVoted
FROM 
    competition_entries ce
JOIN 
    recipes r ON ce.recipe_id = r.id
JOIN 
    users u ON ce.user_id = u.id
LEFT JOIN 
    competition_votes cv ON cv.recipe_id = r.id
WHERE 
    ce.competition_id = ?
GROUP BY 
    ce.recipe_id, r.title, u.username, u.id
ORDER BY 
    votes DESC;");
$stmtEntries->bind_param("ii", $_SESSION["id"], $competitionId);
$stmtEntries->execute();
$entriesResult = $stmtEntries->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Competition - <?php echo htmlspecialchars($competition['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <?php include 'components/navbar.php'; ?>

    <!-- Competition Detail Section -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <?php if($isJoined): ?>
                    <p class="alert alert-info">
                        You have joined the competition with your recipe - <?php echo $recipeName ?>
                    </p>
                <?php endif ?>
                <h3 class="text-primary"><?php echo htmlspecialchars($competition['name']); ?></h3>
                <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($competition['description'])); ?></p>
                <p><strong>Start Time:</strong> <?php echo (new DateTime($competition['start_time']))->format('d/m/Y H:i'); ?></p>
                <p><strong>End Time:</strong> <?php echo (new DateTime($competition['end_time']))->format('d/m/Y H:i'); ?></p>
                <p><strong>Status:</strong> <?php echo $competition['isActive'] ? "Active" : "Closed"; ?></p>
                <p><strong>Participant count:</strong> <?php echo $entriesResult->num_rows ?></p>

                <?php if (isset($_SESSION["username"])): ?>
                    <div class="d-flex justify-content-between mt-3">
                        <?php
                            $startTime = new DateTime($competition['start_time']);
                            $endTime = new DateTime($competition['end_time']);
                            $currentTime = new DateTime();
                            $isCompetitionError = ($currentTime < $startTime) || ($currentTime > $endTime);
                        ?>
                        <a data-bs-toggle="modal" data-bs-target="#joinCompetitionModal" 
                           onclick="updateJoinCompetition(<?php echo $competition['id']; ?>)" 
                           class="btn btn-primary <?php if (!$competition['isActive'] || $isCompetitionError) echo 'disabled opacity-50'; ?>">
                            <?php 
                                if ($currentTime < $startTime) {
                                    echo "Not started";
                                } else if ($currentTime > $endTime) {
                                    echo "Ended";
                                } else {
                                    echo $isJoined ? "Update Entry" : "Join";
                                }
                            ?>
                        </a>
                    </div>
                <?php else: ?>
                    <p class="text-muted mt-3">Please login to join the competition.</p>
                <?php endif; ?>
            </div>
        </div>

<!-- Add this in the HTML part of your competition page where you want to display the recipe entries -->
<div class="container mt-4">
    <h3>Entries for the Competition</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Recipe Name</th>
                <th>Submitted By</th>
                <th>Votes</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($entry = $entriesResult->fetch_assoc()): ?>
                <tr>
                    <td><a href="./index.php?id=<?php echo $entry["recipe_id"] ?>"><?php echo htmlspecialchars($entry['recipe_name']); ?></a></td>
                    <td><?php echo htmlspecialchars($entry['user_name']); ?></td>
                    <td><?php echo $entry['votes']; ?></td>
                    <td>
                    <?php if (isset($_SESSION['id'])): ?>
                        <?php if ($_SESSION['id'] == $entry['user_id']): ?>
                            <!-- User owns this recipe → Cannot vote -->
                            <span class="text-muted">You can't vote for your own recipe</span>
                        <?php else: ?>
                            <?php if ($entry["hasVoted"]): ?>
                                <button class="btn btn-secondary" disabled>Voted</button>
                            <?php else: ?>
                                <form method="POST" action="db/handleVote.php">
                                    <input type="hidden" name="recipe_id" value="<?php echo $entry['recipe_id']; ?>">
                                    <input type="hidden" name="competition_id" value="<?php echo $competitionId; ?>">
                                    <button type="submit" class="btn btn-success">Vote</button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="text-muted">Log in to vote</span>
                    <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

        <!-- Modal for Joining or Updating Competition -->
        <div class="modal fade" id="joinCompetitionModal" tabindex="-1" aria-labelledby="joinCompetitionModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="joinCompetitionModalLabel">Join Competition</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="joinCompetitionForm">
                            <div class="mb-3">
                                <label for="recipeSelect" class="form-label">Select Recipe</label>
                                <select class="form-select" id="recipeSelect" name="recipe_id">
                                    <option value="-1">Quit competition</option>
                                    <?php while ($recipe = $recipesResult->fetch_assoc()): ?>
                                        <option value="<?php echo $recipe['id']; ?>"><?php echo htmlspecialchars($recipe['title']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <input type="hidden" id="competitionId" name="competition_id" value="<?php echo $competition['id']; ?>">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                            <div class="mb-3" id="joinCompetitionMessage">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <?php include 'components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateJoinCompetition(competitionId) {
            document.getElementById("competitionId").value = competitionId;
        }

        // Form submission to join competition
        document.getElementById("joinCompetitionForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);
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
            .catch(error => {
                alert("An error occurred. Please try again.");
            });
        });
    </script>
</body>
</html>