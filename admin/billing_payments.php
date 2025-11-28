<?php
$pageTitle = "Billing & Payments";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
require_once __DIR__ . '/../includes/auth.php';  // Ensure admin is logged in
require_once __DIR__ . '/../includes/db.php';

$message = "";

// Handle generate bill (unchanged)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_bill'])) {
    $tenant_id = intval($_POST['tenant_id']);
    $length_of_stay = intval($_POST['length_of_stay']);
    $utilities = floatval($_POST['utilities']);
    $amenities = floatval($_POST['amenities']);
    $room_rate = floatval($_POST['room_rate']);
    $total = ($room_rate * $length_of_stay) + $utilities + $amenities;
    $due_date = date('Y-m-01', strtotime('+1 month'));  // 1st of next month
    $current_month = date('Y-m');
    $electricity_reading = floatval($_POST['electricity_reading']);

    try {
        $stmt = $pdo->prepare("SELECT room_id FROM roomtenant WHERE tenant_id = ? AND check_out_date IS NULL LIMIT 1");
        $stmt->execute([$tenant_id]);
        $room_id = $stmt->fetch()['room_id'];

        $stmt = $pdo->prepare("INSERT INTO utilities (room_id, month_year, electricity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE electricity = ?");
        $stmt->execute([$room_id, $current_month, $electricity_reading, $electricity_reading]);

        $stmt = $pdo->prepare("INSERT INTO billing (tenant_id, room_id, rent_amount, utilities_amount, other_charges, due_date, pstat_id) VALUES (?, ?, ?, ?, ?, ?, 2)");
        $stmt->execute([$tenant_id, $room_id, $room_rate * $length_of_stay, $utilities, $amenities, $due_date]);

        $message = "Bill generated successfully!";
    } catch (PDOException $e) {
        $message = "Error generating bill: " . $e->getMessage();
    }
}

// Handle mark paid (unchanged)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_paid'])) {
    $payment_id = intval($_POST['payment_id']);
    try {
        $stmt = $pdo->prepare("UPDATE billing SET pstat_id = 1, payment_date = CURDATE() WHERE payment_id = ?");
        $stmt->execute([$payment_id]);
        $stmt = $pdo->prepare("INSERT INTO payment_history (payment_id, received_amount, received_by) SELECT payment_id, total_amount, 'Admin' FROM billing WHERE payment_id = ?");
        $stmt->execute([$payment_id]);
        $stmt = $pdo->prepare("SELECT tenant_id, room_id, due_date FROM billing WHERE payment_id = ?");
        $stmt->execute([$payment_id]);
        $bill = $stmt->fetch();
        if ($bill) {
            $next_due = date('Y-m-01', strtotime($bill['due_date'] . ' +1 month'));
            $stmt = $pdo->prepare("INSERT IGNORE INTO billing (tenant_id, room_id, rent_amount, due_date, pstat_id) SELECT ?, ?, room_rate, ?, 2 FROM room WHERE room_id = ?");
            $stmt->execute([$bill['tenant_id'], $bill['room_id'], $next_due, $bill['room_id']]);
        }
        $message = "Payment marked as paid!";
        preventResubmission();
    } catch (PDOException $e) {
        $message = "Error marking paid: " . $e->getMessage();
    }
}

// Handle notify overdue (unchanged)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notify_overdue'])) {
    $tenant_id = intval($_POST['tenant_id']);
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications_log (tenant_id, channel, subject, message) VALUES (?, 'email', 'Overdue Payment', 'Your payment is overdue. Please pay ASAP.')");
        $stmt->execute([$tenant_id]);
        $message = "Notification sent!";
    } catch (PDOException $e) {
        $message = "Error sending notification: " . $e->getMessage();
    }
}

// Fetch active tenants for dropdown (unchanged)
$tenants = $pdo->query("
    SELECT t.tenant_id, CONCAT(t.first_name, ' ', t.last_name) AS name, rt.check_in_date, r.room_rate, r.room_number, r.room_id
    FROM tenant t
    JOIN roomtenant rt ON t.tenant_id = rt.tenant_id AND rt.check_out_date IS NULL
    JOIN room r ON rt.room_id = r.room_id
    WHERE t.tstat_id = 2
")->fetchAll();

// Fetch all active tenants with billing info (UPDATED: Includes all tenants, calculates due date if no bill)
$billings = $pdo->query("
    SELECT 
        CONCAT(t.first_name, ' ', t.last_name) AS tenant_name,
        r.room_number,
        COALESCE(b.total_amount, r.room_rate) AS amount,  -- Use room rate if no bill
        COALESCE(b.due_date, DATE_FORMAT(DATE_ADD(rt.check_in_date, INTERVAL 1 MONTH), '%Y-%m-01')) AS due_date,  -- Calculate if no bill
        CASE 
            WHEN b.payment_id IS NULL THEN 'No Bill'
            WHEN b.pstat_id = 1 THEN 'Paid'
            WHEN b.due_date < CURDATE() THEN 'Overdue'
            ELSE 'Pending'
        END AS status,
        b.payment_id,
        t.tenant_id
    FROM tenant t
    JOIN roomtenant rt ON t.tenant_id = rt.tenant_id AND rt.check_out_date IS NULL
    JOIN room r ON rt.room_id = r.room_id
    LEFT JOIN billing b ON t.tenant_id = b.tenant_id AND b.due_date = (
        SELECT MAX(due_date) FROM billing WHERE tenant_id = t.tenant_id  -- Latest bill
    )
    WHERE t.tstat_id = 2
    ORDER BY t.last_name
")->fetchAll();

// Fetch payment history for modal (filtered by tenant if provided)
$selectedTenantId = isset($_GET['tenant_history']) ? intval($_GET['tenant_history']) : null;
$historyQuery = "SELECT ph.received_amount, ph.received_date, CONCAT(t.first_name, ' ', t.last_name) AS tenant_name, DATE_FORMAT(ph.received_date, '%Y-%m') AS month
                 FROM payment_history ph
                 JOIN billing b ON ph.payment_id = b.payment_id
                 JOIN tenant t ON b.tenant_id = t.tenant_id";
if ($selectedTenantId) {
    $historyQuery .= " WHERE b.tenant_id = ?";
}
$historyQuery .= " ORDER BY ph.received_date DESC";
$stmt = $pdo->prepare($historyQuery);
if ($selectedTenantId) {
    $stmt->execute([$selectedTenantId]);
} else {
    $stmt->execute();
}
$history = $stmt->fetchAll();
?>

<main class="main-content">
    <!-- Page Header -->
    <div class="page-header">
        <h1>Billing & Payments</h1>
        <p>Calculate rents automatically and track payment statuses. All payments are cash-only.</p>
    </div>

    <!-- Success/Error Message -->
    <?php if ($message): ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Automatic Rent Calculation Section (unchanged) -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Automatic Rent Calculation</h2>
        </div>
        <form method="POST" class="tenant-form" id="rent-calculator">
            <div class="form-grid">
                <div class="form-group">
                    <label for="tenant_id">Select Tenant</label>
                    <select id="tenant_id" name="tenant_id" onchange="populateTenantData()">
                        <option value="">Choose Tenant</option>
                        <?php foreach ($tenants as $tenant): ?>
                            <option value="<?php echo $tenant['tenant_id']; ?>" data-checkin="<?php echo $tenant['check_in_date']; ?>" data-rate="<?php echo $tenant['room_rate']; ?>" data-room="<?php echo $tenant['room_number']; ?>" data-roomid="<?php echo $tenant['room_id']; ?>">
                                <?php echo htmlspecialchars($tenant['name']); ?> (Room <?php echo $tenant['room_number']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="length_of_stay">Length of Stay (Months)</label>
                    <input type="number" id="length_of_stay" name="length_of_stay" min="1" oninput="this.value = this.value.replace(/[^0-9]/g, '');" readonly>
                </div>
                <div class="form-group">
                    <label for="room_rate">Room Rate (₱/month)</label>
                    <input type="number" id="room_rate" name="room_rate" min="0" step="0.01" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" readonly>
                </div>
                <div class="form-group">
                    <label for="people">Number of People (for Water)</label>
                    <input type="number" id="people" name="people" min="1" value="1" oninput="this.value = this.value.replace(/[^0-9]/g, '');" onchange="calculateTotal()">
                </div>
                <div class="form-group">
                    <label for="electricity_reading">Electricity Meter Reading (Current)</label>
                    <input type="number" id="electricity_reading" name="electricity_reading" min="0" step="0.01" value="0" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" onchange="calculateElectricity()">
                </div>
                <div class="form-group">
                    <label for="electricity_bill">Electricity Bill (₱)</label>
                    <input type="number" id="electricity_bill" name="electricity_bill" min="0" step="0.01" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" readonly>
                </div>
                <div class="form-group">
                    <label>Amenities (Select up to 3)</label>
                    <div>
                        <input type="checkbox" id="water" onchange="calculateTotal()"> Water (75₱/person)<br>
                        <input type="checkbox" id="wifi" onchange="calculateTotal()"> WiFi (250₱)<br>
                    </div>
                </div>
                <div class="form-group">
                    <label for="total_rent">Total Rent (₱)</label>
                    <input type="text" id="total_rent" name="total_rent" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" readonly>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" name="generate_bill" class="btn-action primary">Generate Bill</button>
                <button type="button" onclick="showHistory()" class="btn-action">Show Paid History</button>
            </div>
        </form>
    </div>

    <!-- Payment Tracking Section (UPDATED) -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Payment Tracking</h2>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Tenant Name</th>
                    <th>Room</th>
                    <th>Amount (₱)</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($billings as $bill): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($bill['tenant_name']); ?></td>
                        <td><?php echo htmlspecialchars($bill['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($bill['amount']); ?></td>
                        <td><?php echo htmlspecialchars($bill['due_date']); ?></td>
                        <td><span class="status-badge <?php echo strtolower(str_replace(' ', '-', $bill['status'])); ?>"><?php echo htmlspecialchars($bill['status']); ?></span></td>
                        <td>
                            <a href="?tenant_history=<?php echo $bill['tenant_id']; ?>" class="btn-action">Paid History</a>
                            <?php if ($bill['status'] === 'Overdue' && $bill['payment_id']): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="tenant_id" value="<?php echo $bill['tenant_id']; ?>">
                                    <button type="submit" name="notify_overdue" class="btn-action warning">Notify Tenant</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="payment_id" value="<?php echo $bill['payment_id']; ?>">
                                    <button type="submit" name="mark_paid" class="btn-action">Mark Paid</button>
                                </form>
                            <?php elseif ($bill['status'] === 'Pending' && $bill['payment_id']): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="payment_id" value="<?php echo $bill['payment_id']; ?>">
                                    <button type="submit" name="mark_paid" class="btn-action">Mark Paid</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Paid History Modal (UPDATED: Filtered by tenant) -->
    <div id="historyModal" class="modal-overlay" style="display: none;">
        <div class="modal-box" style="background: white; padding: 20px; border-radius: 8px; max-width: 600px; width: 90%;">
            <h2>Paid History<?php if ($selectedTenantId): ?> for Tenant<?php endif; ?></h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Month</th>
                        <th>Amount (₱)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($h['tenant_name']); ?></td>
                            <td><?php echo htmlspecialchars($h['month']); ?></td>
                            <td><?php echo htmlspecialchars($h['received_amount']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button onclick="closeHistory()" style="background: #007bff; color: white; border: none; padding: 10px; cursor: pointer;">Close</button>
        </div>
    </div>
</main>

<script>
    function populateTenantData() {
        const select = document.getElementById('tenant_id');
        const option = select.options[select.selectedIndex];
        const checkin = option.getAttribute('data-checkin');
        const rate = option.getAttribute('data-rate');
        const roomId = option.getAttribute('data-roomid');
        if (checkin) {
            const months = Math.max(1, Math.floor((new Date() - new Date(checkin)) / (1000 * 60 * 60 * 24 * 30)));
            document.getElementById('length_of_stay').value = months;
            document.getElementById('room_rate').value = rate;
            fetch(`?get_prev_electricity=${roomId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('electricity_reading').value = data.current || 0;
                    calculateElectricity();
                });
            calculateTotal();
        }
    }

    function calculateElectricity() {
        const current = parseFloat(document.getElementById('electricity_reading').value) || 0;
        const prev = parseFloat(document.getElementById('prev_electricity').value) || 0;
        const consumption = Math.max(0, current - prev);
        const bill = consumption * 17;
        document.getElementById('electricity_bill').value = bill.toFixed(2);
        calculateTotal();
    }

    function calculateTotal() {
        const length = parseFloat(document.getElementById('length_of_stay').value) || 0;
        const rate = parseFloat(document.getElementById('room_rate').value) || 0;
        const people = parseInt(document.getElementById('people').value) || 1;
        const electricity = parseFloat(document.getElementById('electricity_bill').value) || 0;
        let amenities = 0;
        if (document.getElementById('water').checked) amenities += 75 * people;
        if (document.getElementById('wifi').checked) amenities += 250;
        const total = (rate * length) + electricity + amenities;
        document.getElementById('total_rent').value = total.toFixed(2);
    }

    function showHistory() {
        document.getElementById('historyModal').style.display = 'flex';
    }

    function closeHistory() {
        document.getElementById('historyModal').style.display = 'none';
    }
</script>