<?php
$pageTitle = "Contact Us";
require_once __DIR__ . '/../includes/header.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$success = "";
$error   = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Load PHPMailer from your exact path
    require_once __DIR__ . '/../public/PHPMailer-master/src/Exception.php';
    require_once __DIR__ . '/../public/PHPMailer-master/src/PHPMailer.php';
    require_once __DIR__ . '/../public/PHPMailer-master/src/SMTP.php';

    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $mail = new PHPMailer(true);

        try {
            // === GMAIL SMTP SETTINGS (WORKS 100% ON XAMPP 2025) ===
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'martinez.andrelorenz@gmail.com';           // Your Gmail
            $mail->Password   = 'xbix aisi alfz htsy';      // ← CHANGE ONLY THIS LINE
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Fix XAMPP localhost SSL issues
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Email details
            $mail->setFrom('martinez.andrelorenz@gmail.com', 'Villagracia Contact Form');
            $mail->addAddress('martinez.andrelorenz@gmail.com');   // You receive the email
            $mail->addReplyTo($email, $name);

            $mail->isHTML(false);
            $mail->Subject = 'New Message from Contact Form - Villagracia Boarding House';
            $mail->Body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";

            $mail->send();
            $success = "Message sent successfully! Thank you.";
        } catch (Exception $e) {
            $error = "Message could not be sent. Error: " . $mail->ErrorInfo;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Villagracia Boarding House</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;color:#333;line-height:1.6;background:linear-gradient(180deg,#fdfbf7 0%,#f5f0e8 50%,#fdfbf7 100%);position:relative}
        body::before{content:'';position:fixed;top:0;left:0;right:0;bottom:0;background-image:radial-gradient(circle,rgba(212,175,55,0.08) 1px,transparent 1px),radial-gradient(circle,rgba(218,165,32,0.06) 1.5px,transparent 1.5px);background-size:60px 60px,100px 100px;background-position:0 0,30px 30px;pointer-events:none;z-index:0}
        .about-hero{background:linear-gradient(135deg,#d4af37 0%,#f4e5c0 25%,#c9a961 50%,#f4e5c0 75%,#d4af37 100%);padding:60px 20px 40px;text-align:center;color:white;position:relative;overflow:hidden}
        .about-hero::before{content:'';position:absolute;top:0;left:0;right:0;bottom:0;background-image:radial-gradient(circle,rgba(255,255,255,0.3) 2px,transparent 2px),radial-gradient(circle,rgba(255,215,0,0.4) 1.5px,transparent 1.5px),radial-gradient(circle,rgba(139,69,19,0.2) 1px,transparent 1px);background-size:80px 80px,120px 120px,50px 50px;background-position:0 0,40px 40px,20px 20px;animation:shimmer 20s ease-in-out infinite}
        @keyframes shimmer{0%,100%{opacity:1;transform:translateY(0)}50%{opacity:0.7;transform:translateY(-10px)}}
        .about-hero h1{font-size:3.5rem;margin-bottom:5px;font-weight:700;position:relative;z-index:2;text-shadow:2px 2px 4px rgba(0,0,0,0.3)}
        .about-hero p{font-size:1.1rem;color:#2d2d2d;font-weight:600;position:relative;z-index:2}
        .breadcrumbs-area{background:#ffffff;padding:20px 0;border-bottom:1px solid rgba(212,175,55,0.15)}
        .breadcrumbs{display:flex;align-items:center;justify-content:flex-start;gap:10px;font-size:0.95rem;color:#666;max-width:1200px;margin:0 auto;padding:0 20px}
        .breadcrumbs a{color:#d4af37;text-decoration:none;transition:all .3s cubic-bezier(.4,0,.2,1);font-weight:500}
        .breadcrumbs a:hover{color:#c9a961}
        .breadcrumbs .separator{color:#e0e0e0;font-weight:300}
        .breadcrumbs .last-item{color:#2d2d2d;font-weight:500}
        .map-section{padding:80px 20px;max-width:1200px;margin:0 auto;text-align:center;position:relative;z-index:1}
        .map-section h2{font-size:2.5rem;background:linear-gradient(135deg,#d4af37 0%,#c9a961 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:10px;font-weight:700}
        .map-section h3{font-size:1.8rem;color:#64748b;margin-bottom:10px;font-weight:600}
        .map-section p{color:#94a3b8;margin-bottom:40px;font-size:1.1rem}
        .map-container{width:100%;max-width:1000px;margin:0 auto;border-radius:15px;overflow:hidden;box-shadow:0 10px 40px rgba(212,175,55,0.2);border:3px solid #d4af37}
        .map-container iframe{width:100%;height:450px;display:block}
        .contact-section{padding:80px 20px;background:rgba(248,249,250,0.8);position:relative;z-index:1}
        .contact-title{text-align:center;font-size:2.5rem;background:linear-gradient(135deg,#d4af37 0%,#c9a961 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:50px;font-weight:700}
        .contact-container{max-width:1200px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:start}
        .contact-info{display:flex;flex-direction:column;gap:30px}
        .contact-info-item{background:white;padding:30px;border-radius:12px;box-shadow:0 4px 20px rgba(212,175,55,0.15);transition:all .3s;border:2px solid rgba(212,175,55,0.1)}
        .contact-info-item:hover{transform:translateY(-5px);box-shadow:0 8px 30px rgba(212,175,55,0.25);border-color:rgba(212,175,55,0.3)}
        .contact-info-item h3{font-size:1.3rem;margin-bottom:15px;color:#2d2d2d;display:flex;align-items:center;gap:12px}
        .contact-info-item h3 i{width:40px;height:40px;background:linear-gradient(145deg,#f4e5c0 0%,#d4af37 50%,#b8941f 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#000;font-size:18px;box-shadow:0 4px 15px rgba(212,175,55,0.3),inset 0 -2px 5px rgba(139,101,8,0.3),inset 0 2px 5px rgba(255,248,220,0.6);border:2px solid rgba(255,248,220,0.4)}
        .contact-info-item p{color:#64748b;font-size:1rem;line-height:1.8}
        .social-icons{display:flex;gap:15px;margin-top:10px}
        .social-icons a{width:40px;height:40px;background:linear-gradient(145deg,#f4e5c0 0%,#d4af37 50%,#b8941f 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#000;text-decoration:none;transition:all .3s;box-shadow:0 4px 15px rgba(212,175,55,0.3),inset 0 -2px 5px rgba(139,101,8,0.3),inset 0 2px 5px rgba(255,248,220,0.6);border:2px solid rgba(255,248,220,0.4)}
        .social-icons a:hover{transform:rotate(360deg) scale(1.1);box-shadow:0 6px 20px rgba(212,175,55,0.5),inset 0 -2px 5px rgba(139,101,8,0.4),inset 0 2px 8px rgba(255,248,220,0.8)}
        .scroll-top{position:fixed;bottom:30px;right:30px;width:50px;height:50px;background:linear-gradient(135deg,#d4af37 0%,#c9a961 100%);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 4px 15px rgba(212,175,55,0.4);transition:all .3s;z-index:100;color:white}
        .scroll-top:hover{background:linear-gradient(135deg,#c9a961 0%,#b8941f 100%);transform:translateY(-5px);box-shadow:0 6px 20px rgba(212,175,55,0.5)}
        .contact-form-wrapper{background:white;padding:40px;border-radius:15px;box-shadow:0 10px 40px rgba(212,175,55,0.15);border:2px solid rgba(212,175,55,0.1)}
        .form-header{text-align:center;margin-bottom:30px;padding-bottom:20px;border-bottom:2px solid rgba(212,175,55,0.2)}
        .form-header i{font-size:3rem;background:linear-gradient(135deg,#d4af37 0%,#c9a961 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:10px}
        .form-header h3{font-size:1.8rem;background:none;-webkit-background-clip:unset;-webkit-text-fill-color:initial;color:#000000;font-weight:700}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px}
        .form-group{margin-bottom:20px}
        .form-group input,.form-group textarea{width:100%;padding:15px;border:2px solid rgba(212,175,55,0.2);border-radius:8px;font-size:1rem;font-family:inherit;transition:all .3s;background:#fdfbf7}
        .form-group input:focus,.form-group textarea:focus{outline:none;border-color:#d4af37;background:white;box-shadow:0 0 0 3px rgba(212,175,55,0.1)}
        .form-group textarea{min-height:150px;resize:vertical}
        .submit-btn{width:100%;padding:15px;background:linear-gradient(135deg,#d4af37 0%,#c9a961 100%);color:white;border:none;border-radius:8px;font-size:1.1rem;font-weight:600;cursor:pointer;transition:all .3s;box-shadow:0 4px 15px rgba(212,175,55,0.3)}
        .submit-btn:hover{background:linear-gradient(135deg,#c9a961 0%,#b8941f 100%);transform:translateY(-2px);box-shadow:0 6px 20px rgba(212,175,55,0.4)}
        .success-msg{background:#d4edda;color:#155724;padding:15px;border-radius:8px;margin-bottom:20px;border:1px solid #c3e6cb}
        .error-msg{background:#f8d7da;color:#721c24;padding:15px;border-radius:8px;margin-bottom:20px;border:1px solid #f5c6cb}
        @media (max-width:968px){.contact-container{grid-template-columns:1fr;gap:40px}.map-container iframe{height:350px}.about-hero h1{font-size:2.5rem}.map-section h2,.contact-title{font-size:2rem}}
        @media (max-width:640px){.form-row{grid-template-columns:1fr}.about-hero h1{font-size:2rem}.map-container iframe{height:300px}.contact-form-wrapper{padding:25px}}
    </style>
</head>

<body>
    <section class="about-hero">
        <h1>Contact</h1>
        <p>Contact with us</p>
    </section>

    <div class="breadcrumbs-area">
        <div class="breadcrumbs">
            <span class="first-item"><a href="index.php">Home</a></span>
            <span class="separator">></span>
            <span class="last-item">Contact us</span>
        </div>
    </div>

    <div class="map-section">
        <h2>Find Our location</h2>
        <h3>Map & Directions</h3>
        <p>Find out how to find us from your current location</p>
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3924.961485051478!2d123.9399084!3d10.3449648!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a9998802cdc303%3A0x922391774c67c57d!2sVillagracia%20Boarding%20House!5e0!3m2!1sen!2sph!4v1759675593308!5m2!1sen!2sph" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>

    <div class="contact-section">
        <h2 class="contact-title">Contact us live</h2>
        <div class="contact-container">
            <div class="contact-info">
                <div class="contact-info-item">
                    <h3>Mail</h3>
                    <p>villagracia@gmail.com</p>
                </div>
                <div class="contact-info-item">
                    <h3>Call</h3>
                    <p>63+ 9309132995<br>666 35874692050</p>
                </div>
                <div class="contact-info-item">
                    <h3>Social account</h3>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>

            <div class="contact-form-wrapper">
                <div class="form-header">
                    <i class="fas fa-envelope-open-text"></i>
                    <h3>Send Us A email</h3>
                </div>

                <?php if ($success): ?>
                    <div class="success-msg"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Name*" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email*" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="your message" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <div class="scroll-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        <i class="fas fa-chevron-up"></i>
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>