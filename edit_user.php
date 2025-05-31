<?php
require('./data/db.php');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch user details for pre-filling the form
    $sql_fetch_user = "SELECT * FROM Users WHERE user_id = ?";
    $stmt_fetch_user = $conn->prepare($sql_fetch_user);
    $stmt_fetch_user->bind_param("i", $user_id);
    $stmt_fetch_user->execute();
    $result_fetch_user = $stmt_fetch_user->get_result();
    $user = $result_fetch_user->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $full_name = $_POST['full_name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // Validation
        $errors = [];

        if (!preg_match("/^[A-Za-z\s]+$/", $full_name)) {
            $errors[] = "Full name should only contain letters and spaces.";
        }
        if (!preg_match("/^\d{10}$/", $phone)) {
            $errors[] = "Phone number should be 10 digits.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        if (empty($errors)) {
            // Start transaction
            $conn->begin_transaction();

            try {
                // Update user details
                $sql_update_user = "UPDATE users SET username = ?, full_name = ?, phone = ?, email = ?, role = ?, is_active = ? WHERE user_id = ?";
                $stmt_update_user = $conn->prepare($sql_update_user);
                $stmt_update_user->bind_param("ssssssi", $username, $full_name, $phone, $email, $role, $is_active, $user_id);
                $stmt_update_user->execute();

                // Insert activity record
                $activity_description = "Updated user information";
                $sql_insert_activity = "INSERT INTO activities (user_id, activity_description) VALUES (?, ?)";
                $stmt_insert_activity = $conn->prepare($sql_insert_activity);
                $stmt_insert_activity->bind_param("is", $user_id, $activity_description);
                $stmt_insert_activity->execute();

                // Commit transaction
                $conn->commit();

                echo "User updated successfully!";
                header("Refresh: 3; url=admin_panel.php");
            } catch (mysqli_sql_exception $exception) {
                // Rollback transaction
                $conn->rollback();
                echo "Error updating user: " . $exception->getMessage();
            }
        } else {
            foreach ($errors as $error) {
                echo "<p style='color: red;'>$error</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 20px;
}

h2 {
    text-align: center;
}

form {
    max-width: 600px;
    margin: 0 auto;
    background: #f0f7ff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

label {
    display: block;
    margin: 15px 0 5px;
    font-weight: bold;
}

input[type="text"],
input[type="email"],
input[type="checkbox"] {
    width: calc(100% - 22px);
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

input[type="submit"] {
    width: 100%;
    background-color: #fa5b3d;
    margin-top: 10px;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-align: center;
    transition: background-color 0.3s, color 0.3s;
}

input[type="submit"]:hover {
    background-color: white;
    color: black;
}
    </style>
</head>
<body>
    <h2>Edit User</h2>
    <form method="POST">
        <label>Username: <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>"></label><br>
        <label>Full Name: <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>"></label><br>
        <label>Phone: <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>"></label><br>
        <label>Email: <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"></label><br>
        <label>Role: <input type="text" name="role" value="<?php echo htmlspecialchars($user['role']); ?>"></label><br>
        <label>Is Active: <input type="checkbox" name="is_active" <?php echo $user['is_active'] ? 'checked' : ''; ?>></label><br>
        <input type="submit" value="Update">
    </form>
</body>
</html>
