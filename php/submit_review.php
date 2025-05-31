<?php
require_once '../data/db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['username'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    // Fetch user's image URL from the database
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT avatar_url FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($user_image_url);
    $stmt->fetch();
    $stmt->close();

    if (!$user_image_url) {
        echo "User image not found.";
        exit();
    }

    // Prepare SQL and bind parameters
    $stmt = $conn->prepare("INSERT INTO testimonials (username, user_image_url, rating, review) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $username, $user_image_url, $rating, $review);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Review submitted successfully!";
        header("Refresh: 3; url=../dashboard.php");
    } else {
        echo "Error: " . $stmt->error;
        header("Refresh: 3; url=../dashboard.php");
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
