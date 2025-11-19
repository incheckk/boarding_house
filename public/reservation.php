<?php
$pageTitle = "Reserve a Room";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$message = "";

// Check if a room_id is passed from rooms.php or room.php
$preselectedRoomID = isset($_GET['room_id']) ? intval($_GET['room_id']) : null;

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fname   = $_POST['fname'];
    $mname   = $_POST['mname'];
    $lname   = $_POST['lname'];
    $number  = $_POST['number'];
    $email   = $_POST['email'];
    $emer    = $_POST['emer_contact'];
    $room_id = $_POST['room_id'];
    $notes   = $_POST['notes'];

    // Default reservation status = 1 (Pending)
    $res_status_id = 1;

    $stmt = $pdo->prepare("
        INSERT INTO reservation 
        (tenant_fname, tenant_mname, tenant_lname, tenant_number, tenant_email, 
         room_id, tenant_emernum, res_notes, res_status_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([$fname, $mname, $lname, $number, $email, $room_id, $emer, $notes, $res_status_id]);
    
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('successModal').classList.add('show');
        });
    </script>";
}

// Fetch rooms for dropdown
$rooms = $pdo->query("SELECT room_id, room_number FROM room")->fetchAll();
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

        <form method="POST" class="reservation-form" id="reservationForm">

            <label>First Name</label>
            <input type="text" name="fname" required>

            <label>Last Name</label>
            <input type="text" name="lname" required>

            <label>Middle Name (Optional)</label>
            <input type="text" name="mname">

            <label>Contact Number</label>
            <input type="text" name="number" required>

            <label>Emergency Contact Number</label>
            <input type="text" name="emer_contact" required>

            <label>Email Address</label>
            <input type="email" name="email" required>

            <label>Select Room</label>
            <select name="room_id" id="roomSelect" required>
                <option value="">Choose...</option>

                <?php foreach ($rooms as $r): ?>
                    <option value="<?= $r['room_id'] ?>"
                        <?= ($preselectedRoomID == $r['room_id']) ? 'selected' : '' ?>>
                        Room <?= $r['room_number'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Notes (Optional)</label>
            <textarea name="notes" rows="4" placeholder="Any specific request or note..."></textarea>

            <button type="submit" class="reserve-submit-btn">Submit Reservation</button>
        </form>
    </div>
</div>

<!-- SUCCESS POPUP MODAL -->
<div id="successModal" class="modal-overlay">
    <div class="modal-box">
        <h2>Reservation Successful!</h2>
        <p>Our staff will contact you soon.</p>
        <button class="modal-close-btn" onclick="document.getElementById('successModal').classList.remove('show')">
            Close
        </button>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
