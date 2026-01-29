<?php
/**
 * Load database connection and session management
 */
require '../includes/db.php';
require '../includes/functions.php'; // Refactored to handle centralized session_start()

/**
 * Access Control: Ensure the user is actually in the registration flow
 * If no temporary email exists in the session, redirect to login
 */
if (!isset($_SESSION['temp_email'])) {
    header("Location: login.php");
    exit();
}

/**
 * Handle OTP Verification Form Submission
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Combine the 6 individual input fields into a single OTP string
    $user_otp = implode('', $_POST['otp']);
    
    // Validate the user's input against the code stored in the session
    if ($user_otp == $_SESSION['otp']) {
        // Security: Use a prepared statement to verify the user in the database
        $stmt = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
        $stmt->execute([$_SESSION['temp_email']]);
        
        // Cleanup: Remove temporary session data after successful verification
        unset($_SESSION['otp'], $_SESSION['temp_email']);
        
        // Redirect to login with a success flag
        header("Location: login.php?verified=1");
        exit();
    } else {
        // User-friendly error message for incorrect codes
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
        /**
         * Auto-focus Logic: Automatically moves the cursor to the next field 
         * as the user types, or back if they press Backspace.
         */
        const inputs = document.querySelectorAll('.otp-input');
        inputs.forEach((input, index) => {
            // Move focus forward after a character is entered
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            // Move focus backward on backspace if current field is empty
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && input.value === '' && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>
</body>
</html>