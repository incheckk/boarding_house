<?php 
if (!isset($pageTitle)) { 
    $pageTitle = "Admin Panel"; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- Fixed CSS path: Now relative to /admin/ (where admin.php is) -->
    <link rel="stylesheet" href="css/admin_style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<header class="admin-header">
    <div class="admin-logo">
        <!-- Fixed image path: Now relative to /admin/ -->
        <img src="images/c1.png" alt="Logo">
        <a>CASA VILLAGRACIA</a>
    </div>

    <div class="admin-actions">
        <!-- Fixed logout path: Assuming logout.php is in root (/), this goes up one level from /admin/ -->
        <a href="../logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</header>

<main class="admin-content">