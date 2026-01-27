<?php
require 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];
    $post_image = null; 

    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['post_image']['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $target_dir = "uploads/post_images/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $post_image = "post_" . time() . "." . $ext;
            move_uploaded_file($_FILES['post_image']['tmp_name'], $target_dir . $post_image);
        }
    }

    $stmt = $pdo->prepare("INSERT INTO posts (title, content, user_id, post_image) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$title, $content, $user_id, $post_image])) {
        header("Location: index.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Post | Social Blog</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main-content { margin-left: 250px; padding: 40px; display: flex; justify-content: center; }
        .form-card { background: white; padding: 40px; border-radius: 15px; width: 100%; max-width: 700px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        label { display: block; margin-bottom: 8px; font-weight: bold; color: #2c3e50; }
        input[type="text"], textarea { 
            width: 100%; padding: 12px; margin-bottom: 25px; 
            border: 1px solid #ddd; border-radius: 8px; font-family: inherit; font-size: 16px; 
        }
        textarea { height: 250px; resize: vertical; }
        input[type="file"] { margin-bottom: 25px; }

        body.dark-mode .form-card { background: #2d2d2d; color: white; border: 1px solid #444; }
        body.dark-mode label { color: #ecf0f1; }
        body.dark-mode input[type="text"], body.dark-mode textarea { 
            background: #1a1a1a; color: white; border-color: #444; 
        }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="form-card">
        <h1 style="margin-bottom: 30px;">✍️ Create a New Post</h1>

        <form method="POST" enctype="multipart/form-data">
            <label>Post Title</label>
            <input type="text" name="title" placeholder="Enter a catchy title..." required>

            <label>Featured Image (Optional)</label>
            <input type="file" name="post_image" accept="image/*">
            <p style="font-size: 0.8em; color: #7f8c8d; margin-top: -20px; margin-bottom: 25px;">
                Supported formats: JPG, PNG, GIF
            </p>

            <label>Content</label>
            <textarea name="content" placeholder="What's on your mind?" required></textarea>
            
            <button type="submit" class="button" style="width: 100%; padding: 15px; font-size: 1.1em; cursor: pointer;">
                Publish Post
            </button>
        </form>
    </div>
</div>

</body>
</html>