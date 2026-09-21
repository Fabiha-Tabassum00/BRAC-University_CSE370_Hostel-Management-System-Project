<?php
session_start();

// Ensure the user is logged in as an admin
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");  // Redirect to admin login if not logged in as admin
    exit();
}

// Fetch admin details
$admin_name = $_SESSION['admin_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Welcome, <?php echo $admin_name; ?>!</h1>
        <div class="row">
            <!-- Admin functionalities like handling requests -->
            <div class="col-md-4">
                <a href="manage_visitor_requests.php" class="btn btn-primary w-100 mb-2">Manage Visitor Requests</a>
            </div>
            <div class="col-md-4">
                <a href="manage_maintenance_requests.php" class="btn btn-primary w-100 mb-2">Manage Maintenance Requests</a>
            </div>
            <div class="col-md-4">
                <a href="manage_leave_requests.php" class="btn btn-primary w-100 mb-2">Manage Leave Requests</a>
            </div>
            <div class="col-md-4">
                <a href="manage_room_bookings.php" class="btn btn-primary w-100 mb-2">Manage Room Bookings</a>
            </div>
            <div class="col-md-4">
                <a href="manage_students.php" class="btn btn-primary w-100 mb-2">Manage Students</a>
            </div>
            <div class="col-md-4">
                <a href="manage_attendance.php" class="btn btn-primary w-100 mb-2">Manage Attendance</a>
            </div>
        </div>
    </div>
</body>
</html>
