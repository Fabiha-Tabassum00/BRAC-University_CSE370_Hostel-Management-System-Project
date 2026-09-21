<?php

session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Request Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Visitor Request Form</h2>
        <form action="submit_visitor_request.php" method="POST">
            <div class="mb-3">
                <label for="student_id" class="form-label">Student ID:</label>
                <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo isset($_SESSION['st_id']) ? $_SESSION['st_id'] : ''; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="visitor_name" class="form-label">Visitor Name:</label>
                <input type="text" class="form-control" id="visitor_name" name="visitor_name" required>
            </div>
            <div class="mb-3">
                <label for="visitation_date" class="form-label">Visitation Date:</label>
                <input type="date" class="form-control" id="visitation_date" name="visitation_date" required>
            </div>
            <div class="mb-3">
                <label for="visitation_purpose" class="form-label">Visitation Purpose:</label>
                <textarea class="form-control" id="visitation_purpose" name="visitation_purpose" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary" <?php echo (!isset($_SESSION['st_id']) || empty($_SESSION['st_id'])) ? 'disabled' : ''; ?>>Send Request</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>