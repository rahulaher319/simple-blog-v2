<?php
require 'includes/db.php';
session_start();

// 1. Check if the user is logged in and the request is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    
    // 2. Capture and clean the data
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $comment_text = trim($_POST['comment_text']);

    // 3. Only insert if the comment isn't empty
    if (!empty($comment_text)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
            $stmt->execute([$post_id, $user_id, $comment_text]);
        } catch (PDOException $e) {
            // Log error if table doesn't exist yet
            die("Database Error: " . $e->getMessage());
        }
    }

    // 4. Redirect back to the specific post
    header("Location: view_post.php?id=" . $post_id);
    exit();
} else {
    // Redirect home if accessed directly
    header("Location: index.php");
    exit();
}
?>