<?php
require 'includes/db.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'] ?? null;

// 1. Fetch Post Details
$stmt = $pdo->prepare("SELECT posts.*, users.name FROM posts JOIN users ON posts.user_id = users.user_id WHERE post_id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) { die("Post not found."); }

// 2. Fetch Comments
$comStmt = $pdo->prepare("SELECT comments.*, users.name, users.profile_pic FROM comments JOIN users ON comments.user_id = users.user_id WHERE post_id = ? ORDER BY created_at DESC");
$comStmt->execute([$post_id]);
$comments = $comStmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Fetch Like Count
$likeStmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
$likeStmt->execute([$post_id]);
$like_count = $likeStmt->fetchColumn();

// 4. Check if current user liked this post
$userLiked = false;
if ($user_id) {
    $checkLike = $pdo->prepare("SELECT * FROM likes WHERE post_id = ? AND user_id = ?");
    $checkLike->execute([$post_id, $user_id]);
    $userLiked = $checkLike->fetch() ? true : false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['title']); ?> | Social Blog</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main-content { margin-left: 250px; padding: 40px; transition: 0.3s; }
        
        /* OVERRIDE for full view: Stack vertically instead of flex horizontal */
        .full-post-view { 
            display: block !important; 
            min-height: auto !important;
            padding: 40px !important;
        }

        .view-post-header { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        
        .view-post-image-container {
            width: 100%;
            display: flex;
            justify-content: center;
            background: #f8f9fa;
            border-radius: 12px;
            margin: 30px 0;
            overflow: hidden;
        }

        .view-post-image {
            max-width: 100%;
            max-height: 600px;
            object-fit: contain;
        }

        .comment-section { margin-top: 40px; display: block !important; }
        .comment-item { border-bottom: 1px solid #eee; padding: 20px 0; display: flex !important; align-items: flex-start; }
        .comment-avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; margin-right: 15px; }

        body.dark-mode .view-post-header { border-bottom-color: #444; }
        body.dark-mode .view-post-image-container { background: #1a1a1a; }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="post-card full-post-view">
        <div class="view-post-header">
            <h1 style="font-size: 2.5em; margin: 0 0 10px 0;"><?php echo htmlspecialchars($post['title']); ?></h1>
            <p style="color: #7f8c8d; margin: 0;">
                By <a href="user_profile.php?id=<?php echo $post['user_id']; ?>" style="color:#3498db; text-decoration:none; font-weight:bold;">
                    <?php echo htmlspecialchars($post['name']); ?>
                </a> 
                on <?php echo date('F j, Y', strtotime($post['created_at'])); ?> at <?php echo date('g:i A', strtotime($post['created_at'])); ?>
            </p>
        </div>

        <?php if (!empty($post['post_image'])): ?>
            <div class="view-post-image-container">
                <img src="uploads/post_images/<?php echo htmlspecialchars($post['post_image']); ?>" class="view-post-image">
            </div>
        <?php endif; ?>

        <div style="font-size: 1.15em; line-height: 1.8; color: #2c3e50;">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <span style="font-size: 1.2em; margin-right: 20px;">❤️ <strong><?php echo $like_count; ?></strong> Likes</span>
            <?php if ($user_id): ?>
                <a href="like_handler.php?post_id=<?php echo $post_id; ?>" class="button" style="background: <?php echo $userLiked ? '#e74c3c' : '#3498db'; ?>;">
                    <?php echo $userLiked ? '❤️ Unlike' : '🤍 Like'; ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="post-card full-post-view comment-section">
        <h3>Comments (<?php echo count($comments); ?>)</h3>
        
        <?php if ($user_id): ?>
            <form action="add_comment.php" method="POST" style="margin-bottom: 30px;">
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <textarea name="comment_text" required placeholder="Join the discussion..." style="width:100%; height:100px; padding:15px; border-radius:8px; border:1px solid #ddd;"></textarea>
                <button type="submit" class="button" style="margin-top:10px; background:#2ecc71;">Post Comment</button>
            </form>
        <?php endif; ?>

        <?php foreach ($comments as $comment): ?>
            <div class="comment-item">
                <img src="uploads/profile_pics/<?php echo htmlspecialchars($comment['profile_pic'] ?? 'default.png'); ?>" class="comment-avatar">
                <div>
                    <strong><?php echo htmlspecialchars($comment['name']); ?></strong> 
                    <small style="color:#95a5a6; margin-left:10px;"><?php echo date('M d, g:i a', strtotime($comment['created_at'])); ?></small>
                    <p style="margin-top:8px;"><?php echo htmlspecialchars($comment['comment_text']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>