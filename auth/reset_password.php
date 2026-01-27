<?php
require '../includes/db.php';
session_start();

if (!isset($_GET['token'])) { die("Access Denied."); }
$token = $_GET['token'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) { die("Invalid or expired link."); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
    $stmt->execute([$new_pass, $token]);

    header("Location: login.php?msg=Success! Use your new password.");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="auth-body">
    <div class="auth-card">
        <h2>New Password</h2>
        <form method="POST">
            <input type="password" name="password" placeholder="New Password" required>
            <button type="submit">Update Password</button>
        </form>
    </div>
</body>
</html>