<?php
// filepath: c:\xampp\htdocs\Hostel_Management_System\manage_attendance.php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$today = date('Y-m-d');
$success_message = '';
$error_message = '';

// Get admin information
$admin_info_query = "SELECT admin_name FROM admin WHERE admin_id = '$admin_id'";
$admin_result = $conn->query($admin_info_query);
$admin_info = $admin_result->fetch_assoc();

// Check if admin has already marked attendance today
$check_query = "SELECT * FROM admin_attendance WHERE admin_id = '$admin_id' AND attendance_date = '$today'";
$check_result = $conn->query($check_query);
$already_marked = ($check_result && $check_result->num_rows > 0);
$attendance_record = $already_marked ? $check_result->fetch_assoc() : null;

// Handle attendance marking
if (isset($_POST['mark_attendance']) && !$already_marked) {
    $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
    
    // Sanitize inputs
    $admin_id = mysqli_real_escape_string($conn, $admin_id);
    $today = mysqli_real_escape_string($conn, $today);
    $notes = mysqli_real_escape_string($conn, $notes);
    
    $insert_query = "INSERT INTO admin_attendance (admin_id, attendance_date, notes, status) 
                     VALUES ('$admin_id', '$today', '$notes', 'present')";
    
    if ($conn->query($insert_query) === TRUE) {
        $success_message = "Your attendance has been marked for today.";
        // Reload the page to update the status
        header("Location: manage_attendance.php?success=marked");
        exit();
    } else {
        $error_message = "Error recording attendance: " . $conn->error;
    }
}

// Handle success messages from redirects
if (isset($_GET['success']) && $_GET['success'] == 'marked') {
    $success_message = "Your attendance has been marked successfully.";
}

// Reload the check result for the latest status
$check_result = $conn->query($check_query);
$already_marked = ($check_result && $check_result->num_rows > 0);
$attendance_record = $already_marked ? $check_result->fetch_assoc() : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Attendance - Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .attendance-container {
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .card-header {
            background-color: #4e73df;
            color: white;
            padding: 20px;
            border-bottom: none;
        }
        .card-title {
            margin-bottom: 0;
            font-weight: 600;
        }
        .admin-profile {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .admin-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #4e73df;
        }
        .attendance-status {
            background-color: #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .attendance-status.marked {
            background-color: #d1e7dd;
            border-left: 5px solid #198754;
        }
        .attendance-status.unmarked {
            background-color: #f8d7da;
            border-left: 5px solid #dc3545;
        }
        .time-display {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
            color: #212529;
        }
        .date-display {
            font-size: 1.2rem;
            margin-bottom: 15px;
            color: #495057;
        }
        .btn-mark {
            padding: 10px 30px;
            font-weight: 500;
            border-radius: 30px;
            font-size: 1.1rem;
            margin-top: 10px;
            background-color: #198754;
            border-color: #198754;
            transition: all 0.3s;
        }
        .btn-mark:hover {
            background-color: #157347;
            border-color: #157347;
        }
        .notes-field {
            margin-top: 15px;
        }
        .view-records-btn {
            background-color: #4e73df;
            border-color: #4e73df;
            padding: 8px 20px;
            font-weight: 500;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .view-records-btn:hover {
            background-color: #375bc8;
            border-color: #375bc8;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 15px;
            font-size: 1rem;
        }
        .present {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        @media (max-width: 767px) {
            .time-display {
                font-size: 2rem;
            }
            .date-display {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container attendance-container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-user-check me-2"></i> Staff Attendance</h2>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <?php echo $success_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?php echo $error_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-center mb-4">
                            <a href="view_attendance_records.php" class="btn btn-primary view-records-btn">
                                <i class="fas fa-list-alt me-2"></i> View Attendance Records
                            </a>
                        </div>
                        
                        <div class="admin-profile">
                            <div class="admin-name">
                                <i class="fas fa-user-shield me-2"></i> 
                                <?php echo htmlspecialchars($admin_info['admin_name']); ?>
                            </div>
                        </div>
                        
                        <div class="attendance-status <?php echo $already_marked ? 'marked' : 'unmarked'; ?>">
                            <div class="date-display">
                                <i class="far fa-calendar-alt me-2"></i>
                                <?php echo date('l, F d, Y'); ?>
                            </div>
                            
                            <div class="time-display" id="current-time">
                                <?php echo date('h:i:s A'); ?>
                            </div>
                            
                            <?php if (!$already_marked): ?>
                                <!-- Mark Attendance Form -->
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                    <div class="notes-field">
                                        <textarea class="form-control" name="notes" rows="2" placeholder="Add notes (optional)"></textarea>
                                    </div>
                                    <button type="submit" name="mark_attendance" class="btn btn-primary btn-mark">
                                        <i class="fas fa-check-circle me-2"></i> Mark Attendance
                                    </button>
                                </form>
                            <?php else: ?>
                                <!-- Display Attendance Marked -->
                                <div class="status-badge present">
                                    <i class="fas fa-check-circle me-2"></i> Attendance Marked
                                </div>
                                
                                <?php if (!empty($attendance_record['notes'])): ?>
                                    <div class="alert alert-info mt-3">
                                        <strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($attendance_record['notes'])); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-4">
                            <a href="admin_dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live clock update
        function updateClock() {
            const now = new Date();
            const timeDisplay = document.getElementById('current-time');
            timeDisplay.textContent = now.toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit', 
                second: '2-digit', 
                hour12: true 
            });
        }
        
        // Update clock every second
        setInterval(updateClock, 1000);
        updateClock(); // Initial update
    </script>
</body>
</html>