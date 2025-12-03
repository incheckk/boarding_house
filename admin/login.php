<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | CASA VILLAGRACIA</title>

    <!-- Font Awesome + Google Fonts (same as admin panel) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .login-container {
            background: #fff;
            color: #1d1d1f;
            width: 100%;
            max-width: 440px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
            animation: fadeInUp 0.9s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Matching Header Logo Style */
        .login-logo {
            background: #1a1a1a;
            padding: 35px 40px;
            text-align: center;
            border-bottom: 3px solid #ff9500;
        }

        .login-logo img {
            width: 90px;
            height: auto;
            margin-bottom: 12px;
            filter: brightness(0) invert(1); /* Makes logo white if it's colored */
        }

        .login-logo a {
            color: #fff;
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: 1px;
            display: block;
        }

        .login-logo::after {
            content: '';
            display: block;
            width: 80px;
            height: 3px;
            background: #ff9500;
            margin: 18px auto 0;
            border-radius: 3px;
        }

        .login-body {
            padding: 45px 40px;
            background: #fff;
        }

        .login-body h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 26px;
            font-weight: 600;
            color: #1d1d1f;
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 16px 18px;
            border: 1px solid #d1d1d6;
            border-radius: 14px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #ff9500;
            box-shadow: 0 0 0 4px rgba(255, 149, 0, 0.15);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: #ff9500;
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            background: #e68600;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 149, 0, 0.3);
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 5px solid #ff3b30;
            text-align: center;
        }

        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 5px solid #4caf50;
            text-align: center;
        }

        .footer-text {
            text-align: center;
            margin-top: 30px;
            color: #888;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Matching Header Logo -->
    <div class="login-logo">
        <img src="images/c1.png" alt="Casa Villagracia Logo">
        <a href="#">CASA VILLAGRACIA</a>
    </div>

    <div class="login-body">
        <h2>Admin Login</h2>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                Invalid username or password. Please try again.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['logout'])): ?>
            <div class="success-message">
                You have been logged out successfully.
            </div>
        <?php endif; ?>

        <form action="php/auth.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Enter your username" autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>

            <button type="submit" class="btn-login">
                Login
            </button>
        </form>

        <div class="footer-text">
            © 2025 CASA VILLAGRACIA     • All rights reserved
        </div>
    </div>
</div>

</body>
</html>