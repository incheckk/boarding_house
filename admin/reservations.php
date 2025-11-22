<?php  
$pageTitle = "Reservations";
require_once __DIR__ . '/php/admin-header.php';
require_once __DIR__ . '/php/admin-sidebar.php';
?>

<!-- Main Content Wrapper -->
<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <h1>Reservations</h1>
        <p>Manage online bookings and waiting lists for your properties.</p>
    </div>

    <!-- Online Reservation System -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2>Online Reservation System</h2>
        </div>

        <!-- Availability Checker -->
        <div class="availability-checker">
            <h3>Check Room Availability</h3>
            <form class="availability-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="checkin">Check-in Date</label>
                        <input type="date" id="checkin" required>
                    </div>
                    <div class="form-group">
                        <label for="checkout">Check-out Date</label>
                        <input type="date" id="checkout" required>
                    </div>
                    <div class="form-group">
                        <label for="room-type">Room Type</label>
                        <select id="room-type">
                            <option value="single">Single</option>
                            <option value="double">Double</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="guests">Number of Guests</label>
                        <input type="number" id="guests" min="1" max="10" value="1">
                    </div>
                </div>
                <button type="submit" class="btn-action primary">Check Availability</button>
            </form>
        </div>

        <!-- Available Rooms Display -->
        <div class="available-rooms">
            <h3>Available Rooms</h3>
            <div class="rooms-grid">
                <div class="room-card">
                    <img src="https://via.placeholder.com/300x200" alt="Room Image">
                    <h4>Single Room</h4>
                    <p>$120/night</p>
                    <button class="btn-action">Book Now</button>
                </div>
                <div class="room-card">
                    <img src="https://via.placeholder.com/300x200" alt="Room Image">
                    <h4>Double Room</h4>
                    <p>$150/night</p>
                    <button class="btn-action">Book Now</button>
                </div>
            </div>
        </div>

        <!-- Reservation Form -->
        <div class="reservation-form">
            <h3>Make a Reservation</h3>
            <form>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="selected-room">Selected Room</label>
                        <select id="selected-room">
                            <option value="">Select Room</option>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-action primary">Confirm Reservation</button>
                </div>
            </form>
        </div>

    </section>

    <!-- Waiting List Management -->
    <section class="dashboard-section">
        <div class="section-header">
            <h2>Waiting List Management</h2>
            <a href="#" class="view-all">Add to List <i class="fas fa-plus"></i></a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Requested Room</th>
                    <th>Date Added</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>John Doe</td>
                    <td>john@example.com</td>
                    <td>Double</td>
                    <td>2023-10-01</td>
                    <td><span class="status-badge pending">Pending</span></td>
                    <td>
                        <button class="btn-action">Promote</button>
                        <button class="btn-action warning">Remove</button>
                    </td>
                </tr>

                <tr>
                    <td>Jane Smith</td>
                    <td>jane@example.com</td>
                    <td>Single</td>
                    <td>2023-10-05</td>
                    <td><span class="status-badge pending">Pending</span></td>
                    <td>
                        <button class="btn-action">Promote</button>
                        <button class="btn-action warning">Remove</button>
                    </td>
                </tr>
            </tbody>

        </table>
    </section>

</main>

<script>
    console.log("Reservations page loaded. Add JS for interactivity.");
</script>
