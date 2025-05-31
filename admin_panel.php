<!DOCTYPE html>
<html lang="en">
<?php
session_start();
require_once './data/db.php';

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) {
    header('Location: login.php');
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
  // Retrieve form data
  $title = $_POST['title'];
  $description = $_POST['description'];
  $location = $_POST['location'];
  $status = $_POST['status'];
  $price = $_POST['price'];
  $bedrooms = $_POST['bedrooms'];
  $bathrooms = $_POST['bathrooms'];
  $square_ft = $_POST['square_ft'];
  $property_type = $_POST['property_type'];
  $keyword = $_POST['keyword']; // Add keyword variable

  // Handle file upload
  if ($_FILES['image']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['image']['tmp_name'])) {
      $uploadDir = './data/uploads/';
      $filename = uniqid('img_') . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
      if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
          // Construct the image URL
          $image_url = $uploadDir . $filename;

          // Prepare and execute the SQL query to insert property data
          $sql = "INSERT INTO Properties (user_id, property_type, image_url, status, location, price, title, description, bedrooms, bathrooms, square_ft, keyword) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
          $stmt = mysqli_prepare($conn, $sql);
          // Assuming user_id is stored in the session
          $user_id = $_SESSION['user_id'];
          mysqli_stmt_bind_param($stmt, "issssdssiiss", $user_id, $property_type, $image_url, $status, $location, $price, $title, $description, $bedrooms, $bathrooms, $square_ft, $keyword);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_close($stmt);

          // Log the activity in the activities table
          $action_description = "Admin added a new property: $title";
          $current_date_time = date("Y-m-d H:i:s");
          $insert_activity_query = "INSERT INTO activities (user_id, activity_description, activity_date) VALUES ('$user_id', '$action_description', '$current_date_time')";
          if (mysqli_query($conn, $insert_activity_query)) {
              echo "Activity logged successfully.";
          } else {
              echo "Error logging activity: " . mysqli_error($conn);
          }

          // Redirect to admin_panel.php after inserting the property
          header('Location: admin_panel.php');
          exit;
      } else {
          // Error moving file
          echo "Error uploading image.";
      }
  } else {
      // No file uploaded or error occurred
      echo "Please select an image file.";
  }
}
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/admin.css">
    <style>
      @media screen and (max-width: 768px) {
        table {
          overflow-x: auto;
          display: block;
          white-space: nowrap;
        }
      }
      main {
        padding: 20px;
        font-family: 'Nunito Sans', sans-serif;
      }
      .navbar {
        display: none;
      }
      .navbar.active {
        display: block; /* Show the navbar when it has the 'active' class */
      }

      .overlay {
        display: none; /* Initially hide the overlay */
      }

      .overlay.active {
        display: block; /* Show the overlay when it has the 'active' class */
      }
      .navbar-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        color: black;
        }

      .navbar-link {
        text-decoration: none;
        color: #000;
        padding: 10px 15px;
        display: block;
      }

      .navbar-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .nav-close-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
      }

      .header-bottom-actions {
        display: flex;
        align-items: center;
      }

      .header-bottom-actions-btn {
        background: none;
        border: none;
        font-size: 1.5rem;
      }
      /* General card styling */
      section {
        margin-bottom: 20px;
        background-color: #f0f7ff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
        transition: box-shadow 0.3s ease;
      }

      section:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
      }

      /* Header styling for sections */
      section h3 {
        margin-top: 0;
        font-size: 1.5em;
        color: brown;
        border-bottom: 2px solid #f1f1f1;
        padding-bottom: 10px;
        text-align: center;
      }

      /* Sub-section card styling */
      section div {
        margin-bottom: 15px;
        background-color: #f0f7ff;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        padding: 15px;
        transition: box-shadow 0.3s ease;
      }
      section div:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }

      /* Header styling for sub-sections */
      section div h4 {
        margin-top: 0;
        font-size: 1.2em;
        text-decoration: double;
        color: black;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
      }

      /* Additional styling to ensure padding and spacing */
      section div p, section div ul {
        margin: 0;
        padding: 10px 0;
      }
      /* Table styling */
      table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
      }
      thead {
        background-color: #f2f2f2;
      }
      th, td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
      }
      th {
        font-weight: bold;
      }
      .table-image {
        width: 50px; /* Adjust image width as desired */
        height: auto; /* Maintain aspect ratio */
      }

      h2{
        color:#fa5b3d;
        padding:2px;
        text-decoration:solid;
        text-align:center;
      }
      /* Table heading styling */
      h4 {
        margin-top: 20px;
        margin-bottom: 10px;
        font-size: 18px;
      }

      /* Alternate row background color */
      tbody tr:nth-child(even) {
        background-color: #f9f9f9;
      }

      /* Hover effect */
      tbody tr:hover {
        background-color: #f2f2f2;
      }
      .error, .success {
        color: red;
        font-weight: bold;
        margin-bottom: 10px;
      }
      .success {
        color: green;
      }
      body {
        padding: 0;
        font-family: Arial, sans-serif;
        background-color: white;
      }

      .container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding-inline: 15px;
        box-sizing: border-box;
      }
      .actions {
        display: flex;
        justify-content: center;
        gap: 8px;
      }
      .actions .action-icon {
        color: white;
        padding: 8px;
        margin: 2px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
      }
      .actions .edit { background-color: #f39c12; }
      .actions .view { background-color: #3498db; }
      .actions .delete { background-color: #e74c3c; }
      .actions .edit:hover { background-color: #e67e22; }
      .actions .view:hover { background-color: #2980b9; }
      .actions .delete:hover { background-color: #c0392b; }
      
      
    </style>

    <!-- google font link-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
      href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&family=Poppins:wght@400;500;600;700&display=swap"
      rel="stylesheet">
</head>
<body>
  <header class="header" data-header>
    <div class="overlay" data-overlay></div>
    <div class="header-top">
      <div class="container">
        <ul class="header-top-list">
            <li>
              <a href="krepmestates@gmail.com" class="header-top-link">
                <ion-icon name="mail-outline"></ion-icon>
                <span>krepmestates@gmail.com</span>
              </a>
            </li>
            <li>
              <a href="#" class="header-top-link">
                <ion-icon name="location-outline"></ion-icon>
                <address>4th Floor, One Tana Towers, Kitale</address>
              </a>
            </li>
        </ul>
        <div class="wrapper">
          <ul class="header-top-social-list">
            <li> 
              <a href="https://www.facebook.com/MbituJames" class="header-top-social-link" target="_blank">
                <ion-icon name="logo-facebook"></ion-icon>
              </a>
            </li>
            <li>
              <a href="https://x.com/mbituke?t=mv9XXsRlVtVuec1vDPbUbQ&s=09" class="header-top-social-link" target="_blank">
                <ion-icon name="logo-twitter"></ion-icon>
              </a>
            </li>
            <li>
              <a href="https://www.instagram.com/mbitu?igsh=MWs3bG53cHhwYWNpOA==" class="header-top-social-link" target="_blank">
                <ion-icon name="logo-instagram"></ion-icon>
              </a>
            </li>
            <li>
              <a href="https://pin.it/2fseiKYde" class="header-top-social-link" target="_blank">
                <ion-icon name="logo-pinterest"></ion-icon>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="header-bottom">
      <div class="container">
        <a href="#home" class="logo">
          <img src="./assets/images/logo1.jpg" alt="KREPM">
        </a>
        <nav class="navbar" data-navbar>
          <div class="navbar-top">
            <a href="#home" class="logo">
              <img src="./assets/images/logo1.jpg" alt="KREPM">
            </a>
            <button class="nav-close-btn" data-nav-close-btn aria-label="Close Menu">
              <ion-icon name="close-outline"></ion-icon>
            </button>
          </div>

          <div class="navbar-bottom">
            <ul class="navbar-list">
              <li>
                <a href="../index.php" class="navbar-link" data-nav-link>Home</a>
              </li>
              <li>
                <a href="#" class="navbar-link" data-nav-link>Users</a>
              </li>
              <li>
                <a href="#" class="navbar-link" data-nav-link>Users Activities</a>
              </li>
              <li>
                <a href="./properties.php" class="navbar-link" data-nav-link>Properties</a>
              </li>
              <li>
                <a href="./reports.php" class="navbar-link" data-nav-link>Reports</a>
              </li>
            </ul>
          </div>
        </nav>
        <div class="header-bottom-actions">
          <div class="dropdown">
            <button class="btn">
              <a href="./logout.php"><span>Logout</span></a>
            </button>
            <button class="header-bottom-actions-btn" aria-label="admin" id="admin-btn">
              <ion-icon name="people-outline"></ion-icon>
              <span>Admin</span>
            </button>
          </div>
          <button class="header-bottom-actions-btn" data-nav-open-btn aria-label="Open Menu">
            <ion-icon name="menu-outline"></ion-icon>
            <span>Menu</span>
          </button>
        </div>
    </div>
  </div>
  </header>
  <main>
    <h2>KREPM ADMINISTRATION PANEL</h2>
    <section id="dashboard">
      <h3>Welcome, <?php echo $_SESSION['username']; ?>!</h3>
      <hr>
        <p>Manage your properties, users, and reports here.</p></br>
    </section>

      <section id="total-users">
        <?php
        require ('./data/db.php');
        // Fetch total number of users
        $sql_total_users = "SELECT COUNT(*) AS total_users FROM Users";
        $result_total_users = $conn->query($sql_total_users);
        $total_users = $result_total_users->fetch_assoc()['total_users'];
        // Fetch all users
        $sql_users = "SELECT * FROM Users";
        $result_users = $conn->query($sql_users);
        ?>
        <h3>Total Users</h3>
        <p>Total Users: <?php echo htmlspecialchars($total_users); ?></p>
        <table class="user-table">
          <thead>
            <tr>
              <th>User ID</th>
              <th>Username</th>
              <th>Full Name</th>
              <th>Phone</th>
              <th>Email</th>
              <th>Role</th>
              <th>Created At</th>
              <th>Is Active</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($user = $result_users->fetch_assoc()) { ?>
              <tr>
                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                <td><?php echo htmlspecialchars($user['is_active'] ? 'Yes' : 'No'); ?></td>
                <td class="actions">
                  <button class="action-icon edit" title="Edit" onclick="location.href='edit_user.php?id=<?php echo $user['user_id']; ?>'">
                    <ion-icon name="create-outline"></ion-icon>
                  </button>
                  <button class="action-icon view" title="View" onclick="location.href='view_user.php?id=<?php echo $user['user_id']; ?>'">
                    <ion-icon name="eye-outline"></ion-icon>
                  </button>
                  <button class="action-icon delete" title="Delete" onclick="location.href='delete_user.php?id=<?php echo $user['user_id']; ?>'">
                    <ion-icon name="trash-outline"></ion-icon>
                  </button>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </section>

      <section id="recent-activities">
        <h3>Recent Activities</h3>
        <?php
        // Include your database connection file
        include './data/db.php';
        // Query to retrieve user activities
        $sql = "
        SELECT a.activity_id, u.username, a.activity_description, a.activity_date
        FROM activities a
        JOIN users u ON a.user_id = u.user_id
        ORDER BY a.activity_date DESC
        LIMIT 10";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
          echo '<table>';
          echo '<tr><th>Activity ID</th><th>Username</th><th>Description</th><th>Date</th></tr>';
          while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['activity_id'] . '</td>';
            echo '<td>' . $row['username'] . '</td>';
            echo '<td>' . $row['activity_description'] . '</td>';
            echo '<td>' . $row['activity_date'] . '</td>';
            echo '</tr>';
          }
          echo '</table>';
        } else {
          echo 'No activities found.';
        }
        ?>
      </section>
      <section id="add-properties">
        <div class="card add-property" id="add-property">
        <h2>Add Property</h2>
        <?php if (!empty($add_property_err)) { ?>
          <p class="error"><?php echo $add_property_err; ?></p>
          <?php } ?>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required>

            <label for="status">Status:</label>
            <select id="status" name="status" required>
            <option value="For Sale ">For Sale</option>
            <option value="For Rent">For Rent</option>
            </select>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required>

            <label for="bedrooms">Bedrooms:</label>
            <input type="number" id="bedrooms" name="bedrooms" required>

            <label for="bathrooms">Bathrooms:</label>
            <input type="number" id="bathrooms" name="bathrooms" required>

            <label for="square_ft">Square Ft:</label>
            <input type="number" id="square_ft" name="square_ft" required>

            <label for="property_type">Type:</label>
            <select id="property_type" name="property_type" required>
            <option value="rent">Rent</option>
            <option value="buy">Sale</option>
            </select>

            <label for="image">Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <label for="keyword">Keyword(s):</label>
            <input type="text" id="keyword" name="keyword" required>
          
            <button type="submit" name="submit">Add Property</button>
          </form>
        </div>
      </section>
      
      <!--section for displaying all the properties-->
      <section id="properties">
        <h3>Properties</h3>
        <table class="properties-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Location</th>
              <th>Status</th>
              <th>Price</th>
              <th>Bedrooms</th>
              <th>Bathrooms</th>
              <th>Square Ft</th>
              <th>Type</th>
              <th>Image</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            //Query to fetch properties from your Properties table
            $query = "SELECT * FROM Properties ORDER BY property_id DESC"; 
            $result = mysqli_query($conn, $query);
            // Check if the query was successful
            if ($result === false) {
              echo "<tr><td colspan='11'>Error fetching properties: " . mysqli_error($conn) . "</td></tr>";
            } else {
              echo "Number of properties found: " . $result->num_rows . "<br>"; // Add this line
              // Fetch and display properties
              while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['property_id'] . "</td>";
                echo "<td>" . $row['title'] . "</td>";
                echo "<td>" . $row['location'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>" . $row['price'] . "</td>";
                echo "<td>" . $row['bedrooms'] . "</td>";
                echo "<td>" . $row['bathrooms'] . "</td>";
                echo "<td>" . $row['square_ft'] . "</td>";
                echo "<td>" . $row['property_type'] . "</td>";
                echo "<td><img src='" . $row['image_url'] . "' alt='" . $row['title'] . "' class='table-image'></td>";
                echo "<td class='actions'>
                  <button class='action-icon edit' title='Edit' onclick=\"location.href='edit_property.php?id=" . $row['property_id'] . "'\">
                    <ion-icon name='create-outline'></ion-icon>
                  </button>
                  <button class='action-icon view' title='View' onclick=\"location.href='view_property.php?id=" . $row['property_id'] . "'\">
                    <ion-icon name='eye-outline'></ion-icon>
                  </button>
                  <button class='action-icon delete' title='Delete' onclick=\"location.href='delete_properties.php?id=" . $row['property_id'] . "'\">
                    <ion-icon name='trash-outline'></ion-icon>
                  </button>
                </td>";
                echo "</tr>";
              }
            }
            ?>
          </tbody>
        </table>
      </section>      
    </main>
  <!-- #FOOTER-->
  <footer class="footer">
      <div class="footer-top">
        <div class="container">
          <div class="footer-brand">
            <a href="./index.php" class="logo">
              <img src="./assets/images/favicon.ico" alt="KREPM logo">
              <p >Kitale Real Estate & Property Management </p>
            </a>

            <p class="section-text">
              Feel free to explore our website and contact us for any inquiries or assistance.
            </p>

            <ul class="contact-list">
              <li>
                <a href="./index.php" class="contact-link">
                  <ion-icon name="location-outline"></ion-icon>
                  <address>4th Floor, One Tana Towers, Kitale</address>
                </a>
              </li>

              <li>
                <a href="tel:+254716519766" class="contact-link">
                  <ion-icon name="call-outline"></ion-icon>
                  <span>+254716519766</span>
                </a>
              </li>

              <li>
                <a href="mailto:mbitumutonga@gmail.com" class="contact-link" target="_blank">
                  <ion-icon name="mail-outline"></ion-icon>
                  <span>mbitumutonga@gmail.com</span>
                </a>
              </li>

            </ul>

            <ul class="social-list">
              <li>
                <a href="https://www.facebook.com/MbituJames" class="social-link" target="_blank">
                  <ion-icon name="logo-facebook"></ion-icon>
                </a>
              </li>

              <li>
                <a href="https://x.com/mbituke?t=mv9XXsRlVtVuec1vDPbUbQ&s=09" class="social-link" target="_blank">
                  <ion-icon name="logo-twitter"></ion-icon>
                </a>
              </li>

              <li>
                <a href="https://www.linkedin.com/in/mbitujames/" class="social-link" target="_blank">
                  <ion-icon name="logo-linkedin"></ion-icon>
                </a>
              </li>
            </ul>
          </div>

          <div class="footer-link-box">
            <ul class="footer-list">
              <li>
                <p class="footer-list-title">Company</p>
              </li>

              <li>
                <a href="./index.php" class="footer-link">About Us</a>
              </li>

              <li>
                <a href="./index.php" class="footer-link">Reviews</a>
              </li>

              <li>
                <a href="./properties.php" class="footer-link">Properties</a>
              </li>

              <li>
                <a href="./index.php" class="footer-link">Contact us</a>
              </li>

            </ul>

            <ul class="footer-list">
              <li>
                <p class="footer-list-title">Services</p>
              </li>
              <li>
                <a href="./login.php" class="footer-link">Login</a>
              </li>

              <li>
                <a href="./admin_panel.php" class="footer-link">My account</a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <div class="container">
          <p class="copyright">
            &copy; 2024 <a href="./index.php">Kitale Real Estate & Property Management</a>. All Rights Reserved
          </p>
        </div>
      </div>
    </footer>
  
    <!-- custom js link-->
    <script>
    // JavaScript for handling the navigation menu
    document.addEventListener('DOMContentLoaded', function() {
      const navOpenBtn = document.querySelector('[data-nav-open-btn]');
      const navCloseBtn = document.querySelector('[data-nav-close-btn]');
      const navbar = document.querySelector('[data-navbar]');
      const overlay = document.querySelector('[data-overlay]');

      navOpenBtn.addEventListener('click', function() {
        navbar.classList.add('active');
        overlay.classList.add('active');
      });

      navCloseBtn.addEventListener('click', function() {
        navbar.classList.remove('active');
        overlay.classList.remove('active');
      });

      overlay.addEventListener('click', function() {
        navbar.classList.remove('active');
        overlay.classList.remove('active');
      });
    });
  </script>
    <script src="./assets/js/admin.js"></script>
  
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  </body>
</html>