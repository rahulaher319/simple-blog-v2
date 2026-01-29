<?php
// Load database connection
require '../includes/db.php';
// Load helper functions which handles the centralized session_start()
require '../includes/functions.php'; 

// Security: Verify that a reset token exists in the URL; block access if missing
if (!isset($_GET['token'])) { 
    die("Access Denied."); 
}

$token = $_GET['token'];

// SQL Injection Protection: Use prepared statements to find user with a valid, non-expired token
$stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

// Validation: If no user is found or the token is expired, stop the process
if (!$user) { 
    die("Invalid or expired link."); 
}

// Process the form submission to update the password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Security: Securely hash the new password before saving it
    $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Update the user record: set the new password and clear the reset token/expiry for security
    $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
    $stmt->execute([$new_pass, $token]);

    // Redirect to login with a success message
    header("Location: login.php?msg=Success! Use your new password.");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | Social Blog</title>
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