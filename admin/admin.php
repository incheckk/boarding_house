<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
$pageTitle = "Admin Dashboard";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

// Fetch statistics
$totalRooms = $pdo->query("SELECT COUNT(*) AS count FROM room")->fetch()['count'];
$activeTenants = $pdo->query("SELECT COUNT(*) AS count FROM tenant WHERE tstat_id = 2")->fetch()['count'];
$overduePayments = $pdo->query("SELECT COUNT(*) AS count FROM billing WHERE pstat_id != 1 AND due_date < CURDATE()")->fetch()['count'];
$monthlyRevenue = $pdo->query("SELECT COALESCE(SUM(received_amount), 0) AS sum FROM payment_history WHERE DATE_FORMAT(received_date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->fetch()['sum'];

// Fetch tenant churn and stay data
$totalTenants = $pdo->query("SELECT COUNT(*) AS count FROM tenant")->fetch()['count'];
$churnedTenantsLastQuarter = $pdo->query("
    SELECT COUNT(*) AS count
    FROM churned_tenants
    WHERE churn_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
")->fetch()['count'];
$churnRate = $totalTenants > 0 ? round(($churnedTenantsLastQuarter / $totalTenants) * 100, 1) : 0;
$avgStay = $pdo->query("    
    SELECT COALESCE(AVG(TIMESTAMPDIFF(MONTH, check_in_date, check_out_date)), 0) AS avg_stay
    FROM churned_tenants
")->fetch()['avg_stay'];

// Fetch payment status overview (latest 5)
$paymentOverview = $pdo->query("
    SELECT r.room_number, CONCAT(t.first_name, ' ', t.last_name) AS tenant_name, b.due_date, b.total_amount,
           CASE WHEN b.pstat_id = 1 THEN 'Paid' WHEN b.due_date < CURDATE() THEN 'Overdue' ELSE 'Pending' END AS status
    FROM billing b
    JOIN tenant t ON b.tenant_id = t.tenant_id
    JOIN room r ON b.room_id = r.room_id
    ORDER BY b.due_date DESC
    LIMIT 5
")->fetchAll();

// Fetch occupancy data
$occupiedRooms = $pdo->query("SELECT COUNT(*) AS count FROM room WHERE rstat_id = 2")->fetch()['count'];
$vacantRooms = $pdo->query("SELECT COUNT(*) AS count FROM room WHERE rstat_id IN (1,3)")->fetch()['count'];
$occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;
$avgStayDuration = $pdo->query("
    SELECT COALESCE(AVG(TIMESTAMPDIFF(MONTH, rt.check_in_date, CURDATE())), 0) AS avg_stay
    FROM roomtenant rt
    JOIN tenant t ON rt.tenant_id = t.tenant_id
    WHERE t.tstat_id = 2 AND rt.check_out_date IS NULL
")->fetch()['avg_stay'];
$pendingReservations = $pdo->query("SELECT COUNT(*) AS count FROM reservation WHERE restat_id = 1")->fetch()['count'];

// Fetch Room Analytics
$sql = "
    SELECT 
        r.room_size,
        r.room_number,
        COUNT(CASE WHEN el.event_type = 'view' THEN 1 END) AS views,
        COUNT(CASE WHEN el.event_type = 'reserve' THEN 1 END) AS reservations
    FROM room r
    LEFT JOIN event_logs el ON r.room_id = el.room_id
    GROUP BY r.room_id
    ORDER BY r.room_size, r.room_number
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$analytics = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by Room Type
$grouped_analytics = [];
foreach ($analytics as $row) {
    $grouped_analytics[$row['room_size']][] = $row;
}

// Basic Stats
$total_views = array_sum(array_column($analytics, 'views'));
$total_reservations = array_sum(array_column($analytics, 'reservations'));

$max_views = $analytics ? max(array_column($analytics, 'views')) : 0;
$most_viewed = array_filter($analytics, fn($r) => $r['views'] == $max_views);
$most_viewed_rooms = array_map(function($r) { return $r['room_number']; }, $most_viewed);
$most_viewed_display = $max_views > 0 ? "Room" . (count($most_viewed_rooms) > 1 ? "s " : " ") . implode(', ', $most_viewed_rooms) . " ($max_views views)" : "No views yet";

// AI Insights (simplified for this example)
$insights = null;
$python_script = __DIR__ . '/analyze_rooms.py';

if (file_exists($python_script) && !empty($analytics)) {
    $json_data = json_encode($analytics, JSON_UNESCAPED_SLASHES);
    $descriptor = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w']
    ];
    $process = proc_open('python -u "' . $python_script . '"', $descriptor, $pipes);
    if (is_resource($process)) {
        fwrite($pipes[0], $json_data);
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        proc_close($process);
        if ($error) {
            $insights = ['error' => 'Python Error: ' . trim($error)];
        } else {
            $insights = json_decode($output, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $insights = ['error' => 'Invalid JSON from Python: ' . json_last_error_msg()];
            }
        }
    }
} else {
    $insights = empty($analytics) ? ['info' => 'No data to analyze yet'] : ['error' => 'analyze_rooms.py not found'];
}
?>

<style>
/* ===== FIXED STAT CARDS STYLING ===== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.stat-card {
    background: #fff;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.stat-card-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    font-size: 28px;
    color: #fff;
}

.stat-card-icon.blue {
    background: linear-gradient(135deg, #007aff 0%, #005ecb 100%);
}

.stat-card-icon.green {
    background: linear-gradient(135deg, #34c759 0%, #28a745 100%);
}

.stat-card-icon.orange {
    background: linear-gradient(135deg, #ff9500 0%, #ff8c00 100%);
}

.stat-card-icon.purple {
    background: linear-gradient(135deg, #af52de 0%, #9b59b6 100%);
}

.stat-card-icon.red {
    background: linear-gradient(135deg, #ff3b30 0%, #dc3545 100%);
}

.stat-card h3 {
    font-size: 15px;
    font-weight: 600;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
}

.stat-card .number {
    font-size: 42px;
    font-weight: 700;
    color: #1d1d1f;
    margin-bottom: 15px;
    line-height: 1.2;
}

.stat-card .trend {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #666;
}

.stat-card .trend i {
    font-size: 14px;
}

.stat-card .trend.warning {
    color: #ff9500;
}

/* ===== INSIGHTS GRID ===== */
.insights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-top: 25px;
}

.rooms-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

/* ===== ACCORDION ===== */
.accordion {
    margin-top: 25px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.accordion-item {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    overflow: hidden;
}

.accordion-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: #f5f5f7;
    cursor: pointer;
    transition: background 0.3s;
}

.accordion-header:hover {
    background: #e8e8ed;
}

.accordion-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.accordion-summary {
    display: flex;
    gap: 15px;
    color: #666;
    font-size: 14px;
}

.accordion-icon {
    transition: transform 0.3s;
}

.accordion-item.active .accordion-icon {
    transform: rotate(180deg);
}

.accordion-body {
    max-height: 0;
    overflow: hidden;
    padding: 0 20px;
    background: #fafafa;
    transition: max-height 0.4s ease, padding 0.4s ease;
}

.accordion-item.active .accordion-body {
    max-height: 2000px;
    padding: 20px;
}

/* ===== ROOM CARDS ===== */
.room-card {
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: transform 0.3s;
}

.room-card:hover {
    transform: translateY(-3px);
}

.room-card h4 {
    margin: 0 0 15px 0;
    font-size: 18px;
    font-weight: 600;
    color: #1d1d1f;
}

/* ===== STAT BOX ===== */
.stat-box {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background-color: #f5f5f7;
    border-left: 4px solid #ff9500;
    border-radius: 8px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
}

.stat-box:hover {
    background-color: #e8e8ed;
}

.stat-box i {
    font-size: 24px;
    color: #ff9500;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(255, 149, 0, 0.1);
    border-radius: 50%;
}

.stat-box div {
    display: flex;
    flex-direction: column;
}

.stat-box strong {
    font-size: 20px;
    color: #1d1d1f;
    font-weight: 700;
}

.stat-box span {
    font-size: 13px;
    color: #666;
    margin-top: 2px;
}

/* ===== NO DATA ===== */
.no-data {
    text-align: center;
    padding: 40px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    color: #666;
    font-size: 15px;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-card .number {
        font-size: 36px;
    }
}
</style>

<!-- Main Content Wrapper -->
<main class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Dashboard Overview</h1>
        <p>Welcome back! Here's what's happening with your boarding house today.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-icon blue">
                <i class="fas fa-bed"></i>
            </div>
            <h3>Total Rooms</h3>
            <div class="number"><?php echo $totalRooms; ?></div>
            <div class="trend">
                <i class="fas fa-check-circle"></i>
                <span><?php echo $occupiedRooms; ?> Occupied, <?php echo $vacantRooms; ?> Vacant</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon green">
                <i class="fas fa-users"></i>
            </div>
            <h3>Active Tenants</h3>
            <div class="number"><?php echo $activeTenants; ?></div>
            <div class="trend">
                <i class="fas fa-arrow-up"></i>
                <span>Check tenant management for new additions</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon orange">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>Overdue Payments</h3>
            <div class="number"><?php echo $overduePayments; ?></div>
            <div class="trend warning">
                <i class="fas fa-exclamation-circle"></i>
                <span>Action required</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon purple">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <h3>Monthly Revenue</h3>
            <div class="number">₱<?php echo number_format($monthlyRevenue, 2); ?></div>
            <div class="trend">
                <i class="fas fa-check"></i>
                <span>Collected this month</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon red">
                <i class="fas fa-user-times"></i>
            </div>
            <h3>Tenant Churn Rate</h3>
            <div class="number"><?php echo $churnRate; ?>%</div>
            <div class="trend warning">
                <i class="fas fa-chart-line"></i>
                <span>Based on last quarter</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon purple">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h3>Average Stay Duration</h3>
            <div class="number"><?php echo round($avgStay, 1); ?> month/s</div>
            <div class="trend">
                <i class="fas fa-clock"></i>
                <span>For churned tenants</span>
            </div>
        </div>
    </div>

    <!-- Payment Status Overview -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Payment Status Overview</h2>
            <a href="billing_payments.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>ROOM</th>
                    <th>TENANT NAME</th>
                    <th>DUE DATE</th>
                    <th>AMOUNT</th>
                    <th>STATUS</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paymentOverview as $payment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($payment['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($payment['tenant_name']); ?></td>
                        <td><?php echo htmlspecialchars($payment['due_date']); ?></td>
                        <td>₱<?php echo number_format($payment['total_amount'], 2); ?></td>
                        <td><span class="status-badge <?php echo strtolower($payment['status']); ?>"><?php echo htmlspecialchars($payment['status']); ?></span></td>
                        <td>
                            <?php if ($payment['status'] !== 'Paid'): ?>
                                <a href="billing_payments.php" class="btn-action">Mark Paid</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Room Occupancy Overview -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Room Occupancy Overview</h2>
            <a href="room_management.php" class="view-all">Manage Rooms <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="occupancy-grid">
            <div class="occupancy-card">
                <h3>Occupied Rooms</h3>
                <div class="number"><?php echo $occupiedRooms; ?></div>
            </div>
            <div class="occupancy-card">
                <h3>Vacant Rooms</h3>
                <div class="number"><?php echo $vacantRooms; ?></div>
            </div>
        </div>

        <div class="occupancy-stats">
            <div class="stat-box">
                <i class="fas fa-chart-pie"></i>
                <div>
                    <strong><?php echo $occupancyRate; ?>%</strong>
                    <span>Occupancy Rate</span>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-clock"></i>
                <div>
                    <strong><?php echo round($avgStayDuration, 1); ?> months</strong>
                    <span>Avg. Stay Duration</span>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-hourglass-half"></i>
                <div>
                    <strong><?php echo $pendingReservations; ?></strong>
                    <span>Pending Reservations</span>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Analytics Dashboard -->
    <div class="page-header" style="margin-top: 60px;">
        <h1>AI Analytics Dashboard</h1>
        <p>Real-time insights powered by machine learning</p>
    </div>

    <!-- AI Summary Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-icon blue">
                <i class="fas fa-eye"></i>
            </div>
            <h3>Total Views</h3>
            <div class="number"><?= $total_views ?></div>
            <div class="trend">
                <i class="fas fa-chart-line"></i>
                <span>Across all rooms</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon green">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h3>Total Inquiries</h3>
            <div class="number"><?= $total_reservations ?></div>
            <div class="trend">
                <i class="fas fa-user-check"></i>
                <span>Reservation requests</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon orange">
                <i class="fas fa-star"></i>
            </div>
            <h3>Most Viewed Room</h3>
            <div class="number" style="font-size: 20px;"><?= htmlspecialchars($most_viewed_display) ?></div>
            <div class="trend">
                <i class="fas fa-trophy"></i>
                <span>Top Room</span>
            </div>
        </div>
    </div>

    <!-- Room Analytics by Type -->
    <section class="section">
        <div class="section-header">
            <h2>Room Analytics by Type</h2>
        </div>

        <?php if (empty($grouped_analytics)): ?>
            <p class="no-data">No room data available yet.</p>
        <?php else: ?>
            <div class="accordion">
                <?php foreach ($grouped_analytics as $type => $rooms): ?>
                    <?php
                    $type_views = array_sum(array_column($rooms, 'views'));
                    $type_reservations = array_sum(array_column($rooms, 'reservations'));
                    ?>
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <h3><?= htmlspecialchars($type) ?></h3>
                            <div class="accordion-summary">
                                <span><i class="fas fa-eye"></i> Views: <?= $type_views ?></span>
                                <span><i class="fas fa-calendar-check"></i> Inquiries: <?= $type_reservations ?></span>
                            </div>
                            <i class="fas fa-chevron-down accordion-icon"></i>
                        </div>
                        <div class="accordion-body">
                            <div class="rooms-grid">
                                <?php foreach ($rooms as $row): ?>
                                    <div class="room-card">
                                        <h4>Room <?= htmlspecialchars($row['room_number']) ?></h4>
                                        <div class="stat-box">
                                            <i class="fas fa-eye"></i>
                                            <div>
                                                <strong><?= $row['views'] ?></strong>
                                                <span>Views</span>
                                            </div>
                                        </div>
                                        <div class="stat-box">
                                            <i class="fas fa-calendar-check"></i>
                                            <div>
                                                <strong><?= $row['reservations'] ?></strong>
                                                <span>Inquiries</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- AI Insights Section -->
    <?php if ($insights && !isset($insights['info'])): ?>
    <section class="section" style="margin-top:50px;">
        <div class="section-header">
            <h2>AI-Powered Insights & Predictions</h2>
        </div>

        <?php if (isset($insights['error'])): ?>
            <div class="no-data" style="background:#ffebee;color:#c62828;">
                <strong>AI Engine Error:</strong><br>
                <?= nl2br(htmlspecialchars($insights['error'])) ?>
            </div>
        <?php else: ?>
            <div class="insights-grid">
                <div class="stat-card">
                    <div class="stat-card-icon blue">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Views → Inquiries Correlation</h3>
                    <div class="number"><?= $insights['correlation'] ?></div>
                    <div class="trend">
                        <span>Closer to 1 = Views strongly predict bookings</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-icon green">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3>Predicted Bookings</h3>
                    <div class="number">≈ <?= $insights['predicted_reservations'] ?></div>
                    <div class="trend">
                        <span>For a room with <strong>10 more views</strong> than average</span>
                    </div>
                </div>
            </div>

            <h3 style="margin:40px 0 15px;font-size:1.4rem;">Recommended Rooms to Promote</h3>
            <div class="rooms-grid">
                <?php foreach ($insights['top_rooms'] as $room): ?>
                    <div class="stat-card" style="text-align:center;">
                        <h4 style="color:#ff9500;margin:0 0 10px;">Room <?= htmlspecialchars($room['room_number']) ?></h4>
                        <p style="margin:8px 0;font-size:1.1rem;">
                            <strong><?= $room['views'] ?></strong> views → 
                            <strong><?= $room['reservations'] ?></strong> inquired
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>
</main>

<script>
document.querySelectorAll('.accordion-header').forEach(header => {
    header.addEventListener('click', () => {
        const item = header.parentElement;
        const body = item.querySelector('.accordion-body');
        item.classList.toggle('active');
        body.style.maxHeight = item.classList.contains('active') ? body.scrollHeight + 'px' : '0';
    });
});
</script>