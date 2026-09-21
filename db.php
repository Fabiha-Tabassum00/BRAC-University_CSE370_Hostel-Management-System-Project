<?php
$servername = "localhost"; // Use 'localhost' if you're using XAMPP/WAMP
$username = "root"; // Default username for XAMPP and WAMP
$password = ""; // Default password for XAMPP and WAMP
$dbname = "hostel_management"; // Name of the database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connection successful"; // Ensure this line is within quotes
}
?>
