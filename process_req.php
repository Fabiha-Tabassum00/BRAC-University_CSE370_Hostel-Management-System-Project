<?php
session_start();
include 'db.php';  // Database connection

// Ensure the user is logged in as an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];  // Admin's ID from session

// Process visitor request approval
if (isset($_POST['approve_visitor'])) {
    $request_id = $_POST['request_id'];
    $update_query = "UPDATE Visitor_Request SET status = 'Approved', admin_id = '$admin_id' WHERE request_id = '$request_id'";
    if ($conn->query($update_query) === TRUE) {
        echo "Visitor request approved!";
    } else {
        echo "Error approving request: " . $conn->error;
    }
}

// Process visitor request rejection
if (isset($_POST['reject_visitor'])) {
    $request_id = $_POST['request_id'];
    $update_query = "UPDATE Visitor_Request SET status = 'Rejected', admin_id = '$admin_id' WHERE request_id = '$request_id'";
    if ($conn->query($update_query) === TRUE) {
        echo "Visitor request rejected!";
    } else {
        echo "Error rejecting request: " . $conn->error;
    }
}
?>
