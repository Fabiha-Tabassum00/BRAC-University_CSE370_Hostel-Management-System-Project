<?php
include 'db.php';
// Comment out connection message in db.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Hostel Management System</title>
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
            max-width: 1200px;
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
        .filter-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .filter-form {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .filter-form label {
            font-weight: bold;
            margin-right: 5px;
        }
        .filter-form select, .filter-form input {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            font-size: 16px;
        }
        .filter-form button {
            padding: 8px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .filter-form button:hover {
            background-color: #2980b9;
        }
        .reset-link {
            display: inline-block;
            margin-left: 10px;
            color: #3498db;
            text-decoration: none;
        }
        .reset-link:hover {
            text-decoration: underline;
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
        .dept-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
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
        .no-students {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .student-count {
            text-align: right;
            margin-top: 10px;
            font-weight: bold;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Students</h1>
        
        <div class="filter-section">
            <form class="filter-form" method="GET" action="">
                <div>
                    <label for="st_id_filter">Student ID:</label>
                    <input type="text" name="st_id" id="st_id_filter" placeholder="Enter ID" value="<?php echo isset($_GET['st_id']) ? htmlspecialchars($_GET['st_id']) : ''; ?>">
                </div>
                <div>
                    <label for="dept_filter">Department:</label>
                    <select name="dept" id="dept_filter">
                        <option value="">All Departments</option>
                        <option value="CSE" <?php echo (isset($_GET['dept']) && $_GET['dept'] == 'CSE') ? 'selected' : ''; ?>>CSE</option>
                        <option value="EEE" <?php echo (isset($_GET['dept']) && $_GET['dept'] == 'EEE') ? 'selected' : ''; ?>>EEE</option>
                        <option value="MNS" <?php echo (isset($_GET['dept']) && $_GET['dept'] == 'MNS') ? 'selected' : ''; ?>>MNS</option>
                        <option value="BBA" <?php echo (isset($_GET['dept']) && $_GET['dept'] == 'BBA') ? 'selected' : ''; ?>>BBA</option>
                    </select>
                </div>
                <button type="submit">Apply Filter</button>
                <a href="manage_students.php" class="reset-link">Reset Filter</a>
            </form>
        </div>
        
        <?php
        // Build SQL query based on filters
        $sql = "SELECT * FROM user WHERE 1=1";
        
        // If student ID filter is applied
        if (isset($_GET['st_id']) && !empty($_GET['st_id'])) {
            $st_id = mysqli_real_escape_string($conn, $_GET['st_id']);
            $sql .= " AND st_id = '$st_id'";
        }
        
        // If department filter is applied
        if (isset($_GET['dept']) && !empty($_GET['dept'])) {
            $dept = mysqli_real_escape_string($conn, $_GET['dept']);
            $sql .= " AND dept = '$dept'";
        }
        
        // Add ordering
        $sql .= " ORDER BY st_id ASC";
        
        $result = $conn->query($sql);
        
        // Count results for display
        $student_count = $result ? $result->num_rows : 0;
        
        if ($result && $student_count > 0) {
            echo '<div class="student-count">' . $student_count . ' student(s) found</div>';
            
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th>Name</th>';
            echo '<th>Email</th>';
            echo '<th>Department</th>';
            echo '<th>Phone</th>';
            
            // Add more columns as needed based on your user table structure
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['st_id'] . '</td>';
                echo '<td>' . (isset($row['st_name']) ? htmlspecialchars($row['st_name']) : 'N/A') . '</td>';
                echo '<td>' . (isset($row['email']) ? htmlspecialchars($row['email']) : 'N/A') . '</td>';
                
                // Department with badge styling
                $deptClass = strtolower($row['dept']);
                echo '<td><span class="dept-badge ' . $deptClass . '">' . $row['dept'] . '</span></td>';
                
                echo '<td>' . (isset($row['st_contact']) ? htmlspecialchars($row['st_contact']) : 'N/A') . '</td>';
                
                // Add more columns as needed
                
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<div class="no-students">No students found matching the criteria.</div>';
        }
        ?>
    </div>
</body>
</html>