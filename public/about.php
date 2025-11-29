<?php
$pageTitle = "About Us";
require_once __DIR__ . '/../includes/header.php';
$profile_image = "malupiton.jpg"
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - House Rent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            background: linear-gradient(180deg, #fdfbf7 0%, #f5f0e8 50%, #fdfbf7 100%);
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

        .about-hero {
            background: linear-gradient(135deg, #d4af37 0%, #f4e5c0 25%, #c9a961 50%, #f4e5c0 75%, #d4af37 100%);
            padding: 60px 20px 40px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .about-hero::before {
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

        .about-hero h1 {
            font-size: 3.5rem;
            margin-bottom: 5px;
            font-weight: 700;
            position: relative;
            z-index: 2;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .about-hero p {
            font-size: 1.1rem;
            color: #2d2d2d;
            font-weight: 600;
            position: relative;
            z-index: 2;
        }

        .breadcrumb {
            padding: 20px;
            background: rgba(248, 249, 250, 0.8);
            position: relative;
            z-index: 1;
        }

        .breadcrumb a {
            color: #c9a961;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .breadcrumb a:hover {
            color: #d4af37;
        }

        .breadcrumb span {
            color: #9ca3af;
            margin: 0 8px;
        }

        .why-choose {
            padding: 80px 20px;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            background: linear-gradient(135deg, #ffffff 0%, #fdfbf7 100%);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(212, 175, 55, 0.15);
            z-index: 1;
        }

        .why-choose::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle, rgba(212, 175, 55, 0.08) 1px, transparent 1px),
                radial-gradient(circle, rgba(201, 169, 97, 0.06) 1px, transparent 1px);
            background-size: 50px 50px, 80px 80px;
            background-position: 0 0, 40px 40px;
            border-radius: 20px;
            pointer-events: none;
        }

        .why-choose h2 {
            text-align: center;
            font-size: 3rem;
            background: linear-gradient(135deg, #d4af37 0%, #c9a961 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
            font-weight: 800;
        }

        .why-choose-subtitle {
            text-align: center;
            color: #94a3b8;
            margin-bottom: 60px;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }

        .why-choose-subtitle::after {
            content: '';
            display: block;
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #d4af37, #c9a961);
            margin: 20px auto 0;
            border-radius: 2px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .content-grid::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 2px;
            height: 80%;
            background: linear-gradient(180deg, transparent, rgba(212, 175, 55, 0.3), transparent);
        }

        .content-left {
            position: relative;
            padding: 30px;
        }

        .content-left::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #d4af37, #c9a961);
            border-radius: 3px;
        }

        .content-left h3 {
            font-size: 3.2rem;
            line-height: 1.2;
            margin-bottom: 20px;
            font-weight: 800;
        }

        .content-left h3 .best {
            background: linear-gradient(135deg, #d4af37 0%, #f4e5c0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .content-left h3 .enjoy {
            background: linear-gradient(135deg, #c9a961 0%, #d4af37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .content-right {
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.15);
            position: relative;
            overflow: hidden;
        }

        .content-right::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #d4af37, #f4e5c0, #c9a961);
        }

        .content-right p {
            color: #64748b;
            line-height: 1.8;
            font-size: 1rem;
        }

        .profile-section {
            background: rgba(248, 249, 250, 0.8);
            padding: 80px 20px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .profile-image {
            width: 223px;
            height: 223px;
            border-radius: 50%;
            background: #f4e5c0;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 4px solid #d4af37;
            overflow: hidden;
            position: relative;
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
        }

        .profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .profile-name {
            font-size: 2rem;
            background: linear-gradient(135deg, #d4af37 0%, #c9a961 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            font-weight: 700;
        }

        .profile-name::before,
        .profile-name::after {
            content: "◆";
            color: #d4af37;
            font-size: 1rem;
        }

        .profile-title {
            color: #9ca3af;
            margin-bottom: 30px;
        }

        .profile-bio {
            max-width: 900px;
            margin: 0 auto 40px;
            color: #6b7280;
            line-height: 1.8;
        }

        .social-links {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 900px;
            margin: 0 auto;
        }

        .social-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .social-left span {
            font-weight: 500;
            color: #333;
        }

        .social-icons {
            display: flex;
            gap: 15px;
        }

        .social-icons a {
            color: #c9a961;
            font-size: 1.2rem;
            transition: color 0.3s;
        }

        .social-icons a:hover {
            color: #d4af37;
        }

        .signature {
            font-family: 'Brush Script MT', cursive;
            font-size: 2rem;
            background: linear-gradient(135deg, #d4af37 0%, #c9a961 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #d4af37 0%, #c9a961 100%);
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
            transition: all 0.3s;
            z-index: 100;
            color: white;
        }

        .scroll-top:hover {
            background: linear-gradient(135deg, #c9a961 0%, #b8941f 100%);
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.5);
        }

        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }

            .content-grid::before {
                display: none;
            }

            .about-hero h1 {
                font-size: 2.5rem;
            }

            .content-left h3 {
                font-size: 2rem;
            }

            .why-choose {
                margin: 20px;
            }

            .why-choose h2 {
                font-size: 2.2rem;
            }
        }
    </style>
</head>

<body>
    <!-- About Hero Section -->
    <section class="about-hero">
        <h1>About</h1>
        <p>About our company</p>
    </section>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="index.php">Home</a>
        <span>›</span>
        <span>About</span>
    </div>

    <!-- Why Choose Us Section -->
    <section class="why-choose">
        <h2><span class="why">Why</span><br>Choose Us</h2>
        <p class="why-choose-subtitle">Best offers from the house chef</p>

        <div class="content-grid">
            <div class="content-left">
                <h3>
                    <span class="best">Best<br>Rent Service,</span><br>
                    <span class="enjoy">enjoy your<br>life</span>
                </h3>
            </div>
            <div class="content-right">
                <p>
                    Sed pellentesque pulvinar arcu ac congue. Sed sed est nec justo maximus blandit. Curabitur lacinia,
                    eros sit amet maximus suscipit, magna sapien veneuynatlis eros, et gravida urna massa ut lectus.
                    Quisque lacinia laciunia viverra. Nullram nec est et lorem sodales ornare a in sapien. In trtset
                    urna maximus, conse ctetur iligula in, gravida erat. Nullam dignifssrim hendrerit auctor. Sed
                    varius, dolor vitae iaculis condim rtweentum, massa nisl cursus sapien, gravida ultrices nisl dolor
                    non erat.
                </p>
            </div>
        </div>
    </section>

    <!-- Profile Section -->
    <section class="profile-section">
        <div class="profile-image">
            <img src="assets/images/malupiton.jpg" alt="Mr. Villagracia">
        </div>
        <h2 class="profile-name">Mr. Villagracia</h2>
        <p class="profile-title">Rent House Admin</p>
        <p class="profile-bio">
            Cras et mauris eget lorem ultricies fermentum a in diam. Morbi mollis pesilentesque aug ue nec rhoncus. Nam
            ut ogrci augue. Phasellus ac venenatis orci. Nulalam iaculis lao reet maa, vitae tempus ante tincidunte et.
            dolor st ametisnj, consectetur adipiscing elit. Cras vitale nbh nisl. Cras et mauis eget loremams ultricies
            ferme ntum a in diam.Nam ut orci augue. Pha sellus ac venen adatis orci. Nullam iaculis lao reetings mag,
            vitae tempus ante tincidunte et.
        </p>
        <div class="social-links">
            <div class="social-left">
                <span>Follow me :</span>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-facebook"></i></a>
                </div>
            </div>
            <div class="signature">VILLAGRACIA</div>
        </div>
    </section>

    <!-- Scroll to Top Button -->
    <div class="scroll-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-chevron-up"></i>
    </div>
</body>

</html>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>