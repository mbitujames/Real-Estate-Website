<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$db = "krepm_db";
// Create connection
$conn = new mysqli($servername, $username, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Get user data
$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $full_name = $_POST['full_name'];
    $password = $_POST['password'];

    // Handle file upload
    $avatar_url = "";
    $upload_dir = './data/uploads/'; 
    if (isset($_FILES['avatar_url']) && $_FILES['avatar_url']['error'] == UPLOAD_ERR_OK) {
        // Updated path for profile pictures
        $avatar_filename = basename($_FILES['avatar_url']['name']);
        $avatar_path = $upload_dir . $avatar_filename;

        // Check if upload directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Move uploaded file to the designated directory
        if (move_uploaded_file($_FILES['avatar_url']['tmp_name'], $avatar_path)) {
            $avatar_url = $avatar_path;
        } else {
            $_SESSION['error_message'] = 'Error uploading profile picture: Failed to move uploaded file.';
            header('Location: ../dashboard.php'); // Redirect to the correct path
            exit;
        }
    } else {
        if ($_FILES['avatar_url']['error'] != UPLOAD_ERR_NO_FILE) {
            // Handle other file upload errors
            $_SESSION['error_message'] = 'Error uploading profile picture: ' . $_FILES['avatar_url']['error'];
            header('Location: ../dashboard.php'); // Redirect to the correct path
            exit;
        }
    }

    // **Sanitize password input**
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update user information query
    $sql = "UPDATE users SET email = ?, full_name = ?, phone = ?, password = ?";
    if (!empty($avatar_url)) {
        $sql .= ", avatar_url = ?";
    }
    $sql .= " WHERE user_id = ?";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    if (!empty($avatar_url)) {
        $stmt->bind_param("sssssi", $email, $full_name, $phone, $hashed_password, $avatar_url, $user_id);
    } else {
        $stmt->bind_param("ssssi", $email, $full_name, $phone, $hashed_password, $user_id);
    }

    // Execute the statement
    if ($stmt->execute()) {
        // Add activity to activities table
        $current_date_time = date('Y-m-d H:i:s');
        $activity_description = "User with ID $user_id updated their information.";
        $sql_activity = "INSERT INTO activities (user_id, activity_description, activity_date) VALUES (?, ?, ?)";
        $stmt_activity = $conn->prepare($sql_activity);
        $stmt_activity->bind_param("iss", $user_id, $activity_description, $current_date_time);
        $stmt_activity->execute();
        $stmt_activity->close();

        $_SESSION['success_message'] = 'User information updated successfully!';
    } else {
        $_SESSION['error_message'] = 'Error updating user information: ' . $stmt->error;
    }

    $stmt->close();
}

$conn->close(); // Close database connection
header('Location: ../dashboard.php'); // Redirect to the correct path
?>
