<?php
$currentPage = basename($_SERVER['PHP_SELF']); // e.g., index.php
if (!isset($pageTitle)) { $pageTitle = "CASA VILLAGRACIA"; }
require_once __DIR__ . '/../includes/functions.php';  // Adjust path if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/rooms.css">
    <link rel="stylesheet" href="assets/css/room.css">
    <link rel="stylesheet" href="assets/css/reservation.css">
    <link rel="stylesheet" href="assets\css\about.css">
    <link rel="stylesheet" href="assets\css\contact.css">
    <script src="assets/js/rooms-filter.js"></script>
    <script src="assets/js/about-us.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<header class="main-header">
    <div class="logo">
        <img src="./assets/images/c.png"  alt="Logo">
        <a href="index.php">CASA VILLAGRACIA</a>
    </div>

    <div class="hamburger" onclick="toggleMenu()">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <nav class="nav-menu" id="navMenu">
        <a href="index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">Home</a>
        <a href="rooms.php" class="<?= $currentPage == 'rooms.php' ? 'active' : '' ?>">Rooms</a>
        <a href="about.php" class="<?= $currentPage == 'about.php' ? 'active' : '' ?>">About</a>
        <a href="blog.php" class="<?= $currentPage == 'blog.php' ? 'active' : '' ?>">Blog</a>
        <a href="contact.php" class="<?= $currentPage == 'contact.php' ? 'active' : '' ?>">Contact</a>
    </nav>
</header>

<script>
function toggleMenu() {
    document.getElementById("navMenu").classList.toggle("nav-open");
}

function toggleMenu() {
    const menu = document.getElementById("navMenu");
    const hamburger = document.querySelector(".hamburger");
    menu.classList.toggle("nav-open");
    hamburger.classList.toggle("active");
}

const header = document.querySelector('.main-header');
window.addEventListener('scroll', () => {
    if (window.scrollY > 10) {
        document.body.classList.add('scrolled');
    } else {
        document.body.classList.remove('scrolled');
    }
});
</script>

<main class="content">
