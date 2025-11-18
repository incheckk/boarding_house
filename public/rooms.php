<?php
$pageTitle = "Rooms";
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("
  SELECT r.room_id, r.room_number, r.room_size, r.room_rate, rs.rstat_desc,
    GROUP_CONCAT(a.amen_name SEPARATOR ', ') AS amenities
  FROM room r
  LEFT JOIN room_status rs ON r.rstat_id = rs.rstat_id
  LEFT JOIN roomamenities ra ON r.room_id = ra.room_id
  LEFT JOIN amenities a ON ra.amen_id = a.amen_id
  GROUP BY r.room_id
");

$rooms = $stmt->fetchAll();
?>

<h2>Available Rooms</h2>

<div class="rooms-grid">
<?php foreach($rooms as $room): ?>
  <div class="room-card">
      <h3>Room <?= htmlspecialchars($room['room_number']) ?></h3>
      <p>Size: <?= htmlspecialchars($room['room_size']) ?></p>
      <p>Rate: ₱<?= number_format($room['room_rate'], 2) ?></p>
      <p>Status: <?= htmlspecialchars($room['rstat_desc']) ?></p>
      <p>Amenities: <?= htmlspecialchars($room['amenities'] ?: 'None') ?></p>
  </div>
<?php endforeach; ?>
</div>

<a class="reserve-btn" href="reservation.php?room_id=<?= $room['room_id'] ?>">Reserve</a>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
