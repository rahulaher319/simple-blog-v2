<?php
require 'includes/db.php';
session_start();

$query_term = $_GET['query'] ?? '';

// Fetch search results with author details and counts
$stmt = $pdo->prepare("SELECT 
            posts.*, 
            users.name AS author,
            users.profile_pic,
            (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.post_id) AS like_count,
            (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.post_id) AS comment_count
          FROM posts 
          JOIN users ON posts.user_id = users.user_id 
          WHERE title LIKE ? OR content LIKE ?
          ORDER BY created_at DESC");
$stmt->execute(["%$query_term%", "%$query_term%"]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results | Social Blog</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main-content { margin-left: 250px; padding: 40px; min-height: 100vh; }
        /* Reusing global styles for consistency */
        .search-meta { display: flex; align-items: center; margin-bottom: 12px; }
        .feed-avatar { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; margin-right: 10px; }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <h1>Search Results</h1>
    <p style="color: #7f8c8d; margin-bottom: 30px;">
        Showing results for: <strong>"<?php echo htmlspecialchars($query_term); ?>"</strong>
    </p>

    <?php if (empty($results)): ?>
        <div class="post-card" style="padding: 25px; display: block; text-align: center;">
            <p>No posts found matching your search.</p>
        </div>
    <?php else: ?>
        <p style="margin-bottom: 20px; color: #7f8c8d;"><?php echo count($results); ?> post(s) found.</p>
        
        <?php foreach ($results as $post): ?>
            <div class="post-card">
                <div class="post-info-side">
                    <div>
                        <h2 style="margin-top:0; font-size: 1.5em;">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </h2>
                        
                        <div class="search-meta">
                            <img src="uploads/profile_pics/<?php echo htmlspecialchars($post['profile_pic'] ?? 'default.png'); ?>" class="feed-avatar">
                            <p style="color:#7f8c8d; font-size:0.85em; margin: 0;">
                                By <strong><?php echo htmlspecialchars($post['author']); ?></strong> 
                                on <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                            </p>
                        </div>

                        <p style="line-height:1.5; font-size: 0.95em;">
                            <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 180))); ?>...
                        </p>
                    </div>

                    <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                        <div style="color: #7f8c8d; font-size: 0.85em;">
                            <span style="margin-right: 15px;">❤️ <?php echo $post['like_count']; ?></span>
                            <span>💬 <?php echo $post['comment_count']; ?></span>
                        </div>
                        <a href="view_post.php?id=<?php echo $post['post_id']; ?>" class="button">Read More</a>
                    </div>
                </div>

                <?php if (!empty($post['post_image'])): ?>
                    <div class="post-image-side">
                        <img src="uploads/post_images/<?php echo htmlspecialchars($post['post_image']); ?>" class="post-featured-image">
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>