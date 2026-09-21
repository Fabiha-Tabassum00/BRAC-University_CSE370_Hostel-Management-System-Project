<?php
// filepath: c:\xampp\htdocs\Hostel_Management_System\manage_room_bookings.php
session_start();
include 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$success_message = '';
$error_message = '';

// Get admin information
$admin_info_query = "SELECT admin_name FROM admin WHERE admin_id = '$admin_id'";
$admin_result = $conn->query($admin_info_query);
$admin_info = $admin_result->fetch_assoc();

// Handle room addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_room'])) {
    $room_number = $_POST['room_number'];
    $fee = $_POST['fee'];
    $room_type = $_POST['room_type'];
    $status = $_POST['status'];
    
    // Set single or shared based on room type
    $single = ($room_type == 'single') ? 1 : 0;
    $shared = ($room_type == 'shared') ? 1 : 0;
    
    // Calculate available spots based on room type
    $available_spots = ($room_type == 'shared') ? 2 : 1;
    
    // Check if room number already exists
    $check_query = "SELECT * FROM room WHERE room_number = '$room_number'";
    $check_result = $conn->query($check_query);
    
    if ($check_result && $check_result->num_rows > 0) {
        $error_message = "Room number $room_number already exists!";
    } else {
        // Add new room
        $insert_query = "INSERT INTO room (room_number, status, fee, single, shared, admin_id, available_spots, st_id) 
                          VALUES ('$room_number', '$status', '$fee', '$single', '$shared', '$admin_id', '$available_spots', NULL)";
        
        if ($conn->query($insert_query) === TRUE) {
            $success_message = "Room $room_number has been added successfully!";
        } else {
            $error_message = "Error adding room: " . $conn->error;
        }
    }
}

// Handle room status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $room_number = $_POST['room_number'];
    $new_status = $_POST['new_status'];
    
    $update_query = "UPDATE room SET status = '$new_status' WHERE room_number = '$room_number'";
    
    if ($conn->query($update_query) === TRUE) {
        $success_message = "Room status has been updated successfully!";
    } else {
        $error_message = "Error updating room status: " . $conn->error;
    }
}

// Handle room deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_room'])) {
    $room_number = $_POST['room_number'];
    
    // Check if room is currently assigned to any student
    $check_assigned = "SELECT * FROM room WHERE room_number = '$room_number' AND st_id IS NOT NULL";
    $assigned_result = $conn->query($check_assigned);
    
    if ($assigned_result && $assigned_result->num_rows > 0) {
        $error_message = "Cannot delete room as it is currently assigned to a student!";
    } else {
        $delete_query = "DELETE FROM room WHERE room_number = '$room_number'";
        
        if ($conn->query($delete_query) === TRUE) {
            $success_message = "Room has been deleted successfully!";
        } else {
            $error_message = "Error deleting room: " . $conn->error;
        }
    }
}

// Get all rooms
$rooms_query = "SELECT r.*, a.admin_name, 
                  CASE 
                    WHEN r.single = 1 THEN 'Single' 
                    WHEN r.shared = 1 THEN 'Shared' 
                    ELSE 'Unknown' 
                  END as room_type,
                  u.st_name, u.st_id
                FROM room r
                LEFT JOIN admin a ON r.admin_id = a.admin_id
                LEFT JOIN user u ON r.st_id = u.st_id
                ORDER BY r.room_number";
$rooms_result = $conn->query($rooms_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Room Bookings - Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .bookings-container {
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
        .room-card {
            transition: transform 0.3s;
        }
        .room-card:hover {
            transform: translateY(-5px);
        }
        .room-status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-available {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .status-occupied {
            background-color: #f8d7da;
            color: #842029;
        }
        .status-maintenance {
            background-color: #fff3cd;
            color: #664d03;
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
        .assigned-student {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .student-name {
            font-weight: 600;
            color: #495057;
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
        .modal-header {
            background-color: #4e73df;
            color: white;
        }
        .form-label {
            font-weight: 500;
        }
        .filter-container {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .room-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            flex: 1;
            min-width: 150px;
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 10px 0;
        }
        .total-rooms {
            color: #4e73df;
        }
        .available-rooms {
            color: #1cc88a;
        }
        .occupied-rooms {
            color: #e74a3b;
        }
        .maintenance-rooms {
            color: #f6c23e;
        }
        .stat-label {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .btn-action {
            margin-right: 5px;
        }
        @media (max-width: 767px) {
            .stat-card {
                min-width: 45%;
            }
        }
        @media (max-width: 480px) {
            .stat-card {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container bookings-container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="card-title">
                    <i class="fas fa-door-open me-2"></i> Manage Room Bookings
                </h2>
                <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                    <i class="fas fa-plus me-2"></i> Add New Room
                </button>
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
                
                <!-- Room Statistics -->
                <?php
                    // Calculate statistics
                    $total_rooms = $rooms_result ? $rooms_result->num_rows : 0;
                    $available_rooms = 0;
                    $occupied_rooms = 0;
                    $maintenance_rooms = 0;
                    
                    if ($rooms_result && $rooms_result->num_rows > 0) {
                        $rooms_data = $rooms_result->fetch_all(MYSQLI_ASSOC);
                        
                        foreach ($rooms_data as $room) {
                            if ($room['status'] == 'available') {
                                $available_rooms++;
                            } elseif ($room['status'] == 'occupied') {
                                $occupied_rooms++;
                            } elseif ($room['status'] == 'maintenance') {
                                $maintenance_rooms++;
                            }
                        }
                        
                        // Reset the result pointer
                        $rooms_result = $conn->query($rooms_query);
                    }
                ?>
                
                <div class="room-stats">
                    <div class="stat-card">
                        <div class="stat-label">Total Rooms</div>
                        <div class="stat-value total-rooms"><?php echo $total_rooms; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Available</div>
                        <div class="stat-value available-rooms"><?php echo $available_rooms; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Occupied</div>
                        <div class="stat-value occupied-rooms"><?php echo $occupied_rooms; ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Maintenance</div>
                        <div class="stat-value maintenance-rooms"><?php echo $maintenance_rooms; ?></div>
                    </div>
                </div>
                
                <!-- Filter Controls -->
                <div class="filter-container">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="statusFilter" class="form-label">Filter by Status:</label>
                            <select class="form-select" id="statusFilter">
                                <option value="all">All Statuses</option>
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="typeFilter" class="form-label">Filter by Type:</label>
                            <select class="form-select" id="typeFilter">
                                <option value="all">All Types</option>
                                <option value="Single">Single</option>
                                <option value="Shared">Shared</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="searchRoom" class="form-label">Search:</label>
                            <input type="text" class="form-control" id="searchRoom" placeholder="Room number or student name">
                        </div>
                    </div>
                </div>
                
                <!-- Rooms Display -->
                <div class="row" id="roomsContainer">
                    <?php if ($rooms_result && $rooms_result->num_rows > 0): ?>
                        <?php while ($room = $rooms_result->fetch_assoc()): ?>
                            <div class="col-lg-4 col-md-6 mb-4 room-item" 
                                 data-status="<?php echo strtolower($room['status']); ?>" 
                                 data-type="<?php echo $room['room_type']; ?>"
                                 data-room="<?php echo $room['room_number']; ?>"
                                 data-student="<?php echo strtolower($room['st_name'] ?? ''); ?>">
                                <div class="card h-100 room-card">
                                    <div class="card-body position-relative">
                                        <span class="room-status status-<?php echo strtolower($room['status']); ?>">
                                            <?php echo ucfirst($room['status']); ?>
                                        </span>
                                        <div class="room-number">Room <?php echo $room['room_number']; ?></div>
                                        <div class="room-type">
                                            <?php if ($room['single'] == 1): ?>
                                                <i class="fas fa-user me-1"></i> Single Room
                                            <?php elseif ($room['shared'] == 1): ?>
                                                <i class="fas fa-users me-1"></i> Shared Room
                                            <?php endif; ?>
                                            
                                            <?php if ($room['shared'] == 1): ?>
                                                <span class="available-spots">
                                                    <i class="fas fa-bed me-1"></i> 
                                                    <?php echo $room['available_spots']; ?> spots available
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="room-fee">
                                            <i class="fas fa-dollar-sign me-1"></i> 
                                            <?php echo number_format($room['fee'], 2); ?> per month
                                        </div>
                                        <div class="room-details">
                                            <i class="fas fa-user-shield me-1"></i> 
                                            Added by: <?php echo $room['admin_name']; ?>
                                        </div>
                                        
                                        <?php if ($room['st_id']): ?>
                                            <div class="assigned-student">
                                                <i class="fas fa-user-graduate me-1"></i> 
                                                Assigned to: <span class="student-name"><?php echo $room['st_name']; ?></span>
                                                <div class="text-muted mt-1">ID: <?php echo $room['st_id']; ?></div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="mt-3">
                                            <button class="btn btn-sm btn-outline-primary btn-action" data-bs-toggle="modal" data-bs-target="#updateStatusModal" 
                                                    data-room-number="<?php echo $room['room_number']; ?>"
                                                    data-room-status="<?php echo $room['status']; ?>">
                                                <i class="fas fa-edit me-1"></i> Change Status
                                            </button>
                                            
                                            <?php if (!$room['st_id']): ?>
                                                <button class="btn btn-sm btn-outline-danger btn-action" data-bs-toggle="modal" data-bs-target="#deleteRoomModal"
                                                        data-room-number="<?php echo $room['room_number']; ?>">
                                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-door-closed fa-3x mb-3 text-muted"></i>
                            <h4>No rooms available</h4>
                            <p>Start by adding some rooms to the system.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="text-center py-4 d-none" id="noRoomsMessage">
                    <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                    <h4>No rooms found</h4>
                    <p>Try adjusting your filter criteria.</p>
                </div>
                
                <div class="mt-4">
                    <a href="admin_dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Room Modal -->
    <div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoomModalLabel">Add New Room</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="room_number" class="form-label">Room Number</label>
                            <input type="text" class="form-control" id="room_number" name="room_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="fee" class="form-label">Monthly Fee</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="fee" name="fee" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Room Type</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="room_type" id="singleRoom" value="single" checked>
                                <label class="form-check-label" for="singleRoom">
                                    <i class="fas fa-user me-1"></i> Single Room
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="room_type" id="sharedRoom" value="shared">
                                <label class="form-check-label" for="sharedRoom">
                                    <i class="fas fa-users me-1"></i> Shared Room
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Room Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="available">Available</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_room" class="btn btn-primary">Add Room</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Update Room Status</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="update_room_number" name="room_number">
                        <div class="mb-3">
                            <label class="form-label">Room Number</label>
                            <div class="form-control bg-light" id="update_room_number_display" readonly></div>
                        </div>
                        <div class="mb-3">
                            <label for="new_status" class="form-label">New Status</label>
                            <select class="form-select" id="new_status" name="new_status" required>
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Room Modal -->
    <div class="modal fade" id="deleteRoomModal" tabindex="-1" aria-labelledby="deleteRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteRoomModalLabel">Delete Room</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="delete_room_number" name="room_number">
                        <p>Are you sure you want to delete Room <strong id="delete_room_number_display"></strong>?</p>
                        <p class="text-danger"><i class="fas fa-exclamation-triangle me-2"></i> This action cannot be undone!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_room" class="btn btn-danger">Delete Room</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update room modal data
        document.addEventListener('DOMContentLoaded', function() {
            // Status update modal
            const updateStatusModal = document.getElementById('updateStatusModal');
            if (updateStatusModal) {
                updateStatusModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const roomNumber = button.getAttribute('data-room-number');
                    const roomStatus = button.getAttribute('data-room-status');
                    
                    document.getElementById('update_room_number').value = roomNumber;
                    document.getElementById('update_room_number_display').textContent = roomNumber;
                    document.getElementById('new_status').value = roomStatus;
                });
            }
            
            // Delete room modal
            const deleteRoomModal = document.getElementById('deleteRoomModal');
            if (deleteRoomModal) {
                deleteRoomModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const roomNumber = button.getAttribute('data-room-number');
                    
                    document.getElementById('delete_room_number').value = roomNumber;
                    document.getElementById('delete_room_number_display').textContent = roomNumber;
                });
            }
            
            // Filters
            const statusFilter = document.getElementById('statusFilter');
            const typeFilter = document.getElementById('typeFilter');
            const searchInput = document.getElementById('searchRoom');
            const roomItems = document.querySelectorAll('.room-item');
            const noRoomsMessage = document.getElementById('noRoomsMessage');
            
            function filterRooms() {
                const statusValue = statusFilter.value.toLowerCase();
                const typeValue = typeFilter.value;
                const searchValue = searchInput.value.toLowerCase();
                
                let visibleRooms = 0;
                
                roomItems.forEach(room => {
                    const roomStatus = room.getAttribute('data-status');
                    const roomType = room.getAttribute('data-type');
                    const roomNumber = room.getAttribute('data-room');
                    const studentName = room.getAttribute('data-student');
                    
                    // Check if room matches all filters
                    const matchesStatus = statusValue === 'all' || roomStatus === statusValue;
                    const matchesType = typeValue === 'all' || roomType === typeValue;
                    const matchesSearch = searchValue === '' || 
                                         roomNumber.toLowerCase().includes(searchValue) || 
                                         studentName.includes(searchValue);
                    
                    if (matchesStatus && matchesType && matchesSearch) {
                        room.style.display = 'block';
                        visibleRooms++;
                    } else {
                        room.style.display = 'none';
                    }
                });
                
                // Show "no rooms" message if needed
                if (visibleRooms === 0) {
                    noRoomsMessage.classList.remove('d-none');
                } else {
                    noRoomsMessage.classList.add('d-none');
                }
            }
            
            // Add event listeners to filters
            statusFilter.addEventListener('change', filterRooms);
            typeFilter.addEventListener('change', filterRooms);
            searchInput.addEventListener('input', filterRooms);
        });
    </script>
</body>
</html>