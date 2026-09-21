<?php
session_start();
include 'db.php';  // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email format
    if (substr($email, -14) === "@g.bracu.ac.bd") {
        // Query to check if user exists
        $query = "SELECT * FROM User WHERE email = '$email'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            // Fetch user data
            $row = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $row['password'])) {
                // Set session variables
                $_SESSION['st_id'] = $row['st_id'];
                $_SESSION['st_name'] = $row['st_name'];

                // Redirect to student dashboard
                header("Location: student_dashboard.php");
                exit();
            } else {
                // Invalid password
                $error = "Invalid password! Please try again.";
            }
        } else {
            // No user found with that email
            $error = "No account found with that email!";
        }
    } else {
        // Invalid email domain
        $error = "Please use a valid BRACU mail";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        /* Background gradient for the body */
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-image: linear-gradient(120deg, #800000 0%, #f4f4f4); /* Maroon and Offwhite gradient */
            background-attachment: fixed;
        }

        .navbar {
            background-color: #f4f4f4; /* Off-white background */
        }

        .navbar-brand, .navbar-nav .nav-link {
            color: #800000 !important; /* Maroon text color */
        }

        .navbar .nav-link:hover {
            color: #8B0000; /* Darker maroon text on hover */
        }

        .home-image {
            max-width: 60%; /* Reduced size */
            margin-top: 20px;
        }

        .btn-primary {
            background-color: #800000; /* Maroon */
            border-color: #800000;
        }

        .btn-primary:hover {
            background-color: #5b0000; /* Darker Maroon */
            border-color: #5b0000;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Hostel Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="rooms.php">Rooms</a></li>
                    <li class="nav-item"><a class="nav-link" href="facilities.php">Facilities</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card login-card">
                    <img src="images/studentlogin.png" alt="Hostel Image" class="img-fluid">
                    <h2>Student Login</h2>
                    <form method="POST" action="index.php">
                        <input type="email" name="email" placeholder="Enter your email" class="form-control mb-3" required>
                        <input type="password" name="password" placeholder="Enter your password" class="form-control mb-3" required>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>

                    <?php if (isset($error)) { ?>
                        <div class="alert alert-danger mt-3">
                            <?php echo $error; ?>
                        </div>
                    <?php } ?>

                    <!-- Go to Admin Panel link -->
                    <div class="login-button mt-3">
                        <a href="admin_login.php">Go to Admin Panel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5">
        <p>&copy; 2025 Hostel Management System. All rights reserved.</p>
    </footer>
                        
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
