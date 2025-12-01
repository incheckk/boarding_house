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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
/* ================================================================= */
/* ROOM DETAILS PAGE - CONSISTENT WITH INDEX */
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

/* ====== ROOM DETAILS SECTION ====== */
.details-area {
    padding: 100px 0;
    background: var(--off-white);
}

.details-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 100px;
    max-width: 1100px;
    margin: 0 auto;
    align-items: start;
}

/* ====== IMAGE SECTION ====== */
.gallery-section {
    background: var(--white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(212, 175, 55, 0.2);
    position: sticky;
    top: 20px;
}

.main-image {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
    min-height: 400px;
    border-radius: 16px;
}

/* ====== DETAILS SECTION ====== */
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

.details-list {
    list-style: none;
    padding: 0;
    margin: 0 0 40px 0;
}

.detail-item {
    display: grid;
    grid-template-columns: 140px 1fr;
    padding: 18px 0;
    border-bottom: 1px solid var(--border-light);
    align-items: start;
    gap: 20px;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.detail-label i {
    color: var(--primary-gold);
    font-size: 1.1rem;
    width: 20px;
}

.detail-value {
    color: var(--text-light);
    font-size: 1rem;
    line-height: 1.6;
}

.status-badge {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-available {
    background: linear-gradient(135deg, #d4f1d4 0%, #a8e6a8 100%);
    color: #2d5f2d;
}

.status-occupied {
    background: linear-gradient(135deg, #f1d4d4 0%, #e6a8a8 100%);
    color: #5f2d2d;
}

.status-maintenance {
    background: linear-gradient(135deg, #f4e5c0 0%, #d4af37 100%);
    color: #5f4d2d;
}

/* ====== RESERVE BUTTON ====== */
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
}

.reserve-btn:hover {
    background: linear-gradient(135deg, var(--accent-dark) 0%, #0a0a2a 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 28px rgba(8, 8, 32, 0.4);
}

.reserve-btn i {
    margin-left: 8px;
}

/* ====== PRICE HIGHLIGHT ====== */
.price-highlight {
    background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold-dark) 20%);
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 40px;
    text-align: center;
    border: 2px solid var(--primary-gold);
    box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
}

.price-label {
    font-size: 0.9rem;
    color: var(--text-dark);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.price-amount {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--accent-dark);
    text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.5);
}

.price-period {
    font-size: 1rem;
    color: var(--text-light);
    font-weight: 400;
}

/* ====== AMENITIES TAGS ====== */
.amenities-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.amenity-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: linear-gradient(135deg, var(--off-white) 0%, #fdfbf7 100%);
    border: 1px solid rgba(212, 175, 55, 0.3);
    border-radius: 20px;
    font-size: 0.9rem;
    color: var(--text-dark);
    transition: var(--transition);
}

.amenity-tag:hover {
    background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold-dark) 20%);
    border-color: var(--primary-gold);
    transform: translateY(-2px);
}

.amenity-tag i {
    color: var(--primary-gold);
    font-size: 0.95rem;
}

/* ====== RESPONSIVE DESIGN ====== */
@media (max-width: 992px) {
    .details-container {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .gallery-section {
        position: static;
    }
    
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
    
    .detail-item {
        grid-template-columns: 1fr;
        gap: 8px;
        padding: 15px 0;
    }
    
    .price-amount {
        font-size: 2rem;
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
    
    .reserve-btn {
        padding: 16px 30px;
        font-size: 1rem;
    }
    
    .breadcrumbs {
        font-size: 0.85rem;
    }
    
    .price-highlight {
        padding: 20px;
    }
    
    .amenities-tags {
        gap: 8px;
    }
    
    .amenity-tag {
        font-size: 0.85rem;
        padding: 6px 12px;
    }
}
</style>

<!-- ====== Page Header ====== -->
<div class="page-header">
    <div class="container">
        <h2 class="page-title">Room <?= htmlspecialchars($room['room_number']) ?></h2>
        <p class="page-description">Room Information & Details</p>
    </div>
</div>

<!-- ====== Breadcrumbs ====== -->
<div class="breadcrumbs-area">
    <div class="container">
        <div class="breadcrumbs">
            <span class="first-item"><a href="index.php"><i class="fas fa-home"></i> Home</a></span>
            <span class="separator">›</span>
            <span><a href="rooms.php">Rooms</a></span>
            <span class="separator">›</span>
            <span class="last-item">Room <?= htmlspecialchars($room['room_number']) ?></span>
        </div>
    </div>
</div>

<!-- ====== Room Details ====== -->
<div class="details-area">
    <div class="container">
        <div class="details-container">
            
            <!-- ROOM IMAGE -->
            <div class="gallery-section">
                <img src="./assets/images/Room.png" 
                     onerror="this.src='./assets/images/Room.png';"
                     alt="Room <?= htmlspecialchars($room['room_number']) ?>" 
                     class="main-image">
            </div>

            <!-- ROOM INFO -->
            <div class="details-card">
                <h3 class="details-title">Room Details</h3>

                <!-- Price Highlight -->
                <div class="price-highlight">
                    <div class="price-label">Rate Per Month</div>
                    <div class="price-amount">
                        ₱<?= number_format($room['room_rate'], 2) ?>
                        <span class="price-period">/month</span>
                    </div>
                </div>

                <ul class="details-list">
                    <li class="detail-item">
                        <span class="detail-label">
                            <i class="fas fa-door-open"></i>
                            Room Number
                        </span>
                        <span class="detail-value"><?= htmlspecialchars($room['room_number']) ?></span>
                    </li>
                    <li class="detail-item">
                        <span class="detail-label">
                            <i class="fas fa-ruler-combined"></i>
                            Room Size
                        </span>
                        <span class="detail-value"><?= htmlspecialchars($room['room_size']) ?></span>
                    </li>
                    <li class="detail-item">
                        <span class="detail-label">
                            <i class="fas fa-bed"></i>
                            Bed Type
                        </span>
                        <span class="detail-value"><?= htmlspecialchars($room['bed_type'] ?? "Single Bed") ?></span>
                    </li>
                    <li class="detail-item">
                        <span class="detail-label">
                            <i class="fas fa-info-circle"></i>
                            Status
                        </span>
                        <span class="detail-value">
                            <?php
                            $status = strtolower($room['rstat_desc']);
                            $statusClass = 'status-available';
                            if (strpos($status, 'occupied') !== false) {
                                $statusClass = 'status-occupied';
                            } elseif (strpos($status, 'maintenance') !== false) {
                                $statusClass = 'status-maintenance';
                            }
                            ?>
                            <span class="status-badge <?= $statusClass ?>">
                                <?= htmlspecialchars($room['rstat_desc']) ?>
                            </span>
                        </span>
                    </li>
                    <li class="detail-item">
                        <span class="detail-label">
                            <i class="fas fa-star"></i>
                            Amenities
                        </span>
                        <div class="detail-value">
                            <?php if ($room['amenities']): ?>
                                <div class="amenities-tags">
                                    <?php 
                                    $amenitiesList = explode(', ', $room['amenities']);
                                    foreach ($amenitiesList as $amenity): 
                                    ?>
                                        <span class="amenity-tag">
                                            <i class="fas fa-check-circle"></i>
                                            <?= htmlspecialchars($amenity) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span class="amenity-tag">
                                    <i class="fas fa-times-circle"></i>
                                    No amenities listed
                                </span>
                            <?php endif; ?>
                        </div>
                    </li>
                </ul>

                <a href="reservation.php?room_id=<?= $room['room_id'] ?>" class="reserve-btn">
                    Reserve This Room
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>