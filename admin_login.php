<?php
session_start();
include 'db.php';  // Database connection

// Process admin login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = $_POST['email'];
    $admin_id = $_POST['admin_id'];

    // Query to check if admin exists
    $query = "SELECT * FROM Admin WHERE admin_email = '$email' AND admin_id = '$admin_id'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Admin found, set session and redirect to dashboard
        $row = $result->fetch_assoc();
        $_SESSION['admin_id'] = $row['admin_id'];
        $_SESSION['admin_name'] = $row['admin_name'];

        // Redirect to the admin dashboard
        header("Location: admin_dashboard.php");
        exit();
    } else {
        // Invalid credentials
        $error = "Invalid admin credentials! Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Background gradient for the body */
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-image: linear-gradient(120deg, #800000 0%, #f4f4f4); /* Maroon and Offwhite gradient */
            background-attachment: fixed;
        }
    </style>
</head>
<body>
    <!-- Admin Login Form -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card login-card">
                    <h2>Admin Login</h2>
                    <form method="POST" action="admin_login.php">
                        <input type="email" name="email" placeholder="Enter your email" class="form-control mb-3" required>
                        <input type="text" name="admin_id" placeholder="Enter Admin ID" class="form-control mb-3" required>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>

                    <?php if (isset($error)) { ?>
                        <div class="alert alert-danger mt-3">
                            <?php echo $error; ?>
                        </div>
                    <?php } ?>

                    <div class="login-button mt-3">
                        <a href="index.php">Go to Student Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
