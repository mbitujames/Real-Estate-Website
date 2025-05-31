<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer autoload
require 'vendor/autoload.php';

// Include database connection
require_once './data/db.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $number = $_POST['number'];
    $message = $_POST['message'];

    // Email subject
    $subject = "New Contact Form Submission";

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Your SMTP server address
        $mail->SMTPAuth = true;
        $mail->Username = 'krepmestates@gmail.com'; // My gmail
        $mail->Password = 'owmtamlwyvpetxko'; // Your gmail app password
        $mail->SMTPSecure = 'ssl'; 
        $mail->Port = 465;

        // Email headers
        $mail->setFrom($email, $name);
        $mail->addAddress('krepmestates@gmail.com'); // Recipient email address
        $mail->Subject = $subject;
        $mail->Body = "Name: $name\nEmail: $email\nPhone Number: $number\nMessage:\n$message";

        // Send email
        $mail->send();
        
        // Log the activity
        $action_description = "User sent a message via contact form";
        $current_date_time = date("Y-m-d H:i:s");
        $insert_activity_query = "INSERT INTO activities (user_id, activity_description, activity_date) VALUES ('$user_id', '$action_description', '$current_date_time')";
        
        if (mysqli_query($conn, $insert_activity_query)) {
            $_SESSION['message'] = "Thank you for your message. We will contact you shortly.";
        } else {
            $_SESSION['error'] = "Error logging activity: " . mysqli_error($conn);
        }
        // Redirect user to login page after a short delay
        header("Refresh: 3; url=index.php");
        exit;;
    } catch (Exception $e) {
        $_SESSION['error'] = "Oops! Something went wrong. Please try again later.";
        header("Refresh: 3; url=index.php"); // Redirect back to the contact form page
        exit;
    }
}  
?>
