<?php
// includes/auth.php

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function current_user_role() {
    return $_SESSION['role'] ?? null;
}

function require_role($allowed = []) {
    if (!is_logged_in()) header('Location: /admin/login.php');
    if (!in_array($_SESSION['role'], (array)$allowed)) {
        http_response_code(403);
        echo "Forbidden";
        exit;
    }
}
