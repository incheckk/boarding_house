<?php
$pageTitle = "Contact Us";
require_once __DIR__ . '/../includes/header.php';

$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $msg = $_POST['message'];
    $email = $_POST['email'];

    mail(
        "admin@example.com",
        "Contact Form Message",
        "From: $name ($email)\n\nMessage:\n$msg"
    );

    $success = "Message sent!";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Villagracia Boarding House</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <!-- About Hero Section -->
    <section class="about-hero">
        <h1>Contact</h1>
        <p>Contact with us</p>
    </section>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="index.php">Home</a>
        <span>></span>
        <span>Contact</span>
    </div>

    <!-- Map Section -->
    <div class="map-section">
        <h2>Find Our location</h2>
        <h3>Map & Directions</h3>
        <p>Find out how to find us from your current location</p>

        <div class="map-container">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3924.961485051478!2d123.9399084!3d10.3449648!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a9998802cdc303%3A0x922391774c67c57d!2sVillagracia%20Boarding%20House!5e0!3m2!1sen!2sph!4v1759675593308!5m2!1sen!2sph"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div><!-- /.map-content -->
    </div>
    </div>

    <!-- Contact Section -->
    <div class="contact-section">
        <h2 class="contact-title">Contact us live</h2>

        <div class="contact-container">
            <!-- Contact Info -->
            <div class="contact-info">
                <div class="contact-info-item">
                    <h3><i class="fas fa-map-marker-alt"></i> Address</h3>
                    <p>Villagracia's Boarding House</p>
                </div>

                <div class="contact-info-item">
                    <h3><i class="fas fa-envelope"></i> Mail</h3>
                    <p>villagracia@gmail.com</p>
                </div>

                <div class="contact-info-item">
                    <h3><i class="fas fa-phone"></i> Call</h3>
                    <p>63+ 9309132995<br>666 35874692050</p>
                </div>

                <div class="contact-info-item">
                    <h3><i class="fas fa-user"></i> Social account</h3>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-wrapper">
                <div class="form-header">
                    <i class="fas fa-envelope-open-text"></i>
                    <h3>Send Us A email</h3>
                </div>

                <form>
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" placeholder="Name*" required>
                        </div>
                        <div class="form-group">
                            <input type="email" placeholder="Email*" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <textarea placeholder="your message" required></textarea>
                    </div>

                    <button type="submit" class="submit-btn">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <div class="footer-section">
        <div class="footer-container">
            <!-- About -->
            <div class="footer-box">
                <h3>About</h3>
                <div class="logo">
                    <i class="fas fa-home"></i>
                    <span>House Rent</span>
                </div>
                <p>We Provide Premium Word Press, Ghost and HTML template. Our Perm tritium Templates is, develop gapped
                    in a way so that the clients find Support. Themes are developed in a way so that the clients find.
                </p>
                <a href="#" class="footer-btn">More</a>
            </div>

            <!-- Book Now -->
            <div class="footer-box">
                <h3>Book Now</h3>
                <div class="logo">
                    <i class="fas fa-home"></i>
                    <span>House Rent</span>
                </div>
                <p>We Provide Premium Word Press, Ghost and HTML template. Our Perm tritium Templates is, develop gapped
                    in a way so that the clients find Support. Themes are developed in a way so that the clients find.
                </p>
                <a href="#" class="footer-btn">Book Now</a>
            </div>

            <!-- Instagram -->
            <div class="footer-box">
                <h3>Instagram Image</h3>
                <div class="instagram-grid">
                    <div class="instagram-item">118x114</div>
                    <div class="instagram-item">118x114</div>
                    <div class="instagram-item">120x117</div>
                    <div class="instagram-item">120x116</div>
                    <div class="instagram-item">120x116</div>
                    <div class="instagram-item">120x116</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>