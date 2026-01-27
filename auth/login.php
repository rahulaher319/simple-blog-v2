<?php
require '../includes/db.php';
session_start();

// 1. Process the login only if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 2. Fetch the user from the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 3. Validate credentials and verification status
    if ($user && password_verify($password, $user['password'])) {
        if ($user['is_verified'] == 1) {
            // Success! Store user info in the session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];

            // 4. Redirect to the Home Page (index.php is one level up)
            header("Location: ../index.php");
            exit();
        } else {
            $error = "Please verify your email first!";
        }
    } else {
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