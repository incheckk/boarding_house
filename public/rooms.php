<?php
$pageTitle = "Rooms";
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';

/*
  rooms.php
  - Filters: type (single|double|bunk), price_min, price_max
  - Sorting: sort=price_asc|price_desc
  - Availability: available=1 (only rooms where rs.rstat_desc = 'Vacant')
  - Pagination: page (default 1), per_page (default 9)
  - AJAX: if ajax=1 return only the rooms-grid html fragment
*/

/* -------------------------
   Helper: map incoming type -> DB value
   ------------------------- */
$typeMap = [
    'single' => 'Single Bed',
    'double' => 'Double Bed',
    'bunk'   => 'Bunk Bed'
];

/* -------------------------
   Read GET params (validate/sanitize)
   ------------------------- */
$type = isset($_GET['type']) ? trim($_GET['type']) : null;
$priceMin = isset($_GET['price_min']) && $_GET['price_min'] !== '' ? (float)$_GET['price_min'] : null;
$priceMax = isset($_GET['price_max']) && $_GET['price_max'] !== '' ? (float)$_GET['price_max'] : null;
$availableOnly = isset($_GET['available']) && $_GET['available'] == '1';
$sort = isset($_GET['sort']) ? $_GET['sort'] : null;

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 9;

$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == '1';

/* -------------------------
   Build SQL WHERE conditions and params
   ------------------------- */
$where = [];
$params = [];

// type filter
if ($type && array_key_exists($type, $typeMap)) {
    $where[] = "r.room_size = :room_size";
    $params[':room_size'] = $typeMap[$type];
}

// price filters
if (!is_null($priceMin)) {
    $where[] = "r.room_rate >= :price_min";
    $params[':price_min'] = $priceMin;
}
if (!is_null($priceMax)) {
    $where[] = "r.room_rate <= :price_max";
    $params[':price_max'] = $priceMax;
}

// availability
if ($availableOnly) {
    $where[] = "rs.rstat_desc = 'Vacant'";
}

// Combine
$whereSQL = "";
if (!empty($where)) {
    $whereSQL = "WHERE " . implode(" AND ", $where);
}

/* -------------------------
   Sorting (whitelist)
   ------------------------- */
$allowedSorts = [
    'price_asc'  => 'r.room_rate ASC',
    'price_desc' => 'r.room_rate DESC'
];
$orderBy = "r.room_number ASC"; // default

if ($sort && isset($allowedSorts[$sort])) {
    $orderBy = $allowedSorts[$sort];
}

/* -------------------------
   Count total for pagination
   ------------------------- */
$countSql = "
    SELECT COUNT(DISTINCT r.room_id) AS total
    FROM room r
    LEFT JOIN room_status rs ON r.rstat_id = rs.rstat_id
    LEFT JOIN roomamenities ra ON r.room_id = ra.room_id
    LEFT JOIN amenities a ON ra.amen_id = a.amen_id
    $whereSQL
";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = (int)ceil($total / $per_page);
$offset = ($page - 1) * $per_page;

/* -------------------------
   Main query (with GROUP_CONCAT and pagination)
   ------------------------- */
$sql = "
    SELECT r.room_id, r.room_number, r.room_size, r.room_rate, rs.rstat_desc,
           GROUP_CONCAT(a.amen_name SEPARATOR ', ') AS amenities
    FROM room r
    LEFT JOIN room_status rs ON r.rstat_id = rs.rstat_id
    LEFT JOIN roomamenities ra ON r.room_id = ra.room_id
    LEFT JOIN amenities a ON ra.amen_id = a.amen_id
    $whereSQL
    GROUP BY r.room_id
    ORDER BY $orderBy
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);

// bind params
foreach ($params as $k => $v) {
    if (is_int($v) || is_float($v)) {
        $stmt->bindValue($k, $v);
    } else {
        $stmt->bindValue($k, $v, PDO::PARAM_STR);
    }
}
$stmt->bindValue(':limit', (int)$per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* -------------------------
   Helper: render rooms grid fragment
   ------------------------- */
function render_rooms_grid($rooms) {
    if (empty($rooms)) {
        echo '<div class="no-results-container">';
        echo '<i class="fas fa-search"></i>';
        echo '<p class="no-results">No rooms match your search criteria.</p>';
        echo '<p class="no-results-hint">Try adjusting your filters or browse all available rooms.</p>';
        echo '</div>';
        return;
    }

    echo '<div class="rooms-grid" id="roomsGrid">';
    foreach ($rooms as $room) {
        $amen = $room['amenities'] ?: 'None';
        $roomNumber = htmlspecialchars($room['room_number']);
        $roomSize   = htmlspecialchars($room['room_size']);
        $roomRate   = number_format($room['room_rate'], 2);
        $status     = htmlspecialchars($room['rstat_desc']);
        $roomId     = (int)$room['room_id'];
        
        // Status class
        $statusLower = strtolower($status);
        $statusClass = 'status-available';
        if (strpos($statusLower, 'occupied') !== false) {
            $statusClass = 'status-occupied';
        } elseif (strpos($statusLower, 'maintenance') !== false) {
            $statusClass = 'status-maintenance';
        }

        echo <<<HTML
        <div class="room-card">
            <div class="room-image">
                <img src="./assets/images/Room.png" alt="Room {$roomNumber}">
                <span class="status-badge {$statusClass}">{$status}</span>
            </div>
            <div class="room-content">
                <h3 class="room-title">Room {$roomNumber}</h3>
                <div class="room-details">
                    <div class="detail-row">
                        <i class="fas fa-bed"></i>
                        <span>{$roomSize}</span>
                    </div>
                    <div class="detail-row">
                        <i class="fas fa-star"></i>
                        <span>{$amen}</span>
                    </div>
                </div>
                <div class="room-price">
                    <span class="price-label">Rate</span>
                    <span class="price-amount">₱{$roomRate}<span class="price-period">/mo</span></span>
                </div>
                <div class="room-actions">
                    <a class="btn-details" href="room.php?id={$roomId}">
                        <i class="fas fa-info-circle"></i> View Details
                    </a>
                    <a class="btn-reserve" href="reservation.php?room_id={$roomId}">
                        <i class="fas fa-calendar-check"></i> Reserve
                    </a>
                </div>
            </div>
        </div>
HTML;
    }
    echo '</div>'; // .rooms-grid
}

/* If AJAX request */
if ($isAjax) {
    render_rooms_grid($rooms);

    echo '<div id="ajaxPagination">';
    if ($page > 1) {
        $prev = $page - 1;
        echo '<a class="ajax-page" data-page="'.$prev.'" href="#">Prev</a> ';
    }
    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);
    for ($p = $start; $p <= $end; $p++) {
        $class = $p == $page ? 'ajax-page current' : 'ajax-page';
        echo '<a class="'.$class.'" data-page="'.$p.'" href="#">'.$p.'</a> ';
    }
    if ($page < $totalPages) {
        $next = $page + 1;
        echo '<a class="ajax-page" data-page="'.$next.'" href="#">Next</a>';
    }
    echo '</div>';

    exit;
}

/* -------------------------
   Helper function for pagination URLs
   ------------------------- */
function build_page_url($newPage) {
    $qs = $_GET;
    $qs['page'] = $newPage;
    if (!isset($qs['per_page'])) {
        $qs['per_page'] = 9;
    }
    return 'rooms.php?' . http_build_query($qs);
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
/* ================================================================= */
/* ROOMS PAGE - CONSISTENT WITH INDEX & ROOM DETAILS */
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

/* ====== ROOMS SECTION ====== */
.rooms-section {
    padding: 80px 0;
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
    padding-left: 20px;
    padding-right: 20px;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    text-align: center;
    background: linear-gradient(135deg, var(--primary-gold) 0%, var(--gold-dark) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 15px;
    letter-spacing: 0.5px;
}

/* ====== FILTER SUMMARY ====== */
.filter-results {
    text-align: center;
    padding: 20px;
    background: var(--white);
    border-radius: 12px;
    margin-bottom: 30px;
    border: 1px solid rgba(212, 175, 55, 0.2);
    box-shadow: var(--shadow-sm);
    color: var(--text-light);
    font-size: 0.95rem;
}

.filter-results strong {
    color: var(--text-dark);
    font-weight: 600;
}

/* ====== CONTROLS - IMPROVED ====== */
.controls {
    background: var(--white);
    padding: 25px 30px;
    border-radius: 12px;
    margin-bottom: 40px;
    box-shadow: var(--shadow-sm);
    border: 1px solid rgba(212, 175, 55, 0.2);
}

.controls form {
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}

.control-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.controls label {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

.controls label i {
    color: var(--primary-gold);
}

.controls select {
    padding: 10px 16px;
    border: 2px solid var(--border-light);
    border-radius: 8px;
    font-size: 0.95rem;
    background: var(--off-white);
    color: var(--text-dark);
    cursor: pointer;
    transition: var(--transition);
    font-family: inherit;
    min-width: 150px;
}

.controls select:focus {
    outline: none;
    border-color: var(--primary-gold);
    background: var(--white);
}

.controls select:hover {
    border-color: var(--gold-dark);
}

/* ====== ROOMS GRID ====== */
.rooms-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 30px;
    margin-bottom: 50px;
}

/* ====== ROOM CARD ====== */
.room-card {
    background: var(--white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(212, 175, 55, 0.2);
    transition: var(--transition);
    display: flex;
    flex-direction: column;
}

.room-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-gold);
}

.room-image {
    position: relative;
    width: 100%;
    height: 220px;
    overflow: hidden;
}

.room-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.room-card:hover .room-image img {
    transform: scale(1.05);
}

.status-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
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

.room-content {
    padding: 25px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.room-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 15px;
}

.room-details {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
}

.detail-row {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text-light);
    font-size: 0.95rem;
}

.detail-row i {
    color: var(--primary-gold);
    width: 18px;
    font-size: 1rem;
}

.room-price {
    padding: 20px;
    background: linear-gradient(135deg, var(--gold-light) 0%, rgba(244, 229, 192, 0.5) 100%);
    border-radius: 12px;
    margin-bottom: 20px;
    text-align: center;
    border: 1px solid rgba(212, 175, 55, 0.3);
}

.price-label {
    display: block;
    font-size: 0.85rem;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 5px;
    font-weight: 500;
}

.price-amount {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-dark);
}

.price-period {
    font-size: 1rem;
    font-weight: 400;
    color: var(--text-light);
}

.room-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: auto;
}

.btn-details,
.btn-reserve {
    padding: 12px 20px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    text-align: center;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.btn-details {
    background: linear-gradient(135deg, var(--off-white) 0%, #e8e8e8 100%);
    color: var(--text-dark);
    border: 2px solid var(--border-light);
}

.btn-details:hover {
    background: linear-gradient(135deg, var(--primary-gold) 0%, var(--gold-dark) 100%);
    color: var(--white);
    border-color: var(--primary-gold);
    transform: translateY(-2px);
}

.btn-reserve {
    background: linear-gradient(135deg, var(--secondary-red) 0%, #d40909 100%);
    color: var(--white);
    border: 2px solid var(--secondary-red);
}

.btn-reserve:hover {
    background: linear-gradient(135deg, var(--accent-dark) 0%, #0a0a2a 100%);
    border-color: var(--accent-dark);
    transform: translateY(-2px);
}

/* ====== NO RESULTS ====== */
.no-results-container {
    text-align: center;
    padding: 80px 20px;
    background: var(--white);
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    border: 1px solid rgba(212, 175, 55, 0.2);
}

.no-results-container i {
    font-size: 4rem;
    color: var(--gold-light);
    margin-bottom: 20px;
}

.no-results {
    font-size: 1.5rem;
    color: var(--text-dark);
    font-weight: 600;
    margin-bottom: 10px;
}

.no-results-hint {
    font-size: 1rem;
    color: var(--text-light);
}

/* ====== PAGINATION ====== */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 40px;
}

.page-link {
    padding: 12px 18px;
    background: var(--white);
    color: var(--text-dark);
    text-decoration: none;
    border-radius: 8px;
    border: 2px solid var(--border-light);
    font-weight: 600;
    transition: var(--transition);
    min-width: 45px;
    text-align: center;
}

.page-link:hover {
    background: linear-gradient(135deg, var(--primary-gold) 0%, var(--gold-dark) 100%);
    color: var(--white);
    border-color: var(--primary-gold);
    transform: translateY(-2px);
}

.page-link.current {
    background: linear-gradient(135deg, var(--primary-gold) 0%, var(--gold-dark) 100%);
    color: var(--white);
    border-color: var(--primary-gold);
    cursor: default;
}

/* ====== RESPONSIVE DESIGN ====== */
@media (max-width: 992px) {
    .rooms-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
    }
    
    .page-title {
        font-size: 2.2rem;
    }
}

@media (max-width: 768px) {
    .rooms-section {
        padding: 60px 15px;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .rooms-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .controls form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .control-group {
        width: 100%;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .controls select {
        width: 100%;
    }
    
    .controls label {
        margin-bottom: 0;
    }
    
    .room-actions {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .page-title {
        font-size: 1.8rem;
    }
    
    .room-title {
        font-size: 1.3rem;
    }
    
    .price-amount {
        font-size: 1.6rem;
    }
    
    .filter-results {
        padding: 15px;
        font-size: 0.9rem;
    }
    
    .controls {
        padding: 20px 15px;
    }
    
    .pagination {
        gap: 8px;
    }
    
    .page-link {
        padding: 10px 14px;
        min-width: 40px;
        font-size: 0.9rem;
    }
}
</style>

<section class="rooms-section">
    <h2 class="page-title">Available Rooms</h2>

    <!-- FILTER SUMMARY -->
    <?php if (!empty($_GET) && (isset($_GET['type']) || isset($_GET['price_min']) || isset($_GET['price_max']) || isset($_GET['sort']) || isset($_GET['available']))): ?>
        <p class="filter-results">
            <i class="fas fa-filter"></i> Showing results for:
            <?php
                $filters = [];
                if (!empty($_GET['type'])) {
                    $filters[] = "<strong>Type:</strong> " . htmlspecialchars($_GET['type']);
                }
                if (!empty($_GET['price_min'])) {
                    $filters[] = "<strong>Min Price:</strong> ₱" . htmlspecialchars($_GET['price_min']);
                }
                if (!empty($_GET['price_max'])) {
                    $filters[] = "<strong>Max Price:</strong> ₱" . htmlspecialchars($_GET['price_max']);
                }
                if (!empty($_GET['sort'])) {
                    $sortLabel = $_GET['sort'] == 'price_asc' ? 'Price: Low → High' : 'Price: High → Low';
                    $filters[] = "<strong>Sort:</strong> " . $sortLabel;
                }
                if (!empty($_GET['available'])) {
                    $filters[] = "<strong>Only Available Rooms</strong>";
                }
                echo implode(' • ', $filters);
            ?>
        </p>
    <?php endif; ?>

    <!-- SORT / PER PAGE CONTROLS -->
    <div class="controls">
        <form id="nonAjaxControls" method="get" action="rooms.php">
            <!-- Preserve existing filters -->
            <?php if (!empty($type)): ?>
                <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
            <?php endif; ?>
            
            <?php if (!is_null($priceMin)): ?>
                <input type="hidden" name="price_min" value="<?= htmlspecialchars($priceMin) ?>">
            <?php endif; ?>
            
            <?php if (!is_null($priceMax)): ?>
                <input type="hidden" name="price_max" value="<?= htmlspecialchars($priceMax) ?>">
            <?php endif; ?>
            
            <?php if ($availableOnly): ?>
                <input type="hidden" name="available" value="1">
            <?php endif; ?>

            <div class="control-group">
                <label for="sortSelect">
                    <i class="fas fa-sort"></i> Sort By:
                </label>
                <select id="sortSelect" name="sort" onchange="this.form.submit()">
                    <option value="">Default (Room Number)</option>
                    <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Price: Low → High</option>
                    <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Price: High → Low</option>
                </select>
            </div>

            <div class="control-group">
                <label for="perPage">
                    <i class="fas fa-th"></i> Show:
                </label>
                <select id="perPage" name="per_page" onchange="this.form.submit()">
                    <option value="6" <?= $per_page==6?'selected':'' ?>>6 Rooms</option>
                    <option value="9" <?= $per_page==9?'selected':'' ?>>9 Rooms</option>
                    <option value="12" <?= $per_page==12?'selected':'' ?>>12 Rooms</option>
                    <option value="18" <?= $per_page==18?'selected':'' ?>>18 Rooms</option>
                </select>
            </div>
        </form>
    </div>

    <!-- MAIN ROOMS GRID -->
    <?php render_rooms_grid($rooms); ?>

    <!-- PAGINATION LINKS -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="<?= htmlspecialchars(build_page_url($page-1)) ?>" class="page-link">
                <i class="fas fa-chevron-left"></i> Prev
            </a>
        <?php endif; ?>

        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        for ($p = $start; $p <= $end; $p++):
            if ($p == $page):
        ?>
                <span class="page-link current"><?= $p ?></span>
        <?php else: ?>
                <a href="<?= htmlspecialchars(build_page_url($p)) ?>" class="page-link"><?= $p ?></a>
        <?php endif; endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="<?= htmlspecialchars(build_page_url($page+1)) ?>" class="page-link">
                Next <i class="fas fa-chevron-right"></i>
            </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>