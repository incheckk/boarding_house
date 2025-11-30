<?php
$pageTitle = "Home";
require_once __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
/* ================================================================= */
/* MERGED & CONSOLIDATED CSS STYLES */
/* (Form button placement and About Us gold glow are finalized) */
/* ================================================================= */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --primary-color: linear-gradient(
        #cfa049 80%, 
        #a8763a 100% 
    );
    --secondary-color: #f60a0a;
    --accent-color: #080820;
    --dark-text: #2d2d2d;
    --light-text: #666;
    --white: #ffffff;
    --off-white: #f8f9fa;
    --border-color: #e0e0e0;
    --shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    --shadow-hover: 0 5px 25px rgba(0, 0, 0, 0.15);
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: var(--dark-text);
    background: linear-gradient(
        180deg,
        #fdfbf7 0%,
        #f5f0e8 50%,
        #fdfbf7 100%
    );
    position: relative;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle, rgba(212, 175, 55, 0.08) 1px, transparent 1px),
        radial-gradient(circle, rgba(218, 165, 32, 0.06) 1.5px, transparent 1.5px);
    background-size: 60px 60px, 100px 100px;
    background-position: 0 0, 30px 30px;
    pointer-events: none;
    z-index: 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
    z-index: 1;
}

/* ====== Hero Section ====== */
.hero {
    background: linear-gradient(
        135deg,
        #d4af37 0%,
        #f4e5c0 25%,
        #c9a961 50%,
        #f4e5c0 75%,
        #d4af37 100%
    );
    min-height: 645px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: var(--white);
    position: relative;
    overflow: hidden;
}

.hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle, rgba(255, 255, 255, 0.3) 2px, transparent 2px),
        radial-gradient(circle, rgba(255, 215, 0, 0.4) 1.5px, transparent 1.5px),
        radial-gradient(circle, rgba(139, 69, 19, 0.2) 1px, transparent 1px);
    background-size: 80px 80px, 120px 120px, 50px 50px;
    background-position: 0 0, 40px 40px, 20px 20px;
    animation: shimmer 20s ease-in-out infinite;
}

.hero::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 70% 30%, rgba(255, 215, 0, 0.15), transparent 50%),
                radial-gradient(circle at 30% 70%, rgba(218, 165, 32, 0.15), transparent 50%);
}

@keyframes shimmer {
    0%, 100% {
        opacity: 1;
        transform: translateY(0);
    }
    50% {
        opacity: 0.7;
        transform: translateY(-10px);
    }
}

.hero-text {
    position: relative;
    z-index: 2;
    max-width: 700px;
    padding: 20px;
    animation: fadeInUp 1s ease-out;
}

.hero-text h1 {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 2px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-text p {
    font-size: 1.4rem;
    margin-bottom: 30px;
    font-weight: 300;
    letter-spacing: 1px;
}

.hero-btn {
    display: inline-block;
    padding: 15px 40px;
    background: var(--secondary-color);
    color: var(--white);
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 120, 53, 0.449);
}

.hero-btn:hover {
    background: var(--accent-color);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 165, 0, 0.5);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ====== Availability Section ====== */
.availability-area {
    padding: 80px 0;
    background: white;
}

.bg-white-smoke {
    background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
}

.availability-area h2 {
    text-align: center;
    font-size: 2.5rem;
    background: var(--primary-color);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 10px;
    font-weight: 700;
}

.availability-area h3 {
    text-align: center;
    font-size: 1.2rem;
    color: var(--light-text);
    margin-bottom: 40px;
    font-weight: 400;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.availability-form {
    display: grid;
    /* Updated to allow for 4 columns */
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    max-width: 1000px;
    margin: 0 auto;
    background: var(--white);
    padding: 40px;
    border-radius: 15px;
    box-shadow: var(--shadow);
}

.availability-form label {
    display: flex;
    flex-direction: column;
    font-weight: 600;
    color: var(--dark-text);
    font-size: 0.95rem;
    text-align: left;
    flex-grow: 1;
}

.availability-form select,
.availability-form input {
    margin-top: 8px;
    padding: 12px 15px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: var(--off-white);
}

.availability-form select:focus,
.availability-form input:focus {
    outline: none;
    border-color: #d4af37;
    background: var(--white);
}

/* New container class for button alignment */
.submit-button-container {
    display: flex; 
    flex-direction: column;
    /* Pushes the button down to align with the bottom of the input fields */
    justify-content: flex-end; 
}

/* Targeted styling for the button */
.availability-submit-btn {
    width: 100%;
    /* Adjusted padding to match input field height better */
    padding: 12px 15px; 
    background: linear-gradient(135deg, #d4af37 0%, #c9a961 100%);
    color: var(--white);
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    height: auto; 
    margin-top: 0; 
}

.availability-submit-btn:hover {
    background: #d8c29a;
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
    color: var(--dark-text);
}


/* ====== About Us Section ====== */
.aboutus-area {
    padding: 80px 0;
    background: whitesmoke;
}

.aboutus-area h2 {
    text-align: center;
    font-size: 2.5rem;
    background: var(--primary-color);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 50px;
    font-weight: 700;
}

.aboutus-flex {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 40px;
    max-width: 1000px;
    margin: 0 auto;
}

.aboutus-tabs-area {
    background: var(--off-white);
    border-radius: 12px;
    padding: 20px;
    /* Added Gold Glow and Border */
    box-shadow: var(--shadow), 0 0 15px rgba(212, 175, 55, 0.4); 
    border: 1px solid rgba(212, 175, 55, 0.6); 
    transition: all 0.3s ease;
}

.aboutus-tabs-area:hover {
    box-shadow: var(--shadow-hover), 0 0 25px rgba(212, 175, 55, 0.7);
    border-color: rgba(212, 175, 55, 0.8);
}


.aboutus-tabs {
    list-style: none;
    padding: 0;
    margin: 0;
}

.aboutus-tabs .tab {
    padding: 15px 20px;
    margin-bottom: 10px;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-weight: 500;
    color: var(--dark-text);
}

.aboutus-tabs .tab:hover {
    background: linear-gradient(135deg, #fdfbf7 0%, #f5f0e8 100%);
    color: var(--dark-text);
    transform: translateX(5px);
}

.aboutus-tabs .tab.active {
    background: linear-gradient(135deg, #d4af37 0%, #c9a961 100%);
    color: var(--white);
    box-shadow: 0px 4px 15px rgba(212, 175, 55, 0.3);
    border-radius: 8px;
}

.aboutus-description {
    background: var(--off-white);
    border-radius: 12px;
    padding: 60px;
    /* Added Gold Glow and Border */
    box-shadow: var(--shadow), 0 0 15px rgba(212, 175, 55, 0.4); 
    border: 1px solid rgba(212, 175, 55, 0.6); 
    min-height: 350px;
    transition: all 0.3s ease;
}

.aboutus-description:hover {
    box-shadow: var(--shadow-hover), 0 0 25px rgba(212, 175, 55, 0.7);
    border-color: rgba(212, 175, 55, 0.8);
}

.aboutus-description .content {
    display: none;
    animation: fadeIn 0.5s ease;
}

.aboutus-description .content.active {
    display: block;
}

.aboutus-description p {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--light-text);
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

/* ====== Fun Facts Section ====== */
.fun-facts {
    padding: 80px 0;
    background: linear-gradient(
        135deg,
        #c9a961 0%,
        #f4e5c0 25%,
        #d4af37 50%,
        #f4e5c0 75%,
        #c9a961 100%
    );
    color: var(--white);
    text-align: center;
    position: relative;
    overflow: hidden;
}

.fun-facts::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle, rgba(255, 255, 255, 0.4) 2px, transparent 2px),
        radial-gradient(circle, rgba(139, 69, 19, 0.15) 1px, transparent 1px);
    background-size: 70px 70px, 40px 40px;
    background-position: 0 0, 35px 35px;
    opacity: 0.6;
}

.facts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 40px;
    text-align: center;
}

.fact {
    padding: 30px;
    background: rgba(245, 241, 241, 0.1);
    border-radius: 12px;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.fact:hover {
    transform: translateY(-10px);
    background: rgba(255, 255, 255, 0.15);
}

.fact h3 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: var(--accent-color);
}

.fact p {
    font-size: 1.1rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* ====== Contact Section ====== */
.contact-area {
    padding: 80px 0;
    background: var(--off-white);
    position: relative;
}

.contact-area::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle, rgba(212, 175, 55, 0.05) 1px, transparent 1px);
    background-size: 50px 50px;
    pointer-events: none;
}

.contact-flex {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 40px;
    max-width: 1100px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.contact-map {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: var(--shadow);
    border: 3px solid #f3d470ff;
}

.contact-map iframe {
    display: block;
}

.contact-info {
    background: linear-gradient(135deg, #ffffff 0%, #fdfbf7 100%);
    padding: 40px;
    border-radius: 15px;
    box-shadow: var(--shadow);
    border: 2px solid #f4e5c0;
    position: relative;
    overflow: hidden;
}

.contact-info::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(212, 175, 55, 0.05) 0%, transparent 70%);
    pointer-events: none;
}

.contact-info h4 {
    font-size: 2rem;
    background: linear-gradient(135deg, #d4af37 0%, #c9a961 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 30px;
    font-weight: 700;
    position: relative;
}

.contact-info p {
    font-size: 1.1rem;
    margin-bottom: 25px;
    color: var(--dark-text);
    display: flex;
    align-items: center;
    gap: 15px;
    position: relative;
    transition: all 0.3s ease;
}

.contact-info p:hover {
    transform: translateX(5px);
}

/* 🌟 STYLES FOR CONTACT ICONS 🌟 */
.contact-info-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(145deg, #fcefa4 0%, #d4af37 30%, #c19c2c 60%, #b8941f 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #333333;
    font-size: 20px;
    box-shadow:
        0 8px 20px rgba(0, 0, 0, 0.3),
        inset 0 -3px 8px rgba(139, 101, 8, 0.4),
        inset 0 3px 8px rgba(255, 248, 220, 0.7),
        inset 0 0 15px rgba(255, 248, 220, 0.2);
    border: 1px solid rgba(255, 248, 220, 0.6);
    flex-shrink: 0;
    transition: all 0.2s ease-in-out;
}

.contact-info-icon:hover {
    transform: translateY(-2px);
    box-shadow:
        0 10px 25px rgba(0, 0, 0, 0.4),
        inset 0 -4px 10px rgba(139, 101, 8, 0.5),
        inset 0 4px 10px rgba(255, 248, 220, 0.8),
        inset 0 0 20px rgba(255, 248, 220, 0.3);
}

/* ====== Responsive Design ====== */
@media (max-width: 992px) {
    .hero {
        min-height: 500px;
    }

    .hero-text h1 {
        font-size: 2.5rem;
    }

    .aboutus-flex {
        grid-template-columns: 1fr;
        gap: 30px;
    }

    .aboutus-tabs-area {
        flex: auto;
    }

    .aboutus-tabs {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        padding: 0;
    }

    .aboutus-tabs .tab {
        flex-grow: 1;
        text-align: center;
        margin-bottom: 0;
        border-radius: 0;
        border-right: 1px solid #eee;
        border-bottom: 1px solid #eee;
    }
    
    .aboutus-tabs .tab:last-child {
        border-right: none;
    }

    .aboutus-tabs .tab.active {
        border-radius: 8px;
    }

    .contact-flex {
        grid-template-columns: 1fr;
    }

    .contact-map {
        order: 2;
        height: 350px;
    }

    .contact-info {
        order: 1;
    }
}

@media (max-width: 640px) {
    .hero-text h1 {
        font-size: 2rem;
    }

    .hero-text p {
        font-size: 1.1rem;
    }

    .availability-area h2 {
        font-size: 2rem;
    }

    .facts-grid {
        grid-template-columns: 1fr;
    }

    .availability-form {
        grid-template-columns: 1fr; /* On small screens, force single column */
    }
    
    .aboutus-tabs {
        flex-direction: column;
    }
    
    .aboutus-tabs .tab {
        border-right: none;
    }
    
    .aboutus-description {
        padding: 30px;
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
            <label class="submit-button-container">
                <button type="submit" class="availability-submit-btn">Check Availability</button>
            </label>
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