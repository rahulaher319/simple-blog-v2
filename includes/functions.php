<?php

session_start();

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function requireVerification() {
    if (isset($_SESSION['is_verified']) && $_SESSION['is_verified'] == 0) {
        header("Location: verify.php");
        exit();
    }
}

function e($data) {
    return htmlspecialchars($birthdate ?? $data, ENT_QUOTES, 'UTF-8');
}
?>