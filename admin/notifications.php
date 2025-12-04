<?php 
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
$pageTitle = "Notifications";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <div class="page-header">
        <h1>Notifications</h1>
        <p>Manage alerts and notices for payments and maintenance.</p>
    </div>

    <!-- Late Payment Alerts -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2>Late Payment Alerts</h2>
            <a href="#" class="view-all">Send Alert <i class="fas fa-paper-plane"></i></a>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tenant Name</th>
                    <th>Room</th>
                    <th>Due Date</th>
                    <th>Amount Due</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>John Doe</td>
                    <td>Single Room 101</td>
                    <td>2023-10-15</td>
                    <td>$120</td>
                    <td><span class="status-badge overdue">Overdue</span></td>
                    <td>
                        <button class="btn-action">Send Reminder</button>
                        <button class="btn-action warning">Mark Paid</button>
                    </td>
                </tr>
                <tr>
                    <td>Jane Smith</td>
                    <td>Double Room 202</td>
                    <td>2023-10-20</td>
                    <td>$150</td>
                    <td><span class="status-badge pending">Pending</span></td>
                    <td>
                        <button class="btn-action">Send Reminder</button>
                        <button class="btn-action warning">Mark Paid</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>

    <!-- Maintenance or Inspection Notices -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2>Maintenance or Inspection Notices</h2>
            <a href="#" class="view-all">Create Notice <i class="fas fa-plus"></i></a>
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Notice Type</th>
                    <th>Room/Property</th>
                    <th>Scheduled Date</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Maintenance</td>
                    <td>Single Room 101</td>
                    <td>2023-10-25</td>
                    <td>Plumbing repair</td>
                    <td><span class="status-badge pending">Scheduled</span></td>
                    <td>
                        <button class="btn-action">Update</button>
                        <button class="btn-action warning">Cancel</button>
                    </td>
                </tr>
                <tr>
                    <td>Inspection</td>
                    <td>Double Room 202</td>
                    <td>2023-10-30</td>
                    <td>Annual safety check</td>
                    <td><span class="status-badge paid">Completed</span></td>
                    <td>
                        <button class="btn-action">View Report</button>
                        <button class="btn-action warning">Reschedule</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
</main>

<script>
    console.log("Notifications page loaded. Add JS for interactivity.");
</script>

