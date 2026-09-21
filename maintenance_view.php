<?php
include 'db.php';
// Remove the connection echo from db.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Maintenance Requests - Hostel Management System</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .pending {
            background-color: #ffeeba;
            color: #856404;
        }
        .completed {
            background-color: #d4edda;
            color: #155724;
        }
        .in-progress {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
            text-align: center;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .no-requests {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Maintenance Requests</h1>
        
        <?php
        // Query to get all maintenance requests
        $sql = "SELECT * FROM maintenance_req ORDER BY request_date DESC";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Request ID</th>';
            echo '<th>Room Number</th>';
            echo '<th>Issue Type</th>';
            echo '<th>Description</th>';
            echo '<th>Request Date</th>';
            echo '<th>Status</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['req_id'] . '</td>';
                echo '<td>' . $row['room_number'] . '</td>';
                
                // If you don't have issue_type in your table, you can remove this line
                echo '<td>' . (isset($row['issue_type']) ? $row['issue_type'] : 'N/A') . '</td>';
                
                // If you don't have description in your table, you can remove this line
                echo '<td>' . (isset($row['description']) ? $row['description'] : 'N/A') . '</td>';
                
                echo '<td>' . date('M d, Y ', strtotime($row['request_date'])) . '</td>';
                
                // Status with different styling based on the status value
                $statusClass = '';
                switch(strtolower($row['status'])) {
                    case 'pending':
                        $statusClass = 'pending';
                        break;
                    case 'completed':
                        $statusClass = 'completed';
                        break;
                    case 'in progress':
                    case 'in-progress':
                        $statusClass = 'in-progress';
                        break;
                    case 'rejected':
                        $statusClass = 'rejected';
                        break;
                    default:
                        $statusClass = '';
                }
                
                echo '<td><span class="status ' . $statusClass . '">' . ucfirst($row['status']) . '</span></td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<div class="no-requests">No maintenance requests found.</div>';
        }
        ?>
        
        <a href="maintenance_request.php" class="back-link">Submit a New Request</a>
    </div>
</body>
</html>