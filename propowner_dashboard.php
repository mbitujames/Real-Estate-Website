<!--home owner can add properties, view all added properties, view reports on bought or rent properties,buy or rent properties-->
<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: signup.php'); // Redirect to login page if not logged in
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$db = "krepm_db";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $db);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error() . "</br>");
}
// echo "Connection successfully created</br>";

// Fetch user data from the database
$user_id = $_SESSION['user_id']; // Assuming you store user ID in session
$sql = "SELECT * FROM users WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Fetch count of listed properties for rent and for sale
$sqlRent = "SELECT COUNT(*) AS rent_count FROM properties WHERE property_type = 'rent' AND status = 'For Rent'";
$sqlSale = "SELECT COUNT(*) AS sale_count FROM properties WHERE property_type = 'buy' AND status = 'For Sale'";
$sqlTotal = "SELECT COUNT(property_id) AS total_count FROM properties";
$resultRent = mysqli_query($conn, $sqlRent);
$resultSale = mysqli_query($conn, $sqlSale);
$resultTotal = mysqli_query($conn, $sqlTotal);
$rent_count = mysqli_fetch_assoc($resultRent)['rent_count'];
$sale_count = mysqli_fetch_assoc($resultSale)['sale_count'];
$total_count = mysqli_fetch_assoc($resultTotal)['total_count'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
    /* Sidebar Styles */
    section {
        margin-bottom: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        transition: box-shadow 0.3s ease;
    }

    section:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }
    .sidebar {
        height: 100%;
        width: 250px;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #fff2f0;
        padding-top: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
    }
    .sidebar:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); 
    }

    .sidebar h2 {
        color: black;
        text-align: center;
    }

    .sidebar ul {
        list-style-type: none;
        padding: 0;
    }

    .sidebar li {
        padding: 10px;
        transition: background-color 0.3s, color 0.3s;
    }

    .sidebar li:hover {
        background-color: #fa5b3d;
        color: white;
    }

    .sidebar a {
        text-decoration: none;
        color: black;
        display: block;
        transition: color 0.3s;
    }
    .sidebar a:hover {
    color: white;
    }

    /* Content Styles */
    .content {
        margin-left: 250px;
        padding: 20px;
    }

    .card {
        background-color:#f0f7ff;
        margin-bottom: 20px;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .card img {
        width: 100px;
        height: auto;
        display: block;
        margin-bottom: 10px;
        border-radius: 50%;
    }
    .welcome-text {
        font-weight: bold;
        font-size: 30px;
        color: brown;
        text-align: center;
    }
    button{
        margin-top: 10px;
        float: bottom;
        width: 80%;
        background-color:#fa5b3d;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 12px 24px;
        font-size: 18px;
        cursor: pointer;
        text-align: center;
        transition: background-color 0.3s, color 0.3s;
    }
    button:hover{
        background-color: white;
        color: #333;
    }
    .card button {
        width: 100%;
        background-color:#fa5b3d;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 12px 24px;
        font-size: 18px;
        cursor: pointer;
        text-align: center;
        transition: background-color 0.3s, color 0.3s;
    }
    .card button:hover {
        background-color: white;
        color: #333;
    }
    .success-message {
        color: green; 
        font-size: 14px;
        margin-bottom: 5px; 
    }
    .error-message {
        color: red;
        font-size: 14px;
        margin-bottom: 5px; 
    }
    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group input, .form-group textarea {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
    }
    .user-info {
        display: flex; /* Make user info section a flexbox */
        align-items: center; /* Align image and details horizontally */
        margin-bottom: 10px; /* Add some spacing */
    }

    .user-info img {
        width: 50px; /* Set image width */
        height: 50px; /* Set image height */
        border-radius: 50%; /* Make image circular */
        margin-right: 10px; /* Add margin between image and details */
    }

    .user-details {
        display: flex; /* Make user details a flexbox */
        flex-direction: column; /* Arrange details vertically */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .username,
    .email {
        font-weight: bold; /* Make username and email bold */
        margin-bottom: 5px; /* Add some spacing */
    }

    /* Media Queries for Responsive Design */
    /* For screens smaller than 768px (tablets) */
    @media only screen and (max-width: 768px) {
    .sidebar {
        width: 100%; /* Sidebar takes up full width */
        position: relative; /* Remove fixed positioning */
        height: auto; /* Remove fixed height */
    }

    .content {
        margin-left: 0; /* Remove left margin */
    }

    .card button {
        width: 100%; /* Buttons take up full width */
    }}
    /* For screens smaller than 576px (phones) */
    @media only screen and (max-width: 576px) {
    .sidebar {
        padding-top: 10px; /* Adjust padding */
    }

    .card button {
        font-size: 16px; /* Adjust font size for smaller screens */
    }}

</style>
</head>

<body>
    <div class="sidebar">
        <h2>Dashboard</h2><br>
        <div class="user-info">
        <img src="<?php echo htmlspecialchars($user['avatar_url']); ?>" alt="User Image">
            <div class="user-details">
                <div class="username"><?php echo $user['username']; ?></div>
                <div class="email"><?php echo $user['email']; ?></div>
            </div>
        </div>
        <ul>
            <li><i class="fa-solid fa-house"></i><a href="./index.php"><strong>Home</strong></a></li>
            <li><i class="fa-solid fa-building"></i><a href="#listed-properties"><strong>Listed Properties</strong></a></li> 
            <li><i class="fa-sharp fa-solid fa-pen-to-square"></i><a href="#update-information"><strong>Update Information</strong></a></li>
            <li><i class="fa-sharp fa-solid fa-star-sharp"></i><a href="#reviews"><strong>Write a Review</strong></a></li>
            <li><i class="fa-regular fa-file-lines"></i><a href="#reports"><strong>Reports</strong></a></li> <!--generate users reports -->
        </ul>
        <br><br>
        <button><span> <a href="./logout.php">Logout</a></span></button>
        
    </div>
    <div class="content">
        <section id= "welcome" class="card">
            <p class="welcome-text">Welcome, <?php echo $user['username'];?>!</p>
            <p>This is your personal dashboard where you can update your details and view your reports.</p>
        </section>

        <section id="listed-properties" class="card">
            <!-- Listed Properties Section -->
            <h2>Listed Properties</h2>
            <p>Properties for Rent: <?php echo $rent_count; ?></p>
            <p>Properties for Sale: <?php echo $sale_count; ?></p>
            <p>Total Properties: <?php echo $total_count; ?></p>
            <button><span> <a href="./properties.php">View All Properties</a></span></button>
        </section>

        <section id="update-information" class="card">
            <!-- Settings Section -->
            <h2>Update Your Information</h2>
            <!-- Display success or error message -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="success-message"><?php echo $_SESSION['success_message']; ?></div>
                <?php unset($_SESSION['success_message']); ?>
                <?php elseif (isset($_SESSION['error_message'])): ?>
                    <div class="error-message"><?php echo $_SESSION['error_message']; ?></div>
                    <?php unset($_SESSION['error_message']); ?>
                    <?php endif; ?>
            <!-- Form to update user details -->
            <form action="./php/update_dashboard.php" method="post" enctype="multipart/form-data" onsubmit="return validateSettingsForm()">
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo $user['full_name']; ?>" required><br>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" autocomplete="on"><br>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" value="<?php echo $user['phone']; ?>" autocomplete="on"><br>
            </div>
            <div class="form-group">
                <label for="avatar_url">Upload Profile Picture:</label>
                <input type="file" id="avatar_url" name="avatar_url"><br>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password"><br>
            </div>
                <button type="submit" id="update">Update Information</button>
            </form>
        </section>
        
    </div>

    <!--js for validating the settings form-->
    <script>
        function validateSettingsForm() {
        var email = document.getElementById("email").value;
        var phone = document.getElementById("phone").value;
        var password = document.getElementById("password").value;

        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var phonePattern = /^[0-9]{10}$/;
        var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        // Validate Email
        if (!emailPattern.test(email)) {
            alert('Please enter a valid email address.');
            return false;
        }

        // Validate Phone Number
        if (!phonePattern.test(phone)) {
            alert('Phone number must be exactly 10 digits and contain only numbers.');
            return false;
        }

        // Validate Password
        if (!passwordPattern.test(password)) {
            alert('Password must contain at least 8 characters, including uppercase, lowercase, a number, and a special character.');
            return false;
        }

        // All validations passed
        return true;
    }
    document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reviewForm');

    form.addEventListener('submit', function(event) {
        const rating = parseFloat(document.getElementById('rating').value);
        if (rating < 1 || rating > 5) {
            alert('Rating must be between 1 and 5.');
            event.preventDefault();
        }
    });
    });

    </script>
    <script src="https://kit.fontawesome.com/43e53da9a6.js" crossorigin="anonymous"></script>
</body>
</html>