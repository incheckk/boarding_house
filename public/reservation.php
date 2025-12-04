<?php
$pageTitle = "Reserve a Room";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$message = "";
$errors = [];
$formData = $_POST; // Store form data to repopulate fields

// Check if a room_id is passed from rooms.php or room.php
$preselectedRoomID = isset($_GET['room_id']) ? intval($_GET['room_id']) : null;

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = isset($formData['fname']) ? trim($formData['fname']) : '';
    $mname = isset($formData['mname']) ? trim($formData['mname']) : '';
    $lname = isset($formData['lname']) ? trim($formData['lname']) : '';
    $number = isset($formData['number']) ? trim($formData['number']) : '';
    $email = isset($formData['email']) ? trim($formData['email']) : '';
    $emer = isset($formData['emer_contact']) ? trim($formData['emer_contact']) : '';
    $room_id = (isset($formData['room_id']) && $formData['room_id'] !== '') ? intval($formData['room_id']) : 0;
    $notes = isset($formData['notes']) ? trim($formData['notes']) : '';

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
            // Check if room is still available (rstat_id != 2 - Occupied)
            $stmt = $pdo->prepare("SELECT rstat_id FROM room WHERE room_id = ?");
            $stmt->execute([$room_id]);
            $roomStatus = $stmt->fetchColumn();

            if ($roomStatus == 2) { // 2 = Occupied
                $errors[] = "The selected room is no longer available.";
            } else {
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

                // Insert reservation (restat_id=1 for Pending/Initial Status)
                $stmt = $pdo->prepare("
                    INSERT INTO reservation (restat_id, tenant_id, room_id, notes)
                    VALUES (1, ?, ?, ?)
                ");
                $stmt->execute([$tenant_id, $room_id, $notes]);

                // Update room status to Reserved (rstat_id=3)
                $stmt = $pdo->prepare("UPDATE room SET rstat_id = 3 WHERE room_id = ?");
                $stmt->execute([$room_id]);

                // NEW: Log the 'reserve' event (silent, no UI change)
                $user_ip = $_SERVER['REMOTE_ADDR'];  // Anonymous IP for tracking
                $log_stmt = $pdo->prepare("
                    INSERT INTO event_logs (event_type, room_id, user_ip)
                    VALUES ('reserve', ?, ?)
                ");
                $log_stmt->execute([$room_id, $user_ip]);

                // Redirect to prevent form resubmission (PRG pattern)
                header("Location: reservation.php?success=1");
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Check for success message after redirect
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "Reservation submitted successfully! Our staff will contact you soon.";
}


// Fetch available rooms (exclude occupied ones, rstat_id != 2)
$rooms = $pdo->query("
    SELECT room_id, room_number, rstat_id
    FROM room
    WHERE rstat_id != 2
    ORDER BY room_number
")->fetchAll();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
/* ================================================================= */
/*                               CSS
/* ================================================================= */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --primary-gold: #d4af37;
    --gold-light: #f4e5c0;
    --gold-dark: #c9a961;
    --gold-darker: #a8763a;
    --secondary-red: #f60a0a;
    --accent-dark: #080820;
    --text-dark: #2d2d2d;
    --text-light: #666;
    --white: #ffffff;
    --off-white: #f8f9fa;
    --border-light: #e0e0e0;
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.12);
    --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.16);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: var(--text-dark);
    background: linear-gradient(180deg, #fdfbf7 0%, #f5f0e8 50%, #fdfbf7 100%);
    position: relative;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle, rgba(212, 175, 55, 0.05) 1px, transparent 1px);
    background-size: 50px 50px;
    pointer-events: none;
    z-index: 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
    z-index: 1;
}

/* ====== PAGE HEADER ====== */
.page-header {
    background: linear-gradient(135deg, var(--primary-gold) 0%, var(--gold-light) 25%, var(--gold-dark) 50%, var(--gold-light) 75%, var(--primary-gold) 100%);
    padding: 80px 0 60px;
    text-align: center;
    color: var(--white);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: 
        radial-gradient(circle, rgba(255, 255, 255, 0.2) 2px, transparent 2px),
        radial-gradient(circle, rgba(255, 215, 0, 0.3) 1.5px, transparent 1.5px);
    background-size: 80px 80px, 120px 120px;
    background-position: 0 0, 40px 40px;
    opacity: 0.6;
}

.page-header .container {
    position: relative;
    z-index: 2;
}

.page-title {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 2px;
    text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.3);
}

.page-description {
    font-size: 1.2rem;
    font-weight: 300;
    letter-spacing: 1px;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
}

/* ====== BREADCRUMBS ====== */
.breadcrumbs-area {
    background: var(--white);
    padding: 20px 0;
    border-bottom: 1px solid rgba(212, 175, 55, 0.15);
}

.breadcrumbs {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 10px;
    font-size: 0.95rem;
    color: var(--text-light);
}

.breadcrumbs a {
    color: var(--primary-gold);
    text-decoration: none;
    transition: var(--transition);
    font-weight: 500;
}

.breadcrumbs a:hover {
    color: var(--gold-dark);
}

.breadcrumbs .separator {
    color: var(--border-light);
    font-weight: 300;
}

.breadcrumbs .last-item {
    color: var(--text-dark);
    font-weight: 500;
}

/* ====== DETAILS SECTION (General Container for Content) ====== */
.details-area {
    padding: 100px 0;
    background: var(--off-white);
}

.details-container {
    max-width: 800px; 
    margin: 0 auto;
    align-items: start;
    display: block;
}

/* ====== DETAILS CARD (Used for the Form background) ====== */
.details-card {
    background: var(--white);
    border-radius: 16px;
    padding: 50px;
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(212, 175, 55, 0.2);
}

.details-title {
    font-size: 2rem;
    background: linear-gradient(135deg, var(--primary-gold) 0%, var(--gold-dark) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 30px;
    font-weight: 700;
    letter-spacing: 0.5px;
}

/* ====== RESERVE BUTTON (Used for Submit Button) ====== */
.reserve-btn {
    display: inline-block;
    width: 100%;
    padding: 18px 40px;
    background: linear-gradient(135deg, var(--secondary-red) 0%, #d40909 100%);
    color: var(--white);
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.1rem;
    text-align: center;
    transition: var(--transition);
    box-shadow: 0 4px 20px rgba(246, 10, 10, 0.3);
    text-transform: uppercase;
    letter-spacing: 1px;
    border: none;
    cursor: pointer;
}

.reserve-btn:hover {
    background: linear-gradient(135deg, var(--accent-dark) 0%, #0a0a2a 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 28px rgba(8, 8, 32, 0.4);
}

.reserve-btn i {
    margin-left: 8px;
}

/* ================================================================= */
/* RESERVATION FORM SPECIFIC STYLES */
/* ================================================================= */

.reservation-form-card {
    width: 100%;
}

.reservation-form label {
    display: block;
    font-weight: 600;
    color: var(--accent-dark);
    margin-top: 20px;
    margin-bottom: 8px;
    font-size: 0.95rem;
    letter-spacing: 0.2px;
}

.reservation-form input[type="text"],
.reservation-form input[type="email"],
.reservation-form select,
.reservation-form textarea {
    width: 100%;
    padding: 14px 18px;
    border: 1px solid var(--border-light);
    border-radius: 8px;
    font-size: 1rem;
    color: var(--text-dark);
    background-color: var(--off-white);
    transition: border-color 0.3s, box-shadow 0.3s;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
}

.reservation-form input:focus,
.reservation-form select:focus,
.reservation-form textarea:focus {
    border-color: var(--primary-gold);
    outline: none;
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
    background-color: var(--white);
}

.reservation-form textarea {
    resize: vertical;
    min-height: 120px;
}

.reservation-form select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2212%22%20height%3D%2212%22%20viewBox%3D%220%200%2012%2012%22%3E%3Cpath%20fill%3D%22%23D4AF37%22%20d%3D%22M6%209l4-4H2z%22%2F%3E%3C%2Fsvg%3E");
    background-repeat: no-repeat;
    background-position: right 18px center;
}

.error-messages {
    margin-top: 20px;
    margin-bottom: 20px;
    padding: 15px;
    background: #fbecec; 
    border: 1px solid #f09a9a; 
    border-radius: 8px;
}

.error-messages p {
    color: #cc0000; 
    font-weight: 600; 
    margin-bottom: 5px;
}

/* Modal Styling */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s, visibility 0.3s;
    z-index: 1000;
}

.modal-overlay.show {
    opacity: 1;
    visibility: visible;
}

.modal-box {
    background: var(--white);
    padding: 40px;
    border-radius: 12px;
    box-shadow: var(--shadow-lg);
    max-width: 450px;
    width: 90%;
    text-align: center;
    border: 2px solid var(--primary-gold);
}

.modal-box h2 {
    color: var(--primary-gold);
    margin-bottom: 15px;
    font-size: 1.8rem;
}

.modal-box p {
    color: var(--text-dark);
    margin-bottom: 30px;
    line-height: 1.5;
}

.modal-close-btn {
    padding: 12px 25px;
    background: linear-gradient(135deg, var(--secondary-red) 0%, #d40909 100%);
    color: var(--white);
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    transition: background 0.3s;
}

.modal-close-btn:hover {
    background: linear-gradient(135deg, var(--accent-dark) 0%, #0a0a2a 100%);
}


/* ====== RESPONSIVE DESIGN (From room.php) ====== */
@media (max-width: 992px) {
    .page-title {
        font-size: 2.5rem;
    }
    .details-card {
        padding: 40px 30px;
    }
}

@media (max-width: 768px) {
    .page-header {
        padding: 60px 0 40px;
    }
    .page-title {
        font-size: 2rem;
    }
    .page-description {
        font-size: 1.1rem;
    }
    .details-card {
        padding: 35px 25px;
    }
    .details-title {
        font-size: 1.8rem;
    }
    .reserve-btn {
        padding: 16px 30px;
        font-size: 1rem;
    }
    .breadcrumbs {
        font-size: 0.85rem;
    }
}

@media (max-width: 480px) {
    .details-card {
        padding: 25px 20px;
    }
    .page-title {
        font-size: 1.6rem;
        letter-spacing: 1px;
    }
    .details-title {
        font-size: 1.5rem;
    }
}
</style>

<div class="page-header">
    <div class="container">
        <h2 class="page-title">Reserve a Room</h2>
        <p class="page-description">Secure your preferred room with ease.</p>
    </div>
</div>

<div class="breadcrumbs-area">
    <div class="container">
        <div class="breadcrumbs">
            <span class="first-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></span>
            <span class="separator">›</span>
            <span><a href="rooms.php">Rooms</a></span>
            <span class="separator">›</span>
            <span class="last-item">Reserve a Room</span>
        </div>
    </div>
</div>

<div class="details-area">
    <div class="container">
        <div class="details-container"> 

            <div class="details-card reservation-form-card">
                <h3 class="details-title">Tenant Reservation Form</h3>
                
                <?php if (!empty($errors)): ?>
                    <div class="error-messages">
                        <p>Reservation Failed. Please correct the following errors:</p>
                        <ul style="list-style-type: disc; margin-left: 20px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" class="reservation-form" id="reservationForm">
                    
                    <label for="fname">First Name <span style="color: red;">*</span></label>
                    <input type="text" id="fname" name="fname" required 
                           value="<?php echo htmlspecialchars($formData['fname'] ?? ''); ?>" 
                           oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">

                    <label for="lname">Last Name <span style="color: red;">*</span></label>
                    <input type="text" id="lname" name="lname" required 
                           value="<?php echo htmlspecialchars($formData['lname'] ?? ''); ?>" 
                           oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">

                    <label for="mname">Middle Name (Optional)</label>
                    <input type="text" id="mname" name="mname" 
                           value="<?php echo htmlspecialchars($formData['mname'] ?? ''); ?>" 
                           oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '');">

                    <label for="number">Contact Number <span style="color: red;">*</span></label>
                    <input type="text" id="number" name="number" required 
                           value="<?php echo htmlspecialchars($formData['number'] ?? ''); ?>" 
                           oninput="this.value = this.value.replace(/[^0-9]/g, '');">

                    <label for="emer_contact">Emergency Contact Number <span style="color: red;">*</span></label>
                    <input type="text" id="emer_contact" name="emer_contact" required 
                           value="<?php echo htmlspecialchars($formData['emer_contact'] ?? ''); ?>" 
                           oninput="this.value = this.value.replace(/[^0-9]/g, '');">

                    <label for="email">Email Address <span style="color: red;">*</span></label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>">

                    <label for="roomSelect">Select Room <span style="color: red;">*</span></label>
                    <select name="room_id" id="roomSelect" required>
                        <option value="">Choose...</option>
                        <?php foreach ($rooms as $r): ?>
                            <option value="<?= $r['room_id'] ?>" 
                                <?= ($preselectedRoomID == $r['room_id'] && !$_POST) ? 'selected' : '' ?>
                                <?= (isset($_POST['room_id']) && $_POST['room_id'] == $r['room_id']) ? 'selected' : '' ?>>
                                Room <?= $r['room_number'] ?> 
                                <?= ($r['rstat_id'] == 3) ? '(Reserved - Pending)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="reserve-btn" style="margin-top: 30px;">
                        Submit Reservation
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>


<?php if ($message): ?>
<div id="successModal" class="modal-overlay show">
    <div class="modal-box">
        <h2><i class="fas fa-check-circle" style="color: var(--primary-gold); margin-right: 10px;"></i> Reservation Successful!</h2>
        <p><?php echo htmlspecialchars($message); ?></p>
        <button class="modal-close-btn" onclick="window.location.href='index.php'">Go to Home</button>
    </div>
</div>
<script>
    // Show modal on load if success message is present
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('successModal').classList.add('show');
    });
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>