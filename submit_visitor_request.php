<?php
session_start();
include 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $st_id = $_POST['student_id'];
    $visitor_name = $_POST['visitor_name'];
    $visitation_date = $_POST['visitation_date'];
    $visitation_purpose = $_POST['visitation_purpose'];

    // Sanitize the data
    $s_id = mysqli_real_escape_string($conn, $student_id);
    $visitor_name = mysqli_real_escape_string($conn, $visitor_name);
    $visitation_date = mysqli_real_escape_string($conn, $visitation_date);
    $visitation_purpose = mysqli_real_escape_string($conn, $visitation_purpose);

    // Insert the data into the database
    $sql = "INSERT INTO visitor (st_id, name, date, purpose)
            VALUES ('$st_id', '$visitor_name', '$visitation_date', '$visitation_purpose')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to a confirmation page or back to the dashboard
        header("Location: visitor_request_confirmation.php"); // Create this page
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
} else {
    // If the form was not submitted, redirect to the visitor request form
    header("Location: visitor_request.php");
    exit();
}
?>