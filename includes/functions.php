<?php
// Start or resume the session to access global user data
session_start();

/**
 * Gatekeeper: Ensures the user is logged in before accessing a page
 * Redirects to login.php if session 'user_id' is missing
 */
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

/**
 * Security Layer: Ensures the user has verified their email via OTP
 * Redirects to verify.php if 'is_verified' status is 0
 */
function requireVerification() {
    if (isset($_SESSION['is_verified']) && $_SESSION['is_verified'] == 0) {
        header("Location: verify.php");
        exit();
    }
}

/**
 * XSS Protection: Sanitizes user-generated content for safe HTML output
 * Converts special characters into HTML entities using UTF-8
 */
function e($data) {
    // Note: the ?? $data ensures it falls back to input if a specific variable is missing
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>