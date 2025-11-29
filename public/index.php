<?php
$pageTitle = "Home";
require_once __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    /* Global Styles for Layout */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    /* Hero Section */
    .hero {
        background: linear-gradient(135deg, #d4af37 0%, #f4e5c0 25%, #c9a961 50%, #f4e5c0 75%, #d4af37 100%);
        height: 60vh;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: white;
    }

    .hero-text h1 {
        font-size: 4rem;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .hero-text p {
        font-size: 1.5rem;
        margin-bottom: 30px;
    }

    .hero-btn {
        display: inline-block;
        padding: 12px 30px;
        background: #d46137ff;
        /* Gold color */
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 600;
        transition: background 0.3s;
    }

    .hero-btn:hover {
        background: #c9a961;
    }

    /* Availability Section */
    .bg-white-smoke {
        background-color: #f5f5f5;
    }

    .availability-area {
        padding: 60px 0;
        text-align: center;
    }

    .availability-area h2 {
        font-size: 2.5rem;
        background: linear-gradient(135deg, #d4af37 0%, #c9a961 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 5px;
        font-weight: 700;
    }

    .availability-area h3 {
        font-size: 1.8rem;
        color: #64748b;
        margin-bottom: 30px;
        font-weight: 600;
    }

    .availability-form {
        display: flex;
        justify-content: center;
        gap: 20px;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        max-width: 900px;
        margin: 0 auto;
    }

    .availability-form label {
        display: flex;
        flex-direction: column;
        text-align: left;
        font-weight: 500;
        color: #333;
        flex-grow: 1;
    }

    .availability-form select,
    .availability-form input[type="number"] {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-top: 5px;
        font-size: 1rem;
    }

    .availability-form button {
        padding: 10px 20px;
        border: none;
        background: linear-gradient(135deg, #d4af37 0%, #c9a961 100%);
        color: white;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        border-radius: 5px;
        align-self: flex-end;
        transition: background 0.3s;
    }

    .availability-form button:hover {
        background: linear-gradient(135deg, #c9a961 0%, #b8941f 100%);
    }

    /* About Us Section */
    .aboutus-area {
        padding: 80px 0;
    }

    .aboutus-area h2 {
        text-align: center;
        font-size: 3rem;
        color: #333;
        margin-bottom: 50px;
        font-weight: 700;
    }

    .aboutus-flex {
        display: flex;
        gap: 50px;
    }

    .aboutus-tabs-area {
        flex: 0 0 250px;
        /* Fixed width for tabs */
    }

    .aboutus-tabs {
        list-style: none;
        padding: 0;
        margin: 0;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .aboutus-tabs li {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        font-weight: 500;
        transition: background-color 0.3s, color 0.3s;
    }

    .aboutus-tabs li:last-child {
        border-bottom: none;
    }

    .aboutus-tabs li:hover {
        background-color: #f7f7f7;
        color: #d4af37;
    }

    .aboutus-tabs li.active {
        background: #d4af37;
        color: white;
        border-radius: 10px 10px 0 0;
        /* Only top corners rounded if first item */
    }

    .aboutus-description {
        flex-grow: 1;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        min-height: 350px;
    }

    .aboutus-description .content {
        display: none;
        animation: fadeIn 0.5s ease-in-out;
    }

    .aboutus-description .content.active {
        display: block;
    }

    .aboutus-description p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #555;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Fun Facts / Stats */
    .fun-facts {
        background: linear-gradient(135deg, #d4af37 0%, #d4af37 25%, #c9a961 50%, #e7d29cff 75%, #d4af37 100%);
        color: white;
        padding: 60px 0;
        text-align: center;
    }

    .facts-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
    }

    .fact h3 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .fact p {
        font-size: 1.2rem;
        font-weight: 300;
    }

    /* Contact Section */
    .contact-area {
        padding: 80px 0;
        background-color: #f8f9fa;
    }

    .contact-flex {
        display: flex;
        gap: 40px;
        align-items: stretch;
    }

    .contact-map {
        flex: 2;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border: 1px solid #ddd;
    }

    .contact-map iframe {
        display: block;
    }

    .contact-info {
        flex: 1;
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .contact-info h4 {
        font-size: 1.8rem;
        color: #d4af37;
        margin-bottom: 25px;
        font-weight: 700;
    }

    /* 🌟 STYLES FOR CONTACT ICONS 🌟 */
    .contact-info p {
        color: #64748b;
        font-size: 1rem;
        line-height: 1.8;
        display: flex;
        align-items: center;
        gap: 15px;
        /* Increased gap for larger icon */
        margin-bottom: 15px;
    }

    .contact-info-icon {
        width: 45px;
        /* Slightly larger for more prominence */
        height: 45px;
        /* More pronounced gradient for a rounded, metallic look */
        background: linear-gradient(145deg, #fcefa4 0%, #d4af37 30%, #c19c2c 60%, #b8941f 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #333333;
        /* Darker icon color for contrast against brighter gold */
        font-size: 20px;
        /* Slightly larger icon */
        /* Enhanced shadows for depth */
        box-shadow:
            0 8px 20px rgba(0, 0, 0, 0.3),
            /* Stronger outer shadow */
            inset 0 -3px 8px rgba(139, 101, 8, 0.4),
            /* Bottom inner shadow for depth */
            inset 0 3px 8px rgba(255, 248, 220, 0.7),
            /* Top inner highlight */
            inset 0 0 15px rgba(255, 248, 220, 0.2);
        /* Subtle overall inner glow */
        border: 1px solid rgba(255, 248, 220, 0.6);
        /* Slightly softer border */
        flex-shrink: 0;
        transition: all 0.2s ease-in-out;
        /* Smooth transition for hover effects if added later */
    }

    /* Optional: Add a hover effect to make them interactive */
    .contact-info-icon:hover {
        transform: translateY(-2px);
        /* Slight lift on hover */
        box-shadow:
            0 10px 25px rgba(0, 0, 0, 0.4),
            inset 0 -4px 10px rgba(139, 101, 8, 0.5),
            inset 0 4px 10px rgba(255, 248, 220, 0.8),
            inset 0 0 20px rgba(255, 248, 220, 0.3);
    }


    /* Responsive Design */
    @media (max-width: 992px) {
        .availability-form {
            flex-direction: column;
            align-items: stretch;
            gap: 15px;
        }

        .availability-form button {
            align-self: auto;
        }

        .aboutus-flex {
            flex-direction: column;
        }

        .aboutus-tabs-area {
            flex: auto;
        }

        .aboutus-tabs {
            display: flex;
            flex-wrap: wrap;
        }

        .aboutus-tabs li {
            flex-grow: 1;
            text-align: center;
            border-right: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }

        .aboutus-tabs li:last-child {
            border-right: none;
        }

        .facts-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .contact-flex {
            flex-direction: column;
        }

        .contact-map {
            order: 2;
        }

        .contact-info {
            order: 1;
        }
    }

    @media (max-width: 600px) {
        .hero-text h1 {
            font-size: 2.5rem;
        }

        .hero-text p {
            font-size: 1.2rem;
        }

        .aboutus-area h2 {
            font-size: 2rem;
        }

        .facts-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="hero">
    <div class="hero-text">
        <h1>Welcome to CASA VILLAGRACIA</h1>
        <p>Comfortable, safe, and affordable rooms.</p>
        <a href="rooms.php" class="hero-btn">View Rooms</a>
    </div>
</section>

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

<section class="aboutus-area">
    <div class="container">
        <h2>About Us</h2>
        <div class="aboutus-flex">

            <div class="aboutus-tabs-area">
                <ul class="aboutus-tabs">
                    <li class="tab active" data-content="company">About Company</li>
                    <li class="tab" data-content="terms">Terms & Condition</li>
                    <li class="tab" data-content="specialty">Our Specialty</li>
                    <li class="tab" data-content="services">Our Services</li>
                </ul>
            </div>

            <div class="aboutus-description">
                <div id="company" class="content active">
                    <p>We provide clean, safe, and comfortable rooms for families, students, and professionals. Enjoy a
                        homely environment with friendly staff.</p>
                </div>
                <div id="terms" class="content">
                    <p>All bookings are subject to our terms and conditions. Please read carefully before making a
                        reservation.</p>
                </div>
                <div id="specialty" class="content">
                    <p>Our specialty includes affordable pricing, 24/7 security, fully furnished rooms, and fast Wi-Fi
                        access.</p>
                </div>
                <div id="services" class="content">
                    <p>We offer additional services such as laundry, housekeeping, and guided local tours for our
                        tenants.</p>
                </div>
            </div>

        </div>
    </div>
</section>

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

<section class="contact-area">
    <div class="container">
        <div class="contact-flex">
            <div class="contact-map">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3924.961485051478!2d123.9399084!3d10.3449648!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a9998802cdc303%3A0x922391774c67c57d!2sVillagracia%20Boarding%20House!5e0!3m2!1sen!2sph!4v1759588140943!5m2!1sen!2sph"
                    width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
            <div class="contact-info">
                <h4>Contact Info</h4>
                <p>
                    <span class="contact-info-icon"><i class="fas fa-map-marker-alt"></i>
                </span>
                    Almers Compound, Tabok, Mandaue City
                </p>
                <p>
                    <span class="contact-info-icon"><i class="fas fa-envelope"></i>
                </span>
                    villagracia@gmail.com
                </p>
                <p>
                    <span class="contact-info-icon"><i class="fas fa-phone"></i>
                </span>
                    +63 930 913 2995
                </p>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('.aboutus-tabs .tab');
        const contents = document.querySelectorAll('.aboutus-description .content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));

                // Add active class to the clicked tab
                tab.classList.add('active');

                // Show the corresponding content
                const targetId = tab.getAttribute('data-content');
                document.getElementById(targetId).classList.add('active');
            });
        });
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>