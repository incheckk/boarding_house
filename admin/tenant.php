<?php
$pageTitle = "Tenant Management";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
?>

<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <h1>Tenant Management</h1>
        <p>Add, update, and manage tenant profiles along with room assignments and statuses.</p>
    </div>

    <!-- Add New Tenant Form Section -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Add New Tenant</h2>
            <a href="#" class="view-all" onclick="toggleForm()">Cancel <i class="fas fa-times"></i></a>
        </div>

        <form id="addTenantForm" class="tenant-form" style="display: block;">
            <div class="form-grid">
                <div class="form-group">
                    <label for="tenantName">Full Name</label>
                    <input type="text" id="tenantName" name="tenantName" required>
                </div>
                <div class="form-group">
                    <label for="tenantContact">Contact Info</label>
                    <input type="tel" id="tenantContact" name="tenantContact" required>
                </div>
                <div class="form-group">
                    <label for="tenantId">ID Number</label>
                    <input type="text" id="tenantId" name="tenantId" required>
                </div>
                <div class="form-group">
                    <label for="emergencyContact">Emergency Contact</label>
                    <input type="text" id="emergencyContact" name="emergencyContact" required>
                </div>
                <div class="form-group">
                    <label for="roomAssignment">Room Assignment</label>
                    <select id="roomAssignment" name="roomAssignment" required>
                        <option value="">Select Room</option>
                        <option value="Room 101">Room 101</option>
                        <option value="Room 102">Room 102</option>
                        <option value="Room 103">Room 103</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tenantStatus">Status</label>
                    <select id="tenantStatus" name="tenantStatus" required>
                        <option value="occupied">Occupied</option>
                        <option value="vacant">Vacant</option>
                        <option value="reserved">Reserved</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-action primary">Add Tenant</button>
                <button type="reset" class="btn-action">Reset</button>
            </div>
        </form>
    </div>

    <!-- Tenant List Section -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2>Tenant List</h2>
            <a href="#" class="view-all" onclick="toggleForm()">Add New <i class="fas fa-plus"></i></a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact Info</th>
                    <th>ID</th>
                    <th>Emergency Contact</th>
                    <th>Room Assignment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>John Doe</td>
                    <td>123-456-7890</td>
                    <td>ID123</td>
                    <td>Jane Doe - 098-765-4321</td>
                    <td>Room 101</td>
                    <td><span class="status-badge occupied">Occupied</span></td>
                    <td>
                        <button class="btn-action" onclick="editTenant('ID123')" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="btn-action warning" onclick="removeTenant('ID123')" title="Remove"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>Jane Smith</td>
                    <td>987-654-3210</td>
                    <td>ID456</td>
                    <td>Bob Smith - 012-345-6789</td>
                    <td>Room 102</td>
                    <td><span class="status-badge reserved">Reserved</span></td>
                    <td>
                        <button class="btn-action" onclick="editTenant('ID456')" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="btn-action warning" onclick="removeTenant('ID456')" title="Remove"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>Mike Johnson</td>
                    <td>555-123-4567</td>
                    <td>ID789</td>
                    <td>Sarah Johnson - 777-888-9999</td>
                    <td>Room 103</td>
                    <td><span class="status-badge vacant">Vacant</span></td>
                    <td>
                        <button class="btn-action" onclick="editTenant('ID789')" title="Edit"><i class="fas fa-edit"></i></button>
                        <button class="btn-action warning" onclick="removeTenant('ID789')" title="Remove"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</main>

<script>
    function toggleForm() {
        const form = document.getElementById('addTenantForm');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }

    function editTenant(id) {
        alert('Edit tenant with ID: ' + id + '\n(Note: Implement edit modal/form here with JS)');
    }

    function removeTenant(id) {
        if (confirm('Remove tenant with ID: ' + id + '?')) {
            alert('Tenant removed: ' + id + '\n(Note: Implement removal logic here with JS/backend)');
        }
    }

    document.getElementById('addTenantForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Tenant added successfully!\n(Note: Implement add logic here with JS/backend)');
        this.reset();
    });
</script>
