<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
$pageTitle = "Admin Dashboard";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
require_once __DIR__ . '/../includes/auth.php';  // Ensure admin is logged in
require_once __DIR__ . '/../includes/db.php';

// Fetch statistics
$totalRooms = $pdo->query("SELECT COUNT(*) AS count FROM room")->fetch()['count'];
$activeTenants = $pdo->query("SELECT COUNT(*) AS count FROM tenant WHERE tstat_id = 2")->fetch()['count'];
$overduePayments = $pdo->query("SELECT COUNT(*) AS count FROM billing WHERE pstat_id != 1 AND due_date < CURDATE()")->fetch()['count'];
$monthlyRevenue = $pdo->query("SELECT COALESCE(SUM(received_amount), 0) AS sum FROM payment_history WHERE DATE_FORMAT(received_date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')")->fetch()['sum'];

// ADDED: Fetch tenant churn and stay data
$totalTenants = $pdo->query("SELECT COUNT(*) AS count FROM tenant")->fetch()['count'];
$churnedTenantsLastQuarter = $pdo->query(query: "
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

// === Fetch Room Analytics ===
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

// === Group by Room Type ===
$grouped_analytics = [];
foreach ($analytics as $row) {
    $grouped_analytics[$row['room_size']][] = $row;
}

// === Basic Stats ===
$total_views = array_sum(array_column($analytics, 'views'));
$total_reservations = array_sum(array_column($analytics, 'reservations'));

$max_views = $analytics ? max(array_column($analytics, 'views')) : 0;
$most_viewed = array_filter($analytics, fn($r) => $r['views'] == $max_views);
$most_viewed_rooms = array_map(function($r) { return $r['room_number']; }, $most_viewed);
$most_viewed_display = $max_views > 0 ? "Room" . (count($most_viewed_rooms) > 1 ? "s " : " ") . implode(', ', $most_viewed_rooms) . " ($max_views views)" : "No views yet";

// === CALL PYTHON SCRIPT (100% WORKING ON WINDOWS/XAMPP) ===
$insights = null;
$python_script = __DIR__ . '/analyze_rooms.py';

if (file_exists($python_script) && !empty($analytics)) {
    $json_data = json_encode($analytics, JSON_UNESCAPED_SLASHES);

    $descriptor = [
        0 => ['pipe', 'r'],  // stdin
        1 => ['pipe', 'w'],  // stdout
        2 => ['pipe', 'w']   // stderr
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
    } else {
        $insights = ['error' => 'Failed to start Python process'];
    }
} else {
    $insights = empty($analytics) ? ['info' => 'No data to analyze yet'] : ['error' => 'analyze_rooms.py not found'];
}
?>

<!-- Main Content Wrapper -->
<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <h1>Dashboard Overview</h1>
        <p>Welcome back! Here's what's happening with your boarding house today.</p>
    </div>

    <!-- Statistics Cards (UPDATED: Added Churn Rate and Average Stay with Icons) -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-icon blue">
                <i class="fas fa-bed"></i>
            </div>
            <h3>Total Rooms</h3>
            <div class="number"><?php echo $totalRooms; ?></div>
            <div class="trend"><i class="fas fa-check-circle"></i><?php echo $occupiedRooms; ?> Occupied, <?php echo $vacantRooms; ?> Vacant</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon green">
                <i class="fas fa-users"></i>
            </div>
            <h3>Active Tenants</h3>
            <div class="number"><?php echo $activeTenants; ?></div>
            <div class="trend"><i class="fas fa-arrow-up"></i>Check tenant management for new additions</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon orange">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>Overdue Payments</h3>
            <div class="number"><?php echo $overduePayments; ?></div>
            <div class="trend warning"><i class="fas fa-exclamation-circle"></i>Action required</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon purple">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <h3>Monthly Revenue</h3>
            <div class="number">₱<?php echo number_format($monthlyRevenue, 2); ?></div>
            <div class="trend"><i class="fas fa-check"></i>Collected this month</div>
        </div>

        <!-- ADDED: Tenant Churn Rate Card with Icon -->
        <div class="stat-card">
            <div class="stat-card-icon orange">
                <i class="fas fa-user-times"></i>
            </div>
            <h3>Tenant Churn Rate</h3>
            <div class="number"><?php echo $churnRate; ?>%</div>
            <div class="trend warning"><i class="fas fa-chart-line"></i>Based on last quarter</div>
        </div>

        <!-- ADDED: Average Stay Duration Card with Icon -->
        <div class="stat-card">
            <div class="stat-card-icon purple">
                <i class="fas fa-calendar-check"></i>
            </div>
            <h3>Average Stay Duration</h3>
            <div class="number"><?php echo round($avgStay, 1); ?> months</div>
            <div class="trend"><i class="fas fa-clock"></i>For churned tenants</div>
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
                        <td>₱<?php echo htmlspecialchars($payment['total_amount']); ?></td>
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

    <div class="page-header">
            <h1>AI Analytics Dashboard</h1>
            <p>Real-time insights powered by machine learning</p>
        </div>

        <!-- Summary Cards -->
        <div class="stats-grid">
            <div class="stat-card animated fadeInUp">
                <div class="stat-card-icon blue"><i class="fas fa-eye"></i></div>
                <h3>Total Views</h3>
                <div class="number"><?= $total_views ?></div>
            </div>
            <div class="stat-card animated fadeInUp" style="animation-delay:0.1s">
                <div class="stat-card-icon green"><i class="fas fa-calendar-check"></i></div>
                <h3>Total Inquiries</h3>
                <div class="number"><?= $total_reservations ?></div>
            </div>
            <div class="stat-card animated fadeInUp" style="animation-delay:0.2s">
                <div class="stat-card-icon orange"><i class="fas fa-star"></i></div>
                <h3>Most Viewed Room</h3>
                <div class="number"><?= htmlspecialchars($most_viewed_display) ?></div>
            </div>
        </div>

        <!-- Room Analytics by Type (Accordion) -->
        <section class="section">
            <div class="section-header"><h2>Room Analytics by Type</h2></div>

            <?php if (empty($grouped_analytics)): ?>
                <p class="no-data">No room data available yet.</p>
            <?php else: ?>
                <div class="accordion">
                    <?php $delay = 0; foreach ($grouped_analytics as $type => $rooms): ?>
                        <?php
                        $type_views = array_sum(array_column($rooms, 'views'));
                        $type_reservations = array_sum(array_column($rooms, 'reservations'));
                        ?>
                        <div class="accordion-item animated fadeInUp" style="animation-delay:<?= $delay ?>s">
                            <div class="accordion-header">
                                <h3><?= htmlspecialchars($type) ?></h3>
                                <div class="accordion-summary">
                                    <span>Views: <?= $type_views ?></span>
                                    <span>Inquiries: <?= $type_reservations ?></span>
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
                                                <div><strong><?= $row['views'] ?></strong><span>Views</span></div>
                                            </div>
                                            <div class="stat-box">
                                                <i class="fas fa-calendar-check"></i>
                                                <div><strong><?= $row['reservations'] ?></strong><span>Inquiries</span></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php $delay += 0.1; endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- AI Insights Section -->
        <section class="section" style="margin-top:50px;">
            <div class="section-header">
                <h2>AI-Powered Insights & Predictions</h2>
            </div>

            <?php if (isset($insights['error'])): ?>
                <div class="no-data" style="background:#ffebee;color:#c62828;padding:20px;border-radius:12px;">
                    <strong>AI Engine Error:</strong><br>
                    <?= nl2br(htmlspecialchars($insights['error'])) ?>
                </div>
            <?php elseif (isset($insights['info'])): ?>
                <p class="no-data"><?= htmlspecialchars($insights['info']) ?></p>
            <?php elseif ($insights): ?>
                <div class="insights-grid">
                    <div class="stat-card">
                        <h3>Views → Inquiries Correlation</h3>
                        <div class="number" style="font-size:3rem;color:#ff9500"><?= $insights['correlation'] ?></div>
                        <p>Closer to 1 = Views strongly predict bookings</p>
                    </div>
                    <div class="stat-card">
                        <h3>Predicted Bookings</h3>
                        <div class="number" style="font-size:3rem;color:#34c759">≈ <?= $insights['predicted_reservations'] ?></div>
                        <p>For a room with <strong>10 more views</strong> than average</p>
                    </div>
                </div>

                <h3 style="margin:40px 0 15px;font-size:1.4rem;">Recommended Rooms to Promote</h3>
                <div class="rooms-grid">
                    <?php foreach ($insights['top_rooms'] as $room): ?>
                        <div class="stat-card" style="text-align:center;padding:25px;">
                            <h4 style="color:#ff9500;margin:0 0 10px;">Room <?= htmlspecialchars($room['room_number']) ?></h4>
                            <p style="margin:8px 0;font-size:1.1rem;">
                                <strong><?= $room['views'] ?></strong> views → 
                                <strong><?= $room['reservations'] ?></strong> inquired
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h3 style="margin:50px 0 15px;font-size:1.4rem;">Average Performance by Room Type</h3>
                <table class="data-table performance-table">
                    <thead>
                        <tr><th>Room Type</th><th>Avg Views</th><th>Avg Inquiries</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($insights['group_stats'] as $type => $stats): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($type) ?></strong></td>
                                <td><?= number_format($stats['views'], 2) ?></td>
                                <td><?= number_format($stats['reservations'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <style>
            .stats-grid,
            .insights-grid,
            .rooms-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
            }
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
            .room-card,
            .stat-card {
                transition: transform 0.3s;
            }
            .room-card:hover,
            .stat-card:hover {
                transform: translateY(-5px);
            }
            .room-card {
                background: #f9f9f9;
                padding: 15px;
                border-radius: 12px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                
            }
            .room-card h4 {
                margin: 0 0 10px 0;
                font-size: 16px;
                font-weight: 600;
            }
            .stat-box {
                display: flex;
                align-items: center;
                gap: 20px;
                padding: 15px;
                background-color: #f5f5f7;
                color:  #ff9500;
                border-left: 2px solid #ff9500;
                border-radius: 12px;
                transition: background-color 0.3s ease;
            }
            .stat-box:hover {
                background-color: #e8e8ed;
            }
            .stat-box i {
                font-size: 28px;
                color:  #ff9500;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: rgba(0, 122, 255, 0.1);
                border-radius: 50%;
            }
            .stat-box div {
                display: flex;
                flex-direction: column;
            }
            .stat-box strong {
                font-size: 20px;
                color: #1d1d1f;
            }
            .stat-box span {
                font-size: 13px;
                color: #666;
            }
            .no-data {
                text-align: center;
                padding: 30px;
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                color: #666;
            }
            .stat-card-icon {
                margin-bottom: 10px;
            }
            .trend {
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .occupancy-stats .stat-box {
                border-left: 4px solid #ff9500;
            }
            .performance-table thead th {
                background-color:  #ff9500;
                color: #fff;
            }
            .performance-table tbody tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .performance-table tbody tr:hover {
                background-color: #e8e8ed;
            }
            .performance-table td, .performance-table th {
                text-align: center;
            }
        </style>
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