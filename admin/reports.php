<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
$pageTitle = "Reports & Analytics";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
require_once __DIR__ . '/../includes/auth.php';  // Ensure admin is logged in
require_once __DIR__ . '/../includes/db.php';

// Fetch occupancy data
$totalRooms = $pdo->query("SELECT COUNT(*) AS count FROM room")->fetch()['count'];
$occupiedRooms = $pdo->query("SELECT COUNT(*) AS count FROM room WHERE rstat_id = 2")->fetch()['count'];
$currentOccupancy = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;
$currentPath = htmlspecialchars(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), ENT_QUOTES, 'UTF-8');

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

// Fetch tenant churn and stay data (UPDATED: Use churned_tenants table)
$totalTenants = $pdo->query("SELECT COUNT(*) AS count FROM tenant")->fetch()['count'];

// Churned tenants: Count from churned_tenants in last quarter
$churnedTenantsLastQuarter = $pdo->query("
    SELECT COUNT(*) AS count
    FROM churned_tenants
    WHERE churn_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
")->fetch()['count'];

$churnRate = $totalTenants > 0 ? round(($churnedTenantsLastQuarter / $totalTenants) * 100, 1) : 0;

// Average stay for churned tenants: From churned_tenants
$avgStay = $pdo->query("
    SELECT COALESCE(AVG(TIMESTAMPDIFF(MONTH, check_in_date, check_out_date)), 0) AS avg_stay
    FROM churned_tenants
")->fetch()['avg_stay'];

// Fetch tenant details for table (UPDATED: From churned_tenants)
$tenantDetails = $pdo->query("
    SELECT 
        tenant_id,
        CONCAT(first_name, ' ', last_name) AS tenant_name, 
        check_in_date, 
        check_out_date,
        CONCAT(
            FLOOR(TIMESTAMPDIFF(DAY, check_in_date, check_out_date) / 365), ' year/s ',
            FLOOR((TIMESTAMPDIFF(DAY, check_in_date, check_out_date) % 365) / 30), ' month/s ',
            TIMESTAMPDIFF(DAY, check_in_date, check_out_date) % 30, ' day/s'
        ) AS stay_duration,
        'Churned' AS status
    FROM churned_tenants
    ORDER BY churn_date DESC
")->fetchAll(PDO::FETCH_ASSOC);

$selectedTenant = null;
if (isset($_GET['view_tenant'])) {
    $tenantId = intval($_GET['view_tenant']);

    // Active tenant first
    $stmt = $pdo->prepare("
        SELECT 
            t.first_name, t.last_name, t.middle_name,
            t.number, t.emergency_number, t.email, t.created_at,
            ts.tstat_desc AS tenant_status
        FROM tenant t
        LEFT JOIN tenant_status ts ON t.tstat_id = ts.tstat_id
        WHERE t.tenant_id = ?
        LIMIT 1
    ");
    $stmt->execute([$tenantId]);
    $selectedTenant = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fallback to churned_tenants
    if (!$selectedTenant) {
        $stmt = $pdo->prepare("
            SELECT 
                first_name, last_name, middle_name,
                number, emergency_number, email,
                check_in_date AS created_at,
                'Churned' AS tenant_status
            FROM churned_tenants
            WHERE tenant_id = ?
            ORDER BY churn_date DESC
            LIMIT 1
        ");
        $stmt->execute([$tenantId]);
        $selectedTenant = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
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
                    <th>Actions</th> <!-- New -->
                </tr>
            </thead>

            <tbody>
                <?php foreach ($tenantDetails as $tenant): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tenant['tenant_name']); ?></td>
                        <td><?php echo htmlspecialchars($tenant['check_in_date']); ?></td>
                        <td><?php echo htmlspecialchars($tenant['check_out_date']); ?></td>
                        <td><?php echo htmlspecialchars($tenant['stay_duration']); ?></td>
                        <td>
                            <span class="status-badge <?php echo strtolower(htmlspecialchars($tenant['status'])); ?>">
                                <?php echo htmlspecialchars($tenant['status']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?php echo $currentPath; ?>?view_tenant=<?php echo (int)$tenant['tenant_id']; ?>" 
                            class="btn-action" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

<?php if ($selectedTenant): ?>
    <!-- Modal styles (skip if already global) -->
    <style>
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); display: flex; justify-content: center; align-items: center;
            z-index: 1000; opacity: 1; visibility: visible; }
        .modal-box { background: #fff; padding: 30px; border-radius: 12px; max-width: 450px; width: 90%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .modal-close-btn { background: #6c757d; color: #fff; border: none; padding: 10px 15px;
            border-radius: 8px; cursor: pointer; margin-top: 20px; font-weight: 600; float: right; }
    </style>

    <div class="modal-overlay">
        <div class="modal-box">
            <h2>Tenant Details</h2>
            <ul class="tenant-details-list">
                <li><strong>Full Name:</strong>
                    <?php echo htmlspecialchars(
                        ($selectedTenant['first_name'] ?? '') . ' ' .
                        (!empty($selectedTenant['middle_name']) ? $selectedTenant['middle_name'] . ' ' : '') .
                        ($selectedTenant['last_name'] ?? '')
                    ); ?>
                </li>
                <li><strong>Email:</strong> <?php echo htmlspecialchars($selectedTenant['email'] ?? ''); ?></li>
                <li><strong>Contact Number:</strong> <?php echo htmlspecialchars($selectedTenant['number'] ?? ''); ?></li>
                <li><strong>Emergency Contact:</strong> <?php echo htmlspecialchars($selectedTenant['emergency_number'] ?? ''); ?></li>
                <li><strong>Status:</strong>
                    <span class="status-badge <?php echo strtolower($selectedTenant['tenant_status'] ?? ''); ?>">
                        <?php echo htmlspecialchars($selectedTenant['tenant_status'] ?? ''); ?>
                    </span>
                </li>
                <li><strong>Created At:</strong> <?php echo htmlspecialchars($selectedTenant['created_at'] ?? ''); ?></li>
            </ul>
            <!-- Close button goes back to the page without query string -->
            <button class="modal-close-btn" onclick="window.location.href='<?php echo $currentPath; ?>'">Close</button>
        </div>
    </div>
<?php endif; ?>
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