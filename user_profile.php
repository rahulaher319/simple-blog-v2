<?php
require 'includes/db.php';
session_start();

$view_user_id = $_GET['id'] ?? null;

if (!$view_user_id) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT name, bio, created_at, profile_pic FROM users WHERE user_id = ?");
$stmt->execute([$view_user_id]);
$view_user = $stmt->fetch();

if (!$view_user) { die("User not found."); }

$postStmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$postStmt->execute([$view_user_id]);
$user_posts = $postStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($view_user['name']); ?>'s Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main-content { margin-left: 250px; padding: 40px; }
        
        .profile-header { 
            background: white; 
            padding: 40px; 
            border-radius: 15px; 
            margin-bottom: 30px; 
            text-align: center; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
        }
        
        .public-profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #3498db;
            margin-bottom: 20px;
        }

        .bio-text { 
            color: #7f8c8d; 
            font-style: italic; 
            margin-top: 15px; 
            max-width: 600px; 
            margin-left: auto; 
            margin-right: auto; 
            line-height: 1.6;
        }

        .profile-post-card { 
            margin-bottom: 20px; 
        }
        
        body.dark-mode .profile-header { 
            background: #2d2d2d; 
            color: white; 
            border: 1px solid #444; 
        }
    </style>
</head>
<body>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="profile-header">
        <img src="uploads/profile_pics/<?php echo htmlspecialchars($view_user['profile_pic'] ?? 'default.png'); ?>" 
             class="public-profile-pic">
             
        <h1 style="margin: 0;"><?php echo htmlspecialchars($view_user['name']); ?></h1>
        
        <p style="color: #3498db; font-weight: bold; margin-top: 5px;">
            Member since <?php echo date('F Y', strtotime($view_user['created_at'])); ?>
        </p>

        <div class="bio-text">
            <?php echo $view_user['bio'] ? nl2br(htmlspecialchars($view_user['bio'])) : "This user hasn't written a bio yet."; ?>
        </div>
    </div>

    <h2 style="margin-bottom: 20px;">Posts by <?php echo htmlspecialchars($view_user['name']); ?></h2>
    
    <?php if (empty($user_posts)): ?>
    <p style="color: #7f8c8d;">This user hasn't posted anything yet.</p>
<?php else: ?>
    <?php foreach ($user_posts as $post): ?>
        <div class="post-card">
            <div class="post-info-side">
                <div>
                    <h3 style="margin-top: 0;"><?php echo htmlspecialchars($post['title']); ?></h3>
                    <p style="color: #7f8c8d; font-size: 0.9em; margin-bottom: 15px;">
                        Published on <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                    </p>
                    
                    <p style="line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 150))); ?>...
                    </p>
                </div>

                <div style="margin-top: 15px;">
                    <a href="view_post.php?id=<?php echo $post['post_id']; ?>" 
                       class="button" style="font-size: 0.85em;">Read More →</a>
                </div>
            </div>

            <?php if (!empty($post['post_image'])): ?>
                <div class="post-image-side">
                    <img src="uploads/post_images/<?php echo htmlspecialchars($post['post_image']); ?>" 
                         class="post-featured-image">
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>
</body>
</html>