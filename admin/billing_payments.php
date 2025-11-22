<?php 
$pageTitle = "Billing & Payments";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
?>
 
<!-- Main Content Wrapper -->
<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <h1>Billing & Payments</h1>
        <p>Calculate rents automatically and track payment statuses. All payments are cash-only.</p>
    </div>

    <!-- Automatic Rent Calculation Section -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Automatic Rent Calculation</h2>
        </div>
        <form class="tenant-form" id="rent-calculator">
            <div class="form-grid">
                <div class="form-group">
                    <label for="room-type">Room Type</label>
                    <select id="room-type">
                        <option value="single">Single (Base: ₱5000)</option>
                        <option value="double">Double (Base: ₱8000)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="utilities">Utilities (₱)</label>
                    <input type="number" id="utilities" placeholder="e.g., 1000" value="0">
                </div>
                <div class="form-group">
                    <label for="stay-length">Length of Stay (Months)</label>
                    <input type="number" id="stay-length" placeholder="e.g., 1" value="1">
                </div>
                <div class="form-group">
                    <label for="total-rent">Total Rent (₱)</label>
                    <input type="text" id="total-rent" readonly placeholder="Calculated automatically">
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-action primary" onclick="calculateRent()">Calculate Rent</button>
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
                    <th>Amount (₱)</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>John Doe</td>
                    <td>101</td>
                    <td>6000</td>
                    <td>2023-10-01</td>
                    <td><span class="status-badge paid">Paid</span></td>
                    <td><button class="btn-action" onclick="markAsPaid(this)">Mark Paid</button></td>
                </tr>
                <tr>
                    <td>Jane Smith</td>
                    <td>102</td>
                    <td>5500</td>
                    <td>2023-10-15</td>
                    <td><span class="status-badge pending">Pending</span></td>
                    <td><button class="btn-action" onclick="markAsPaid(this)">Mark Paid</button></td>
                </tr>
                <tr>
                    <td>Bob Johnson</td>
                    <td>103</td>
                    <td>7000</td>
                    <td>2023-09-30</td>
                    <td><span class="status-badge overdue">Overdue</span></td>
                    <td><button class="btn-action warning" onclick="notifyOverdue()">Notify Tenant</button></td>
                </tr>
            </tbody>

        </table>
    </div>

</main>

