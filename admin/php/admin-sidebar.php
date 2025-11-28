<?php
// Add DB connection for notifications (FIXED: Correct path)
require_once __DIR__ . '/../../includes/db.php';

// Fetch count of pending reservations
$pendingReservations = $pdo->query("SELECT COUNT(*) AS count FROM reservation WHERE restat_id = 1")->fetch()['count'];
?>

<!-- admin-sidebar.php -->
<aside class="sidebar">
    <ul class="sidebar-menu">
        <li><a href="admin.php" class="<?= basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard</a>
        </li>

        <li><a href="tenant.php" class="<?= basename($_SERVER['PHP_SELF']) == 'tenant.php' ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Tenant Management</a>
        </li>

        <li><a href="room_management.php" class="<?= basename($_SERVER['PHP_SELF']) == 'room_management.php' ? 'active' : '' ?>">
            <i class="fas fa-door-open"></i> Room Management</a>
        </li>

        <li><a href="billing_payments.php" class="<?= basename($_SERVER['PHP_SELF']) == 'billing_payments.php' ? 'active' : '' ?>">
            <i class="fas fa-dollar-sign"></i> Billing & Payments</a>
        </li>

        <li><a href="reservations.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reservations.php' ? 'active' : '' ?>">
            <i class="fas fa-calendar-check"></i> Reservations
            <?php if ($pendingReservations > 0): ?>
                <span style="background: red; color: white; border-radius: 50%; padding: 2px 7px; font-size: 10px; margin-left: 7px;"><?php echo $pendingReservations; ?></span>
            <?php endif; ?>
        </a></li>

        <li><a href="notifications.php" class="<?= basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : '' ?>">
            <i class="fas fa-bell"></i> Notifications</a>
        </li>

        <li><a href="reports.php" class="<?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-bar"></i> Reports & Analytics</a>
        </li>

        <li><a href="visitor_log.php" class="<?= basename($_SERVER['PHP_SELF']) == 'visitor_log.php' ? 'active' : '' ?>">
            <i class="fas fa-user-shield"></i> Visitor Logs</a>
        </li>
    </ul>
</aside>
