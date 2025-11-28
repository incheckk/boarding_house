<?php
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
?>

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
                <i class="fas fa-calendar-alt"></i>
                <div>
                    <strong><?php echo $pendingReservations; ?></strong>
                    <span>Pending Reservations</span>
                </div>
            </div>
        </div>
    </div>

</main>