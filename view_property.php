<?php
require('./data/db.php');

if (isset($_GET['id'])) {
    $property_id = $_GET['id'];

    // Fetch property details
    $sql_fetch_property = "SELECT * FROM properties WHERE property_id = ?";
    $stmt_fetch_property = $conn->prepare($sql_fetch_property);
    $stmt_fetch_property->bind_param("i", $property_id);
    $stmt_fetch_property->execute();
    $result_fetch_property = $stmt_fetch_property->get_result();
    $property = $result_fetch_property->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Property</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .property-container {
            max-width: 600px;
            margin: 0 auto;
            background: #f0f7ff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .property-container img {
            width: 100%;
            border-radius: 8px;
        }
        h2 {
            text-align: center;
        }
        .property-details {
            margin-top: 20px;
        }
        .property-details label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="property-container">
        <h2><?php echo htmlspecialchars($property['title']); ?></h2>
        <img src="<?php echo htmlspecialchars($property['image_url']); ?>" alt="<?php echo htmlspecialchars($property['title']); ?>">
        <div class="property-details">
            <p><label>Location:</label> <?php echo htmlspecialchars($property['location']); ?></p>
            <p><label>Status:</label> <?php echo htmlspecialchars($property['status']); ?></p>
            <p><label>Price:</label> <?php echo htmlspecialchars($property['price']); ?></p>
            <p><label>Bedrooms:</label> <?php echo htmlspecialchars($property['bedrooms']); ?></p>
            <p><label>Bathrooms:</label> <?php echo htmlspecialchars($property['bathrooms']); ?></p>
            <p><label>Square Ft:</label> <?php echo htmlspecialchars($property['square_ft']); ?></p>
            <p><label>Type:</label> <?php echo htmlspecialchars($property['property_type']); ?></p>
        </div>
    </div>
</body>
</html>
