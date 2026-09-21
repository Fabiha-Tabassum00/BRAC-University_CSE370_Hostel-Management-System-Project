<?php
session_start();
include 'db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email ends with "@g.bracu.ac.bd"
    if (substr($email, -14) === "@g.bracu.ac.bd") {
        // Query to check if the user exists with the provided email
        $query = "SELECT * FROM User WHERE email = '$email'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            // User found, fetch the data
            $row = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $row['password'])) {
                // Password is correct, set session variables
                $_SESSION['st_id'] = $row['st_id'];
                $_SESSION['st_name'] = $row['st_name'];

                // Redirect to the student dashboard
                header("Location: student_dashboard.php");
                exit();
            } else {
                // Invalid password
                echo "Invalid password!";
            }
        } else {
            // No user found with that email
            echo "No account found with that email!";
        }
    } else {
        // Email does not end with @g.bracu.ac.bd
        echo "Please use a valid BRACU email address (ending with @g.bracu.ac.bd).";
    }
}
?>
