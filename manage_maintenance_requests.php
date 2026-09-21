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
    $req_id = $_POST['req_id'];
    $new_status = $_POST['status'];
    $admin_id = $_SESSION['admin_id']; // Get the current admin's ID from session
    
    // Sanitize inputs
    $req_id = mysqli_real_escape_string($conn, $req_id);
    $new_status = mysqli_real_escape_string($conn, $new_status);
    $admin_id = mysqli_real_escape_string($conn, $admin_id);
    
    // Update the status and admin_id in the database
    $update_sql = "UPDATE maintenance_req SET status = '$new_status', admin_id = '$admin_id' WHERE req_id = '$req_id'";
    
    if ($conn->query($update_sql) === TRUE) {
        $status_message = "Status updated successfully!";
    } else {
        $status_error = "Error updating status: " . $conn->error;
    }
}

// Get all maintenance requests
$sql = "SELECT m.*, a.admin_name 
        FROM maintenance_req m
        LEFT JOIN admin a ON m.admin_id = a.admin_id
        ORDER BY m.request_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Maintenance Requests - Admin Panel</title>
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
        .processing {
            background-color: #cfe2ff;
            color: #0a58ca;
        }
        .resolved {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .request-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 0.9rem;
        }
        .request-info p {
            margin-bottom: 5px;
        }
        .issue-type {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: 500;
            margin-bottom: 8px;
            background-color: #e9ecef;
        }
        .plumbing {
            border-left: 4px solid #20c997;
        }
        .electrical {
            border-left: 4px solid #fd7e14;
        }
        .furniture {
            border-left: 4px solid #6f42c1;
        }
        .cleaning {
            border-left: 4px solid #0dcaf0;
        }
        .other {
            border-left: 4px solid #6c757d;
        }
        .room-badge {
            background-color: #4e73df;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 0.9rem;
            display: inline-block;
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
        .admin-info {
            font-size: 0.8rem;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container admin-container">
        <div class="admin-header">
            <h2><i class="fas fa-tools me-2"></i> Manage Maintenance Requests</h2>
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
                                <th>Room</th>
                                <th>Request Details</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['req_id']; ?></td>
                                    <td>
                                        <span class="room-badge">
                                            <i class="fas fa-door-open me-1"></i> 
                                            Room <?php echo htmlspecialchars($row['room_number']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="request-info <?php echo strtolower($row['issue_type']); ?>">
                                            <div class="issue-type">
                                                <?php 
                                                    $icon = 'wrench';
                                                    switch(strtolower($row['issue_type'])) {
                                                        case 'plumbing':
                                                            $icon = 'faucet';
                                                            break;
                                                        case 'electrical':
                                                            $icon = 'bolt';
                                                            break;
                                                        case 'furniture':
                                                            $icon = 'couch';
                                                            break;
                                                        case 'cleaning':
                                                            $icon = 'broom';
                                                            break;
                                                    }
                                                ?>
                                                <i class="fas fa-<?php echo $icon; ?> me-1"></i>
                                                <?php echo htmlspecialchars($row['issue_type']); ?>
                                            </div>
                                            <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                                            <p><span class="date-badge">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                Reported: <?php echo date('M d, Y', strtotime($row['request_date'])); ?>
                                            </span></p>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <?php 
                                            $statusClass = '';
                                            $status = strtolower($row['status'] ?? 'pending');
                                            
                                            if ($status == 'resolved') {
                                                $statusClass = 'resolved';
                                            } elseif ($status == 'processing') {
                                                $statusClass = 'processing';
                                            } else {
                                                $statusClass = 'pending';
                                            }
                                        ?>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                        
                                        <?php if (!empty($row['admin_name']) && ($status == 'resolved' || $status == 'processing')): ?>
                                            <div class="admin-info mt-2">
                                                <i class="fas fa-user-shield"></i> Handled by: <?php echo htmlspecialchars($row['admin_name']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="action-form">
                                            <input type="hidden" name="req_id" value="<?php echo $row['req_id']; ?>">
                                            <select name="status" class="form-select form-select-sm" required>
                                                <option value="">Select Status</option>
                                                <option value="pending">Pending</option>
                                                <option value="processing">Processing</option>
                                                <option value="resolved">Resolved</option>
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
                    <i class="fas fa-tools fa-3x mb-3"></i>
                    <h4>No maintenance requests found</h4>
                    <p>There are currently no maintenance requests in the system.</p>
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