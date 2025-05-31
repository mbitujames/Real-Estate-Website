<!DOCTYPE html>
<html lang="en">
<?php
session_start();
require_once './data/db.php';
// Include M-PESA Daraja library (download from Safaricom developer portal)
require_once('./php/safaricom_daraja.php');
// Retrieve property_id from the URL
if (isset($_GET['property_id'])) {
  $property_id = $_GET['property_id']; // Ensure it's an integer to prevent SQL injection
  $_SESSION['property_id'] = $_GET['property_id'];
  // Fetch property details from the database
  $sql = "SELECT * FROM properties WHERE property_id = $property_id";
  $result = mysqli_query($conn, $sql);

  if ($result && mysqli_num_rows($result) > 0) {
    $property = mysqli_fetch_assoc($result);

    // Access property details
    $title = $property['title'];
    $location = $property['location'];
    $price = $property['price'];
    $property_type = $property['property_type'];
  } else {
    echo "Invalid property ID or no property found";
    exit;
  }
} else {
  echo "No property ID provided";
  exit;
}
?>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>checkout</title>
  <style>
    body {
      font-family: sans-serif;
      margin: 20px;
    }

    main {
      display: flex;
      justify-content: center;
    }

    input[type="tel"],
    input[type="number"] {
      padding: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    .payment-container {
      width: 90%;
      padding-bottom: 20px;
      background-color: #f0f7ff;
      max-width: 1200px;
      padding: 40px;
      border-radius: 4px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .container {
      width: 100%;
      max-width: 1200px;
      padding: 20px;
      border-radius: 4px;
      box-sizing: border-box;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    /* Header styles */
    .logo {
      height: 50px; /* Adjust height as needed based on your logo image */
    }
    .logo img {
      height: 100%; /* Ensures image fills the logo container */
    }
    .header-container {
      width: 100%;
      background-color: whitesmoke;
      max-width: 1200px;
      padding-top: 10px;
      padding: 25px;
      border-radius: 4px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .checkout-title {
      margin: 0; /* Remove default margin */
      font-size: 1.5rem;
      font-weight: bold;
      color: brown;
      text-align: center;
    }
    .property-details {
      margin-bottom: 25px;
    }
    .property-details h2 {
      text-align: center;
      margin-bottom: 10px;
    }

    .property-details p {
      margin-bottom: 10px;
      font-weight: bold;
    }

    #payment-form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }

    .form-group label {
      font-weight: bold;
    }

    button[type="submit"] {
      text-align: center;
      font-weight: bold;
      width: 100%;
      padding: 10px;
      background-color: #fa5b3d;
      border: black;
      border-radius: 5px;
      color: black;
      cursor: pointer;
      margin-inline: auto;
    }

    button[type="submit"]:hover {
      background: white;
      color: black;
      border-color: transparent;
      text-align: center;

    }

    #message {
      margin-top: 10px;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }
  </style>
  <link rel="stylesheet" href="./assets/css/style.css">
  <!-- google font link-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
  <!-- #HEADER-->
  <header class="header">
    <div class="header-container">
      <a href="#" class="logo">
        <img src="./assets/images/logo1.jpg" alt="KREPM">
      </a>
      <h1 class="checkout-title">Checkout</h1>
    </div>
  </header>

  <!--form codes here-->
  <main>
    <div class="payment-container">
      <div class="property-details">
        <h2>Property Details</h2>
        <p><strong>Title:</strong> <?php echo $title; ?></p>
        <p><strong>Property Type:</strong> <?php echo $property_type; ?></p>
        <p><strong>Location:</strong> <?php echo $location; ?></p>
        <p><strong>Price:</strong> Ksh <?php echo $price; ?></p>
      </div>
      <form id="payment-form" action="./php/process_payment.php" method="post">
        <input type="hidden" name="property_id" value="<?php echo $property_id; ?>">
        <div class="form-group">
          <label for="phone">Phone Number:</label>
          <input type="tel" id="phone" name="MSISDN" placeholder="Enter phone number (e.g., 254712345678)" autocomplete="on">
        </div>
        <div class="form-group">
          <label for="payment_amount">Amount (Ksh):</label>
          <input type="number" id="payment_amount" name="payment_amount" min="1" placeholder="Enter amount to pay" required>
        </div>
        <button type="submit" name="pay-btn">Pay with M-Pesa</button>
      </form>
      <div id="message"></div>
    </div>
  </main>
  <!-- #FOOTER-->
  <?php
  require('./php/components/footer.php');
  ?>
  
  <script>
    function validateForm() {
      const phone = document.getElementById('phone').value;
      const paymentAmount = document.getElementById('payment_amount').value;

      // Basic validation
      if (phone === '' || paymentAmount === '') {
        alert('Please fill in all fields.');
        return false;
      }

      if (!/^\d{12}$/.test(phone)) {
        alert('Please enter a valid phone number (1 digits, e.g., 254712345678).');
        return false;
      }

      return true;
    }
  </script>
  <!-- custom js link-->
  <script src="./assets/js/script.js"></script>
  <!-- ionicon link-->
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>