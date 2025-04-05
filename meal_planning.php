<?php 
include "./components/session.php";
include "./db/connect_db.php";

// Check if user is logged in
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

// Handle form submission for adding/editing meal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $meal_name = $conn->real_escape_string($_POST['meal_name']);
        $meal_date = $conn->real_escape_string($_POST['meal_date']);
        $meal_type = $conn->real_escape_string($_POST['meal_type']);
        $recipe_id = isset($_POST['recipe_id']) ? $conn->real_escape_string($_POST['recipe_id']) : NULL;
        $user_id = $_SESSION["id"];

        $sql = "INSERT INTO meal_plans (user_id, recipe_id, meal_name, meal_date, meal_type) 
                VALUES ('$user_id', " . ($recipe_id ? "'$recipe_id'" : "NULL") . ", '$meal_name', '$meal_date', '$meal_type')";
        
        if ($conn->query($sql)) {
            $success_message = "Meal added successfully!";
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }
    // Handle update action
    elseif ($_POST['action'] == 'update' && isset($_POST['meal_id'])) {
        $meal_id = $conn->real_escape_string($_POST['meal_id']);
        $meal_name = $conn->real_escape_string($_POST['meal_name']);
        $meal_date = $conn->real_escape_string($_POST['meal_date']);
        $meal_type = $conn->real_escape_string($_POST['meal_type']);
        $recipe_id = isset($_POST['recipe_id']) ? $conn->real_escape_string($_POST['recipe_id']) : NULL;
        $user_id = $_SESSION["id"];

        $sql = "UPDATE meal_plans 
                SET meal_name = '$meal_name', 
                    meal_date = '$meal_date', 
                    meal_type = '$meal_type', 
                    recipe_id = " . ($recipe_id ? "'$recipe_id'" : "NULL") . " 
                WHERE id = '$meal_id' AND user_id = '$user_id'";

        if ($conn->query($sql)) {
            $success_message = "Meal updated successfully!";
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }
    // Handle delete action
    elseif ($_POST['action'] == 'delete' && isset($_POST['meal_id'])) {
        $meal_id = $conn->real_escape_string($_POST['meal_id']);
        $user_id = $_SESSION["id"];
        
        $sql = "DELETE FROM meal_plans WHERE id = '$meal_id' AND user_id = '$user_id'";
        if ($conn->query($sql)) {
            $success_message = "Meal deleted successfully!";
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }
}

// Get meal plan for editing if edit_id is set
$edit_meal = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $conn->real_escape_string($_GET['edit_id']);
    $user_id = $_SESSION["id"];
    $sql = "SELECT * FROM meal_plans WHERE id = '$edit_id' AND user_id = '$user_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $edit_meal = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Planning</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include "./components/navbar.php"; ?>
    
    <div class="container mt-5">
        <h2 class="text-center mb-4">My Meal Plan</h2>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Add/Edit Meal Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h4><?php echo $edit_meal ? 'Edit Meal' : 'Add New Meal'; ?></h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="<?php echo $edit_meal ? 'update' : 'add'; ?>">
                    <?php if ($edit_meal): ?>
                        <input type="hidden" name="meal_id" value="<?php echo $edit_meal['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="meal_name" class="form-label">Meal Name</label>
                        <input type="text" class="form-control" id="meal_name" name="meal_name" 
                               value="<?php echo $edit_meal ? htmlspecialchars($edit_meal['meal_name']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="meal_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="meal_date" name="meal_date" 
                               value="<?php echo $edit_meal ? $edit_meal['meal_date'] : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="meal_type" class="form-label">Meal Type</label>
                        <select class="form-control" id="meal_type" name="meal_type" required>
                            <?php 
                            $meal_types = ['breakfast', 'lunch', 'dinner', 'snack'];
                            foreach ($meal_types as $type): 
                                $selected = ($edit_meal && $edit_meal['meal_type'] == $type) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $type; ?>" <?php echo $selected; ?>>
                                    <?php echo ucfirst($type); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="recipe_id" class="form-label">Select Recipe (Optional)</label>
                        <select class="form-control" id="recipe_id" name="recipe_id">
                            <option value="">-- No Recipe --</option>
                            <?php
                            $sql = "SELECT id, title FROM recipes";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()):
                                $selected = ($edit_meal && $edit_meal['recipe_id'] == $row['id']) ? 'selected' : '';
                            ?>
                                <option value="<?php echo $row['id']; ?>" <?php echo $selected; ?>>
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <?php echo $edit_meal ? 'Update Meal' : 'Add Meal'; ?>
                    </button>
                    <?php if ($edit_meal): ?>
                        <a href="meal_planning.php" class="btn btn-secondary">Cancel Edit</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Display Meal Plans -->
        <div class="card">
            <div class="card-header">
                <h4>My Planned Meals</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Meal Type</th>
                                <th>Meal Name</th>
                                <th>Recipe</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $user_id = $_SESSION["id"];
                            $sql = "SELECT mp.*, r.title as recipe_title 
                                   FROM meal_plans mp 
                                   LEFT JOIN recipes r ON mp.recipe_id = r.id 
                                   WHERE mp.user_id = '$user_id' 
                                   ORDER BY mp.meal_date ASC";
                            $result = $conn->query($sql);
                            
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . date('Y-m-d', strtotime($row['meal_date'])) . "</td>";
                                echo "<td>" . ucfirst($row['meal_type']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['meal_name']) . "</td>";
                                echo "<td>" . ($row['recipe_title'] ? htmlspecialchars($row['recipe_title']) : 'Custom Meal') . "</td>";
                                echo "<td>
                                        <a href='meal_planning.php?edit_id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Edit</a>
                                        <form method='POST' action='' style='display: inline;'>
                                            <input type='hidden' name='action' value='delete'>
                                            <input type='hidden' name='meal_id' value='" . $row['id'] . "'>
                                            <button type='submit' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include "./components/footer.php"; ?>
</body>
</html>
