<?php 
$pageTitle = "Home";
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ====== Hero Section ====== -->
<section class="hero">
    <div class="hero-text">
        <h1>Welcome to CASA VILLAGRACIA</h1>
        <p>Comfortable, safe, and affordable rooms.</p>
        <a href="rooms.php" class="hero-btn">View Rooms</a>
    </div>
</section>

<!-- ====== Availability Section ====== --> 
<section class="availability-area bg-white-smoke">
    <div class="container">
        <h2>For rates & Availability</h2>
        <h3>Search your ROOM</h3>
        <form action="rooms.php" method="get" class="availability-form">
            <label>Room Type
                <select name="type">
                    <option value="">Any</option>
                    <option value="single">Single</option>
                    <option value="double">Double</option>
                    <option value="bunk">Bunk Bed</option>
                </select>
            </label>
            <label>Price (Min)
                <input type="number" name="price_min" placeholder="₱0">
            </label>
            <label>Price (Max)
                <input type="number" name="price_max" placeholder="₱0">
            </label>
            <button type="submit">Check Availability</button>
        </form>
    </div>
</section>

<!-- ====== About Us Section ====== --> 
<section class="aboutus-area">
    <div class="container">
        <h2>About Us</h2>
        <div class="aboutus-flex">
            
            <!-- LEFT: Tabs -->
            <div class="aboutus-tabs-area">
                <ul class="aboutus-tabs">
                    <li class="tab active" data-content="company">About Company</li>
                    <li class="tab" data-content="terms">Terms & Condition</li>
                    <li class="tab" data-content="specialty">Our Specialty</li>
                    <li class="tab" data-content="services">Our Services</li>
                </ul>
            </div>

            <!-- CENTER: Description -->
            <div class="aboutus-description">
                <div id="company" class="content active">
                    <p>We provide clean, safe, and comfortable rooms for families, students, and professionals. Enjoy a homely environment with friendly staff.</p>
                </div>
                <div id="terms" class="content">
                    <p>All bookings are subject to our terms and conditions. Please read carefully before making a reservation.</p>
                </div>
                <div id="specialty" class="content">
                    <p>Our specialty includes affordable pricing, 24/7 security, fully furnished rooms, and fast Wi-Fi access.</p>
                </div>
                <div id="services" class="content">
                    <p>We offer additional services such as laundry, housekeeping, and guided local tours for our tenants.</p>
                </div>
            </div>

            <!-- RIGHT: Image -->
            <div class="aboutus-image">
                <img src="assets/images/about-image.png" alt="about" />
            </div>

        </div>
    </div>
</section>

<!-- ====== Fun Facts / Stats ====== -->
<section class="fun-facts">
    <div class="container">
        <div class="facts-grid">
            <div class="fact">
                <h3>120+</h3>
                <p>Happy Tenants</p>
            </div>
            <div class="fact">
                <h3>50+</h3>
                <p>Rooms Available</p>
            </div>
            <div class="fact">
                <h3>5</h3>
                <p>Years of Service</p>
            </div>
            <div class="fact">
                <h3>24/7</h3>
                <p>Security</p>
            </div>
        </div>
    </div>
</section>

<!-- ====== Contact Section ====== -->
<section class="contact-area">
    <div class="container">
        <div class="contact-flex">
            <div class="contact-map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3924.961485051478!2d123.9399084!3d10.3449648!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a9998802cdc303%3A0x922391774c67c57d!2sVillagracia%20Boarding%20House!5e0!3m2!1sen!2sph!4v1759588140943!5m2!1sen!2sph" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
            <div class="contact-info">
                <h4>Contact Info</h4>
                <p>📍 Almers Compound, Tabok, Mandaue City</p>
                <p>📧 villagracia@gmail.com</p>
                <p>📞 +63 930 913 2995</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
