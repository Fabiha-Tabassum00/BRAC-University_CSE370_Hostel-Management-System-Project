<?php
session_start();
include 'db.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['st_id'])) {
    header("Location: index.php");  // Redirect to homepage if not logged in
    exit();
}

// Get room details from POST request
if (isset($_POST['room_number'])) {
    $room_number = $_POST['room_number'];
    $st_id = $_SESSION['st_id'];  // Logged-in student ID

    // Fetch room details
    $query = "SELECT * FROM Room WHERE room_number = '$room_number' AND status = 'Available'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        // Room is available, proceed with booking
        $row = $result->fetch_assoc();
        $available_spots = $row['available_spots'];

        // Check if user already booked a room
        $check_booking = "SELECT * FROM Room WHERE st_id = '$st_id' AND status = 'Booked'";
        $booking_result = $conn->query($check_booking);

        if ($booking_result->num_rows > 0) {
            // User has already booked a room
            echo "<div class='alert alert-danger'>You have already booked a room! You cannot book another room.</div>";
        } else {
            if ($row['shared'] == 1 && $available_spots > 0) {
                // Decrease available spots for shared rooms
                $new_available_spots = $available_spots - 1;
                $update_room = "UPDATE Room SET status = 'Booked', st_id = '$st_id', available_spots = '$new_available_spots' WHERE room_number = '$room_number'";
            } elseif ($row['shared'] == 0) {
                // Book the single room
                $update_room = "UPDATE Room SET status = 'Booked', st_id = '$st_id' WHERE room_number = '$room_number'";
            } else {
                echo "<div class='alert alert-danger'>No available spots in this room!</div>";
                exit();
            }

            if ($conn->query($update_room) === TRUE) {
                echo "<div class='alert alert-success'>Room booked successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error updating room status: " . $conn->error . "</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Room not available or invalid selection.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>Invalid room selection.</div>";
}
?>
