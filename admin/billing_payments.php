<?php
session_start();

// Include non-outputting requires first (DB and auth)
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

// Handle AJAX and PDF requests BEFORE any output
if (isset($_GET['get_prev_electricity'])) {
    $roomId = intval($_GET['get_prev_electricity']);
    $prevMonth = date('Y-m', strtotime('-1 month'));
    $stmt = $pdo->prepare("SELECT electricity FROM utilities WHERE room_id = ? AND month_year = ?");
    $stmt->execute([$roomId, $prevMonth]);
    $prev = $stmt->fetch()['electricity'] ?? 0;
    header('Content-Type: application/json');
    echo json_encode(['previous' => $prev]);
    exit;
}

// Handle PDF receipt view (inline)
if (isset($_GET['view_receipt'])) {
    $tenant_id = intval($_GET['tenant_id']);
    $month = $_GET['month'];

    // Fetch bill details for the month
    $stmt = $pdo->prepare("
        SELECT b.rent_amount, b.utilities_amount, b.other_charges, b.total_amount, b.due_date,
               CONCAT(t.first_name, ' ', t.last_name) AS tenant_name, r.room_number
        FROM billing b
        JOIN tenant t ON b.tenant_id = t.tenant_id
        JOIN room r ON b.room_id = r.room_id
        WHERE b.tenant_id = ? AND DATE_FORMAT(b.due_date, '%Y-%m') = ?
    ");
    $stmt->execute([$tenant_id, $month]);
    $bill = $stmt->fetch();

    if ($bill) {
        require_once __DIR__ . '/../vendor/setasign/fpdf/fpdf.php';

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 128);
        $pdf->Cell(0, 12, 'CASA VILLAGRACIA Payment Receipt', 0, 1, 'C');
        $pdf->SetLineWidth(0.5);
        $pdf->Line(10, 25, 200, 25);
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 8, 'Tenant Details:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, 'Tenant: ' . $bill['tenant_name'], 0, 1);
        $pdf->Cell(0, 8, 'Room: ' . $bill['room_number'], 0, 1);
        $pdf->Cell(0, 8, 'Month: ' . $month, 0, 1);
        $pdf->Cell(0, 8, 'Due Date: ' . $bill['due_date'], 0, 1);
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'Bill Breakdown:', 0, 1);
        $pdf->Ln(5);

        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(80, 10, 'Description', 1, 0, 'L', true);
        $pdf->Cell(110, 10, 'Amount (PHP)', 1, 1, 'R', true);

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(80, 8, 'Rent', 1, 0, 'L');
        $pdf->Cell(110, 8, number_format($bill['rent_amount'], 2), 1, 1, 'R');
        $pdf->Cell(80, 8, 'Electricity', 1, 0, 'L');
        $pdf->Cell(110, 8, number_format($bill['utilities_amount'], 2), 1, 1, 'R');
        $pdf->Cell(80, 8, 'Amenities', 1, 0, 'L');
        $pdf->Cell(110, 8, number_format($bill['other_charges'], 2), 1, 1, 'R');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, 10, 'Total', 1, 0, 'L');
        $pdf->Cell(110, 10, number_format($bill['total_amount'], 2), 1, 1, 'R');
        $pdf->Ln(15);

        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, 'Thank you!', 0, 1, 'C');

        $pdf->Output('I', 'receipt_' . $month . '.pdf');
        exit;
    }
    exit;
}

// Handle PDF download (force download)
if (isset($_GET['download_receipt'])) {
    $tenant_id = intval($_GET['tenant_id']);
    $month = $_GET['month'];

    $stmt = $pdo->prepare("
        SELECT b.rent_amount, b.utilities_amount, b.other_charges, b.total_amount, b.due_date,
               CONCAT(t.first_name, ' ', t.last_name) AS tenant_name, r.room_number
        FROM billing b
        JOIN tenant t ON b.tenant_id = t.tenant_id
        JOIN room r ON b.room_id = r.room_id
        WHERE b.tenant_id = ? AND DATE_FORMAT(b.due_date, '%Y-%m') = ?
    ");
    $stmt->execute([$tenant_id, $month]);
    $bill = $stmt->fetch();

    if ($bill) {
        require_once __DIR__ . '/../vendor/setasign/fpdf/fpdf.php';

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 128);
        $pdf->Cell(0, 12, 'Boarding House Payment Receipt', 0, 1, 'C');
        $pdf->SetLineWidth(0.5);
        $pdf->Line(10, 25, 200, 25);
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 8, 'Tenant Details:', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, 'Tenant: ' . $bill['tenant_name'], 0, 1);
        $pdf->Cell(0, 8, 'Room: ' . $bill['room_number'], 0, 1);
        $pdf->Cell(0, 8, 'Month: ' . $month, 0, 1);
        $pdf->Cell(0, 8, 'Due Date: ' . $bill['due_date'], 0, 1);
        $pdf->Ln(10);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 8, 'Bill Breakdown:', 0, 1);
        $pdf->Ln(5);

        $pdf->SetFillColor(220, 220, 220);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(80, 10, 'Description', 1, 0, 'L', true);
        $pdf->Cell(110, 10, 'Amount (PHP)', 1, 1, 'R', true);

        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(80, 8, 'Rent', 1, 0, 'L');
        $pdf->Cell(110, 8, number_format($bill['rent_amount'], 2), 1, 1, 'R');
        $pdf->Cell(80, 8, 'Electricity', 1, 0, 'L');
        $pdf->Cell(110, 8, number_format($bill['utilities_amount'], 2), 1, 1, 'R');
        $pdf->Cell(80, 8, 'Amenities', 1, 0, 'L');
        $pdf->Cell(110, 8, number_format($bill['other_charges'], 2), 1, 1, 'R');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, 10, 'Total', 1, 0, 'L');
        $pdf->Cell(110, 10, number_format($bill['total_amount'], 2), 1, 1, 'R');
        $pdf->Ln(15);

        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 10, 'Thank you for your payment!', 0, 1, 'C');

        $pdf->Output('D', 'receipt_' . $month . '.pdf');
        exit;
    }
    exit;
}

// Now handle POST requests
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_bill'])) {
    $tenant_id = intval($_POST['tenant_id']);
    $length_of_stay = intval($_POST['length_of_stay']);
    $room_rate = floatval($_POST['room_rate']);
    $electricity_reading = floatval($_POST['electricity_reading']);
    $people = intval($_POST['people']);
    $water = isset($_POST['water']) ? 1 : 0;
    $wifi = isset($_POST['wifi']) ? 1 : 0;

    // Calculate utilities (electricity)
    $room_id = null;
    $stmt = $pdo->prepare("SELECT room_id FROM roomtenant WHERE tenant_id = ? AND check_out_date IS NULL LIMIT 1");
    $stmt->execute([$tenant_id]);
    $room_id = $stmt->fetch()['room_id'];

    $prevMonth = date('Y-m', strtotime('-1 month'));
    $stmt = $pdo->prepare("SELECT electricity FROM utilities WHERE room_id = ? AND month_year = ?");
    $stmt->execute([$room_id, $prevMonth]);
    $prevElectricity = $stmt->fetch()['electricity'] ?? 0;
    $electricityConsumption = max(0, $electricity_reading - $prevElectricity);
    $electricityBill = $electricityConsumption * 17;

    // Calculate amenities
    $amenitiesBill = 0;
    if ($water) $amenitiesBill += 75 * $people;
    if ($wifi) $amenitiesBill += 250;

    // Total bill
    $rentAmount = $room_rate * $length_of_stay;
    $total = $rentAmount + $electricityBill + $amenitiesBill;
    $due_date = date('Y-m-01', strtotime('+1 month'));
    $current_month = date('Y-m');

    // Check if bill already exists for this tenant in the next month
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM billing WHERE tenant_id = ? AND DATE_FORMAT(due_date, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')");
    $stmt->execute([$tenant_id, $due_date]);
    if ($stmt->fetchColumn() > 0) {
        $message = "A bill has already been generated for this tenant for the upcoming month.";
    } else {
        try {
            // Save electricity reading
            $stmt = $pdo->prepare("INSERT INTO utilities (room_id, month_year, electricity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE electricity = ?");
            $stmt->execute([$room_id, $current_month, $electricity_reading, $electricity_reading]);

            // Save bill with total_amount
            $stmt = $pdo->prepare("INSERT INTO billing (tenant_id, room_id, rent_amount, utilities_amount, other_charges, total_amount, due_date, pstat_id) VALUES (?, ?, ?, ?, ?, ?, ?, 2)");
            $stmt->execute([$tenant_id, $room_id, $rentAmount, $electricityBill, $amenitiesBill, $total, $due_date]);

            $message = "Bill generated successfully! Total: PHP" . number_format($total, 2);
        } catch (PDOException $e) {
            $message = "Error generating bill: " . $e->getMessage();
        }
    }
}

// FIXED: Removed auto-generation of next bill when marking as paid
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_paid'])) {
    $payment_id = intval($_POST['payment_id']);
    try {
        // Mark as paid
        $stmt = $pdo->prepare("UPDATE billing SET pstat_id = 1, payment_date = CURDATE() WHERE payment_id = ?");
        $stmt->execute([$payment_id]);
        
        // Record payment history
        $stmt = $pdo->prepare("INSERT INTO payment_history (payment_id, received_amount, received_by, received_date) SELECT payment_id, total_amount, 'Admin', CURDATE() FROM billing WHERE payment_id = ?");
        $stmt->execute([$payment_id]);
        
        // REMOVED: Auto-generation of next month's bill
        // Admin must now manually generate bills using the form above
        
        $message = "Payment marked as paid! Please generate the next month's bill manually when ready.";
    } catch (PDOException $e) {
        $message = "Error marking paid: " . $e->getMessage();
    }
}

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

// Now include output-generating files
$pageTitle = "Billing & Payments";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';

// Fetch active tenants for dropdown
$tenants = $pdo->query("
    SELECT t.tenant_id, CONCAT(t.first_name, ' ', t.last_name) AS name, rt.check_in_date, r.room_rate, r.room_number, r.room_id
    FROM tenant t
    JOIN roomtenant rt ON t.tenant_id = rt.tenant_id AND rt.check_out_date IS NULL
    JOIN room r ON rt.room_id = r.room_id
    WHERE t.tstat_id = 2
")->fetchAll();

// Fetch all bills per tenant
$billings = $pdo->query("
    SELECT 
        CONCAT(t.first_name, ' ', t.last_name) AS tenant_name,
        r.room_number,
        COALESCE(b.total_amount, b.rent_amount + b.utilities_amount + b.other_charges) AS amount,
        b.due_date,
        CASE 
            WHEN b.pstat_id = 1 THEN 'Paid'
            WHEN b.due_date < CURDATE() THEN 'Overdue'
            ELSE 'Pending'
        END AS status,
        b.payment_id,
        t.tenant_id,
        rt.check_in_date
    FROM tenant t
    JOIN roomtenant rt ON t.tenant_id = rt.tenant_id AND rt.check_out_date IS NULL
    JOIN room r ON rt.room_id = r.room_id
    LEFT JOIN billing b ON t.tenant_id = b.tenant_id AND b.due_date = (
        SELECT MAX(due_date) FROM billing WHERE tenant_id = t.tenant_id
    )
    WHERE t.tstat_id = 2
    ORDER BY t.last_name
")->fetchAll();

// Group bills by tenant
$tenantBills = [];
foreach ($billings as $bill) {
    $tenantBills[$bill['tenant_id']][] = $bill;
}

// For modal, fetch all bills separately
$allBills = $pdo->query("
    SELECT 
        CONCAT(t.first_name, ' ', t.last_name) AS tenant_name,
        r.room_number,
        b.total_amount AS amount,
        b.due_date,
        CASE 
            WHEN b.pstat_id = 1 THEN 'Paid'
            WHEN b.due_date < CURDATE() THEN 'Overdue'
            ELSE 'Pending'
        END AS status,
        b.payment_id,
        t.tenant_id
    FROM tenant t
    JOIN roomtenant rt ON t.tenant_id = rt.tenant_id AND rt.check_out_date IS NULL
    JOIN room r ON rt.room_id = r.room_id
    LEFT JOIN billing b ON t.tenant_id = b.tenant_id
    WHERE t.tstat_id = 2
    ORDER BY t.tenant_id, b.due_date DESC
")->fetchAll();

$allTenantBills = [];
foreach ($allBills as $bill) {
    $allTenantBills[$bill['tenant_id']][] = $bill;
}

// Fetch payment history
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
        <p>Calculate rents manually and track payment statuses. Bills must be generated manually each month.</p>
    </div>

    <!-- Success/Error Message -->
    <?php if ($message): ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Manual Rent Calculation Section -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Manual Bill Generation</h2>
            <p>Generate bills manually for each tenant. Bills will not auto-generate after payment.</p>
        </div>
        <form method="POST" class="tenant-form" id="rent-calculator">
            <div class="form-grid">
                <div class="form-group">
                    <label for="tenant_id">Select Tenant</label>
                    <select id="tenant_id" name="tenant_id" onchange="populateTenantData()" required>
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
                    <input type="number" id="length_of_stay" name="length_of_stay" min="1" required readonly>
                </div>
                <div class="form-group">
                    <label for="room_rate">Room Rate (₱/month)</label>
                    <input type="number" id="room_rate" name="room_rate" min="0" step="0.01" required readonly>
                </div>
                <div class="form-group">
                    <label for="people">Number of People (for Water)</label>
                    <input type="number" id="people" name="people" min="1" value="1" required onchange="calculateTotal()">
                </div>
                <div class="form-group">
                    <label for="electricity_reading">Electricity Meter Reading (Current)</label>
                    <input type="number" id="electricity_reading" name="electricity_reading" min="0" step="0.01" value="0" required onchange="calculateElectricity()">
                </div>
                <div class="form-group">
                    <label for="electricity_bill">Electricity Bill (₱)</label>
                    <input type="number" id="electricity_bill" name="electricity_bill" min="0" step="0.01" readonly>
                </div>
                <div class="form-group">
                    <label>Amenities</label>
                    <div>
                        <input type="checkbox" id="water" name="water" value="1" onchange="calculateTotal()"> Water (75₱/person)<br>
                        <input type="checkbox" id="wifi" name="wifi" value="1" onchange="calculateTotal()"> WiFi (250₱)
                    </div>
                </div>
                <div class="form-group">
                    <label for="total_rent">Total Rent (₱)</label>
                    <input type="text" id="total_rent" name="total_rent" readonly>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" name="generate_bill" class="btn-action primary">Generate Bill</button>
            </div>
        </form>
    </div>

    <!-- Payment Tracking Section -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Payment Tracking</h2>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Tenant Name</th>
                    <th>Room</th>
                    <th>Latest Amount (₱)</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $displayedTenants = [];
                foreach ($billings as $bill): 
                    if (in_array($bill['tenant_id'], $displayedTenants)) continue;
                    $displayedTenants[] = $bill['tenant_id'];
                    $currentBill = $tenantBills[$bill['tenant_id']][0] ?? null;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($bill['tenant_name']); ?></td>
                        <td><?php echo htmlspecialchars($bill['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($currentBill['amount'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($currentBill['due_date'] ?? 'N/A'); ?></td>
                        <td><span class="status-badge <?php echo strtolower(str_replace(' ', '-', $currentBill['status'] ?? 'No Bill')); ?>"><?php echo htmlspecialchars($currentBill['status'] ?? 'No Bill'); ?></span></td>
                        <td>
                            <a href="?tenant_bills=<?php echo $bill['tenant_id']; ?>" class="btn-action">View Bills</a>
                            <?php if (($currentBill['status'] ?? '') === 'Overdue' && $currentBill['payment_id']): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="tenant_id" value="<?php echo $bill['tenant_id']; ?>">
                                    <button type="submit" name="notify_overdue" class="btn-action warning">Notify Tenant</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="payment_id" value="<?php echo $currentBill['payment_id']; ?>">
                                    <button type="submit" name="mark_paid" class="btn-action">Mark Paid</button>
                                </form>
                            <?php elseif (($currentBill['status'] ?? '') === 'Pending' && $currentBill['payment_id']): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="payment_id" value="<?php echo $currentBill['payment_id']; ?>">
                                    <button type="submit" name="mark_paid" class="btn-action">Mark Paid</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Monthly Bills Modal -->
    <?php if (isset($_GET['tenant_bills'])): 
        $billsTenantId = intval($_GET['tenant_bills']);
        $bills = $allTenantBills[$billsTenantId] ?? [];
    ?>
    <div id="billsModal" class="modal-overlay show" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;">
        <div class="modal-box" style="background: white; padding: 20px; border-radius: 8px; max-width: 800px; width: 90%;">
            <h2>Monthly Bills for <?php echo htmlspecialchars($billings[array_search($billsTenantId, array_column($billings, 'tenant_id'))]['tenant_name'] ?? 'Tenant'); ?></h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Bill Amount (₱)</th>
                        <th>Status</th>
                        <th>Show Receipts</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bills as $b): 
                        if (!$b['due_date']) continue;
                        $month = date('Y-m', strtotime($b['due_date']));
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($month); ?></td>
                            <td><?php echo htmlspecialchars($b['amount']); ?></td>
                            <td><?php echo htmlspecialchars($b['status']); ?></td>
                            <td>
                                    <!-- Open PDF in a new tab -->
                                <a
                                    href="billing_payments.php?view_receipt=1&tenant_id=<?php echo $billsTenantId; ?>&month=<?php echo $month; ?>"
                                    class="btn-action"
                                    target="_blank"
                                    rel="noopener"
                                >
                                    View Receipt
                                </a>
                                <!-- Optional: add a direct download link too -->
                                <a
                                    href="billing_payments.php?download_receipt=1&tenant_id=<?php echo $billsTenantId; ?>&month=<?php echo $month; ?>"
                                    class="btn-action"
                                >
                                    Download
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button class="btn-action primary" onclick="window.location.href='billing_payments.php'">Close</button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Paid History Modal -->
    <?php if ($selectedTenantId): 
        $selectedMonth = isset($_GET['month']) ? $_GET['month'] : null;
        if ($selectedMonth) {
            $history = array_filter($history, function($h) use ($selectedMonth) {
                return $h['month'] === $selectedMonth;
            });
        }
    ?>
    <div id="historyModal" class="modal-overlay show" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 1000;">
        <div class="modal-box" style="background: white; padding: 20px; border-radius: 8px; max-width: 600px; width: 90%;">
            <h2>Payment Receipt for <?php echo htmlspecialchars($history[0]['tenant_name'] ?? 'Tenant'); ?><?php if ($selectedMonth): ?> - <?php echo htmlspecialchars($selectedMonth); ?><?php endif; ?></h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Amount Paid (₱)</th>
                        <th>Date Received</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($h['month']); ?></td>
                            <td><?php echo htmlspecialchars($h['received_amount']); ?></td>
                            <td><?php echo htmlspecialchars($h['received_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button class="btn-action primary" onclick="window.location.href='billing_payments.php'">Close</button>
        </div>
    </div>
    <?php endif; ?>
</main>

<!-- Receipt Modal -->
<div id="receiptModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1100;">    <div class="modal-box" style="background: white; padding: 20px; border-radius: 8px; max-width: 800px; width: 90%; max-height: 90%; overflow: auto; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
        <h2>Receipt Preview</h2>
        <iframe id="receiptFrame" style="width: 100%; height: 500px; border: 1px solid #ddd; border-radius: 4px;"></iframe>
        <div style="text-align: right; margin-top: 10px;">
            <button class="btn-action primary" onclick="closeReceiptModal()">Close</button>
        </div>
    </div>
</div>

<!-- Hidden input for previous electricity -->
<input type="hidden" id="prev_electricity" value="0">

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
                    document.getElementById('prev_electricity').value = data.previous || 0;
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

    // FIXED: Event delegation for view receipt - works even for dynamically loaded content
    document.addEventListener('click', function (e) {
        const link = e.target.closest('a.view-receipt');
        if (!link) return;
        e.preventDefault();
        const tenantId = link.dataset.tenant;
        const month = link.dataset.month;
        if (!tenantId || !month) return;
        // Build absolute URL to this script
        const base = window.location.pathname; // e.g., /admin/billing_payments.php
        const url = `${base}?view_receipt=1&tenant_id=${encodeURIComponent(tenantId)}&month=${encodeURIComponent(month)}`;
        const frame = document.getElementById('receiptFrame');
        frame.src = url;
        const modal = document.getElementById('receiptModal');
        modal.style.display = 'flex';
        modal.style.zIndex = 1100; // ensure on top of bills modal
    });
    function closeReceiptModal() {
        const modal = document.getElementById('receiptModal');
        modal.style.display = 'none';
        document.getElementById('receiptFrame').src = '';
    }
</script>