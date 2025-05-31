<?php
// Database connection
require_once '../data/db.php';

// Function to handle file upload
function uploadImage($file)
{
    $uploadDir = '../data/uploads/';
    $fileName = basename($file['name']);
    $uploadPath = $uploadDir . $fileName;
    $imageFileType = strtolower(pathinfo($uploadPath, PATHINFO_EXTENSION));

    // Check if file is an actual image
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        return false; // File is not an image
    }

    // Check if file already exists
    if (file_exists($uploadPath)) {
        return false; // File already exists
    }
    // Check file size (limit to 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return false; // File is too large
    }
    // Allow certain file formats
    if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif') {
        return false; // Unsupported file format
    }

    // Move uploaded file to destination directory
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return false; // Error while moving file
    }

    return $fileName; // Return uploaded file name
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle image upload
    if (isset($_FILES['image'])) {
        $imageURL = uploadImage($_FILES['image']);
        if ($imageURL === false) {
            echo 'Failed to upload image.';
            exit;
        }
    } else {
        echo 'No image uploaded.';
        exit;
    }

    // Process other form data
    // Retrieve other form fields like username, rating, and review
    $userName = $_POST['userName'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    // Insert data into the database
    $sql = "INSERT INTO Testimonials (user_name, user_image_url, rating, review) 
            VALUES ('$userName', '$imageURL', '$rating', '$review')";
    if (mysqli_query($conn, $sql)) {
        echo 'Testimonial added successfully.';
    } else {
        echo 'Error: ' . mysqli_error($conn);
    }
}
