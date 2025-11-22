<?php 
$pageTitle = "Admin Dashboard";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
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
            <div class="number">24</div>
            <div class="trend"><i class="fas fa-check-circle"></i>18 Occupied, 6 Vacant</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon green">
                <i class="fas fa-users"></i>
            </div>
            <h3>Active Tenants</h3>
            <div class="number">18</div>
            <div class="trend"><i class="fas fa-arrow-up"></i>2 new this month</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon orange">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>Overdue Payments</h3>
            <div class="number">3</div>
            <div class="trend warning"><i class="fas fa-exclamation-circle"></i>Action required</div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon purple">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <h3>Monthly Revenue</h3>
            <div class="number">₱145,600</div>
            <div class="trend"><i class="fas fa-check"></i>75% collected</div>
        </div>
    </div>

    <!-- Payment Status Overview -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Payment Status Overview</h2>
            <a href="#" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
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
                <!-- Data can be dynamically loaded here with PHP -->
            </tbody>
        </table>
    </div>

    <!-- Room Occupancy Overview -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Room Occupancy Overview</h2>
            <a href="#" class="view-all">Manage Rooms <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="occupancy-grid">
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
                <i class="fas fa-clock"></i>
                <div>
                    <strong>6.5 months</strong>
                    <span>Avg. Stay Duration</span>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-calendar-alt"></i>
                <div>
                    <strong>2</strong>
                    <span>Pending Reservations</span>
                </div>
            </div>
        </div>
    </div>

</main>
