<?php
require '../includes/db.php';
session_start();

if (!isset($_SESSION['temp_email'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_otp = implode('', $_POST['otp']);
    
    if ($user_otp == $_SESSION['otp']) {
        $stmt = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
        $stmt->execute([$_SESSION['temp_email']]);
        unset($_SESSION['otp'], $_SESSION['temp_email']);
        header("Location: login.php?verified=1");
        exit();
    } else {
        $error = "Invalid OTP. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Account | Social Blog</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="auth-body">

    <div class="auth-card">
        <h2>Verify OTP</h2>
        <p style="margin-bottom: 20px; color: #666;">Enter the 6-digit code sent to your email.</p>
        
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <form method="POST" id="otp-form">
            <div style="display: flex; gap: 10px; justify-content: center; margin-bottom: 30px;">
                <input type="text" name="otp[]" class="otp-input" maxlength="1" required autofocus>
                <input type="text" name="otp[]" class="otp-input" maxlength="1" required>
                <input type="text" name="otp[]" class="otp-input" maxlength="1" required>
                <input type="text" name="otp[]" class="otp-input" maxlength="1" required>
                <input type="text" name="otp[]" class="otp-input" maxlength="1" required>
                <input type="text" name="otp[]" class="otp-input" maxlength="1" required>
            </div>
            <button type="submit">Verify Now</button>
        </form>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && input.value === '' && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>
</body>
</html>