<?php
$currentPage = basename($_SERVER['PHP_SELF']);
if (!isset($pageTitle)) {
    $pageTitle = "CASA VILLAGRACIA";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        /* RESET */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* HEADER */
        .main-header {
            width: 100%;
            background: black;
            padding: 14px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        /* LOGO */
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            width: 45px;
            height: auto;
        }

        .logo a {
            color: white;
            font-size: 20px;
            text-decoration: none;
            font-weight: 600;
        }

        /* NAV MENU */
        .nav-menu {
            display: flex;
            gap: 30px;
            /* spacing between links */
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 6px;
            transition: background 0.3s, color 0.3s, transform 0.2s;
        }

        .nav-menu a:hover {
            background: #d4af37;
            /* yellow hover */
            color: black;
            transform: translateY(-2px);
        }

        .nav-menu a.active {
            background: #f4e34a;
            /* bright yellow active */
            color: black;
        }

        /* HAMBURGER */
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: white;
            transition: 0.3s;
        }

        /* MOBILE */
        @media (max-width: 768px) {
            .nav-menu {
                position: fixed;
                top: 70px;
                right: -100%;
                height: 100vh;
                width: 220px;
                background: black;
                flex-direction: column;
                padding: 30px 20px;
                gap: 25px;
                /* spacing in mobile */
                transition: right 0.3s ease;
            }

            .nav-menu.nav-open {
                right: 0;
            }

            .hamburger {
                display: flex;
            }
        }

        /* HAMBURGER ANIMATION */
        .hamburger.active span:nth-child(1) {
            transform: translateY(8px) rotate(45deg);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: translateY(-8px) rotate(-45deg);
        }
    </style>
</head>

<body>

    <header class="main-header">
        <div class="logo">
            <img src="./assets/images/c.png" alt="Logo">
            <a href="index.php">CASA VILLAGRACIA</a>
        </div>

        <div class="hamburger" id="hamburgerBtn">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <nav id="navMenu" class="nav-menu">
            <a href="index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">Home</a>
            <a href="rooms.php" class="<?= $currentPage == 'rooms.php' ? 'active' : '' ?>">Rooms</a>
            <a href="about.php" class="<?= $currentPage == 'about.php' ? 'active' : '' ?>">About</a>
            <a href="contact.php" class="<?= $currentPage == 'contact.php' ? 'active' : '' ?>">Contact</a>
        </nav>
    </header>

    <script>
        // Hamburger toggle
        document.getElementById("hamburgerBtn").addEventListener("click", function () {
            const nav = document.getElementById("navMenu");
            nav.classList.toggle("nav-open");
            this.classList.toggle("active");
        });

        // Scroll effect (optional)
        window.addEventListener("scroll", () => {
            const header = document.querySelector(".main-header");
            if (window.scrollY > 10) {
                header.classList.add("scrolled");
            } else {
                header.classList.remove("scrolled");
            }
        });
    </script>

    <main class="content">
        <!-- Page content goes here -->
    </main>

</body>

</html>