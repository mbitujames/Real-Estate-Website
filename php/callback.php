<?php
session_start();

// echo '<a href="index.php">Home<br /></a>';

require_once '../data/db.php'; 
require '../vendor/autoload.php'; // Include PHPMailer autoload
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$payment_id = isset($_SESSION['payment_id']) ? $_SESSION['payment_id'] : '';

// Retrieve and decode the JSON result from Safaricom
$content = file_get_contents('php://input'); //Receives the JSON Result from safaricom
$res = json_decode($content, true); //Convert the json to an array

$dataToLog = array(
    date("Y-m-d H:i:s"), //Date and time
    " MerchantRequestID: " . $res['Body']['stkCallback']['MerchantRequestID'],
    " CheckoutRequestID: " . $res['Body']['stkCallback']['CheckoutRequestID'],
    " ResultCode: " . $res['Body']['stkCallback']['ResultCode'],
    " ResultDesc: " . $res['Body']['stkCallback']['ResultDesc'],
);

$data = implode(" - ", $dataToLog);
$data .= PHP_EOL;
file_put_contents('transaction_log', $data, FILE_APPEND); //Logs the results to our log file


if ($res['Body']['stkCallback']['ResultCode'] == "0") {
    // Get the property_id and user_id from the payments table
    $sql_get_payment = "SELECT property_id, user_id FROM Payments WHERE payment_id = ?";
    $stmt_get_payment = mysqli_prepare($conn, $sql_get_payment);
    mysqli_stmt_bind_param($stmt_get_payment, "i", $payment_id);
    mysqli_stmt_execute($stmt_get_payment);
    mysqli_stmt_bind_result($stmt_get_payment, $property_id, $user_id);
    mysqli_stmt_fetch($stmt_get_payment);
    mysqli_stmt_close($stmt_get_payment);

    // Update the payment status to successful
    $sql = "UPDATE Payments SET payment_status = 'successful' WHERE payment_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $payment_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Mark the property as unavailable
    $sql_availability = "UPDATE Properties SET availability = 0 WHERE property_id = ?";
    $stmt_availability = mysqli_prepare($conn, $sql_availability);
    mysqli_stmt_bind_param($stmt_availability, "i", $property_id);
    mysqli_stmt_execute($stmt_availability);
    mysqli_stmt_close($stmt_availability);

    // Insert a record into the IssuedProperties table
    $agent_id = $user_id; // Assuming the user is the agent. Adjust this if the agent_id is different.
    $date_taken = date('Y-m-d');

    $sql_issue = "INSERT INTO IssuedProperties (property_id, user_id, date_taken, agent_id) VALUES (?, ?, ?, ?)";
    $stmt_issue = mysqli_prepare($conn, $sql_issue);
    mysqli_stmt_bind_param($stmt_issue, "iisi", $property_id, $user_id, $date_taken, $agent_id);
    mysqli_stmt_execute($stmt_issue);
    mysqli_stmt_close($stmt_issue);

    // Get user email
    $sql_get_user_email = "SELECT email FROM users WHERE user_id = ?";
    $stmt_get_user_email = mysqli_prepare($conn, $sql_get_user_email);
    mysqli_stmt_bind_param($stmt_get_user_email, "i", $user_id);
    mysqli_stmt_execute($stmt_get_user_email);
    mysqli_stmt_bind_result($stmt_get_user_email, $user_email);
    mysqli_stmt_fetch($stmt_get_user_email);
    mysqli_stmt_close($stmt_get_user_email);

    // Send email notification
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Your SMTP server address
        $mail->SMTPAuth = true;
        $mail->Username = 'krepmestates@gmail.com'; // SMTP username
        $mail->Password = 'owmtamlwyvpetxko'; // Your gmail app password
        $mail->SMTPSecure = 'ssl'; // Enable SSL encryption
        $mail->Port = 465;// TCP port to connect to

        //Recipients
        $mail->setFrom('krepmestates@gmail.com', 'Kitale Real Estate & Property Management System');
        $mail->addAddress($user_email); // Add a recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Payment Successful - Property Purchase';
        $mail->Body    = 'Dear User,<br><br>Your payment for the property with ID ' . $property_id . ' has been successfully processed. The property has been issued check the details to confirm the purchase.<br><br>Thank you for your purchase!<br><br>Best regards,<br>Kitale Real Estate 6 Property Management System';

        $mail->send();
        echo 'Notification email sent.';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    // Insert a record into the activities table
    $activity_description = "Payment successful for property ID: $property_id";
    $sql_activity = "INSERT INTO activities (user_id, activity_description) VALUES (?, ?)";
    $stmt_activity = mysqli_prepare($conn, $sql_activity);
    mysqli_stmt_bind_param($stmt_activity, "is", $user_id, $activity_description);
    mysqli_stmt_execute($stmt_activity);
    mysqli_stmt_close($stmt_activity);

    // Display confirmation message
    echo 'Payment successful! Property marked as unavailable and issued property record inserted.';

} else {
    // Update the payment status to failed
    $sql = "UPDATE Payments SET payment_status = 'failed' WHERE payment_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $payment_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Insert a record into the activities table for failed payment
    $activity_description = "Payment failed for property ID: $property_id";
    $sql_activity = "INSERT INTO activities (user_id, activity_description) VALUES (?, ?)";
    $stmt_activity = mysqli_prepare($conn, $sql_activity);
    mysqli_stmt_bind_param($stmt_activity, "is", $user_id, $activity_description);
    mysqli_stmt_execute($stmt_activity);
    mysqli_stmt_close($stmt_activity);

    // Display failure message
    echo 'Payment failed. Please try again.';

}

if (isset($stmt_issue) && $stmt_issue) {
    file_put_contents('error_log', "Records Inserted\n", FILE_APPEND);
} else {
    file_put_contents('error_log', "Failed to insert records\n", FILE_APPEND);
}
$conn->close();
?>