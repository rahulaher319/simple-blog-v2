<?php
require 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Fetch posts including the post_image column
$query = "SELECT posts.*, 
          (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.post_id) AS like_count,
          (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.post_id) AS comment_count
          FROM posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$my_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate Totals for the Stats Row
$total_likes = array_sum(array_column($my_posts, 'like_count'));
$total_comments = array_sum(array_column($my_posts, 'comment_count'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Creator Studio | Social Blog</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main-content { margin-left: 250px; padding: 40px; transition: 0.3s; }
        
        /* Stats Cards Styling */
        .stat-card {
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            color: white;
            flex: 1;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        /* Dashboard Table Styling */
        .post-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-top: 20px;
        }
        
        .post-table th, .post-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        /* Thumbnail Style */
        .dash-thumb {
            width: 60px;
            height: 45px;
            object-fit: cover;
            border-radius: 6px;
            background: #eee;
            display: block;
        }

        .post-table th { background-color: #f8f9fa; color: #2c3e50; }
        .btn-edit { color: #3498db; text-decoration: none; margin-right: 15px; font-weight: bold; }
        .btn-delete { color: #e74c3c; text-decoration: none; font-weight: bold; }
        
        /* Dark Mode Adjustments */
        body.dark-mode .post-table { background: #2d2d2d; color: white; }
        body.dark-mode .post-table th { background: #3d3d3d; color: white; border-bottom: 1px solid #444; }
        body.dark-mode .post-table td { border-bottom: 1px solid #444; }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1>Creator Studio</h1>
        <a href="create_post.php" class="button" style="text-decoration: none;">+ New Post</a>
    </div>

    <div class="stats-container" style="display: flex; gap: 20px; margin-bottom: 40px;">
        <div class="stat-card" style="background: #3498db;">
            <h3 style="margin:0; font-size: 2em;"><?php echo count($my_posts); ?></h3>
            <p style="margin:5px 0 0;">Total Posts</p>
        </div>
        <div class="stat-card" style="background: #e74c3c;">
            <h3 style="margin:0; font-size: 2em;"><?php echo $total_likes; ?></h3>
            <p style="margin:5px 0 0;">Likes Received</p>
        </div>
        <div class="stat-card" style="background: #2ecc71;">
            <h3 style="margin:0; font-size: 2em;"><?php echo $total_comments; ?></h3>
            <p style="margin:5px 0 0;">Comments</p>
        </div>
    </div>

    <?php if (empty($my_posts)): ?>
        <div class="post-card" style="text-align: center; padding: 50px;">
            <p>You haven't written any posts yet. <a href="create_post.php">Write your first post!</a></p>
        </div>
    <?php else: ?>
        <table class="post-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Stats</th>
                    <th>Date Published</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($my_posts as $post): ?>
                <tr>
                    <td>
                        <?php if (!empty($post['post_image'])): ?>
                            <img src="uploads/post_images/<?php echo htmlspecialchars($post['post_image']); ?>" class="dash-thumb">
                        <?php else: ?>
                            <div class="dash-thumb" style="display: flex; align-items: center; justify-content: center; font-size: 9px; color: #999; border: 1px dashed #ccc;">No Image</div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($post['title']); ?></strong></td>
                    <td>
                        <small>❤️ <?php echo $post['like_count']; ?> | 💬 <?php echo $post['comment_count']; ?></small>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                    <td>
                        <a href="edit_post.php?id=<?php echo $post['post_id']; ?>" class="btn-edit">Edit</a>
                        <a href="delete_post.php?id=<?php echo $post['post_id']; ?>" 
                           class="btn-delete" 
                           onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>