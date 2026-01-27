<?php
require 'includes/db.php';
session_start();

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $comment_id = $_GET['id'];
    $post_id = $_GET['post_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("DELETE FROM comments WHERE comment_id = ? AND user_id = ?");
    $stmt->execute([$comment_id, $user_id]);

    header("Location: view_post.php?id=" . $post_id);
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>