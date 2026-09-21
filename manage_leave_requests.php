<?php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle status updates
if (isset($_POST['update_status'])) {
    $request_id = $_POST['request_id'];
    $new_status = $_POST['status'];
    $admin_id = $_SESSION['admin_id']; // Get the current admin's ID from session
    
    // Sanitize inputs
    $request_id = mysqli_real_escape_string($conn, $request_id);
    $new_status = mysqli_real_escape_string($conn, $new_status);
    $admin_id = mysqli_real_escape_string($conn, $admin_id);
    
    // Update the status and admin_id in the database
    $update_sql = "UPDATE leave_request SET status = '$new_status', admin_id = '$admin_id' WHERE request_id = '$request_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        $status_message = "Status updated successfully!";
    } else {
        $status_error = "Error updating status: " . $conn->error;
    }
}

// Get all leave requests with student details and admin info using JOIN
$sql = "SELECT lr.*, u.st_name, u.st_contact, u.dept, a.admin_name as admin_name 
        FROM leave_request lr 
        JOIN user u ON lr.st_id = u.st_id 
        LEFT JOIN admin a ON lr.admin_id = a.admin_id
        ORDER BY lr.start_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Leave Requests - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .admin-container {
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .admin-header {
            background-color: #4e73df;
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            margin-bottom: 0;
        }
        .admin-header h2 {
            margin-bottom: 0;
            font-weight: 600;
        }
        .table-container {
            background-color: white;
            border-radius: 0 0 10px 10px;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .status-badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.875rem;
        }
        .pending {
            background-color: #f0f0f0;
            color: #6c757d;
        }
        .approved {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .denied {
            background-color: #f8d7da;
            color: #842029;
        }
        .leave-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 0.9rem;
        }
        .leave-info p {
            margin-bottom: 5px;
        }
        .student-details {
            margin-bottom: 5px;
            font-weight: 500;
        }
        .student-details i {
            width: 20px;
            text-align: center;
            margin-right: 5px;
            color: #4e73df;
        }
        .date-badge {
            background-color: #e9ecef;
            color: #495057;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 5px;
        }
        .action-form {
            display: flex;
            gap: 10px;
        }
        .table th {
            background-color: #4e73df;
            color: white;
        }
        .no-requests {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }
        .alert {
            margin-bottom: 20px;
        }
        .dept-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-left: 8px;
        }
        .cse {
            background-color: #d4edda;
            color: #155724;
        }
        .eee {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .mns {
            background-color: #fff3cd;
            color: #856404;
        }
        .bba {
            background-color: #f8d7da;
            color: #721c24;
        }
        .admin-info {
            font-size: 0.8rem;
            color: #6c757d;
            font-style: italic;
        }
        .date-range {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .date-range .fas {
            color: #4e73df;
        }
    </style>
</head>
<body>
    <div class="container admin-container">
        <div class="admin-header">
            <h2><i class="fas fa-calendar-alt me-2"></i> Manage Leave Requests</h2>
        </div>
        
        <div class="table-container">
            <?php if (isset($status_message)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $status_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($status_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php echo $status_error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($result && $result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student Details</th>
                                <th>Leave Information</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['request_id']; ?></td>
                                    <td>
                                        <div class="student-details">
                                            <p>
                                                <i class="fas fa-user"></i> 
                                                <?php echo htmlspecialchars($row['st_name']); ?> 
                                                (<?php echo $row['st_id']; ?>)
                                                <span class="dept-badge <?php echo strtolower($row['dept']); ?>">
                                                    <?php echo $row['dept']; ?>
                                                </span>
                                            </p>
                                            <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($row['st_contact']); ?></p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="leave-info">
                                            <div class="date-range">
                                                <span class="date-badge">
                                                    <i class="fas fa-calendar-day"></i> 
                                                    <?php echo date('M d, Y', strtotime($row['start_date'])); ?>
                                                </span>
                                                <i class="fas fa-arrow-right"></i>
                                                <span class="date-badge">
                                                    <i class="fas fa-calendar-check"></i> 
                                                    <?php echo date('M d, Y', strtotime($row['end_date'])); ?>
                                                </span>
                                            </div>
                                            <p class="mt-2"><strong>Reason:</strong> <?php echo htmlspecialchars($row['reason']); ?></p>
                                            <p class="mt-1"><strong>Duration:</strong> 
                                                <?php 
                                                    $start = new DateTime($row['start_date']);
                                                    $end = new DateTime($row['end_date']);
                                                    $interval = $start->diff($end);
                                                    echo $interval->days + 1 . ' day(s)';
                                                ?>
                                            </p>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <?php 
                                            $statusClass = '';
                                            $status = strtolower($row['status'] ?? 'pending');
                                            
                                            if ($status == 'approved') {
                                                $statusClass = 'approved';
                                            } elseif ($status == 'denied') {
                                                $statusClass = 'denied';
                                            } else {
                                                $statusClass = 'pending';
                                            }
                                        ?>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                        
                                        <?php if (!empty($row['admin_name']) && ($status == 'approved' || $status == 'denied')): ?>
                                            <div class="admin-info mt-2">
                                                <i class="fas fa-user-shield"></i> Handled by: <?php echo htmlspecialchars($row['admin_name']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="action-form">
                                            <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                            <select name="status" class="form-select form-select-sm" required>
                                                <option value="">Select Status</option>
                                                <option value="approved">Approve</option>
                                                <option value="denied">Deny</option>
                                                <option value="pending">Pending</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-primary btn-sm">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-requests">
                    <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                    <h4>No leave requests found</h4>
                    <p>There are currently no leave requests in the system.</p>
                </div>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="admin_dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>