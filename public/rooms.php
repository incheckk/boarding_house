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

<section class="rooms-section">
    <h2 class="page-title">Available Rooms</h2>

    <div class="rooms-grid">
        <?php foreach($rooms as $room): ?>

        <div class="room-card">
            <div class="room-img">
                <img src="./assets/images/Room.png" alt="Room Image">
            </div>

            <div class="room-info">
                <h3>Room <?= htmlspecialchars($room['room_number']) ?></h3>
                <p><strong>Type:</strong> <?= htmlspecialchars($room['room_size']) ?></p>
                <p><strong>Rate:</strong> ₱<?= number_format($room['room_rate'], 2) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($room['rstat_desc']) ?></p>
                <p><strong>Amenities:</strong> <?= htmlspecialchars($room['amenities'] ?: 'None') ?></p>
            </div>

            <a class="room-btn" 
               href="room.php?id=<?= $room['room_id'] ?>">
               View Details
            </a>

            <a class="reserve-btn" 
               href="reservation.php?room_id=<?= $room['room_id'] ?>">
               Reserve Now
            </a>
        </div>

        <?php endforeach; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
