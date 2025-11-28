<?php
$pageTitle = "Reports & Analytics";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
require_once __DIR__ . '/../includes/auth.php';  // Ensure admin is logged in
require_once __DIR__ . '/../includes/db.php';

// Fetch occupancy data
$totalRooms = $pdo->query("SELECT COUNT(*) AS count FROM room")->fetch()['count'];
$occupiedRooms = $pdo->query("SELECT COUNT(*) AS count FROM room WHERE rstat_id = 2")->fetch()['count'];
$currentOccupancy = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

// Fetch historical occupancy (last 12 months)
$historicalOccupancy = [];
for ($i = 11; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $occupied = $pdo->prepare("
        SELECT COUNT(DISTINCT rt.room_id) AS occupied
        FROM roomtenant rt
        WHERE rt.check_in_date <= LAST_DAY(?) AND (rt.check_out_date IS NULL OR rt.check_out_date >= ?)
    ");
    $occupied->execute([$month . '-01', $month . '-01']);
    $historicalOccupancy[$month] = $occupied->fetch()['occupied'];
}

// Fetch financial data
$totalIncome = $pdo->query("SELECT COALESCE(SUM(received_amount), 0) AS sum FROM payment_history WHERE YEAR(received_date) = YEAR(CURDATE())")->fetch()['sum'];
$overdueRent = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) AS sum FROM billing WHERE pstat_id != 1 AND due_date < CURDATE()")->fetch()['sum'];

// Fetch tenant churn and stay data (UPDATED: Churn rate for last quarter, avg stay for churned)
$totalTenants = $pdo->query("SELECT COUNT(*) AS count FROM tenant")->fetch()['count'];
$churnedTenantsLastQuarter = $pdo->query("SELECT COUNT(*) AS count FROM roomtenant WHERE check_out_date IS NOT NULL AND check_out_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)")->fetch()['count'];
$churnRate = $totalTenants > 0 ? round(($churnedTenantsLastQuarter / $totalTenants) * 100, 1) : 0;
$avgStay = $pdo->query("
    SELECT COALESCE(AVG(TIMESTAMPDIFF(MONTH, check_in_date, check_out_date)), 0) AS avg_stay
    FROM roomtenant
    WHERE check_out_date IS NOT NULL
")->fetch()['avg_stay'];

// Fetch tenant details for table (FIXED: Status is now tenant status, not occupancy)
$tenantDetails = $pdo->query("
    SELECT CONCAT(t.first_name, ' ', t.last_name) AS tenant_name, rt.check_in_date, rt.check_out_date,
           CASE 
               WHEN rt.check_out_date IS NULL THEN 
                   CONCAT(
                       FLOOR(TIMESTAMPDIFF(DAY, rt.check_in_date, CURDATE()) / 365), ' year/s ',
                       FLOOR((TIMESTAMPDIFF(DAY, rt.check_in_date, CURDATE()) % 365) / 30), ' month/s ',
                       TIMESTAMPDIFF(DAY, rt.check_in_date, CURDATE()) % 30, ' day/s'
                   )
               ELSE 
                   CONCAT(
                       FLOOR(TIMESTAMPDIFF(DAY, rt.check_in_date, rt.check_out_date) / 365), ' year/s ',
                       FLOOR((TIMESTAMPDIFF(DAY, rt.check_in_date, rt.check_out_date) % 365) / 30), ' month/s ',
                       TIMESTAMPDIFF(DAY, rt.check_in_date, rt.check_out_date) % 30, ' day/s'
                   )
           END AS stay_duration,
           ts.tstat_desc AS status
    FROM tenant t
    LEFT JOIN tenant_status ts ON t.tstat_id = ts.tstat_id
    LEFT JOIN roomtenant rt ON t.tenant_id = rt.tenant_id
    ORDER BY rt.check_in_date DESC
")->fetchAll();
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <h1>Reports & Analytics</h1>
        <p>View occupancy, financial summaries, and tenant analytics.</p>
    </div>

    <!-- Occupancy Reports -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2>Occupancy Reports</h2>
        </div>
        
        <!-- Current Occupancy -->
        <div class="occupancy-grid">
            <div class="occupancy-card">
                <h3>Current Occupancy</h3>
                <div class="number"><?php echo $currentOccupancy; ?>%</div>
                <p>Occupied: <?php echo $occupiedRooms; ?>/<?php echo $totalRooms; ?> rooms</p>
            </div>
        </div>

        <!-- Historical Trends (Chart) -->
        <div class="chart-placeholder">
            <h3>Historical Trends (Last 12 Months)</h3>
            <canvas id="occupancyChart" width="400" height="200"></canvas>
        </div>
    </section>

    <!-- Financial Reports Summary -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2>Financial Reports Summary</h2>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-icon green">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <h3>Total Income</h3>
                <div class="number">₱<?php echo number_format($totalIncome, 2); ?></div>
                <div class="trend">From this year</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-icon orange">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Overdue Rent</h3>
                <div class="number">₱<?php echo number_format($overdueRent, 2); ?></div>
                <div class="trend warning">Unpaid overdue bills</div>
            </div>
        </div>
    </section>

    <!-- Tenant Churn and Average Stay Duration -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2>Tenant Churn & Average Stay Duration</h2>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-icon purple">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Tenant Churn Rate</h3>
                <div class="number"><?php echo $churnRate; ?>%</div>
                <div class="trend warning">Based on last quarter</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-icon green">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Average Stay Duration</h3>
                <div class="number"><?php echo round($avgStay, 1); ?> month/s</div>
                <div class="trend">For churned tenants</div>
            </div>
        </div>

        <!-- Detailed Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tenant Name</th>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                    <th>Stay Duration</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($tenantDetails as $tenant): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tenant['tenant_name']); ?></td>
                        <td><?php echo htmlspecialchars($tenant['check_in_date']); ?></td>
                        <td><?php echo htmlspecialchars($tenant['check_out_date'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($tenant['stay_duration']); ?></td>
                        <td><span class="status-badge <?php echo strtolower($tenant['status']); ?>"><?php echo htmlspecialchars($tenant['status']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>

<!-- Chart.js for Historical Trends -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('occupancyChart').getContext('2d');
    const labels = <?php echo json_encode(array_keys($historicalOccupancy)); ?>;
    const data = <?php echo json_encode(array_values($historicalOccupancy)); ?>;
    const occupancyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Occupied Rooms',
                data: data,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>