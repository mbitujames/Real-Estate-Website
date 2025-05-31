<?php
require('./data/db.php');

// Check if property ID is set and is a valid integer
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Sanitize the ID to prevent SQL injection
    $property_id = mysqli_real_escape_string($conn, $_GET['id']);

    // SQL query to fetch user ID who owns the property
    $user_id_query = "SELECT user_id FROM Properties WHERE property_id = $property_id";
    $user_id_result = mysqli_query($conn, $user_id_query);

    if ($user_id_result) {
        $user_id_row = mysqli_fetch_assoc($user_id_result);
        $user_id = $user_id_row['user_id'];

        // SQL query to delete related records in the payments table
        $delete_payments_query = "DELETE FROM payments WHERE property_id = $property_id";
        mysqli_query($conn, $delete_payments_query);

        // SQL query to delete the property
        $query = "DELETE FROM Properties WHERE property_id = $property_id";

        // Execute the query
        if (mysqli_query($conn, $query)) {
            // Property deleted successfully
            $message = "Property deleted successfully.";
            $message_class = "success";
            header("Refresh: 3; url=admin_panel.php");

            // Insert a record into the activities table
            $activity_description = "Property with ID " . $property_id . " deleted";
            $insert_query = "INSERT INTO activities (user_id, activity_description) VALUES ('$user_id', '$activity_description')";
            mysqli_query($conn, $insert_query); // Execute the insert query
        } else {
            // Error in deleting property
            $message = "Error deleting property: " . mysqli_error($conn);
            $message_class = "error";
        }
    } else {
        // Error in fetching user ID
        $message = "Error fetching user ID: " . mysqli_error($conn);
        $message_class = "error";
    }
} else {
    // Invalid property ID
    $message = "Invalid property ID.";
    $message_class = "error";
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Property</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f0f7ff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Delete Property</h2>
        <div class="message <?php echo $message_class; ?>">
            <?php echo $message; ?>
        </div>
    </div>
</body>
</html>
