<?php
session_start();
include 'db.php'; // Include database connection

// Redirect to login if not logged in
if (!isset($_SESSION['st_id']) || empty($_SESSION['st_id'])) {
    header("Location: studentlogin.php");
    exit();
}

// For handling form submission
$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $st_id = $_SESSION['st_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];
    
    // Simplified date validation
    if ($end_date < $start_date) {
        $error_message = "End date cannot be before start date.";
    } else {
        // Sanitize inputs
        $st_id = mysqli_real_escape_string($conn, $st_id);
        $start_date = mysqli_real_escape_string($conn, $start_date);
        $end_date = mysqli_real_escape_string($conn, $end_date);
        $reason = mysqli_real_escape_string($conn, $reason);
        
        // Insert into database
        $sql = "INSERT INTO leave_request (st_id, start_date, end_date, reason, status) 
                VALUES ('$st_id', '$start_date', '$end_date', '$reason', 'pending')";
        
        if ($conn->query($sql) === TRUE) {
            $success_message = "Your leave request has been submitted successfully!";
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Request - Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .leave-request-container {
            max-width: 700px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        .leave-request-header {
            text-align: center;
            margin-bottom: 30px;
            color: #3a3a3a;
        }
        .leave-request-header h2 {
            margin-bottom: 10px;
            font-weight: 600;
        }
        .leave-request-header p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #495057;
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
        }
        .alert {
            border-radius: 8px;
            padding: 15px 20px;
        }
        .date-range {
            display: flex;
            gap: 20px;
        }
        .date-range .form-group {
            flex: 1;
        }
        .student-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #4e73df;
        }
        .student-info p {
            margin-bottom: 0;
            font-weight: 500;
        }
        .student-info span {
            font-weight: normal;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="leave-request-container">
            <div class="leave-request-header">
                <h2>Leave Request Form</h2>
                <p>Request permission for temporary absence from the hostel</p>
            </div>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="student-info">
                <p>Student ID: <span><?php echo htmlspecialchars($_SESSION['st_id']); ?></span></p>
                <?php if (isset($_SESSION['st_name'])): ?>
                    <p>Name: <span><?php echo htmlspecialchars($_SESSION['st_name']); ?></span></p>
                <?php endif; ?>
            </div>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="date-range">
                    <div class="form-group mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                </div>
                
                <div class="form-group mb-4">
                    <label for="reason" class="form-label">Reason for Leave</label>
                    <textarea class="form-control" id="reason" name="reason" rows="5" required placeholder="Please provide detailed reason for your leave request"></textarea>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Submit Leave Request</button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <a href="student_dashboard.php" class="text-decoration-none">Back to Dashboard</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript to ensure end date is not before start date
        document.getElementById('start_date').addEventListener('change', function() {
        document.getElementById('end_date').min = this.value;
        });
    </script>
</body>
</html>