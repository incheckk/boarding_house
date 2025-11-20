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
   Adjust these mappings if your room.room_size uses different text
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
$per_page = isset($_GET['per_page']) ? max(1, (int)$_GET['per_page']) : 9; // change default as needed

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

// availability (assuming rs.rstat_desc contains 'Vacant' for available rooms)
// If your DB uses different rstat_desc values or IDs, change the condition accordingly.
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
// We count distinct room_id
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
    // bind as string or number automatically
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
   Helper: render rooms grid fragment (used for AJAX and full page)
   We keep class names and IDs stable: .rooms-grid is the container
   ------------------------- */
function render_rooms_grid($rooms) {
    if (empty($rooms)) {
        echo '<p class="no-results">No rooms match your search.</p>';
        return;
    }

    echo '<div class="rooms-grid" id="roomsGrid">';
    foreach ($rooms as $room) {
        $amen = $room['amenities'] ?: 'None';
        // escape values
        $roomNumber = htmlspecialchars($room['room_number']);
        $roomSize   = htmlspecialchars($room['room_size']);
        $roomRate   = number_format($room['room_rate'], 2);
        $status     = htmlspecialchars($room['rstat_desc']);
        $roomId     = (int)$room['room_id'];

        echo <<<HTML
        <div class="room-card">
            <div class="room-img">
                <img src="./assets/images/Room.png" alt="Room {$roomNumber}">
            </div>
            <div class="room-info">
                <h3>Room {$roomNumber}</h3>
                <p><strong>Type:</strong> {$roomSize}</p>
                <p><strong>Rate:</strong> ₱{$roomRate}</p>
                <p><strong>Status:</strong> {$status}</p>
                <p><strong>Amenities:</strong> {$amen}</p>
            </div>
            <a class="room-btn" href="room.php?id={$roomId}">View Details</a>
            <a class="reserve-btn" href="reservation.php?room_id={$roomId}">Reserve Now</a>
        </div>
HTML;
    }
    echo '</div>'; // .rooms-grid
}

/* If AJAX request (ajax=1) -> return only grid HTML and pagination HTML snippet */
if ($isAjax) {
    render_rooms_grid($rooms);

    // simple pagination fragment returned for JS to update
    echo '<div id="ajaxPagination">';
    // prev link
    if ($page > 1) {
        $prev = $page - 1;
        echo '<a class="ajax-page" data-page="'.$prev.'" href="#">Prev</a> ';
    }
    // page numbers (show a small window)
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
   NON-AJAX: render full page (keeps your original page structure)
   ------------------------- */
?>

<section class="rooms-section">
    <h2 class="page-title">Available Rooms</h2>

    <!-- FILTER SUMMARY -->
    <?php if (!empty($_GET)): ?>
        <p class="filter-results">Showing results for:
            <?php
                if (!empty($_GET['type'])) {
                    echo "<strong>Type:</strong> " . htmlspecialchars($_GET['type']) . " ";
                }
                if (!empty($_GET['price_min'])) {
                    echo "<strong>Min Price:</strong> ₱" . htmlspecialchars($_GET['price_min']) . " ";
                }
                if (!empty($_GET['price_max'])) {
                    echo "<strong>Max Price:</strong> ₱" . htmlspecialchars($_GET['price_max']) . " ";
                }
                if (!empty($_GET['sort'])) {
                    echo "<strong>Sort:</strong> " . htmlspecialchars($_GET['sort']) . " ";
                }
                if (!empty($_GET['available'])) {
                    echo "<strong>Only Available:</strong> Yes";
                }
            ?>
        </p>
    <?php endif; ?>

    <!-- SORT / PER PAGE CONTROLS -->
    <div class="controls">
        <form id="nonAjaxControls" method="get" action="rooms.php">
            <!-- Preserve existing filters in controls -->
            <input type="hidden" name="type" value="<?= htmlspecialchars($type ?? '') ?>">
            <input type="hidden" name="price_min" value="<?= htmlspecialchars($priceMin ?? '') ?>">
            <input type="hidden" name="price_max" value="<?= htmlspecialchars($priceMax ?? '') ?>">
            <input type="hidden" name="available" value="<?= isset($_GET['available']) ? '1' : '' ?>">

            <label for="sortSelect">Sort:</label>
            <select id="sortSelect" name="sort" onchange="this.form.submit()">
                <option value="">Default</option>
                <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Price: Low → High</option>
                <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Price: High → Low</option>
            </select>

            <label for="perPage">Per page:</label>
            <select id="perPage" name="per_page" onchange="this.form.submit()">
                <option value="6" <?= $per_page==6?'selected':'' ?>>6</option>
                <option value="9" <?= $per_page==9?'selected':'' ?>>9</option>
                <option value="12" <?= $per_page==12?'selected':'' ?>>12</option>
            </select>
        </form>
    </div>

    <!-- MAIN ROOMS GRID (rendered by PHP) -->
    <?php render_rooms_grid($rooms); ?>

    <!-- PAGINATION LINKS (server-side fallback) -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="<?= htmlspecialchars(build_page_url($page-1)) ?>" class="page-link">Prev</a>
        <?php endif; ?>

        <?php
        // small window of pages
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
            <a href="<?= htmlspecialchars(build_page_url($page+1)) ?>" class="page-link">Next</a>
        <?php endif; ?>
    </div>
</section>

<?php
require_once __DIR__ . '/../includes/footer.php';

/* -------------------------
   Helper function to preserve query params while building pagination URLs
   ------------------------- */
function build_page_url($newPage) {
    // use current $_GET, but replace 'page' param
    $qs = $_GET;
    $qs['page'] = $newPage;
    // ensure per_page remains
    if (!isset($qs['per_page'])) {
        $qs['per_page'] = 9;
    }
    // build query string
    return 'rooms.php?' . http_build_query($qs);
}
