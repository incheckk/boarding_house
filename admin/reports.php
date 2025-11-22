<?php 
$pageTitle = "Reports & Analytics";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
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
            <a href="#" class="view-all">Export Report <i class="fas fa-download"></i></a>
        </div>
        
        <!-- Current Occupancy -->
        <div class="occupancy-grid">
            <div class="occupancy-card">
                <h3>Current Occupancy</h3>
                <div class="number">85%</div>
                <p>Occupied: 17/20 rooms</p>
            </div>
        </div>

        <!-- Historical Trends (Chart Placeholder) -->
        <div class="chart-placeholder">
            <h3>Historical Trends (Last 12 Months)</h3>
            <p>Placeholder for chart (e.g., Chart.js line graph).</p>
        </div>
    </section>

    <!-- Financial Reports Summary -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2>Financial Reports Summary</h2>
            <a href="#" class="view-all">View Details <i class="fas fa-eye"></i></a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-icon green">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <h3>Total Income</h3>
                <div class="number">$25,000</div>
                <div class="trend">+5% from last month</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-icon orange">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Overdue Rent</h3>
                <div class="number">$1,200</div>
                <div class="trend warning">-10% from last month</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-icon blue">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3>Total Expenses</h3>
                <div class="number">$8,500</div>
                <div class="trend">+2% from last month</div>
            </div>
        </div>
    </section>

    <!-- Tenant Churn and Average Stay Duration -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2>Tenant Churn & Average Stay Duration</h2>
            <a href="#" class="view-all">Analyze Trends <i class="fas fa-chart-line"></i></a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-icon purple">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Tenant Churn Rate</h3>
                <div class="number">12%</div>
                <div class="trend warning">+3% from last quarter</div>
            </div>

            <div class="stat-card">
                <div class="stat-card-icon green">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Average Stay Duration</h3>
                <div class="number">18 months</div>
                <div class="trend">+2 months from last year</div>
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
                <tr>
                    <td>John Doe</td>
                    <td>2022-01-01</td>
                    <td>2023-07-01</td>
                    <td>18 months</td>
                    <td><span class="status-badge paid">Active</span></td>
                </tr>

                <tr>
                    <td>Jane Smith</td>
                    <td>2021-05-01</td>
                    <td>2023-05-01</td>
                    <td>24 months</td>
                    <td><span class="status-badge overdue">Churned</span></td>
                </tr>
            </tbody>
        </table>
    </section>

</main>
