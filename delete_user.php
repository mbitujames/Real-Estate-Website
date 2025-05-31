<?php
require('./data/db.php');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert activity log for user deletion
        $activity_description = "User with ID $user_id has been deleted.";
        $sql_insert_activity = "INSERT INTO activities (user_id, activity_description) VALUES (?, ?)";
        $stmt_activity = $conn->prepare($sql_insert_activity);
        $stmt_activity->bind_param("is", $user_id, $activity_description);
        $stmt_activity->execute();
        
        // Delete related records from activities
        $sql_delete_activities = "DELETE FROM activities WHERE user_id = ?";
        $stmt_activities = $conn->prepare($sql_delete_activities);
        $stmt_activities->bind_param("i", $user_id);
        $stmt_activities->execute();

        // Delete user
        $sql_delete_user = "DELETE FROM Users WHERE user_id = ?";
        $stmt_user = $conn->prepare($sql_delete_user);
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();

        // Commit transaction
        $conn->commit();

        $result = "User deleted successfully!";
        header("Refresh: 3; url=admin_panel.php");
    } catch (mysqli_sql_exception $exception) {
        // Rollback transaction
        $conn->rollback();
        $result = "Error deleting user: " . $exception->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f7ff;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        p {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-size: 18px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Delete User</h2>
    <p><?php echo isset($result) ? $result : 'No user specified for deletion.'; ?></p>
</body>
</html>
