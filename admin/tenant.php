<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
$pageTitle = "Tenant Management";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
require_once __DIR__ . '/../includes/auth.php';  // Ensure admin is logged in
require_once __DIR__ . '/../includes/db.php';

$message = "";
$editTenant = null;

// Cleanup: Remove prospective tenants with no active reservations (follows reject logic)
$pdo->exec("
    DELETE t FROM tenant t
    LEFT JOIN reservation r ON t.tenant_id = r.tenant_id AND r.restat_id IN (1, 2)  -- Pending or Approved
    WHERE t.tstat_id = 1 AND r.reservation_id IS NULL  -- Prospective with no reservations
");

// Handle form submission (Add or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $middle_name = trim($_POST['middle_name']);
    $number = trim($_POST['number']);
    $emergency_number = trim($_POST['emergency_number']);
    $email = trim($_POST['email']);
    $room_id = intval($_POST['room_id']);
    $tstat_id = intval($_POST['tstat_id']);

    // Validation
    $errors = [];
    if (empty($first_name) || empty($last_name) || empty($number) || empty($email) || !$tstat_id) {
        $errors[] = "Required fields are missing.";
    }
    if (!preg_match('/^[a-zA-Z\s]+$/', $first_name)) {
        $errors[] = "First Name must contain only letters and spaces.";
    }
    if (!preg_match('/^[a-zA-Z\s]+$/', $last_name)) {
        $errors[] = "Last Name must contain only letters and spaces.";
    }
    if (!empty($middle_name) && !preg_match('/^[a-zA-Z\s]+$/', $middle_name)) {
        $errors[] = "Middle Name must contain only letters and spaces.";
    }
    if (!preg_match('/^09\d{9}$/', $number)) {
        $errors[] = "Contact Number must be exactly 11 digits, start with 09, and contain only numbers.";
    }
    if (!preg_match('/^09\d{9}$/', $emergency_number)) {
        $errors[] = "Emergency Contact Number must be exactly 11 digits, start with 09, and contain only numbers.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        try {
            if (isset($_POST['update_tenant_id'])) {
                // Update existing tenant
                $tenant_id = intval($_POST['update_tenant_id']);

                // Get current room assignment
                $stmt = $pdo->prepare("SELECT rt.room_id FROM roomtenant rt WHERE rt.tenant_id = ? AND rt.check_out_date IS NULL");
                $stmt->execute([$tenant_id]);
                $currentRoom = $stmt->fetchColumn();

                // Check if room is changing and if new room is available
                if ($tstat_id == 2 && $room_id && $room_id != $currentRoom) {
                    // Check if new room is available
                    $stmt = $pdo->prepare("SELECT rstat_id FROM room WHERE room_id = ?");
                    $stmt->execute([$room_id]);
                    $newRoomStatus = $stmt->fetchColumn();
                    if ($newRoomStatus != 1) {
                        $errors[] = "Cannot assign to an occupied room.";
                    } else {
                        // Set check_out for current room if exists
                        if ($currentRoom) {
                            $stmt = $pdo->prepare("UPDATE roomtenant SET check_out_date = CURDATE() WHERE tenant_id = ? AND check_out_date IS NULL");
                            $stmt->execute([$tenant_id]);
                            // Free current room
                            $stmt = $pdo->prepare("UPDATE room SET rstat_id = 1 WHERE room_id = ?");
                            $stmt->execute([$currentRoom]);
                        }
                        // Assign new room
                        $stmt = $pdo->prepare("INSERT INTO roomtenant (tenant_id, room_id, check_in_date, role_in_room) VALUES (?, ?, CURRENT_DATE, 'Member')");
                        $stmt->execute([$tenant_id, $room_id]);
                        // Occupy new room
                        $stmt = $pdo->prepare("UPDATE room SET rstat_id = 2 WHERE room_id = ?");
                        $stmt->execute([$room_id]);
                    }
                } elseif ($tstat_id != 2 && $currentRoom) {
                    // If status changed to non-active, check out from room
                    $stmt = $pdo->prepare("UPDATE roomtenant SET check_out_date = CURDATE() WHERE tenant_id = ? AND check_out_date IS NULL");
                    $stmt->execute([$tenant_id]);
                    $stmt = $pdo->prepare("UPDATE room SET rstat_id = 1 WHERE room_id = ?");
                    $stmt->execute([$currentRoom]);
                }

                if (empty($errors)) {
                    $stmt = $pdo->prepare("UPDATE tenant SET first_name = ?, last_name = ?, middle_name = ?, number = ?, emergency_number = ?, email = ?, tstat_id = ? WHERE tenant_id = ?");
                    $stmt->execute([$first_name, $last_name, $middle_name, $number, $emergency_number, $email, $tstat_id, $tenant_id]);
                    $message = "Tenant updated successfully!";
                }
            } else {
                // Add new tenant
                $stmt = $pdo->prepare("INSERT INTO tenant (first_name, last_name, middle_name, number, emergency_number, email, tstat_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$first_name, $last_name, $middle_name, $number, $emergency_number, $email, $tstat_id]);
                $tenant_id = $pdo->lastInsertId();

                // If status is Active, assign to room (check if available)
                if ($tstat_id == 2 && $room_id) {
                    $stmt = $pdo->prepare("SELECT rstat_id FROM room WHERE room_id = ?");
                    $stmt->execute([$room_id]);
                    $roomStatus = $stmt->fetchColumn();
                    if ($roomStatus != 1) {
                        $errors[] = "Cannot assign to an occupied room.";
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO roomtenant (tenant_id, room_id, check_in_date, role_in_room) VALUES (?, ?, CURRENT_DATE, 'Member')");
                        $stmt->execute([$tenant_id, $room_id]);
                        $stmt = $pdo->prepare("UPDATE room SET rstat_id = 2 WHERE room_id = ?");
                        $stmt->execute([$room_id]);
                    }
                }

                if (empty($errors)) {
                    $message = "Tenant added successfully!";
                    preventResubmission();  // Replaces header() call
                }
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
        }
    }
    if (!empty($errors)) {
        $message = implode('<br>', $errors);  // Show all errors
    }
}

// Handle delete (UPDATED: Log to churned_tenants with dates, then fully delete tenant)
if (isset($_GET['delete_tenant'])) {
    $tenant_id = intval($_GET['delete_tenant']);
    try {
        // Fetch tenant details and room history for logging
        $stmt = $pdo->prepare("SELECT first_name, last_name, middle_name, number, emergency_number, email FROM tenant WHERE tenant_id = ?");
        $stmt->execute([$tenant_id]);
        $tenantData = $stmt->fetch();

        // Fetch check-in and check-out dates from roomtenant
        $stmt = $pdo->prepare("SELECT MIN(check_in_date) AS check_in_date, MAX(check_out_date) AS check_out_date FROM roomtenant WHERE tenant_id = ?");
        $stmt->execute([$tenant_id]);
        $roomData = $stmt->fetch();

        if ($tenantData) {
            // Log to churned_tenants (set check_out_date to CURDATE() if NULL for consistency)
            $checkOutDate = $roomData['check_out_date'] ?? date('Y-m-d');
            $stmt = $pdo->prepare("INSERT INTO churned_tenants (tenant_id, first_name, last_name, middle_name, number, emergency_number, email, check_in_date, check_out_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tenant_id, $tenantData['first_name'], $tenantData['last_name'], $tenantData['middle_name'], $tenantData['number'], $tenantData['emergency_number'], $tenantData['email'], $roomData['check_in_date'], $checkOutDate]);

            // Free the room if assigned (set check_out and update room status)
            $stmt = $pdo->prepare("UPDATE roomtenant SET check_out_date = CURDATE() WHERE tenant_id = ? AND check_out_date IS NULL");
            $stmt->execute([$tenant_id]);

            $stmt = $pdo->prepare("UPDATE room SET rstat_id = 1 WHERE room_id = (SELECT room_id FROM roomtenant WHERE tenant_id = ? AND check_out_date = CURDATE() LIMIT 1)");
            $stmt->execute([$tenant_id]);

            // Now, delete the tenant (cascades to roomtenant)
            $stmt = $pdo->prepare("DELETE FROM tenant WHERE tenant_id = ?");
            $stmt->execute([$tenant_id]);

            $message = "Tenant removed successfully.";
        } else {
            $message = "Tenant not found.";
        }
    } catch (PDOException $e) {
        $message = "Error churning tenant: " . $e->getMessage();
    }
}

// Handle edit (populate form)
if (isset($_GET['edit_tenant'])) {
    $tenant_id = intval($_GET['edit_tenant']);
    $stmt = $pdo->prepare("SELECT t.*, rt.room_id FROM tenant t LEFT JOIN roomtenant rt ON t.tenant_id = rt.tenant_id AND rt.check_out_date IS NULL WHERE t.tenant_id = ?");
    $stmt->execute([$tenant_id]);
    $editTenant = $stmt->fetch();
}

// Fetch tenant statuses
$tenantStatuses = $pdo->query("SELECT tstat_id, tstat_desc FROM tenant_status")->fetchAll();

// Fetch rooms (all for now, filter in loop)
$rooms = $pdo->query("SELECT room_id, room_number, rstat_id FROM room ORDER BY room_number")->fetchAll();

// Fetch tenants for table
$tenants = $pdo->query("
    SELECT t.tenant_id, t.first_name, t.last_name, t.middle_name, t.number, t.emergency_number, t.email, ts.tstat_desc AS status,
           COALESCE(r.room_number, '-') AS room_assignment
    FROM tenant t
    LEFT JOIN tenant_status ts ON t.tstat_id = ts.tstat_id
    LEFT JOIN roomtenant rt ON t.tenant_id = rt.tenant_id AND rt.check_out_date IS NULL
    LEFT JOIN room r ON rt.room_id = r.room_id
    ORDER BY t.last_name
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>

    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* ====== Tenant Management Page ====== */

        /* Page Layout */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 40px;
            background-color: #f9f9f9;
        }

        .page-header {
            margin-bottom: 40px;
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .page-header h1 {
            font-size: 40px;
            font-weight: 700;
            color: #1d1d1f;
            margin-bottom: 10px;
            letter-spacing: -1px;
        }

        .page-header p {
            color: #666;
            margin: 0;
            font-size: 18px;
        }

        /* Dashboard Section Containers */
        .dashboard-section {
            background-color: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            border: 1px solid #f0f0f0;
            animation: fadeInUp 0.7s ease-out;
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .section-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1d1d1f;
            margin: 0;
            letter-spacing: -0.5px;
        }

        /* Tenant Form */
        .tenant-form {
            margin-bottom: 25px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            color: #1d1d1f;
            margin-bottom: 8px;
            font-size: 16px;
        }

        .form-group input,
        .form-group select {
            padding: 14px;
            border: 1px solid #d1d1d6;
            border-radius: 12px;
            font-size: 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007aff;
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }

        /* Buttons */
        .btn-action {
            padding: 12px 24px;
            border: none;
            background-color: #007aff;
            color: #fff;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(0, 122, 255, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 120px;
            height: 45px;
        }

        .btn-action:hover {
            background-color: #0056cc;
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0, 122, 255, 0.3);
        }

        .btn-action.primary {
            background-color: #34c759;
        }

        .btn-action.primary:hover {
            background-color: #28a745;
        }

        .btn-action.warning {
            background-color: #ff3b30;
        }

        .btn-action.warning:hover {
            background-color: #d63027;
        }

        /* Tenant List Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
        }

        .data-table thead {
            background-color: #f5f5f7;
        }

        .data-table th {
            padding: 20px;
            text-align: left;
            font-weight: 600;
            color: #1d1d1f;
            font-size: 16px;
            text-transform: uppercase;
            border-bottom: 1px solid #e0e0e0;
        }

        .data-table td {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
        }

        .data-table tr:hover {
            background-color: #f9f9f9;
            transition: background-color 0.3s ease;
        }

        /* Status Badge */
        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }

        .status-badge.paid {
            background-color: rgba(52, 199, 89, 0.1);
            color: #34c759;
        }

        .status-badge.pending {
            background-color: rgba(255, 149, 0, 0.1);
            color: #ff9500;
        }

        .status-badge.overdue {
            background-color: rgba(255, 59, 48, 0.1);
            color: #ff3b30;
        }

        .status-badge.occupied {
            background-color: rgba(52, 199, 89, 0.1);
            color: #34c759;
        }

        .status-badge.vacant {
            background-color: rgba(255, 59, 48, 0.1);
            color: #ff3b30;
        }

        .status-badge.reserved {
            background-color: rgba(255, 149, 0, 0.1);
            color: #ff9500;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .data-table th,
            .data-table td {
                padding: 12px;
                font-size: 14px;
            }

            .btn-action {
                min-width: auto;
                width: 100%;
                justify-content: center;
            }
        }

        /* Small action buttons in table */
        .data-table .btn-action {
            padding: 6px 10px;
            font-size: 14px;
            min-width: auto;
            height: 30px;
            border-radius: 8px;
        }

        .data-table .btn-action i {
            font-size: 14px;
        }
    </style>

    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1>Tenant Management</h1>
            <p>Add, update, and manage tenant profiles along with room assignments and statuses.</p>
        </div>

        <!-- Success/Error Message -->
        <?php if ($message): ?>
            <div
                style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Add New Tenant Form Section -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2><?php echo $editTenant ? 'Edit Tenant' : 'Add New Tenant'; ?></h2>
            </div>

            <form id="addTenantForm" method="POST" class="tenant-form" style="display: block;">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name"
                            value="<?php echo htmlspecialchars($editTenant['first_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name"
                            value="<?php echo htmlspecialchars($editTenant['last_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="middle_name">Middle Name (Optional)</label>
                        <input type="text" id="middle_name" name="middle_name"
                            value="<?php echo htmlspecialchars($editTenant['middle_name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="number">Contact Number</label>
                        <input type="text" id="number" name="number"
                            value="<?php echo htmlspecialchars($editTenant['number'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="emergency_number">Emergency Contact Number</label>
                        <input type="text" id="emergency_number" name="emergency_number"
                            value="<?php echo htmlspecialchars($editTenant['emergency_number'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                            value="<?php echo htmlspecialchars($editTenant['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="room_id">Select Room</label>
                        <select id="room_id" name="room_id">
                            <option value="">Select Room</option>
                            <?php foreach ($rooms as $room): ?>
                                <?php
                                $showRoom = false;
                                if (!$editTenant) {
                                    // For add: only available
                                    $showRoom = ($room['rstat_id'] == 1);
                                } else {
                                    // For edit: available or current
                                    $showRoom = ($room['rstat_id'] == 1 || $room['room_id'] == $editTenant['room_id']);
                                }
                                if ($showRoom): ?>
                                    <option value="<?php echo $room['room_id']; ?>" <?php echo ($editTenant && $editTenant['room_id'] == $room['room_id']) ? 'selected' : ''; ?>>
                                        Room <?php echo htmlspecialchars($room['room_number']); ?>
                                        <?php echo ($room['rstat_id'] != 1) ? '(Occupied)' : ''; ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tstat_id">Status</label>
                        <select id="tstat_id" name="tstat_id" required>
                            <?php foreach ($tenantStatuses as $status): ?>
                                <?php if (!$editTenant && $status['tstat_desc'] == 'Inactive')
                                    continue; // Exclude Inactive for add ?>
                                <option value="<?php echo $status['tstat_id']; ?>" <?php echo ($editTenant && $editTenant['tstat_id'] == $status['tstat_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($status['tstat_desc']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <?php if ($editTenant): ?>
                        <input type="hidden" name="update_tenant_id" value="<?php echo $editTenant['tenant_id']; ?>">
                        <button type="submit" class="btn-action primary">Update Tenant</button>
                        <a href="tenant.php" class="btn-action">Cancel</a>
                    <?php else: ?>
                        <button type="submit" class="btn-action primary">Add Tenant</button>
                        <button type="reset" class="btn-action">Reset</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tenant List Section -->
        <div class="dashboard-section">
            <div class="section-header">
                <h2>Tenant List</h2>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact Info</th>
                        <th>Email</th>
                        <th>Room Assignment</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tenants as $tenant): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($tenant['first_name'] . ' ' . $tenant['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($tenant['number']); ?></td>
                            <td><?php echo htmlspecialchars($tenant['email']); ?></td>
                            <td><?php echo htmlspecialchars($tenant['room_assignment']); ?></td>
                            <td><span
                                    class="status-badge <?php echo strtolower($tenant['status']); ?>"><?php echo htmlspecialchars($tenant['status']); ?></span>
                            </td>
                            <td>
                                <a href="?edit_tenant=<?php echo $tenant['tenant_id']; ?>" class="btn-action"
                                    title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="?delete_tenant=<?php echo $tenant['tenant_id']; ?>" class="btn-action warning"
                                    onclick="return confirm('Remove this tenant?');" title="Remove"><i
                                        class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function toggleForm() {
            const form = document.getElementById('addTenantForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>