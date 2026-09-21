<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['st_id'])) {
    header("Location: index.php");  // Redirect to homepage if not logged in
    exit();
}

include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Gradient background for the page */
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(120deg, #800000, #f4f4f4); /* Maroon and Off-white gradient */
            background-attachment: fixed;
        }
        
        .container {
            margin-top: 50px;
        }

        .btn-primary {
            background-color: #800000;
            border-color: #800000;
        }

        .btn-primary:hover {
            background-color: #5b0000;
            border-color: #5b0000;
        }

        .dashboard-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .dashboard-card h3 {
            color: #800000;
        }

        .dashboard-card a {
            color: white;
            text-decoration: none;
            font-size: 18px;
        }

        .dashboard-card .btn-block {
            margin-bottom: 10px;
        }

        .dashboard-card p {
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-white mb-4">Welcome, <?php echo $_SESSION['st_name']; ?>!</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h3>Room Booking</h3>
                    <p>Book your room in the hostel easily.</p>
                    <a href="room_booking.php" class="btn btn-primary btn-block">Go to Room Booking</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h3>Maintenance Request</h3>
                    <p>Request maintenance for your room or facilities.</p>
                    <a href="maintenance_request.php" class="btn btn-primary btn-block">Go to Maintenance Request</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h3>Visitor Request</h3>
                    <p>Submit your visitor request here.</p>
                    <a href="visitor_request.php" class="btn btn-primary btn-block">Go to Visitor Request</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="dashboard-card">
                    <h3>Leave Request</h3>
                    <p>Request leave from the hostel.</p>
                    <a href="leave_request.php" class="btn btn-primary btn-block">Go to Leave Request</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
