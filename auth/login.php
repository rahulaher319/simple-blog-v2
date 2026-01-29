<?php
// Load database connection settings
require '../includes/db.php';

// Initialize session to manage user login state
session_start();

// Check if the form has been submitted via the POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture user input from the login form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // SQL Injection Protection: Use a prepared statement to find the user by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verify user exists and the provided password matches the secure hash in the database
    if ($user && password_verify($password, $user['password'])) {
        
        // Security Check: Ensure the user has completed email OTP verification
        if ($user['is_verified'] == 1) {
            // Set session variables to authorize the user across the application
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];

            // Redirect authorized user to the home feed
            header("Location: ../index.php");
            exit();
        } else {
            // Error if account exists but is not yet verified
            $error = "Please verify your email first!";
        }
    } else {
        // Generic error message to prevent account enumeration (security best practice)
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="auth-body">
    <div class="auth-card">
    <h2>Login</h2>
    
    <?php if(isset($error)) echo "<p style='color:#e74c3c; font-weight:bold;'>$error</p>"; ?>
    
    <?php if(isset($_GET['msg'])) echo "<p style='color:green; font-weight:bold;'>".$_GET['msg']."</p>"; ?>

    <form action="" method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        
        <div style="text-align: right; margin-bottom: 15px;">
            <a href="forgot_password.php" style="font-size: 14px; color: #666; text-decoration: none;">Forgot Password?</a>
        </div>

        <button type="submit">Login</button>
    </form>
    
    <p style="margin-top: 20px;">New here? <a href="register.php">Sign Up</a></p>
</div>
</body>
</html>