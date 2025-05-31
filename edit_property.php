<?php
require('./data/db.php');

if (isset($_GET['id'])) {
    $property_id = $_GET['id'];

    // Fetch property details for pre-filling the form
    $sql_fetch_property = "SELECT * FROM Properties WHERE property_id = ?";
    $stmt_fetch_property = $conn->prepare($sql_fetch_property);
    $stmt_fetch_property->bind_param("i", $property_id);
    $stmt_fetch_property->execute();
    $result_fetch_property = $stmt_fetch_property->get_result();
    $property = $result_fetch_property->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $title = $_POST['title'];
        $location = $_POST['location'];
        $status = $_POST['status'];
        $price = $_POST['price'];
        $bedrooms = $_POST['bedrooms'];
        $bathrooms = $_POST['bathrooms'];
        $square_ft = $_POST['square_ft'];
        $property_type = $_POST['property_type'];
        $image_url = $_POST['image_url'];

        // Validation
        $errors = [];

        if (empty($title)) {
            $errors[] = "Title is required.";
        }
        if (empty($location)) {
            $errors[] = "Location is required.";
        }
        if (empty($price) || !is_numeric($price)) {
            $errors[] = "Price must be a number.";
        }
        if (empty($bedrooms) || !is_numeric($bedrooms)) {
            $errors[] = "Bedrooms must be a number.";
        }
        if (empty($bathrooms) || !is_numeric($bathrooms)) {
            $errors[] = "Bathrooms must be a number.";
        }
        if (empty($square_ft) || !is_numeric($square_ft)) {
            $errors[] = "Square footage must be a number.";
        }

        if (empty($errors)) {
            // Update property details
            $sql_update_property = "UPDATE Properties SET title = ?, location = ?, status = ?, price = ?, bedrooms = ?, bathrooms = ?, square_ft = ?, property_type = ?, image_url = ? WHERE property_id = ?";
            $stmt_update_property = $conn->prepare($sql_update_property);
            $stmt_update_property->bind_param("sssdiiissi", $title, $location, $status, $price, $bedrooms, $bathrooms, $square_ft, $property_type, $image_url, $property_id);

            // Insert record into activities table
            $activity_description = "Property with ID " . $property_id . " updated";
            $insert_query = "INSERT INTO activities (user_id, activity_description) VALUES (?, ?)";
            $stmt_insert_activity = $conn->prepare($insert_query);
            $stmt_insert_activity->bind_param("is", $property['user_id'], $activity_description);
            
            if ($stmt_update_property->execute()) {
                echo "Property updated successfully!";
                header("Refresh: 3; url=admin_panel.php");
            } else {
                echo "Error updating property: " . $conn->error;
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
    <title>Edit Property</title>
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
        input[type="number"] {
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
    <h2>Edit Property</h2>
    <form method="POST">
        <label>Title: <input type="text" name="title" value="<?php echo htmlspecialchars($property['title']); ?>" required></label><br>
        <label>Location: <input type="text" name="location" value="<?php echo htmlspecialchars($property['location']); ?>" required></label><br>
        <label>Status: <input type="text" name="status" value="<?php echo htmlspecialchars($property['status']); ?>" required></label><br>
        <label>Price: <input type="number" name="price" value="<?php echo htmlspecialchars($property['price']); ?>" required></label><br>
        <label>Bedrooms: <input type="number" name="bedrooms" value="<?php echo htmlspecialchars($property['bedrooms']); ?>" required></label><br>
        <label>Bathrooms: <input type="number" name="bathrooms" value="<?php echo htmlspecialchars($property['bathrooms']); ?>" required></label><br>
        <label>Square Ft: <input type="number" name="square_ft" value="<?php echo htmlspecialchars($property['square_ft']); ?>" required></label><br>
        <label>Type: <input type="text" name="property_type" value="<?php echo htmlspecialchars($property['property_type']); ?>" required></label><br>
        <label>Image URL: <input type="text" name="image_url" value="<?php echo htmlspecialchars($property['image_url']); ?>" required></label><br>
        <input type="submit" value="Update">
    </form>
</body>
</html>
