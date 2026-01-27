<?php
require 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT comments.*, posts.title AS post_title 
          FROM comments 
          JOIN posts ON comments.post_id = posts.post_id 
          WHERE comments.user_id = ? 
          ORDER BY comments.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$my_comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Comments | Social Blog</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main-content { margin-left: 250px; padding: 40px; }
        .comment-card { background: white; padding: 20px; border-radius: 10px; margin-bottom: 15px; border-left: 5px solid #3498db; }
        body.dark-mode .comment-card { background: #2d2d2d; color: white; }
    </style>
</head>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
    <h1>💬 Your Comments</h1>
    <?php if (empty($my_comments)): ?>
        <p>You haven't commented on any posts yet.</p>
    <?php else: ?>
        <?php foreach ($my_comments as $comment): ?>
            <div class="comment-card">
                <p>On: <strong><?php echo htmlspecialchars($comment['post_title']); ?></strong></p>
                <p>"<?php echo htmlspecialchars($comment['comment_text']); ?>"</p>
                <small><?php echo date('M d, Y', strtotime($comment['created_at'])); ?></small>
                <br><br>
                <a href="view_post.php?id=<?php echo $comment['post_id']; ?>" style="color:#3498db; text-decoration:none;">View Conversation →</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>