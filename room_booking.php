<?php
// filepath: c:\xampp\htdocs\Hostel_Management_System\room_booking.php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['st_id'])) {
    header("Location: login.php");
    exit();
}

$st_id = $_SESSION['st_id'];
$success_message = '';
$error_message = '';

// Get student information
$student_query = "SELECT * FROM user WHERE st_id = '$st_id'";
$student_result = $conn->query($student_query);
$student_info = $student_result->fetch_assoc();

// Check if student already has a room
$check_room_query = "SELECT * FROM room WHERE st_id = '$st_id'";
$check_room_result = $conn->query($check_room_query);
$has_room = ($check_room_result && $check_room_result->num_rows > 0);
$current_room = $has_room ? $check_room_result->fetch_assoc() : null;

// Handle room booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_room'])) {
    $room_number = $_POST['room_number'];
    
    // Sanitize input
    $room_number = mysqli_real_escape_string($conn, $room_number);
    $st_id = mysqli_real_escape_string($conn, $st_id);
    
    // If student has a room, first leave that room
    if ($has_room) {
        $current_room_number = $current_room['room_number'];
        
        // For single rooms, just mark as available
        if ($current_room['single'] == 1) {
            $update_current_query = "UPDATE room 
                                    SET st_id = NULL, 
                                        status = 'available', 
                                        available_spots = 1 
                                    WHERE room_number = '$current_room_number'";
        } 
        // For shared rooms, increment available spots
        else if ($current_room['shared'] == 1) {
            $new_spots = $current_room['available_spots'] + 1;
            $update_current_query = "UPDATE room 
                                    SET st_id = NULL, 
                                        status = 'available', 
                                        available_spots = '$new_spots' 
                                    WHERE room_number = '$current_room_number'";
        }
        
        if (!$conn->query($update_current_query)) {
            $error_message = "Error releasing current room: " . $conn->error;
            // Continue with booking only if current room was successfully released
            exit();
        }
    }
    
    // Now book the new room
    // Get room information
    $room_query = "SELECT * FROM room WHERE room_number = '$room_number' AND status = 'available'";
    $room_result = $conn->query($room_query);
    
    if ($room_result && $room_result->num_rows > 0) {
        $room = $room_result->fetch_assoc();
        
        // For single rooms
        if ($room['single'] == 1) {
            $update_query = "UPDATE room 
                            SET st_id = '$st_id', 
                                status = 'occupied', 
                                available_spots = 0 
                            WHERE room_number = '$room_number'";
                            
            if ($conn->query($update_query) === TRUE) {
                $success_message = "You have successfully booked room $room_number!";
                // Refresh the page to show updated info
                header("Location: room_booking.php?success=1");
                exit();
            } else {
                $error_message = "Error booking room: " . $conn->error;
            }
        } 
        // For shared rooms
        else if ($room['shared'] == 1) {
            $new_spots = $room['available_spots'] - 1;
            $new_status = ($new_spots <= 0) ? 'occupied' : 'available';
            
            // For first student in shared room
            if ($room['st_id'] === NULL) {
                $update_query = "UPDATE room 
                                SET st_id = '$st_id', 
                                    available_spots = '$new_spots',
                                    status = '$new_status'
                                WHERE room_number = '$room_number'";
                                
                if ($conn->query($update_query) === TRUE) {
                    $success_message = "You have successfully booked room $room_number!";
                    header("Location: room_booking.php?success=1");
                    exit();
                } else {
                    $error_message = "Error booking room: " . $conn->error;
                }
            } 
            // For second student in shared room
            else {
                $error_message = "This room is already assigned to another student. Please select another room.";
            }
        }
    } else {
        $error_message = "Selected room is not available for booking.";
    }
}

// Handle leaving current room (without booking new one)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['leave_room'])) {
    if ($has_room) {
        $current_room_number = $current_room['room_number'];
        
        // For single rooms, just mark as available
        if ($current_room['single'] == 1) {
            $update_query = "UPDATE room 
                            SET st_id = NULL, 
                                status = 'available', 
                                available_spots = 1 
                            WHERE room_number = '$current_room_number'";
        } 
        // For shared rooms, increment available spots
        else if ($current_room['shared'] == 1) {
            $new_spots = $current_room['available_spots'] + 1;
            $update_query = "UPDATE room 
                            SET st_id = NULL, 
                                status = 'available', 
                                available_spots = '$new_spots' 
                            WHERE room_number = '$current_room_number'";
        }
        
        if ($conn->query($update_query) === TRUE) {
            $success_message = "You have successfully vacated room $current_room_number. You can now book a new room.";
            header("Location: room_booking.php?vacated=1");
            exit();
        } else {
            $error_message = "Error leaving room: " . $conn->error;
        }
    } else {
        $error_message = "You don't have any room assigned to leave.";
    }
}

// Handle success messages from redirects
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $success_message = "You have successfully booked a room!";
    // Refresh student's room information
    $check_room_result = $conn->query($check_room_query);
    $has_room = ($check_room_result && $check_room_result->num_rows > 0);
    $current_room = $has_room ? $check_room_result->fetch_assoc() : null;
} else if (isset($_GET['vacated']) && $_GET['vacated'] == '1') {
    $success_message = "You have successfully vacated your room. You can now book a new room.";
    // Refresh student's room information
    $check_room_result = $conn->query($check_room_query);
    $has_room = ($check_room_result && $check_room_result->num_rows > 0);
    $current_room = $has_room ? $check_room_result->fetch_assoc() : null;
}

// Get all available rooms
$rooms_query = "SELECT * FROM room WHERE status = 'available' ORDER BY room_number";
$rooms_result = $conn->query($rooms_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking - Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .booking-container {
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 25px;
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
        .student-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .student-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #4e73df;
            margin-bottom: 5px;
        }
        .student-id {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .current-room {
            background-color: #d1e7dd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 5px solid #198754;
        }
        .current-room-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #198754;
            margin-bottom: 10px;
        }
        .current-room-details {
            font-size: 0.9rem;
            color: #495057;
            margin-bottom: 5px;
        }
        .room-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .room-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #4e73df;
            margin-bottom: 5px;
        }
        .room-type {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
            margin-bottom: 10px;
            background-color: #e9ecef;
            color: #495057;
        }
        .room-fee {
            font-size: 1.2rem;
            font-weight: 600;
            color: #198754;
            margin-bottom: 15px;
        }
        .room-details {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .available-spots {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 0.85rem;
            background-color: #cfe2ff;
            color: #084298;
            margin-left: 10px;
        }
        .booking-section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .btn-book {
            padding: 8px 20px;
            font-weight: 500;
            border-radius: 5px;
            background-color: #4e73df;
            border-color: #4e73df;
            transition: all 0.2s;
        }
        .btn-book:hover {
            background-color: #3756a4;
            border-color: #3756a4;
        }
        .btn-leave {
            padding: 8px 20px;
            font-weight: 500;
            border-radius: 5px;
            background-color: #dc3545;
            border-color: #dc3545;
            transition: all 0.2s;
        }
        .btn-leave:hover {
            background-color: #bb2d3b;
            border-color: #bb2d3b;
        }
        .no-rooms {
            text-align: center;
            padding: 40px 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            color: #6c757d;
        }
        .filter-container {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .modal-header {
            background-color: #4e73df;
            color: white;
        }
        .modal-header-danger {
            background-color: #dc3545;
            color: white;
        }
        .modal-room-details {
            margin-bottom: 20px;
        }
        .modal-room-info {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .modal-room-type {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .modal-room-fee {
            font-size: 1.2rem;
            font-weight: 600;
            color: #198754;
            margin: 10px 0;
        }
        .booking-terms {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 0.9rem;
        }
        .room-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }
        @media (max-width: 767px) {
            .room-card {
                margin-bottom: 15px;
            }
            .room-actions {
                flex-direction: column;
                gap: 10px;
            }
            .room-actions .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container booking-container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-bed me-2"></i> Room Booking
                </h2>
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
                
                <!-- Student Information -->
                <div class="student-info">
                    <div class="student-name">
                        <i class="fas fa-user-graduate me-2"></i> <?php echo $student_info['st_name']; ?>
                    </div>
                    <div class="student-id">
                        Student ID: <?php echo $student_info['st_id']; ?>
                    </div>
                </div>
                
                <!-- Current Room Information (if assigned) -->
                <?php if ($has_room): ?>
                    <div class="current-room">
                        <div class="current-room-title">
                            <i class="fas fa-home me-2"></i> Your Current Room
                        </div>
                        <div class="current-room-details">
                            <strong>Room Number:</strong> <?php echo $current_room['room_number']; ?>
                        </div>
                        <div class="current-room-details">
                            <strong>Room Type:</strong> 
                            <?php if ($current_room['single'] == 1): ?>
                                Single Room
                            <?php elseif ($current_room['shared'] == 1): ?>
                                Shared Room
                            <?php endif; ?>
                        </div>
                        <div class="current-room-details">
                            <strong>Monthly Fee:</strong> $<?php echo number_format($current_room['fee'], 2); ?>
                        </div>
                        <div class="room-actions">
                            <button class="btn btn-danger btn-leave" data-bs-toggle="modal" data-bs-target="#leaveRoomModal">
                                <i class="fas fa-door-open me-2"></i> Leave Room
                            </button>
                            
                            <a href="student_dashboard.php" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                    
                    <!-- Available Rooms Section (for changing rooms) -->
                    <div class="booking-section-title mt-4">
                        <i class="fas fa-exchange-alt me-2"></i> Change Your Room
                    </div>
                    <p class="text-muted mb-4">If you'd like to change your room, you can select from the available options below. Your current room will be released automatically when you book a new one.</p>
                <?php else: ?>
                    <!-- Available Rooms Section (for new booking) -->
                    <div class="booking-section-title">
                        <i class="fas fa-door-open me-2"></i> Available Rooms
                    </div>
                <?php endif; ?>
                
                <!-- Filter Controls -->
                <div class="filter-container">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="typeFilter" class="form-label">Filter by Room Type:</label>
                            <select class="form-select" id="typeFilter">
                                <option value="all">All Types</option>
                                <option value="single">Single Rooms</option>
                                <option value="shared">Shared Rooms</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="priceFilter" class="form-label">Sort by Price:</label>
                            <select class="form-select" id="priceFilter">
                                <option value="default">Default</option>
                                <option value="low-high">Price: Low to High</option>
                                <option value="high-low">Price: High to Low</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Rooms Display -->
                <div class="row" id="roomsContainer">
                    <?php if ($rooms_result && $rooms_result->num_rows > 0): ?>
                        <?php while ($room = $rooms_result->fetch_assoc()): ?>
                            <div class="col-lg-4 col-md-6 mb-4 room-item" 
                                 data-room-type="<?php echo ($room['single'] == 1) ? 'single' : 'shared'; ?>"
                                 data-price="<?php echo $room['fee']; ?>">
                                <div class="card h-100 room-card">
                                    <div class="card-body">
                                        <div class="room-number">Room <?php echo $room['room_number']; ?></div>
                                        <div class="room-type">
                                            <?php if ($room['single'] == 1): ?>
                                                <i class="fas fa-user me-1"></i> Single Room
                                            <?php elseif ($room['shared'] == 1): ?>
                                                <i class="fas fa-users me-1"></i> Shared Room
                                                <span class="available-spots">
                                                    <i class="fas fa-bed me-1"></i> 
                                                    <?php echo $room['available_spots']; ?> spot<?php echo $room['available_spots'] > 1 ? 's' : ''; ?> available
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="room-fee">
                                            <i class="fas fa-dollar-sign me-1"></i> 
                                            <?php echo number_format($room['fee'], 2); ?> per month
                                        </div>
                                        
                                        <div class="mt-3">
                                            <button class="btn btn-primary btn-book" data-bs-toggle="modal" 
                                                    data-bs-target="#bookRoomModal"
                                                    data-room-number="<?php echo $room['room_number']; ?>"
                                                    data-room-type="<?php echo ($room['single'] == 1) ? 'Single' : 'Shared'; ?>"
                                                    data-room-fee="<?php echo number_format($room['fee'], 2); ?>"
                                                    data-available-spots="<?php echo $room['available_spots']; ?>">
                                                <i class="fas fa-check-circle me-2"></i> 
                                                <?php echo $has_room ? 'Change to This Room' : 'Book This Room'; ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="no-rooms">
                                <i class="fas fa-exclamation-circle fa-3x mb-3 text-muted"></i>
                                <h4>No Available Rooms</h4>
                                <p>There are currently no rooms available for booking. Please check back later or contact the administration.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="no-rooms d-none" id="noFilteredRooms">
                    <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                    <h4>No Rooms Found</h4>
                    <p>No rooms match your filter criteria. Please try different options.</p>
                </div>
                
                <div class="mt-4">
                    <a href="student_dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Book Room Modal -->
    <div class="modal fade" id="bookRoomModal" tabindex="-1" aria-labelledby="bookRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookRoomModalLabel">Confirm Room Booking</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="modal_room_number" name="room_number">
                        
                        <div class="modal-room-details">
                            <div class="modal-room-info">
                                Room <span id="modal_display_room"></span>
                            </div>
                            <div class="modal-room-type">
                                <span id="modal_display_type"></span>
                                <span id="modal_display_spots" class="d-none">
                                    (<span id="modal_spots_count"></span> spot available)
                                </span>
                            </div>
                            <div class="modal-room-fee">
                                $<span id="modal_display_fee"></span> per month
                            </div>
                        </div>
                        
                        <?php if ($has_room): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Note:</strong> You already have a room assigned (Room <?php echo $current_room['room_number']; ?>). 
                                If you proceed, you will automatically vacate your current room and move to this new room.
                            </div>
                        <?php endif; ?>
                        
                        <p>Are you sure you want to book this room? This action cannot be undone.</p>
                        
                        <div class="booking-terms">
                            <h6><i class="fas fa-info-circle me-2"></i> Booking Terms:</h6>
                            <ol>
                                <li>Room fee must be paid within 5 days of booking.</li>
                                <li>Your booking may be cancelled if payment is not received on time.</li>
                                <li>Hostel rules and regulations apply.</li>
                                <li>For cancellations, please contact the hostel administration.</li>
                            </ol>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="book_room" class="btn btn-primary">
                            <?php echo $has_room ? 'Change Room' : 'Confirm Booking'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Leave Room Modal -->
    <div class="modal fade" id="leaveRoomModal" tabindex="-1" aria-labelledby="leaveRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header modal-header-danger">
                    <h5 class="modal-title" id="leaveRoomModalLabel">Confirm Leave Room</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> You are about to vacate your current room (Room <?php echo $current_room['room_number']; ?>).
                        </div>
                        
                        <p>Are you sure you want to leave this room? After vacating, you can select a new room from the available options.</p>
                        
                        <div class="booking-terms">
                            <h6><i class="fas fa-info-circle me-2"></i> Important Notes:</h6>
                            <ol>
                                <li>After vacating, your personal belongings must be removed promptly.</li>
                                <li>Refunds (if applicable) will be processed according to hostel policy.</li>
                                <li>Room must be left in acceptable condition to avoid additional charges.</li>
                            </ol>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="leave_room" class="btn btn-danger">Vacate Room</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Book room modal
            const bookRoomModal = document.getElementById('bookRoomModal');
            if (bookRoomModal) {
                bookRoomModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const roomNumber = button.getAttribute('data-room-number');
                    const roomType = button.getAttribute('data-room-type');
                    const roomFee = button.getAttribute('data-room-fee');
                    const availableSpots = button.getAttribute('data-available-spots');
                    
                    document.getElementById('modal_room_number').value = roomNumber;
                    document.getElementById('modal_display_room').textContent = roomNumber;
                    document.getElementById('modal_display_type').textContent = roomType + ' Room';
                    document.getElementById('modal_display_fee').textContent = roomFee;
                    
                    // Show available spots for shared rooms
                    if (roomType === 'Shared') {
                        document.getElementById('modal_display_spots').classList.remove('d-none');
                        document.getElementById('modal_spots_count').textContent = availableSpots;
                    } else {
                        document.getElementById('modal_display_spots').classList.add('d-none');
                    }
                });
            }
            
            // Filters
            const typeFilter = document.getElementById('typeFilter');
            const priceFilter = document.getElementById('priceFilter');
            const roomItems = document.querySelectorAll('.room-item');
            const noFilteredRooms = document.getElementById('noFilteredRooms');
            
            function filterRooms() {
                const typeValue = typeFilter.value.toLowerCase();
                const priceValue = priceFilter.value;
                
                // First filter by type
                let visibleRooms = 0;
                roomItems.forEach(room => {
                    const roomType = room.getAttribute('data-room-type');
                    
                    if (typeValue === 'all' || roomType === typeValue) {
                        room.style.display = 'block';
                        visibleRooms++;
                    } else {
                        room.style.display = 'none';
                    }
                });
                
                // Show message if no rooms match filter
                if (visibleRooms === 0) {
                    noFilteredRooms.classList.remove('d-none');
                } else {
                    noFilteredRooms.classList.add('d-none');
                    
                    // Sort by price if needed
                    if (priceValue !== 'default') {
                        const roomsContainer = document.getElementById('roomsContainer');
                        const rooms = Array.from(roomItems).filter(room => room.style.display !== 'none');
                        
                        rooms.sort((a, b) => {
                            const priceA = parseFloat(a.getAttribute('data-price'));
                            const priceB = parseFloat(b.getAttribute('data-price'));
                            
                            if (priceValue === 'low-high') {
                                return priceA - priceB;
                            } else {
                                return priceB - priceA;
                            }
                        });
                        
                        // Re-append the sorted rooms
                        rooms.forEach(room => {
                            roomsContainer.appendChild(room);
                        });
                    }
                }
            }
            
            // Add event listeners
            if (typeFilter) {
                typeFilter.addEventListener('change', filterRooms);
            }
            
            if (priceFilter) {
                priceFilter.addEventListener('change', filterRooms);
            }
        });
    </script>
</body>
</html>