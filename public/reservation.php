<?php
$pageTitle = "Reserve a Room";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$message = "";
$errors = [];

// Check if a room_id is passed from rooms.php or room.php
$preselectedRoomID = isset($_GET['room_id']) ? intval($_GET['room_id']) : null;

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = trim($_POST['fname']);
    $mname = trim($_POST['mname']);
    $lname = trim($_POST['lname']);
    $number = trim($_POST['number']);
    $email = trim($_POST['email']);
    $emer = trim($_POST['emer_contact']);
    $room_id = intval($_POST['room_id']);
    $notes = trim($_POST['notes']);

    // Validation
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
                INSERT INTO reservation (restat_id, tenant_id, room_id, notes)
                VALUES (1, ?, ?, ?)
            ");
            $stmt->execute([$tenant_id, $room_id, $notes]);

            // Update room status to Reserved (rstat_id=3)
            $stmt = $pdo->prepare("UPDATE room SET rstat_id = 3 WHERE room_id = ?");
            $stmt->execute([$room_id]);

            $message = "Reservation submitted successfully!";
            preventResubmission();  // Prevent resubmission on refresh
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch available rooms (only those not occupied/reserved)
// Fetch rooms (exclude occupied ones, rstat_id != 2)
$rooms = $pdo->query("
    SELECT room_id, room_number
    FROM room
    WHERE rstat_id != 2
    ORDER BY room_number
")->fetchAll();
?>

<link rel="stylesheet" href="/assets/css/reservation.css">

<div class="reservation-wrapper">
    <!-- LEFT SIDE IMAGE -->
    <div class="reservation-image">
        <img src="./assets/images/Room.png" alt="Reservation Image">
    </div>

    <!-- RIGHT SIDE FORM -->
    <div class="reservation-form-container">
        <h1 class="reservation-title">Reserve a Room</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="reservation-form" id="reservationForm">
            <label>First Name</label>
            <input type="text" name="fname" required value="<?php echo htmlspecialchars($_POST['fname'] ?? ''); ?>" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">

            <label>Last Name</label>
            <input type="text" name="lname" required value="<?php echo htmlspecialchars($_POST['lname'] ?? ''); ?>" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">

            <label>Middle Name (Optional)</label>
            <input type="text" name="mname" value="<?php echo htmlspecialchars($_POST['mname'] ?? ''); ?>" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">

            <label>Contact Number</label>
            <input type="text" name="number" required value="<?php echo htmlspecialchars($_POST['number'] ?? ''); ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');">

            <label>Emergency Contact Number</label>
            <input type="text" name="emer_contact" required value="<?php echo htmlspecialchars($_POST['emer_contact'] ?? ''); ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '');">

            <label>Email Address</label>
            <input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">

            <label>Select Room</label>
            <select name="room_id" id="roomSelect" required>
                <option value="">Choose...</option>
                <?php foreach ($rooms as $r): ?>
                    <option value="<?= $r['room_id'] ?>" <?= ($preselectedRoomID == $r['room_id']) ? 'selected' : '' ?>>
                        Room <?= $r['room_number'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Notes (Optional)</label>
            <textarea name="notes" rows="4" placeholder="Any specific request or note..."><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>

            <button type="submit" class="reserve-submit-btn">Submit Reservation</button>
        </form>

    </div>
</div>

<!-- SUCCESS POPUP MODAL -->
<?php if ($message): ?>
<div id="successModal" class="modal-overlay show">
    <div class="modal-box">
        <h2>Reservation Successful!</h2>
        <p><?php echo htmlspecialchars($message); ?> Our staff will contact you soon.</p>
        <button class="modal-close-btn" onclick="document.getElementById('successModal').classList.remove('show')">Close</button>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>