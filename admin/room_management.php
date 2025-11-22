<?php
$pageTitle = "Room Management";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <h1>Room Management</h1>
        <p>Manage room details and monitor availability status.</p>
    </div>

    <!-- Manage Room Details Section -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Manage Room Details</h2>
        </div>

        <form class="tenant-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="room-number">Room Number</label>
                    <input type="text" id="room-number" placeholder="e.g., 101">
                </div>

                <div class="form-group">
                    <label for="room-size">Size (sqm)</label>
                    <input type="number" id="room-size" placeholder="e.g., 20">
                </div>

                <div class="form-group">
                    <label for="rent-rate">Rent Rate (₱/month)</label>
                    <input type="number" id="rent-rate" placeholder="e.g., 5000">
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-action primary">Add Room</button>
                <button type="button" class="btn-action">Update Room</button>
            </div>
        </form>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Size (sqm)</th>
                    <th>Rent Rate (₱)</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>101</td>
                    <td>20</td>
                    <td>5000</td>
                    <td>
                        <button class="btn-action"><i class="fas fa-edit"></i></button>
                        <button class="btn-action warning"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>

                <tr>
                    <td>102</td>
                    <td>25</td>
                    <td>5500</td>
                    <td>
                        <button class="btn-action"><i class="fas fa-edit"></i></button>
                        <button class="btn-action warning"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Room Availability Status Dashboard -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Room Availability Status Dashboard</h2>
        </div>

        <div class="occupancy-grid">
            <div class="occupancy-card">
                <h3>Total Rooms</h3>
                <div class="number">24</div>
            </div>

            <div class="occupancy-card">
                <h3>Occupied Rooms</h3>
                <div class="number">18</div>
            </div>

            <div class="occupancy-card">
                <h3>Vacant Rooms</h3>
                <div class="number">6</div>
            </div>
        </div>

        <div class="occupancy-stats">
            <div class="stat-box">
                <i class="fas fa-chart-pie"></i>
                <div>
                    <strong>75%</strong>
                    <span>Occupancy Rate</span>
                </div>
            </div>

            <div class="stat-box">
                <i class="fas fa-dollar-sign"></i>
                <div>
                    <strong>₱5,250</strong>
                    <span>Avg. Rent Rate</span>
                </div>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Status</th>
                    <th>Tenant (if occupied)</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>101</td>
                    <td><span class="status-badge occupied">Occupied</span></td>
                    <td>John Doe</td>
                </tr>

                <tr>
                    <td>102</td>
                    <td><span class="status-badge vacant">Vacant</span></td>
                    <td>-</td>
                </tr>

                <tr>
                    <td>103</td>
                    <td><span class="status-badge reserved">Reserved</span></td>
                    <td>Pending</td>
                </tr>
            </tbody>
        </table>
    </div>

</main>

<script>
    console.log("Room Management page loaded.");
</script>
