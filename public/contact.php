<?php
$pageTitle = "Contact Us";
require_once __DIR__ . '/../includes/header.php';

$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $msg  = $_POST['message'];
    $email = $_POST['email'];

    mail("admin@example.com", "Contact Form Message",
        "From: $name ($email)\n\nMessage:\n$msg");

    $success = "Message sent!";
}
?>

<h1>Contact Us</h1>

<?php if ($success): ?>
<p class="success"><?= $success ?></p>
<?php endif; ?>

<form method="POST" class="form-box">
    <label>Your Name</label>
    <input type="text" name="name" required>

    <label>Your Email</label>
    <input type="email" name="email" required>

    <label>Message</label>
    <textarea name="message" required></textarea>

    <button type="submit">Send</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
