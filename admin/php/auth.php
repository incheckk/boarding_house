<?php
session_start();
require_once __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        header('Location: ../login.php?error=1');
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT user_id, username, password_hash, role, full_name FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Success! Set session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'] ?? $user['username'];

            // Redirect to dashboard
            header('Location: ../admin.php');
            exit();
        } else {
            // Invalid credentials
            header('Location: ../login.php?error=1');
            exit();
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        header('Location: ../login.php?error=1');
        exit();
    }
} else {
    // Direct access
    header('Location: ../login.php');
    exit();
}