<?php
include 'db.php';
// Remove the connection echo from db.php

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $room_number = mysqli_real_escape_string($conn, $_POST['room_number']);
    $issue_type = mysqli_real_escape_string($conn, $_POST['issue_type']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Check if room number exists in the room table
    $check_room = "SELECT room_number FROM room WHERE room_number = '$room_number'";
    $room_result = $conn->query($check_room);
    
    if ($room_result && $room_result->num_rows > 0) {
        // Room exists, proceed with insert
        
        // Get current date
        $request_date = date("Y-m-d H:i:s");
        
        // Prepare and execute the insert query
        $sql = "INSERT INTO maintenance_req (room_number, request_date, status, issue_type, description) 
                VALUES ('$room_number', '$request_date', 'pending', '$issue_type', '$description')";
        
        if ($conn->query($sql) === TRUE) {
            // Redirect with success message
            header("Location: maintenance_request.php?status=success");
            exit();
        } else {
            // Redirect with error message
            header("Location: maintenance_request.php?status=error&message=" . urlencode($conn->error));
            exit();
        }
    } else {
        // Room doesn't exist
        header("Location: maintenance_request.php?status=error&message=" . urlencode("Room number does not exist. Please enter a valid room number."));
        exit();
    }
} else {
    // If someone tries to access this file directly, redirect to the form
    header("Location: maintenance_request.php");
    exit();
}
?>