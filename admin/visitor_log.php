<?php
$pageTitle = "Visitor Log";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <h1>Visitor Log</h1>
        <p>Track visitors entering and exiting the property.</p>
    </div>

    <!-- Visitor Logging -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2>Visitor Logging</h2>
            <a href="#" class="view-all" id="add-visitor-btn">Add Visitor <i class="fas fa-plus"></i></a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date/Time In</th>
                    <th>Date/Time Out</th>
                    <th>Purpose</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Alice Johnson</td>
                    <td>2023-10-01 10:00 AM</td>
                    <td>2023-10-01 11:30 AM</td>
                    <td>Meeting Tenant</td>
                    <td>
                        <button class="btn-action" onclick="editVisitor(this)">Update</button>
                        <button class="btn-action warning" onclick="deleteVisitor(this)">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>Bob Smith</td>
                    <td>2023-10-02 02:00 PM</td>
                    <td>-</td>
                    <td>Inspection</td>
                    <td>
                        <button class="btn-action" onclick="editVisitor(this)">Update</button>
                        <button class="btn-action warning" onclick="deleteVisitor(this)">Delete</button>
                    </td>
                </tr>
                <!-- Add more rows dynamically -->
            </tbody>
        </table>
    </section>

    <!-- Add/Edit Visitor Form -->
    <div id="visitor-form" class="dashboard-section" style="display: none;">
        <h3 id="form-title">Add Visitor</h3>
        <form id="visitor-log-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="visitor-name">Name</label>
                    <input type="text" id="visitor-name" required>
                </div>
                <div class="form-group">
                    <label for="time-in">Date/Time In</label>
                    <input type="datetime-local" id="time-in" required>
                </div>
                <div class="form-group">
                    <label for="time-out">Date/Time Out</label>
                    <input type="datetime-local" id="time-out">
                </div>
                <div class="form-group">
                    <label for="purpose">Purpose</label>
                    <input type="text" id="purpose" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-action primary">Save</button>
                <button type="button" class="btn-action warning" onclick="cancelForm()">Cancel</button>
            </div>
        </form>
    </div>

</main>

<script>
    // Show add visitor form
    document.getElementById('add-visitor-btn').addEventListener('click', function() {
        document.getElementById('visitor-form').style.display = 'block';
        document.getElementById('form-title').textContent = 'Add Visitor';
        document.getElementById('visitor-log-form').reset();
    });

    // Cancel form
    function cancelForm() {
        document.getElementById('visitor-form').style.display = 'none';
    }

    // Edit visitor
    function editVisitor(button) {
        const row = button.closest('tr');
        document.getElementById('visitor-form').style.display = 'block';
        document.getElementById('form-title').textContent = 'Update Visitor';
        // TODO: Populate form with row data
    }

    // Delete visitor
    function deleteVisitor(button) {
        if(confirm('Are you sure you want to delete this visitor?')) {
            button.closest('tr').remove();
        }
    }

    // Form submission
    document.getElementById('visitor-log-form').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Visitor saved successfully! (Add backend integration here)');
        document.getElementById('visitor-form').style.display = 'none';
    });
</script>
