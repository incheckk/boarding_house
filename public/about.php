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
    <link rel="stylesheet" href="about.css">
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