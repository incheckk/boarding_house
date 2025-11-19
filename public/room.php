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

<!-- ====== Page Header ====== -->
<div class="page-header default-template-gradient">
    <div class="container">
        <h2 class="page-title">Room <?= htmlspecialchars($room['room_number']) ?></h2>
        <p class="page-description">Room Information & Details</p>
    </div>
</div>

<!-- ====== Breadcrumbs ====== -->
<div class="breadcrumbs-area">
    <div class="container">
        <div class="breadcrumbs">
            <span class="first-item"><a href="index.php">Home</a></span>
            <span class="separator">></span>
            <span class="last-item">Room <?= htmlspecialchars($room['room_number']) ?></span>
        </div>
    </div>
</div>

<!-- ====== Room Details ====== -->
<div class="details-area">
    <div class="container">

        <!-- ROOM IMAGE -->
        <div class="gallery-header">
            <img src="./assets/images/Room.png" 
                 onerror="this.src='./assets/images/Room.png';"
                 alt="Room <?= htmlspecialchars($room['room_number']) ?>" 
                 class="main-image">
        </div>

        <!-- ROOM INFO -->
        <div class="details-content">
            <h3 class="title">Room Details</h3>

            <ul class="details-list">
                <li><span>Room Number:</span> <?= htmlspecialchars($room['room_number']) ?></li>
                <li><span>Size:</span> <?= htmlspecialchars($room['room_size']) ?></li>
                <li><span>Rate:</span> ₱<?= number_format($room['room_rate'], 2) ?></li>
                <li><span>Status:</span> <?= htmlspecialchars($room['rstat_desc']) ?></li>
                <li><span>Bed Type:</span> <?= htmlspecialchars($room['bed_type'] ?? "Single Bed") ?></li>
                <li><span>Amenities:</span> <?= htmlspecialchars($room['amenities'] ?: "None") ?></li>
            </ul>

            <a href="reservation.php?room_id=<?= $room['room_id'] ?>" 
               class="reserve-btn">
               Reserve This Room
            </a>
        </div>

    </div>
</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>