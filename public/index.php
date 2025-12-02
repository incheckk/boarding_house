<?php
$pageTitle = "Home";
require_once __DIR__ . '/../includes/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
/* ================================================================= */
/* IMPROVED & CONSISTENT DESIGN - CASA VILLAGRACIA */
/* ================================================================= */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

:root {
    --primary-gold: #d4af37;
    --gold-light: #f4e5c0;
    --gold-dark: #c9a961;
    --gold-darker: #a8763a;
    --secondary-red: #f60a0a;
    --accent-dark: #080820;
    --text-dark: #2d2d2d;
    --text-light: #666;
    --white: #ffffff;
    --off-white: #f8f9fa;
    --border-light: #e0e0e0;
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.12);
    --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.16);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: var(--text-dark);
    background: linear-gradient(180deg, #fdfbf7 0%, #f5f0e8 50%, #fdfbf7 100%);
    position: relative;
    overflow-x: hidden;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle, rgba(212, 175, 55, 0.05) 1px, transparent 1px);
    background-size: 50px 50px;
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

/* ====== SECTION HEADERS (Consistent Style) ====== */
.section-header {
    text-align: center;
    margin-bottom: 50px;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary-gold) 0%, var(--gold-dark) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 12px;
    letter-spacing: 0.5px;
}

.section-subtitle {
    font-size: 1.1rem;
    color: var(--text-light);
    font-weight: 400;
    text-transform: uppercase;
    letter-spacing: 2px;
}

/* ====== HERO SECTION ====== */
.hero {
    background: linear-gradient(135deg, var(--primary-gold) 0%, var(--gold-light) 25%, var(--gold-dark) 50%, var(--gold-light) 75%, var(--primary-gold) 100%);
    min-height: 720px;
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
    inset: 0;
    background-image: 
        radial-gradient(circle, rgba(255, 255, 255, 0.2) 2px, transparent 2px),
        radial-gradient(circle, rgba(255, 215, 0, 0.3) 1.5px, transparent 1.5px);
    background-size: 80px 80px, 120px 120px;
    background-position: 0 0, 40px 40px;
    animation: shimmer 25s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% { opacity: 1; transform: translateY(0); }
    50% { opacity: 0.6; transform: translateY(-15px); }
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 780px;
    padding: 40px 20px;
    animation: fadeInUp 1s ease-out;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 3px;
    text-shadow: 3px 3px 8px rgba(0, 0, 0, 0.3);
    line-height: 1.2;
}

.hero-description {
    font-size: 1.4rem;
    margin-bottom: 35px;
    font-weight: 300;
    letter-spacing: 1px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.hero-btn {
    display: inline-block;
    padding: 16px 48px;
    background: var(--secondary-red);
    color: var(--white);
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: var(--transition);
    box-shadow: 0 4px 20px rgba(246, 10, 10, 0.4);
}

.hero-btn:hover {
    background: var(--accent-dark);
    transform: translateY(-3px);
    box-shadow: 0 6px 28px rgba(8, 8, 32, 0.5);
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ====== AVAILABILITY SECTION ====== */
.availability-section {
    padding: 80px 0;
    background: var(--white);
}

.search-form {
    max-width: 1000px;
    margin: 0 auto;
    background: var(--white);
    padding: 40px;
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(212, 175, 55, 0.2);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    align-items: end;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 0.95rem;
    margin-bottom: 8px;
    letter-spacing: 0.3px;
}

.form-input,
.form-select {
    padding: 14px 16px;
    border: 2px solid var(--border-light);
    border-radius: 10px;
    font-size: 1rem;
    transition: var(--transition);
    background: var(--off-white);
    font-family: inherit;
}

.form-input:focus,
.form-select:focus {
    outline: none;
    border-color: var(--primary-gold);
    background: var(--white);
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
}

.form-submit {
    padding: 14px 16px;
    background: linear-gradient(135deg, var(--primary-gold) 0%, var(--gold-dark) 100%);
    color: var(--white);
    border: none;
    border-radius: 10px;
    font-size: 1.05rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    font-family: inherit;
}

.form-submit:hover {
    background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold-darker) 100%);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* ====== ABOUT US SECTION ====== */
.about-section {
    padding: 80px 0;
    background: var(--off-white);
}

.about-container {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 30px;
    max-width: 1100px;
    margin: 0 auto;
}

.tabs-wrapper {
    background: var(--white);
    border-radius: 16px;
    padding: 20px;
    box-shadow: var(--shadow-sm), 0 0 0 1px rgba(212, 175, 55, 0.15);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.tabs-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.tab-item {
    padding: 16px 20px;
    cursor: pointer;
    border-radius: 10px;
    transition: var(--transition);
    font-weight: 500;
    color: var(--text-dark);
    font-size: 0.95rem;
}

.tab-item:hover {
    background: var(--off-white);
    transform: translateX(4px);
}

.tab-item.active {
    background: linear-gradient(135deg, var(--primary-gold) 0%, var(--gold-dark) 100%);
    color: var(--white);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
}

.content-wrapper {
    background: var(--white);
    border-radius: 16px;
    padding: 50px;
    box-shadow: var(--shadow-sm), 0 0 0 1px rgba(212, 175, 55, 0.15);
    min-height: 300px;
}

.content-panel {
    display: none;
    animation: fadeIn 0.4s ease;
}

.content-panel.active {
    display: block;
}

.content-text {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--text-light);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* ====== FUN FACTS SECTION ====== */
.facts-section {
    padding: 80px 0;
    background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold-light) 25%, var(--primary-gold) 50%, var(--gold-light) 75%, var(--gold-dark) 100%);
    color: var(--white);
    position: relative;
    overflow: hidden;
}

.facts-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image: 
        radial-gradient(circle, rgba(255, 255, 255, 0.3) 2px, transparent 2px);
    background-size: 60px 60px;
    opacity: 0.5;
}

.facts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 30px;
    position: relative;
    z-index: 1;
}

.fact-card {
    padding: 40px 20px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    text-align: center;
    transition: var(--transition);
}

.fact-card:hover {
    transform: translateY(-8px);
    background: rgba(255, 255, 255, 0.15);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
}

.fact-number {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: var(--accent-dark);
    text-shadow: 2px 2px 4px rgba(255, 255, 255, 0.3);
}

.fact-label {
    font-size: 1.1rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1.5px;
}

/* ====== CONTACT SECTION ====== */
.contact-section {
    padding: 80px 0;
    background: var(--white);
}

.contact-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
    max-width: 1100px;
    margin: 0 auto;
}

.map-container {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    border: 2px solid var(--gold-light);
    height: 450px;
}

.map-container iframe {
    display: block;
    width: 100%;
    height: 100%;
}

.contact-card {
    background: linear-gradient(135deg, var(--white) 0%, var(--off-white) 100%);
    padding: 40px 30px;
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(212, 175, 55, 0.2);
}

.contact-title {
    font-size: 2rem;
    background: linear-gradient(135deg, var(--primary-gold) 0%, var(--gold-dark) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 30px;
    font-weight: 700;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
    color: var(--text-dark);
    font-size: 1rem;
    transition: var(--transition);
}

.contact-item:hover {
    transform: translateX(5px);
}

.contact-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(145deg, #fcefa4 0%, var(--primary-gold) 30%, #c19c2c 60%, #b8941f 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #333;
    font-size: 18px;
    box-shadow: 
        0 4px 12px rgba(0, 0, 0, 0.15),
        inset 0 -2px 6px rgba(139, 101, 8, 0.3),
        inset 0 2px 6px rgba(255, 248, 220, 0.6);
    border: 1px solid rgba(255, 248, 220, 0.5);
    flex-shrink: 0;
    transition: var(--transition);
}

.contact-icon:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 6px 16px rgba(0, 0, 0, 0.2),
        inset 0 -3px 8px rgba(139, 101, 8, 0.4),
        inset 0 3px 8px rgba(255, 248, 220, 0.7);
}

/* ====== RESPONSIVE DESIGN ====== */
@media (max-width: 992px) {
    .hero {
        min-height: 500px;
    }
    
    .hero-title {
        font-size: 2.8rem;
    }
    
    .section-title {
        font-size: 2.2rem;
    }
    
    .about-container {
        grid-template-columns: 1fr;
        gap: 25px;
    }
    
    .tabs-wrapper {
        position: static;
    }
    
    .tabs-list {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .tab-item {
        flex: 1 1 calc(50% - 5px);
        text-align: center;
        min-width: 140px;
    }
    
    .tab-item:hover {
        transform: none;
    }
    
    .contact-grid {
        grid-template-columns: 1fr;
    }
    
    .map-container {
        order: 2;
        height: 350px;
    }
    
    .contact-card {
        order: 1;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.2rem;
        letter-spacing: 1.5px;
    }
    
    .hero-description {
        font-size: 1.2rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .section-subtitle {
        font-size: 1rem;
    }
    
    .search-form {
        padding: 30px 20px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .content-wrapper {
        padding: 35px 25px;
    }
    
    .facts-grid {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
    }
    
    .fact-number {
        font-size: 2.5rem;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 15px;
    }
    
    .hero {
        min-height: 450px;
    }
    
    .hero-title {
        font-size: 1.8rem;
        letter-spacing: 1px;
    }
    
    .hero-description {
        font-size: 1.1rem;
    }
    
    .hero-btn {
        padding: 14px 36px;
        font-size: 1rem;
    }
    
    .section-title {
        font-size: 1.8rem;
    }
    
    .search-form {
        padding: 25px 15px;
    }
    
    .tabs-list {
        flex-direction: column;
    }
    
    .tab-item {
        flex: 1 1 auto;
    }
    
    .content-wrapper {
        padding: 25px 20px;
    }
    
    .content-text {
        font-size: 1rem;
    }
    
    .contact-card {
        padding: 30px 20px;
    }
    
    .contact-title {
        font-size: 1.6rem;
    }
    
    .contact-item {
        font-size: 0.95rem;
    }
}
</style>

<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Welcome to CASA VILLAGRACIA</h1>
        <p class="hero-description">Comfortable, safe, and affordable rooms for everyone.</p>
        <a href="rooms.php" class="hero-btn">View Rooms</a>
    </div>
</section>

<section class="availability-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">For Rates & Availability</h2>
            <p class="section-subtitle">Search Your Room</p>
        </div>
        <form action="rooms.php" method="get" class="search-form">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Room Type</label>
                    <select name="type" class="form-select">
                        <option value="">Any</option>
                        <option value="single">Single</option>
                        <option value="double">Double</option>
                        <option value="bunk">Bunk Bed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Price (Min)</label>
                    <input type="number" name="price_min" placeholder="₱0" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Price (Max)</label>
                    <input type="number" name="price_max" placeholder="₱0" class="form-input">
                </div>
                <div class="form-group">
                    <button type="submit" class="form-submit">Check Availability</button>
                </div>
            </div>
        </form>
    </div>
</section>

<section class="about-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">About Us</h2>
        </div>
        <div class="about-container">
            <div class="tabs-wrapper">
                <ul class="tabs-list">
                    <li class="tab-item active" data-content="company">About Company</li>
                    <li class="tab-item" data-content="terms">Terms & Condition</li>
                    <li class="tab-item" data-content="specialty">Our Specialty</li>
                    <li class="tab-item" data-content="services">Our Services</li>
                </ul>
            </div>
            <div class="content-wrapper">
                <div id="company" class="content-panel active">
                    <p class="content-text">We provide clean, safe, and comfortable rooms for families, students, and professionals. Enjoy a homely environment with friendly staff.</p>
                </div>
                <div id="terms" class="content-panel">
                    <p class="content-text">All bookings are subject to our terms and conditions. Please read carefully before making a reservation.</p>
                </div>
                <div id="specialty" class="content-panel">
                    <p class="content-text">Our specialty includes affordable pricing, 24/7 security, fully furnished rooms, and fast Wi-Fi access.</p>
                </div>
                <div id="services" class="content-panel">
                    <p class="content-text">We offer additional services such as laundry, housekeeping, and guided local tours for our tenants.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="facts-section">
    <div class="container">
        <div class="facts-grid">
            <div class="fact-card">
                <h3 class="fact-number">120+</h3>
                <p class="fact-label">Happy Tenants</p>
            </div>
            <div class="fact-card">
                <h3 class="fact-number">50+</h3>
                <p class="fact-label">Rooms Available</p>
            </div>
            <div class="fact-card">
                <h3 class="fact-number">5</h3>
                <p class="fact-label">Years of Service</p>
            </div>
            <div class="fact-card">
                <h3 class="fact-number">24/7</h3>
                <p class="fact-label">Security</p>
            </div>
        </div>
    </div>
</section>

<section class="contact-section">
    <div class="container">
        <div class="contact-grid">
            <div class="map-container">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3924.961485051478!2d123.9399084!3d10.3449648!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a9998802cdc303%3A0x922391774c67c57d!2sVillagracia%20Boarding%20House!5e0!3m2!1sen!2sph!4v1759588140943!5m2!1sen!2sph"
                    allowfullscreen="" loading="lazy"></iframe>
            </div>
            <div class="contact-card">
                <h4 class="contact-title">Contact Info</h4>
                <div class="contact-item">
                    <span class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </span>
                    <span>Almers Compound, Tabok, Mandaue City</span>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <span>villagracia@gmail.com</span>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </span>
                    <span>+63 930 913 2995</span>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('.tab-item');
        const contents = document.querySelectorAll('.content-panel');

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