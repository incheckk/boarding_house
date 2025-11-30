<?php
$pageTitle = "Room Management";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
require_once __DIR__ . '/../includes/auth.php';  // Ensure admin is logged in
require_once __DIR__ . '/../includes/db.php';
// Sync room statuses with active roomtenant records (fix inconsistency)
$pdo->exec("
    UPDATE room r
    LEFT JOIN (
        SELECT room_id, COUNT(*) AS active_tenants
        FROM roomtenant
        WHERE check_out_date IS NULL
        GROUP BY room_id
    ) rt ON r.room_id = rt.room_id
    SET r.rstat_id = CASE 
        WHEN rt.active_tenants > 0 THEN 2  -- Occupied
        WHEN r.rstat_id = 3 THEN 3  -- Preserve Reserved if no tenant but reserved
        ELSE 1  -- Available
    END
");
$message = "";
$editRoom = null;

// Handle form submission (Add or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_number = trim($_POST['room_number']);
    $room_size = $_POST['room_size'];  // Dropdown: Single Bed, Double Bed, Bunk Bed
    $room_rate = floatval($_POST['room_rate']);

    if (empty($room_number) || empty($room_size) || $room_rate <= 0) {
        $message = "All fields are required and rate must be positive.";
    } else {
        try {
            if (isset($_POST['update_room_id'])) {
                // Update existing room
                $room_id = intval($_POST['update_room_id']);
                $stmt = $pdo->prepare("UPDATE room SET room_number = ?, room_size = ?, room_rate = ? WHERE room_id = ?");
                $stmt->execute([$room_number, $room_size, $room_rate, $room_id]);
                $message = "Room updated successfully!";
            } else {
                // Add new room
                $stmt = $pdo->prepare("INSERT INTO room (room_number, room_size, room_rate, rstat_id) VALUES (?, ?, ?, 1)");
                $stmt->execute([$room_number, $room_size, $room_rate]);
                $message = "Room added successfully!";
                preventResubmission();
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
        }
    }
}

// Handle delete
if (isset($_GET['delete_room'])) {
    $room_id = intval($_GET['delete_room']);
    try {
        $stmt = $pdo->prepare("DELETE FROM room WHERE room_id = ?");
        $stmt->execute([$room_id]);
        $message = "Room deleted successfully!";
    } catch (PDOException $e) {
        $message = "Error deleting room: " . $e->getMessage();
    }
}

// Handle edit (populate form)
if (isset($_GET['edit_room'])) {
    $room_id = intval($_GET['edit_room']);
    $stmt = $pdo->prepare("SELECT room_id, room_number, room_size, room_rate FROM room WHERE room_id = ?");
    $stmt->execute([$room_id]);
    $editRoom = $stmt->fetch();
}

// Fetch all rooms for table
$rooms = $pdo->query("
    SELECT r.room_id, r.room_number, r.room_size, r.room_rate, rs.rstat_desc AS status
    FROM room r
    JOIN room_status rs ON r.rstat_id = rs.rstat_id
    ORDER BY r.room_number
")->fetchAll();

// Fetch statistics
$stats = $pdo->query("
    SELECT 
        COUNT(*) AS total_rooms,
        SUM(CASE WHEN rstat_id = 2 THEN 1 ELSE 0 END) AS occupied_rooms,
        SUM(CASE WHEN rstat_id IN (1,3) THEN 1 ELSE 0 END) AS vacant_rooms,
        ROUND(AVG(room_rate), 2) AS avg_rent_rate
    FROM room
")->fetch();
$totalRooms = $stats['total_rooms'];
$occupiedRooms = $stats['occupied_rooms'];
$vacantRooms = $stats['vacant_rooms'];
$occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;
$avgRentRate = $stats['avg_rent_rate'];

// Fetch room status with tenant info (one per room)
$roomStatuses = $pdo->query("
    SELECT r.room_number, rs.rstat_desc AS status, 
           COALESCE(CONCAT(t.first_name, ' ', t.last_name), '-') AS tenant_name
    FROM room r
    LEFT JOIN room_status rs ON r.rstat_id = rs.rstat_id
    LEFT JOIN roomtenant rt ON r.room_id = rt.room_id AND rt.check_out_date IS NULL
    LEFT JOIN tenant t ON rt.tenant_id = t.tenant_id
    ORDER BY r.room_number
")->fetchAll();
?>

<main class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Room Management</h1>
        <p>Manage room details and monitor availability status.</p>
    </div>

    <!-- Success/Error Message -->
    <?php if ($message): ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Manage Room Details Section -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Manage Room Details</h2>
        </div>

        <form method="POST" class="tenant-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="room-number">Room Number</label>
                    <input type="text" id="room-number" name="room_number" placeholder="e.g., 101" value="<?php echo htmlspecialchars($editRoom['room_number'] ?? ''); ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                </div>

                <div class="form-group">
                    <label for="room-size">Bed Size</label>
                    <select id="room-size" name="room_size" required>
                        <option value="">Choose...</option>
                        <option value="Single Bed" <?php echo ($editRoom['room_size'] ?? '') === 'Single Bed' ? 'selected' : ''; ?>>Single Bed</option>
                        <option value="Double Bed" <?php echo ($editRoom['room_size'] ?? '') === 'Double Bed' ? 'selected' : ''; ?>>Double Bed</option>
                        <option value="Bunk Bed" <?php echo ($editRoom['room_size'] ?? '') === 'Bunk Bed' ? 'selected' : ''; ?>>Bunk Bed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="rent-rate">Rent Rate (₱/month)</label>
                    <input type="number" id="rent-rate" name="room_rate" placeholder="e.g., 5000" value="<?php echo htmlspecialchars($editRoom['room_rate'] ?? ''); ?>" min="0" step="0.01" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" required>
                </div>
            </div>

            <div class="form-actions">
                <?php if ($editRoom): ?>
                    <input type="hidden" name="update_room_id" value="<?php echo $editRoom['room_id']; ?>">
                    <button type="submit" class="btn-action primary">Update Room</button>
                    <a href="room_management.php" class="btn-action">Cancel</a>
                <?php else: ?>
                    <button type="submit" class="btn-action primary">Add Room</button>
                <?php endif; ?>
            </div>
        </form>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Bed Size</th>
                    <th>Rent Rate (₱)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $room): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($room['room_size']); ?></td>
                        <td><?php echo htmlspecialchars($room['room_rate']); ?></td>
                        <td>
                            <a href="?edit_room=<?php echo $room['room_id']; ?>" class="btn-action"><i class="fas fa-edit"></i></a>
                            <a href="?delete_room=<?php echo $room['room_id']; ?>" class="btn-action warning" onclick="return confirm('Are you sure you want to delete this room?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Room Availability Status Dashboard -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Room Availability Status Dashboard</h2>
        </div>

        <div class="occupancy-grid">
            <div class="occupancy-card">
                <h3>Total Rooms</h3>
                <div class="number"><?php echo $totalRooms; ?></div>
            </div>

            <div class="occupancy-card">
                <h3>Occupied Rooms</h3>
                <div class="number"><?php echo $occupiedRooms; ?></div>
            </div>

            <div class="occupancy-card">
                <h3>Vacant Rooms</h3>
                <div class="number"><?php echo $vacantRooms; ?></div>
            </div>
        </div>

        <div class="occupancy-stats">
            <div class="stat-box">
                <i class="fas fa-chart-pie"></i>
                <div>
                    <strong><?php echo $occupancyRate; ?>%</strong>
                    <span>Occupancy Rate</span>
                </div>
            </div>

            <div class="stat-box">
                <i class="fas fa-dollar-sign"></i>
                <div>
                    <strong>₱<?php echo number_format($avgRentRate, 2); ?></strong>
                    <span>Avg. Rent Rate</span>
                </div>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Status</th>
                    <th>Tenant (if occupied)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roomStatuses as $status): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($status['room_number']); ?></td>
                        <td><span class="status-badge <?php echo strtolower($status['status']); ?>"><?php echo htmlspecialchars($status['status']); ?></span></td>
                        <td><?php echo htmlspecialchars($status['tenant_name']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    console.log("Room Management page loaded.");
</script>