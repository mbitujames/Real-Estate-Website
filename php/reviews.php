<?php
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
