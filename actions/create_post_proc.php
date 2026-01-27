<?php
require 'includes/db.php';
session_start();

// Security check: Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    // Insert the post into the database
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
    if ($stmt->execute([$user_id, $title, $content])) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Failed to create post. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Post | Social Blog</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="auth-card" style="width: 100%; max-width: 600px; margin: 0 auto;">
            <h2>Create a New Post</h2>
            <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            
            <form method="POST">
                <input type="text" name="title" placeholder="Post Title" required>
                <textarea name="content" placeholder="What's on your mind?" rows="10" required 
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px; font-family: inherit;"></textarea>
                <button type="submit">Publish Post</button>
            </form>
        </div>
    </div>

</body>
</html>