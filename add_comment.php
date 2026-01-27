<?php
require 'includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $comment_text = trim($_POST['comment_text']);

    if (!empty($comment_text)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
            $stmt->execute([$post_id, $user_id, $comment_text]);
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    header("Location: view_post.php?id=" . $post_id);
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>