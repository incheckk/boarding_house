<?php
$pageTitle = "Home";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';

// Fetch dynamic counts
$happyTenants = $pdo->query("SELECT COUNT(*) AS count FROM tenant WHERE tstat_id = 2")->fetch()['count'];
$roomsAvailable = $pdo->query("SELECT COUNT(*) AS count FROM room WHERE rstat_id = 1 OR rstat_id = 3")->fetch()['count'];
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
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

.tabs-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.tab-item {
    padding: 15px 20px;
    cursor: pointer;
    border-radius: 6px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
    font-weight: 500;
    color: #555;
}

.tab-item:hover {
    background: #f0f0f0;
}

.tab-item.active {
    background: #667eea;
    color: white;
}

.content-wrapper {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 30px;
    max-height: 500px; /* Set fixed height */
    overflow: hidden;
}

.content-panel {
    display: none;
    max-height: 440px; /* Subtract padding from wrapper height */
    overflow-y: auto;
    overflow-x: hidden;
    padding-right: 15px;
}

.content-panel.active {
    display: block;
}

/* Custom Scrollbar */
.content-panel::-webkit-scrollbar {
    width: 10px;
}

.content-panel::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.content-panel::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.content-panel::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Content Styling */
.content-text {
    line-height: 1.8;
    color: #333;
    font-size: 15px;
}

.content-text h2 {
    font-size: 22px;
    color: #667eea;
    margin-bottom: 20px;
    margin-top: 0;
}

.content-text h3 {
    font-size: 16px;
    color: #333;
    margin-top: 25px;
    margin-bottom: 12px;
    font-weight: 600;
}

.content-text p {
    margin-bottom: 15px;
    text-align: justify;
}

.content-text ul {
    margin: 10px 0 20px 20px;
    padding: 0;
}

.content-text li {
    margin-bottom: 8px;
    line-height: 1.6;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .about-container {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .content-wrapper {
        max-height: 400px;
    }
    
    .content-panel {
        max-height: 340px;
    }
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

    .scroll-top{position:fixed;bottom:30px;right:30px;width:50px;height:50px;background:linear-gradient(135deg,#d4af37 0%,#c9a961 100%);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 4px 15px rgba(212,175,55,0.4);transition:all .3s;z-index:100;color:white}
    .scroll-top:hover{background:linear-gradient(135deg,#c9a961 0%,#b8941f 100%);transform:translateY(-5px);box-shadow:0 6px 20px rgba(212,175,55,0.5)}
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
                    <li class="tab-item active" data-content="company">About Us</li>
                    <li class="tab-item" data-content="terms">Terms & Condition</li>
                    <li class="tab-item" data-content="specialty">Our Specialty</li>
                    <li class="tab-item" data-content="services">Our Services</li>
                </ul>
            </div>
            <div class="content-wrapper">
                <div id="company" class="content-panel active">
                    <div class="content-text">
                        <p>Casa Villagracia Boarding House offers a safe, organized, and comfortable living environment for students, employees, and individuals seeking an affordable and convenient place to stay. Located in Almers, Tabok, Mandaue City, the boarding house is easily accessible to public transportation, nearby schools, commercial establishments, and essential services—making it an ideal home for residents who value comfort, accessibility, and a peaceful living setup.</p>
                        
                        <p>Established to provide secure, quality, and budget-friendly accommodation, Casa Villagracia is committed to maintaining a clean and peaceful environment supported by proper facility upkeep and a respectful community culture. To enhance tenant convenience, the boarding house integrates a modern digital management system that streamlines bookings, payments, tenant records, and maintenance requests.</p>
                        
                        <p>At Casa Villagracia, your comfort, safety, and well-being are our top priorities.</p>
                    </div>
                </div>
                
                <div id="terms" class="content-panel">
                    <div class="content-text">
                        <p>To ensure a safe, orderly, and harmonious living environment, all tenants must abide by the following policies:</p>
                        
                        <h3>1. REGISTRATION & MOVE-IN</h3>
                        <ul>
                            <li>All tenants must complete the Tenant Reservation Form and present at least one valid ID in person.</li>
                            <li>A security deposit and one (1) month advance rent are required before move-in.</li>
                            <li>Only registered/approved tenants may occupy the room; no unauthorized overnight guests.</li>
                        </ul>

                        <h3>2. PAYMENT TERMS</h3>
                        <ul>
                            <li>Rent must be paid every 1st–5th day of each month.</li>
                            <li>Late payments are subject to applicable late fees.</li>
                            <li>Tenants must request an official receipt for every payment.</li>
                            <li>Utility bills must be paid within 3 days after issuance.</li>
                        </ul>

                        <h3>3. VISITOR POLICY</h3>
                        <ul>
                            <li>Visitors are allowed only from 8:00 AM to 10:00 PM.</li>
                            <li>All visitors must log in the visitor's logbook.</li>
                            <li>Overnight stays are strictly prohibited unless approved for emergencies.</li>
                        </ul>

                        <h3>4. CLEANLINESS & SANITATION</h3>
                        <ul>
                            <li>Tenants must maintain the cleanliness and orderliness of their rooms.</li>
                            <li>Cooking is allowed only in designated areas.</li>
                            <li>Proper waste disposal must be observed at all times.</li>
                            <li>Participation in scheduled general cleaning is required.</li>
                        </ul>

                        <h3>5. ELECTRICAL APPLIANCES</h3>
                        <ul>
                            <li>Only allowed appliances may be used inside rooms, such as electric fans, laptops, and lights.</li>
                            <li>High-power appliances (induction cookers, heaters, microwaves, etc.) are not allowed inside rooms.</li>
                            <li>Tenants must turn off all appliances when leaving.</li>
                        </ul>

                        <h3>6. SAFETY & SECURITY</h3>
                        <ul>
                            <li>Gates must remain closed and locked for everyone's safety.</li>
                            <li>CCTV cameras must not be tampered with or obstructed.</li>
                            <li>No illegal drugs within the premises.</li>
                            <li>Lost keys will require a replacement fee.</li>
                        </ul>

                        <h3>7. DAMAGE & LIABILITY</h3>
                        <ul>
                            <li>Tenants will be charged for any damage caused by misuse or negligence.</li>
                            <li>Management is not liable for lost or stolen personal items—tenants must secure their belongings.</li>
                            <li>Facility issues must be reported immediately to landlords.</li>
                        </ul>

                        <h3>8. CONDUCT & BEHAVIOR</h3>
                        <ul>
                            <li>Loud noise and disturbances are not allowed, especially after 10:00 PM.</li>
                            <li>Violence, harassment, or disrespectful behavior is strictly prohibited.</li>
                            <li>Tenants must maintain harmonious relationships with co-tenants and staff.</li>
                        </ul>

                        <h3>9. CHECK-OUT & TERMINATION</h3>
                        <ul>
                            <li>A 15–30 day notice is required before moving out.</li>
                            <li>All unpaid balances must be cleared before check-out.</li>
                            <li>Deposits are refundable after room inspection and deductions for damages (if any).</li>
                            <li>Management reserves the right to terminate tenancy for repeated violations.</li>
                        </ul>

                        <h3>10. MANAGEMENT RIGHTS</h3>
                        <p><strong>Management may:</strong></p>
                        <ul>
                            <li>Conduct room inspections with prior notice.</li>
                            <li>Modify or update rules and regulations as needed.</li>
                            <li>Approve or deny tenant applications based on compliance.</li>
                        </ul>
                    </div>
                </div>
                
                <div id="specialty" class="content-panel">
                    <div class="content-text">
                        <p>Casa Villagracia Boarding House stands out because of:</p>
                        <h3>Student and Worker-Friendly Environment</h3>
                        <p>Quiet, organized, and designed to support focus and productivity.</p>

                        <h3>Strong Safety Measures</h3>
                        <p>CCTV surveillance, secure gate access, and responsible management.</p>

                        <h3>Clean and Well-Maintained Facilities</h3>
                        <p>Regular maintenance ensures a pleasant living environment.</p>

                        <h3>Digital Management System</h3>
                        <p>Streamlined handling of:</p>
                        <ul>
                            <li>Rent payments</li>
                            <li>Room reservations</li>
                            <li>Tenant profiles</li>
                            <li>Announcements</li>
                        </ul>

                        <h3>Affordable Quality Accommodation</h3>
                        <p>Ideal for those seeking budget-friendly yet secure and comfortable housing.</p>
                    </div>
                </div>
                
                <div id="services" class="content-panel">
                    <div class="content-text">
                        <h3>SERVICES OFFERED</h3>

                        <h3>1. Room Accommodation Options</h3>
                        <ul>
                            <li>Single occupancy rooms</li>
                            <li>Shared rooms</li>
                            <li>Upgraded rooms (with optional private amenities)</li>
                        </ul>

                        <h3>2. Utilities & Amenities</h3>
                        <ul>
                            <li>WiFi</li>
                            <li>Electricity & water (metered or fixed depending on arrangement)</li>
                            <li>Shared kitchen & dining area</li>
                            <li>Clean common comfort rooms</li>
                            <li>Laundry & drying area</li>
                        </ul>

                        <h3>3. Security & Maintenance</h3>
                        <ul>
                            <li>24/7 CCTV monitoring</li>
                            <li>Regular cleaning of common areas</li>
                            <li>Secure gate and visitor monitoring</li>
                        </ul>

                        <h3>4. Tenant Support</h3>
                        <ul>
                            <li>Maintenance report</li>
                            <li>Digital announcements</li>
                            <li>Responsive management through SMS, call, or online portal</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="facts-section">
    <div class="container">
        <div class="facts-grid">
            <div class="fact-card">
                <h3 class="fact-number"><?php echo $happyTenants; ?>+</h3>
                <p class="fact-label">Happy Tenants</p>
            </div>
            <div class="fact-card">
                <h3 class="fact-number"><?php echo $roomsAvailable; ?></h3>
                <p class="fact-label">Rooms Available</p>
            </div>
            <div class="fact-card">
                <h3 class="fact-number">13</h3>
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
                    <span>Almers Compound, Tabok, Mandaue City, Cebu</span>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <span>villagraciajanray@gmail.com</span>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </span>
                    <span>+63 930 913 2995</span>
                </div>
                <div class="contact-item">
                    <span class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </span>
                    <span>+63 922 345 6458</span>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="scroll-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
    <i class="fas fa-chevron-up"></i>
</div>

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

document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-item');
    const panels = document.querySelectorAll('.content-panel');
    const contentWrapper = document.querySelector('.content-wrapper');
    
    // Tab switching functionality
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs and panels
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show corresponding panel
            const contentId = this.getAttribute('data-content');
            const activePanel = document.getElementById(contentId);
            activePanel.classList.add('active');
            
            // Check if panel needs scroll indicator after a short delay
            setTimeout(() => {
                checkScrollable(activePanel);
            }, 50);
        });
    });
    
    // Check if content is scrollable
    function checkScrollable(panel) {
        if (panel.scrollHeight > panel.clientHeight) {
            contentWrapper.classList.add('has-scroll');
        } else {
            contentWrapper.classList.remove('has-scroll');
        }
    }
    
    // Check initial active panel
    const activePanel = document.querySelector('.content-panel.active');
    if (activePanel) {
        setTimeout(() => {
            checkScrollable(activePanel);
        }, 50);
    }
    
    // Update scroll indicator when scrolling
    panels.forEach(panel => {
        panel.addEventListener('scroll', function() {
            // Hide indicator when scrolled near bottom
            const isNearBottom = this.scrollTop + this.clientHeight >= this.scrollHeight - 10;
            if (isNearBottom) {
                contentWrapper.classList.remove('has-scroll');
            } else {
                checkScrollable(this);
            }
        });
    });
    
    // Recalculate on window resize
    window.addEventListener('resize', function() {
        const activePanel = document.querySelector('.content-panel.active');
        if (activePanel) {
            checkScrollable(activePanel);
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>