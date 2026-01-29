<?php
require 'includes/db.php';
require 'includes/functions.php'; 
session_start();

$query = "SELECT 
            posts.*, 
            users.name AS author,
            users.profile_pic,
            (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.post_id) AS like_count,
            (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.post_id) AS comment_count
          FROM posts 
          JOIN users ON posts.user_id = users.user_id 
          ORDER BY created_at DESC";
$stmt = $pdo->query($query);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Social Blog | Home</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main-content { margin-left: 250px; padding: 40px; min-height: 100vh; transition: 0.3s; }
        .post-card { background: white; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; overflow: hidden; min-height: 250px; }
        .post-info-side { flex: 1; padding: 25px; display: flex; flex-direction: column; justify-content: space-between; }
        .post-image-side { flex: 0 0 300px; background: #f8f9fa; border-left: 1px solid #eee; }
        .post-featured-image { width: 100%; height: 100%; object-fit: cover; display: block; }
        .feed-avatar { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; margin-right: 10px; border: 2px solid #3498db; }
        .post-meta { display: flex; align-items: center; margin-bottom: 12px; }
        body.dark-mode .post-card { background: #2d2d2d; border: 1px solid #444; }
        body.dark-mode .post-card h2 { color: #fff !important; }
        body.dark-mode .post-card p { color: #ccc !important; }
        .author-link { color: #3498db; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<?php 
if (isset($_SESSION['user_id'])) {
    include 'includes/sidebar.php'; 
    echo '<div class="main-content">';
} else {
    echo '<div class="guest-container" style="text-align:center; padding:40px;">';
    echo '<a href="auth/login.php" class="button">Login</a>';
    echo '</div>';
}
?>

    <div class="blog-header">
        <img src="SS_Logo.jpeg" alt="S Logo" class="community-logo">
        <h1>Simple Blog</h1>
    </div>

    <?php if (empty($posts)): ?>
        <div class="post-card" style="text-align:center; padding: 25px; display: block;">
            <p>No posts available yet.</p>
        </div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <div class="post-info-side">
                    <div>
                        <h2 style="margin-top:0; color:#2c3e50; font-size: 1.5em;"><?php echo e($post['title']); ?></h2>
                        <div class="post-meta">
                            <img src="uploads/profile_pics/<?php echo e($post['profile_pic'] ?? 'default.png'); ?>" class="feed-avatar">
                            <p style="color:#7f8c8d; font-size:0.85em; margin: 0;">
                                Posted by 
                                <a href="user_profile.php?id=<?php echo $post['user_id']; ?>" class="author-link">
                                    <?php echo e($post['author']); ?>
                                </a> 
                                on <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                            </p>
                        </div>
                        <p style="line-height:1.5; font-size: 0.95em;">
                            <?php echo nl2br(e(substr($post['content'], 0, 180))); ?>...
                        </p>
                    </div>
                    <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                        <div style="color: #7f8c8d; font-size: 0.85em;">
                            <span style="margin-right: 15px;">❤️ <?php echo $post['like_count']; ?></span>
                            <span>💬 <?php echo $post['comment_count']; ?></span>
                        </div>
                        <a href="view_post.php?id=<?php echo $post['post_id']; ?>" class="button" style="text-decoration:none; font-size:0.85em; padding: 8px 15px;">Read More</a>
                    </div>
                </div>

                <?php if (!empty($post['post_image'])): ?>
                    <div class="post-image-side">
                        <img src="uploads/post_images/<?php echo e($post['post_image']); ?>" class="post-featured-image">
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div> </body>
</html>