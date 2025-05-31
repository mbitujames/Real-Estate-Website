<?php
require('./data/db.php');

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch user details
    $sql_user = "SELECT * FROM Users WHERE user_id = ?";
    $stmt = $conn->prepare($sql_user);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result_user = $stmt->get_result();
    $user = $result_user->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View User</title>
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
    margin: 10px auto;
    background: #fff;
    padding: 10px;
    border-radius: 4px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    font-size: 16px;
}

p strong {
    display: inline-block;
   width:150px;
}
    </style>
</head>
<body>
    <h2>View User</h2>
    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
    <p><strong>Created At:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
    <p><strong>Is Active:</strong> <?php echo htmlspecialchars($user['is_active'] ? 'Yes' : 'No'); ?></p>
</body>
</html>
