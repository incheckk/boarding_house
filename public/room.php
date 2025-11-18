<?php
$pageTitle = "Room Details";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

$id = $_GET['id'] ?? null;
if (!$id) { echo "Room not found."; exit; }

$stmt = $pdo->prepare("
    SELECT r.*, rs.rstat_desc,
    GROUP_CONCAT(a.amen_name SEPARATOR ', ') AS amenities
    FROM room r
    LEFT JOIN room_status rs ON r.rstat_id = rs.rstat_id
    LEFT JOIN roomamenities ra ON r.room_id = ra.room_id
    LEFT JOIN amenities a ON ra.amen_id = a.amen_id
    WHERE r.room_id = ?
    GROUP BY r.room_id
");
$stmt->execute([$id]);
$room = $stmt->fetch();
?>

<h1>Room <?= $room['room_number'] ?></h1>
<p><b>Size:</b> <?= $room['room_size'] ?></p>
<p><b>Rate:</b> ₱<?= number_format($room['room_rate'],2) ?></p>
<p><b>Status:</b> <?= $room['rstat_desc'] ?></p>
<p><b>Amenities:</b> <?= $room['amenities'] ?></p>

<a href="reservation.php?room_id=<?= $room['room_id'] ?>" class="reserve-btn">Reserve Now</a>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>