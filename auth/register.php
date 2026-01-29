<?php
// Import PHPMailer classes into the global namespace for email handling
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load core dependencies: Database connection, Mailer source files, and private credentials
require '../includes/db.php';
require '../includes/PHPMailer/src/Exception.php';
require '../includes/PHPMailer/src/PHPMailer.php';
require '../includes/PHPMailer/src/SMTP.php';
require '../includes/config.php';

// Initialize session to store temporary data like OTPs
session_start();

// Process the registration form when submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and capture basic user information
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    // Security: Securely hash the password before database storage
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    
    // Generate a random 6-digit verification code
    $otp = rand(100000, 999999);

    try {
        // SQL Injection Protection: Use prepared statements for user insertion
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, birthdate, gender, is_verified) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->execute([$name, $email, $password, $birthdate, $gender]);

        // Initialize PHPMailer for sending the OTP email
        $mail = new PHPMailer(true);
        
        // Server settings using constants from config.php
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USER; 
        $mail->Password   = MAIL_PASS; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // SSL Options to ensure compatibility with local server environments
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Email composition settings
        $mail->setFrom(MAIL_USER, 'Social Blog');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Verify your Social Blog Account';
        $mail->Body    = "Hello $name, <br><br> Your verification code is: <b>$otp</b>";

        // Send the email and store OTP in session for verification check
        $mail->send();

        $_SESSION['otp'] = $otp;
        $_SESSION['temp_email'] = $email;

        // Redirect user to the OTP verification page
        header("Location: verify.php");
        exit();

    } catch (Exception $e) {
        // Catch-all error handling for failed database inserts or mail delivery
        $error = "Registration failed. Error: {$mail->ErrorInfo}";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Social Blog</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Form-specific styling for dropdowns and labels */
        .auth-card select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: none;
            border-bottom: 2px solid #ddd;
            outline: none;
            background: transparent;
            font-family: inherit;
        }
        .auth-card label {
            display: block;
            text-align: left;
            font-size: 0.8em;
            color: #777;
            margin-bottom: 5px;
        }
    </style>
</head>
<body class="auth-body">
    <div class="auth-card">
        <h2>Sign Up</h2>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            
            <label>Birthdate</label>
            <input type="date" name="birthdate" required>
            
            <label>Gender</label>
            <select name="gender" required>
                <option value="" disabled selected>Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
                <option value="Prefer not to say">Prefer not to say</option>
            </select>

            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <p style="margin-top: 20px;">Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>