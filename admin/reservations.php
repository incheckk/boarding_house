<?php
$pageTitle = "Reservations";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
require_once __DIR__ . '/../includes/auth.php';  // Ensure admin is logged in
require_once __DIR__ . '/../includes/db.php';

$message = "";

// Handle reservation approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $reservation_id = intval($_POST['reservation_id']);
    $action = $_POST['action'];

    try {
        if ($action === 'approve') {
            // Get reservation details
            $stmt = $pdo->prepare("SELECT tenant_id, room_id FROM reservation WHERE reservation_id = ?");
            $stmt->execute([$reservation_id]);
            $res = $stmt->fetch();

            if ($res) {
                $tenant_id = $res['tenant_id'];
                $room_id = $res['room_id'];

                // Check if room is already occupied
                $stmt = $pdo->prepare("SELECT rstat_id FROM room WHERE room_id = ?");
                $stmt->execute([$room_id]);
                $roomStatus = $stmt->fetch()['rstat_id'];

                if ($roomStatus == 2) {
                    $message = "Room is already occupied. Cannot approve.";
                } else {
                    // Update reservation status
                    $stmt = $pdo->prepare("UPDATE reservation SET restat_id = 2 WHERE reservation_id = ?");
                    $stmt->execute([$reservation_id]);

                    // Insert into roomtenant
                    $stmt = $pdo->prepare("INSERT INTO roomtenant (tenant_id, room_id, check_in_date, role_in_room) VALUES (?, ?, CURRENT_DATE, 'Member')");
                    $stmt->execute([$tenant_id, $room_id]);

                    // Update tenant status to Active
                    $stmt = $pdo->prepare("UPDATE tenant SET tstat_id = 2 WHERE tenant_id = ?");
                    $stmt->execute([$tenant_id]);

                    // Update room status to Occupied
                    $stmt = $pdo->prepare("UPDATE room SET rstat_id = 2 WHERE room_id = ?");
                    $stmt->execute([$room_id]);

                    // Delete other pending reservations for this room
                    $stmt = $pdo->prepare("DELETE FROM reservation WHERE room_id = ? AND restat_id = 1 AND reservation_id != ?");
                    $stmt->execute([$room_id, $reservation_id]);

                    $message = "Reservation approved, tenant assigned, and other reservations for this room rejected!";
                    preventResubmission();
                }
            }
        } elseif ($action === 'reject') {
            // Get reservation details
            $stmt = $pdo->prepare("SELECT tenant_id, room_id FROM reservation WHERE reservation_id = ?");
            $stmt->execute([$reservation_id]);
            $res = $stmt->fetch();

            if ($res) {
                $tenant_id = $res['tenant_id'];
                $room_id = $res['room_id'];

                // Update reservation status to Rejected
                $stmt = $pdo->prepare("UPDATE reservation SET restat_id = 3 WHERE reservation_id = ?");
                $stmt->execute([$reservation_id]);

                // Check if tenant exists before deleting
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM tenant WHERE tenant_id = ?");
                $stmt->execute([$tenant_id]);
                if ($stmt->fetchColumn() > 0) {
                    // Delete tenant (cascades to delete reservation if not already updated)
                    $stmt = $pdo->prepare("DELETE FROM tenant WHERE tenant_id = ?");
                    $stmt->execute([$tenant_id]);
                }

                // Free room (set to Available if not occupied)
                $stmt = $pdo->prepare("UPDATE room SET rstat_id = 1 WHERE room_id = ? AND rstat_id != 2");
                $stmt->execute([$room_id]);

                $message = "Reservation rejected and tenant removed!";
            } else {
                $message = "Reservation not found.";
            }
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
}

// Handle admin manual reservation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fname'])) {  // Check for form fields
    $fname = trim($_POST['fname']);
    $mname = trim($_POST['mname']);
    $lname = trim($_POST['lname']);
    $number = trim($_POST['number']);
    $email = trim($_POST['email']);
    $emer = trim($_POST['emer_contact']);
    $room_id = intval($_POST['room_id']);

    // Validation
    $errors = [];
    if (empty($fname) || empty($lname) || empty($number) || empty($email) || empty($emer) || !$room_id) {
        $errors[] = "All required fields must be filled.";
    }
    if (!preg_match('/^[a-zA-Z\s]+$/', $fname)) {
        $errors[] = "First Name must contain only letters and spaces.";
    }
    if (!preg_match('/^[a-zA-Z\s]+$/', $lname)) {
        $errors[] = "Last Name must contain only letters and spaces.";
    }
    if (!empty($mname) && !preg_match('/^[a-zA-Z\s]+$/', $mname)) {
        $errors[] = "Middle Name must contain only letters and spaces.";
    }
    if (!preg_match('/^09\d{9}$/', $number)) {
        $errors[] = "Contact Number must be exactly 11 digits, start with 09, and contain only numbers.";
    }
    if (!preg_match('/^09\d{9}$/', $emer)) {
        $errors[] = "Emergency Contact Number must be exactly 11 digits, start with 09, and contain only numbers.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        try {
            // Check if tenant exists by email
            $stmt = $pdo->prepare("SELECT tenant_id FROM tenant WHERE email = ?");
            $stmt->execute([$email]);
            $tenant = $stmt->fetch();

            if (!$tenant) {
                // Insert new tenant with "Prospective" status (tstat_id=1)
                $stmt = $pdo->prepare("
                    INSERT INTO tenant (first_name, last_name, middle_name, number, emergency_number, email, tstat_id)
                    VALUES (?, ?, ?, ?, ?, ?, 1)
                ");
                $stmt->execute([$fname, $lname, $mname, $number, $emer, $email]);
                $tenant_id = $pdo->lastInsertId();
            } else {
                $tenant_id = $tenant['tenant_id'];
            }

            // Insert reservation
            $stmt = $pdo->prepare("
                INSERT INTO reservation (restat_id, tenant_id, room_id)
                VALUES (1, ?, ?)
            ");
            $stmt->execute([$tenant_id, $room_id]);

            // Update room status to Reserved (rstat_id=3)
            $stmt = $pdo->prepare("UPDATE room SET rstat_id = 3 WHERE room_id = ?");
            $stmt->execute([$room_id]);

            $message = "Reservation added successfully to waiting list!";
            preventResubmission();
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
        }
    } else {
        $message = implode('<br>', $errors);  // Show all errors
    }
}

// Fetch available rooms with status
$availableRooms = $pdo->query("
    SELECT r.room_id, r.room_number, r.room_size, r.room_rate, rs.rstat_desc AS status
    FROM room r
    JOIN room_status rs ON r.rstat_id = rs.rstat_id
    ORDER BY r.room_number
")->fetchAll();

// Fetch all reservations for table (only Pending)
$reservations = $pdo->query("
    SELECT res.reservation_id, res.notes, 
           t.tenant_id, t.first_name, t.last_name, t.middle_name, t.number, t.emergency_number, t.email, t.created_at, ts.tstat_desc AS tenant_status,
           r.room_number, rs.restat_desc
    FROM reservation res
    JOIN tenant t ON res.tenant_id = t.tenant_id
    JOIN room r ON res.room_id = r.room_id
    JOIN tenant_reservation_status rs ON res.restat_id = rs.restat_id
    JOIN tenant_status ts ON t.tstat_id = ts.tstat_id
    WHERE rs.restat_id = 1  -- Only Pending
    ORDER BY res.created_at DESC
")->fetchAll();

// Fetch rooms with multiple pending reservations
$roomsWithMultipleReservations = $pdo->query("
    SELECT r.room_id, r.room_number, r.room_size, r.room_rate, COUNT(res.reservation_id) AS reservation_count
    FROM room r
    JOIN reservation res ON r.room_id = res.room_id
    WHERE res.restat_id = 1  -- Pending
    GROUP BY r.room_id
    HAVING COUNT(res.reservation_id) > 1
    ORDER BY r.room_number
")->fetchAll();

// For each room with multiple reservations, fetch the list of reservations
$multipleReservationsDetails = [];
foreach ($roomsWithMultipleReservations as $room) {
    $stmt = $pdo->prepare("
        SELECT res.reservation_id, t.tenant_id, t.first_name, t.last_name
        FROM reservation res
        JOIN tenant t ON res.tenant_id = t.tenant_id
        WHERE res.room_id = ? AND res.restat_id = 1
    ");
    $stmt->execute([$room['room_id']]);
    $multipleReservationsDetails[$room['room_id']] = $stmt->fetchAll();
}

// Handle view details (for modal)
$selectedTenant = null;
if (isset($_GET['view_tenant'])) {
    $tenant_id = intval($_GET['view_tenant']);
    $stmt = $pdo->prepare("
        SELECT t.first_name, t.last_name, t.middle_name, t.number, t.emergency_number, t.email, t.created_at, ts.tstat_desc AS tenant_status
        FROM tenant t
        JOIN tenant_status ts ON t.tstat_id = ts.tstat_id
        WHERE t.tenant_id = ?
    ");
    $stmt->execute([$tenant_id]);
    $selectedTenant = $stmt->fetch();
}
?>

<!-- Main Content Wrapper -->
<main class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Reservations</h1>
        <p>Manage online bookings and waiting lists for your properties.</p>
    </div>

    <!-- Success Message -->
    <?php if ($message): ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Online Reservation System -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2>Online Reservation System</h2>
        </div>

        <!-- Reservation Form (Admin Manual) - Updated to Match User-Side Form with Previous Grid Design -->
        <div class="reservation-form">
            <h3>Make a Reservation (Admin)</h3>
            <form method="POST" action="" class="reservation-form">  <!-- FIXED: Posts to self -->
                <div class="form-grid">
                    <div class="form-group">
                        <label for="fname">First Name</label>
                        <input type="text" id="fname" name="fname" required>
                    </div>
                    <div class="form-group">
                        <label for="lname">Last Name</label>
                        <input type="text" id="lname" name="lname" required>
                    </div>
                    <div class="form-group">
                        <label for="mname">Middle Name (Optional)</label>
                        <input type="text" id="mname" name="mname">
                    </div>
                    <div class="form-group">
                        <label for="number">Contact Number</label>
                        <input type="text" id="number" name="number" required>
                    </div>
                    <div class="form-group">
                        <label for="emer_contact">Emergency Contact Number</label>
                        <input type="text" id="emer_contact" name="emer_contact" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="room_id">Select Room</label>
                        <select id="room_id" name="room_id" required>
                            <option value="">Choose...</option>
                            <?php foreach ($availableRooms as $r): ?>
                                <option value="<?php echo $r['room_id']; ?>">
                                    Room <?php echo htmlspecialchars($r['room_number']); ?> (<?php echo htmlspecialchars($r['status']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-action">Confirm Reservation</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Waiting List Management -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2>Waiting List Management</h2>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Requested Room</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $res): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($res['first_name'] . ' ' . $res['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($res['email']); ?></td>
                        <td>Room <?php echo htmlspecialchars($res['room_number']); ?></td>
                        <td><span class="status-badge <?php echo strtolower($res['restat_desc']); ?>"><?php echo htmlspecialchars($res['restat_desc']); ?></span></td>
                        <td>
                            <a href="?view_tenant=<?php echo $res['tenant_id']; ?>" class="btn-action" title="View"><i class="fas fa-eye"></i></a>
                            <?php if ($res['restat_desc'] === 'Pending'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="reservation_id" value="<?php echo $res['reservation_id']; ?>">
                                    <button type="submit" name="action" value="approve" class="btn-action">Approve</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="reservation_id" value="<?php echo $res['reservation_id']; ?>">
                                    <button type="submit" name="action" value="reject" class="btn-action warning">Reject</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <!-- Rooms with Multiple Reservations -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2>Rooms with Multiple Reservations</h2>
            <p>Review rooms with more than one pending reservation to choose the most compatible.</p>
        </div>

        <?php if (!empty($roomsWithMultipleReservations)): ?>
            <?php foreach ($roomsWithMultipleReservations as $room): ?>
                <div class="room-multiple-reservations" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 20px;">
                    <h4>Room <?php echo htmlspecialchars($room['room_number']); ?> (<?php echo htmlspecialchars($room['room_size']); ?>) - ₱<?php echo htmlspecialchars($room['room_rate']); ?>/month</h4>
                    <p><strong>Pending Reservations:</strong> <?php echo $room['reservation_count']; ?></p>
                    <ul>
                        <?php foreach ($multipleReservationsDetails[$room['room_id']] as $resDetail): ?>
                            <li>
                                <?php echo htmlspecialchars($resDetail['first_name'] . ' ' . $resDetail['last_name']); ?>
                                <a href="?view_tenant=<?php echo $resDetail['tenant_id']; ?>" class="btn-action" style="margin-left: 10px;" title="View"><i class="fas fa-eye"></i></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No rooms with multiple reservations.</p>
        <?php endif; ?>
    </section>

    <!-- Tenant Details Modal -->
    <?php if ($selectedTenant): ?>
    <div id="tenantModal" class="modal-overlay show" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;">
        <div class="modal-box" style="background: white; padding: 20px; border-radius: 8px; max-width: 500px; width: 90%;">
            <h2>Tenant Details</h2>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($selectedTenant['first_name'] . ' ' . $selectedTenant['middle_name'] . ' ' . $selectedTenant['last_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($selectedTenant['email']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($selectedTenant['number']); ?></p>
            <p><strong>Emergency Contact:</strong> <?php echo htmlspecialchars($selectedTenant['emergency_number']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($selectedTenant['tenant_status']); ?></p>
            <p><strong>Created At:</strong> <?php echo htmlspecialchars($selectedTenant['created_at']); ?></p>
            <button onclick="window.location.href='reservations.php'" class="modal-close-btn" style="background: #007bff; color: white; border: none; padding: 10px; cursor: pointer;">Close</button>
        </div>
    </div>
    <?php endif; ?>
</main>

<script>
    console.log("Reservations page loaded. Add JS for interactivity.");
</script>