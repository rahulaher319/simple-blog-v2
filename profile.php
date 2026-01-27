<?php
require 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";

// 1. Fetch user data including profile_pic, birthdate, and gender
$stmt = $pdo->prepare("SELECT name, email, created_at, bio, profile_pic, birthdate, gender FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// 2. Handle the update request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);
    $new_bio = trim($_POST['bio']);
    $new_birthdate = $_POST['birthdate']; // Capture birthdate
    $new_gender = $_POST['gender'];       // Capture gender
    $profile_pic = $user['profile_pic']; 

    // Handle File Upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $new_filename = "user_" . $user_id . "_" . time() . "." . $ext;
            $target_dir = "uploads/profile_pics/";
            
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_dir . $new_filename)) {
                $profile_pic = $new_filename;
            }
        }
    }

    if (!empty($new_name) && !empty($new_email)) {
        // Update all fields including birthdate and gender
        $updateStmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, bio = ?, profile_pic = ?, birthdate = ?, gender = ? WHERE user_id = ?");
        if ($updateStmt->execute([$new_name, $new_email, $new_bio, $profile_pic, $new_birthdate, $new_gender, $user_id])) {
            $_SESSION['user_name'] = $new_name; 
            $message = "Profile updated successfully!";
            
            // Refresh local data for display
            $user['name'] = $new_name;
            $user['email'] = $new_email;
            $user['bio'] = $new_bio;
            $user['profile_pic'] = $profile_pic;
            $user['birthdate'] = $new_birthdate;
            $user['gender'] = $new_gender;
        }
    } else {
        $message = "Name and Email are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | Social Blog</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main-content { margin-left: 250px; padding: 40px; display: flex; justify-content: center; }
        .profile-card { background: white; padding: 40px; border-radius: 15px; width: 100%; max-width: 500px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; font-size: 14px; }
        textarea { height: 100px; resize: vertical; }
        
        .profile-img-container { text-align: center; margin-bottom: 30px; }
        .profile-img-preview { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #3498db; margin-bottom: 10px; }

        body.dark-mode .profile-card { background: #2d2d2d; color: white; border: 1px solid #444; }
        body.dark-mode input, body.dark-mode textarea, body.dark-mode select { background: #1a1a1a; color: white; border-color: #444; }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="profile-card">
        <h2 style="margin-bottom: 5px;">👤 Profile Settings</h2>
        <p style="color: #7f8c8d; font-size: 0.9em; margin-bottom: 30px;">
            Member since: <strong><?php echo date('F Y', strtotime($user['created_at'])); ?></strong>
        </p>

        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="profile-img-container">
                <img src="uploads/profile_pics/<?php echo htmlspecialchars($user['profile_pic'] ?? 'default.png'); ?>" class="profile-img-preview">
                <label for="profile_image" style="cursor: pointer; color: #3498db;">Change Photo</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;" onchange="this.form.submit()">
            </div>

            <label>Display Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label>Email Address</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>Birthdate</label>
            <input type="date" name="birthdate" value="<?php echo htmlspecialchars($user['birthdate'] ?? ''); ?>">

            <label>Gender</label>
            <select name="gender">
                <option value="Male" <?php echo ($user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo ($user['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                <option value="Prefer not to say" <?php echo ($user['gender'] == 'Prefer not to say') ? 'selected' : ''; ?>>Prefer not to say</option>
            </select>

            <label>About Me (Bio)</label>
            <textarea name="bio" placeholder="Tell the community about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>

            <button type="submit" class="button" style="width: 100%; padding: 12px; font-size: 16px; font-weight: bold; border: none; cursor: pointer; margin-top: 10px;">Save Changes</button>
        </form>
    </div>
</div>

</body>
</html>