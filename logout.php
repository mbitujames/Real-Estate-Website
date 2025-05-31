<?php
session_start();

// Include database connection
require_once './data/db.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
  } else {
    header('Location: login.php');
    exit;
  }

// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

// Log the activity
$action_description = "User logged out";
$current_date_time = date("Y-m-d H:i:s");
$insert_activity_query = "INSERT INTO activities (user_id, activity_description, activity_date) VALUES ($user_id, '$action_description', '$current_date_time')";

if (mysqli_query($conn, $insert_activity_query)) {
    // Redirect to the login page
    header('Location: login.php');
    exit;
} else {
    // Error logging activity
    $_SESSION['error'] = "Error logging activity: " . mysqli_error($conn);
    header('Location: login.php');
    exit;
}
?>