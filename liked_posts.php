<?php
require 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch posts that the user has liked
$query = "SELECT posts.*, users.name AS author 
          FROM likes 
          JOIN posts ON likes.post_id = posts.post_id 
          JOIN users ON posts.user_id = users.user_id 
          WHERE likes.user_id = ? 
          ORDER BY likes.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$liked_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Liked Posts | Social Blog</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main-content { margin-left: 250px; padding: 40px; }
        
        /* Specific override for the Liked Posts list to fix overlapping */
        .liked-post-card { 
            background: white; 
            padding: 20px 30px; 
            border-radius: 12px; 
            margin-bottom: 20px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            min-height: auto; /* Fixes the massive height issue */
        }

        .liked-post-info h3 { margin: 0; color: #2c3e50; font-size: 1.2em; }
        .liked-post-info p { margin: 5px 0 0; color: #7f8c8d; font-size: 0.9em; }

        body.dark-mode .liked-post-card { background: #2d2d2d; color: white; border: 1px solid #444; }
        body.dark-mode .liked-post-info h3 { color: white; }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h1 style="margin-bottom: 30px;">❤️ Posts You've Liked</h1>

    <?php if (empty($liked_posts)): ?>
        <div class="liked-post-card">
            <p>You haven't liked any posts yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($liked_posts as $post): ?>
            <div class="liked-post-card">
                <div class="liked-post-info">
                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p>By <strong><?php echo htmlspecialchars($post['author']); ?></strong></p>
                </div>
                
                <div class="liked-post-actions">
                    <a href="view_post.php?id=<?php echo $post['post_id']; ?>" class="button">View Post</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>