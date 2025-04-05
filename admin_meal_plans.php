<?php
include "./components/session.php";
include "./db/connect_db.php";

// Check if user is logged in and is admin
if (!isset($_SESSION["id"]) || $_SESSION["role"] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $meal_id = isset($_POST['meal_id']) ? $conn->real_escape_string($_POST['meal_id']) : '';
    
    switch($_POST['action']) {
        case 'add':
            $meal_name = $conn->real_escape_string($_POST['meal_name']);
            $meal_date = $conn->real_escape_string($_POST['meal_date']);
            $meal_type = $conn->real_escape_string($_POST['meal_type']);
            $recipe_id = isset($_POST['recipe_id']) ? $conn->real_escape_string($_POST['recipe_id']) : NULL;
            $user_id = $conn->real_escape_string($_POST['user_id']);

            $sql = "INSERT INTO meal_plans (user_id, recipe_id, meal_name, meal_date, meal_type) 
                    VALUES ('$user_id', " . ($recipe_id ? "'$recipe_id'" : "NULL") . ", '$meal_name', '$meal_date', '$meal_type')";
            
            if ($conn->query($sql)) {
                $success_message = "Meal plan added successfully!";
            } else {
                $error_message = "Error: " . $conn->error;
            }
            break;

        case 'update':
            $meal_name = $conn->real_escape_string($_POST['meal_name']);
            $meal_date = $conn->real_escape_string($_POST['meal_date']);
            $meal_type = $conn->real_escape_string($_POST['meal_type']);
            $recipe_id = isset($_POST['recipe_id']) ? $conn->real_escape_string($_POST['recipe_id']) : NULL;
            $user_id = $conn->real_escape_string($_POST['user_id']);

            $sql = "UPDATE meal_plans 
                    SET meal_name = '$meal_name',
                        meal_date = '$meal_date',
                        meal_type = '$meal_type',
                        recipe_id = " . ($recipe_id ? "'$recipe_id'" : "NULL") . ",
                        user_id = '$user_id'
                    WHERE id = '$meal_id'";

            if ($conn->query($sql)) {
                $success_message = "Meal plan updated successfully!";
            } else {
                $error_message = "Error: " . $conn->error;
            }
            break;

        case 'delete':
            $sql = "DELETE FROM meal_plans WHERE id = '$meal_id'";
            if ($conn->query($sql)) {
                $success_message = "Meal plan deleted successfully!";
            } else {
                $error_message = "Error: " . $conn->error;
            }
            break;
    }
}

// Get meal plan for editing if edit_id is set
$edit_meal = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $conn->real_escape_string($_GET['edit_id']);
    $sql = "SELECT mp.*, u.username 
            FROM meal_plans mp 
            JOIN users u ON mp.user_id = u.id 
            WHERE mp.id = '$edit_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $edit_meal = $result->fetch_assoc();
    }
}

// Get filter parameters
$user_filter = isset($_GET['user_id']) ? $conn->real_escape_string($_GET['user_id']) : '';
$date_filter = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : '';
$type_filter = isset($_GET['meal_type']) ? $conn->real_escape_string($_GET['meal_type']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Meal Plans Overview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include "./components/navbar.php"; ?>
    
    <div class="container mt-5">
        <h2 class="text-center mb-4">Admin - Meal Plans Management</h2>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Add/Edit Meal Plan Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h4><?php echo $edit_meal ? 'Edit Meal Plan' : 'Add New Meal Plan'; ?></h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <input type="hidden" name="action" value="<?php echo $edit_meal ? 'update' : 'add'; ?>">
                    <?php if ($edit_meal): ?>
                        <input type="hidden" name="meal_id" value="<?php echo $edit_meal['id']; ?>">
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">User</label>
                            <select class="form-control" id="user_id" name="user_id" required>
                                <option value="">Select User</option>
                                <?php
                                $sql = "SELECT id, username FROM users";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    $selected = ($edit_meal && $edit_meal['user_id'] == $row['id']) ? 'selected' : '';
                                    echo "<option value='" . $row['id'] . "' $selected>" . 
                                         htmlspecialchars($row['username']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="meal_name" class="form-label">Meal Name</label>
                            <input type="text" class="form-control" id="meal_name" name="meal_name" 
                                   value="<?php echo $edit_meal ? htmlspecialchars($edit_meal['meal_name']) : ''; ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="meal_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="meal_date" name="meal_date" 
                                   value="<?php echo $edit_meal ? $edit_meal['meal_date'] : ''; ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="meal_type" class="form-label">Meal Type</label>
                            <select class="form-control" id="meal_type" name="meal_type" required>
                                <?php 
                                $meal_types = ['breakfast', 'lunch', 'dinner', 'snack'];
                                foreach ($meal_types as $type) {
                                    $selected = ($edit_meal && $edit_meal['meal_type'] == $type) ? 'selected' : '';
                                    echo "<option value='$type' $selected>" . ucfirst($type) . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="recipe_id" class="form-label">Recipe (Optional)</label>
                            <select class="form-control" id="recipe_id" name="recipe_id">
                                <option value="">No Recipe</option>
                                <?php
                                $sql = "SELECT id, title FROM recipes";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    $selected = ($edit_meal && $edit_meal['recipe_id'] == $row['id']) ? 'selected' : '';
                                    echo "<option value='" . $row['id'] . "' $selected>" . 
                                         htmlspecialchars($row['title']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $edit_meal ? 'Update Meal Plan' : 'Add Meal Plan'; ?>
                        </button>
                        <?php if ($edit_meal): ?>
                            <a href="admin_meal_plans.php" class="btn btn-secondary">Cancel Edit</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Filter Meal Plans</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="" class="row g-3">
                    <div class="col-md-3">
                        <label for="filter_user" class="form-label">User</label>
                        <select class="form-control" id="filter_user" name="user_id">
                            <option value="">All Users</option>
                            <?php
                            $sql = "SELECT id, username FROM users";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                $selected = ($user_filter == $row['id']) ? 'selected' : '';
                                echo "<option value='" . $row['id'] . "' $selected>" . 
                                     htmlspecialchars($row['username']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="filter_date" name="date" 
                               value="<?php echo $date_filter; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="filter_type" class="form-label">Meal Type</label>
                        <select class="form-control" id="filter_type" name="meal_type">
                            <option value="">All Types</option>
                            <?php
                            foreach ($meal_types as $type) {
                                $selected = ($type_filter == $type) ? 'selected' : '';
                                echo "<option value='$type' $selected>" . ucfirst($type) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="admin_meal_plans.php" class="btn btn-secondary ms-2">Clear Filters</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Display All Meal Plans -->
        <div class="card">
            <div class="card-header">
                <h4>All Meal Plans</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Date</th>
                                <th>Meal Type</th>
                                <th>Meal Name</th>
                                <th>Recipe</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Build the query with filters
                            $sql = "SELECT mp.*, u.username, r.title as recipe_title 
                                   FROM meal_plans mp 
                                   LEFT JOIN recipes r ON mp.recipe_id = r.id 
                                   JOIN users u ON mp.user_id = u.id 
                                   WHERE 1=1";
                            
                            if ($user_filter) {
                                $sql .= " AND mp.user_id = '$user_filter'";
                            }
                            if ($date_filter) {
                                $sql .= " AND mp.meal_date = '$date_filter'";
                            }
                            if ($type_filter) {
                                $sql .= " AND mp.meal_type = '$type_filter'";
                            }
                            
                            $sql .= " ORDER BY mp.meal_date ASC, u.username ASC";
                            
                            $result = $conn->query($sql);
                            
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                echo "<td>" . date('Y-m-d', strtotime($row['meal_date'])) . "</td>";
                                echo "<td>" . ucfirst($row['meal_type']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['meal_name']) . "</td>";
                                echo "<td>" . ($row['recipe_title'] ? htmlspecialchars($row['recipe_title']) : 'Custom Meal') . "</td>";
                                echo "<td>
                                        <a href='admin_meal_plans.php?edit_id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Edit</a>
                                        <form method='POST' action='' style='display: inline;'>
                                            <input type='hidden' name='action' value='delete'>
                                            <input type='hidden' name='meal_id' value='" . $row['id'] . "'>
                                            <button type='submit' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this meal plan?\")'>Delete</button>
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