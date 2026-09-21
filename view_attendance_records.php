<?php
// filepath: c:\xampp\htdocs\Hostel_Management_System\view_attendance_records.php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Get admin information
$admin_info_query = "SELECT admin_name FROM admin WHERE admin_id = '$admin_id'";
$admin_result = $conn->query($admin_info_query);
$admin_info = $admin_result->fetch_assoc();

// Set default date range (current month)
$start_date = date('Y-m-01'); // First day of current month
$end_date = date('Y-m-t');    // Last day of current month

// Handle date range filter
if (isset($_POST['filter_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
}

// Get all attendance records for all admins
$sql = "SELECT a.*, ad.admin_name 
        FROM admin_attendance a
        JOIN admin ad ON a.admin_id = ad.admin_id
        WHERE a.attendance_date BETWEEN '$start_date' AND '$end_date'
        ORDER BY a.attendance_date DESC, ad.admin_name ASC";

$result = $conn->query($sql);

// Calculate statistics
$total_days = 0;
$present_days = 0;

if ($result && $result->num_rows > 0) {
    $temp_result = $result->fetch_all(MYSQLI_ASSOC);
    $total_days = count($temp_result);
    
    foreach ($temp_result as $record) {
        if ($record['status'] == 'present') {
            $present_days++;
        }
    }
    
    // Reset the result pointer
    $result = $conn->query($sql);
}

// Calculate attendance percentage
$attendance_percentage = ($total_days > 0) ? round(($present_days / $total_days) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records - Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .records-container {
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
        .filter-form {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .stats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            flex: 1;
            min-width: 200px;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #4e73df;
            margin: 10px 0;
        }
        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .record-row {
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
        }
        .record-date {
            font-weight: 600;
            font-size: 1.1rem;
            color: #495057;
            margin-bottom: 5px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .present {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .absent {
            background-color: #f8d7da;
            color: #842029;
        }
        .late {
            background-color: #fff3cd;
            color: #664d03;
        }
        .admin-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
            background-color: #4e73df;
            margin-left: 10px;
        }
        .record-notes {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-style: italic;
            color: #6c757d;
        }
        .no-records {
            text-align: center;
            padding: 40px 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            color: #6c757d;
        }
        .date-badge {
            background-color: #e9ecef;
            border-radius: 5px;
            padding: 5px 10px;
            font-size: 0.9rem;
            color: #495057;
            margin-right: 5px;
        }
        .attendance-progress {
            height: 10px;
            margin-top: 5px;
            border-radius: 5px;
        }
        @media (max-width: 767px) {
            .stat-card {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container records-container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-clipboard-list me-2"></i> 
                    Staff Attendance Records
                </h2>
            </div>
            <div class="card-body">
                <div class="filter-form">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" name="filter_date" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i> Filter Records
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Stats Display -->
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-label">Total Days</div>
                        <div class="stat-value"><?php echo $total_days; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Present Days</div>
                        <div class="stat-value"><?php echo $present_days; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Attendance Percentage</div>
                        <div class="stat-value"><?php echo $attendance_percentage; ?>%</div>
                        <div class="progress attendance-progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $attendance_percentage; ?>%" 
                                aria-valuenow="<?php echo $attendance_percentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
                
                <h3 class="mb-3">
                    Attendance List
                </h3>
                
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($record = $result->fetch_assoc()): ?>
                        <div class="record-row">
                            <div class="record-date">
                                <?php echo date('l, F d, Y', strtotime($record['attendance_date'])); ?>
                                <span class="status-badge <?php echo strtolower($record['status']); ?>">
                                    <?php echo ucfirst($record['status']); ?>
                                </span>
                                
                                <!-- Always show admin name badge for attendance records -->
                                <span class="admin-badge">
                                    <?php echo htmlspecialchars($record['admin_name']); ?>
                                </span>
                            </div>
                            
                            <?php if (!empty($record['notes'])): ?>
                                <div class="record-notes">
                                    <i class="fas fa-sticky-note me-2"></i>
                                    <?php echo nl2br(htmlspecialchars($record['notes'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-records">
                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                        <h4>No attendance records found</h4>
                        <p>There are no records for the selected time period.</p>
                    </div>
                <?php endif; ?>
                
                <div class="mt-4">
                    <a href="manage_attendance.php" class="btn btn-primary me-2">
                        <i class="fas fa-user-check me-2"></i> Back to Attendance
                    </a>
                    <a href="admin_dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>