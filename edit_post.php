<?php
require 'includes/db.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = $_GET['id'] ?? null;

if (!$post_id) {
    header("Location: dashboard.php");
    exit();
}

// 1. Fetch the existing post data
$stmt = $pdo->prepare("SELECT * FROM posts WHERE post_id = ? AND user_id = ?");
$stmt->execute([$post_id, $user_id]);
$post = $stmt->fetch();

// If post doesn't exist or doesn't belong to user, kick them out
if (!$post) {
    header("Location: dashboard.php");
    exit();
}

// 2. Handle the Update request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $updateStmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE post_id = ? AND user_id = ?");
    if ($updateStmt->execute([$title, $content, $post_id, $user_id])) {
        header("Location: dashboard.php?msg=Post updated successfully");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post | Social Blog</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main-content { margin-left: 250px; padding: 40px; display: flex; justify-content: center; }
        textarea { width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px; font-family: inherit; font-size: 16px; resize: vertical; }
    </style>
</head>
<body>

    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="auth-card" style="width: 100%; max-width: 700px; margin-top: 50px;">
            <h2 style="margin-bottom: 30px;">✏️ Edit Your Post</h2>
            
            <form method="POST">
                <label style="display:block; text-align:left; font-weight:bold; margin-bottom:5px;">Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                
                <label style="display:block; text-align:left; font-weight:bold; margin-bottom:5px;">Content</label>
                <textarea name="content" rows="12" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" style="flex: 2;">Save Changes</button>
                    <a href="dashboard.php" class="button" style="flex: 1; background: #95a5a6; text-decoration: none; text-align: center; line-height: 45px;">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>