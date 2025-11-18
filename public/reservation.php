<?php
$pageTitle = "Reserve a Room";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$message = "";

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $room_id = $_POST['room_id'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];

    $stmt = $pdo->prepare("
        INSERT INTO reservations (name, email, room_id, checkin, checkout) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$name, $email, $room_id, $checkin, $checkout]);

    $message = "Reservation submitted! We will contact you soon.";
}

// Fetch rooms for dropdown
$rooms = $pdo->query("SELECT room_id, room_number FROM room")->fetchAll();
?>

<h1>Reserve a Room</h1>

<?php if ($message): ?>
    <p class="success"><?= $message ?></p>
<?php endif; ?>

<form method="POST" class="form-box">

    <label>Name</label>
    <input type="text" name="name" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Select Room</label>
    <select name="room_id" required>
        <option value="">Choose...</option>
        <?php foreach($rooms as $r): ?>
            <option value="<?= $r['room_id'] ?>">Room <?= $r['room_number'] ?></option>
        <?php endforeach; ?>
    </select>

    <label>Check-in Date</label>
    <input type="date" name="checkin" required>

    <label>Check-out Date</label>
    <input type="date" name="checkout" required>

    <button type="submit">Submit Reservation</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
