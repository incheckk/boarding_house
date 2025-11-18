<?php 
$pageTitle = "Home - Boarding House";
require_once __DIR__ . '/../includes/header.php';
?>

<!-- ====== Hero Section ====== -->
<section class="hero">
    <div class="hero-text">
        <h1>Welcome to Our Boarding House</h1>
        <p>Comfortable, clean, and affordable rooms.</p>
        <a href="rooms.php" class="hero-btn">View Rooms</a>
    </div>
</section>

<!-- ====== Why Choose Us ====== -->
<section class="why-choose-us">
    <div class="container">
        <h2>Why Choose Us?</h2>
        <ul>
            <li>✔ Affordable rates</li>
            <li>✔ Safe & secure</li>
            <li>✔ Near schools & workplaces</li>
            <li>✔ Friendly staff</li>
        </ul>
    </div>
</section>

<!-- ====== Availability Section ====== --> 
<section class="availability-area bg-white-smoke">
    <div class="container">
        <h2>Check Room Availability</h2>
        <form action="rooms.php" method="get" class="availability-form">
            <label>Room Type
                <select name="type">
                    <option value="single">Single</option>
                    <option value="double">Double</option>
                    <option value="bunk">Bunk Bed</option>
                </select>
            </label>
            <label>Price Min
                <input type="number" name="price_min" placeholder="₱0">
            </label>
            <label>Price Max
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
            <div class="aboutus-text">
                <p>We provide clean, safe, and comfortable rooms for students, professionals, and travelers. Enjoy a homely environment with friendly staff.</p>
                <ul>
                    <li>Affordable pricing options</li>
                    <li>24/7 security</li>
                    <li>Fully furnished rooms</li>
                    <li>Fast Wi-Fi access</li>
                </ul>
            </div>
            <div class="aboutus-image">
                <img src="assets/images/about-image.png" alt="about" />
            </div>
        </div>
    </div>
</section>

<!-- ====== Rooms Section ====== --> 
<section class="rooms-area bg-gray-color">
    <div class="container">
        <h2>Our Rooms</h2>
        <div class="rooms-grid">
            <div class="room-card">
                <img src="assets/images/apartment/Room.png" alt="Single Room">
                <h3>Single Room</h3>
                <p>₱200/day - 1 Bed</p>
                <a href="room-single.php" class="button">View Details</a>
            </div>
            <div class="room-card">
                <img src="assets/images/apartment/Room.png" alt="Double Room">
                <h3>Double Room</h3>
                <p>₱350/day - 2 Beds</p>
                <a href="room-single.php" class="button">View Details</a>
            </div>
            <div class="room-card">
                <img src="assets/images/apartment/Room.png" alt="Bunk Room">
                <h3>Bunk Bed Room</h3>
                <p>₱150/day per bed</p>
                <a href="room-single.php" class="button">View Details</a>
            </div>
        </div>
        <a href="rooms.php" class="button all-rooms-btn">All Rooms</a>
    </div>
</section>

<!-- ====== Gallery Section ====== -->
<section class="gallery-area">
    <div class="container">
        <h2>Gallery</h2>
        <div class="gallery-grid">
            <img src="assets/images/gallery/1.jpg" alt="Room Image">
            <img src="assets/images/gallery/2.jpg" alt="Common Area">
            <img src="assets/images/gallery/3.jpg" alt="Kitchen">
            <img src="assets/images/gallery/4.jpg" alt="Bathroom">
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

<!-- ====== Testimonials Section ====== -->
<section class="testimonial-area bg-gray-color">
    <div class="container">
        <h2>Testimonials</h2>
        <div class="testimonials-slider">
            <div class="testimonial">
                <p>"Great environment and very affordable rooms. Highly recommended!"</p>
                <h4>Juan Dela Cruz - Student</h4>
            </div>
            <div class="testimonial">
                <p>"Friendly staff and clean rooms. Perfect for staying near work."</p>
                <h4>Maria Santos - Professional</h4>
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
