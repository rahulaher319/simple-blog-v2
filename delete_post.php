<?php
require 'includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        $delLikes = $pdo->prepare("DELETE FROM likes WHERE post_id = ?");
        $delLikes->execute([$post_id]);

        $delComments = $pdo->prepare("DELETE FROM comments WHERE post_id = ?");
        $delComments->execute([$post_id]);

        $stmt = $pdo->prepare("DELETE FROM posts WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $user_id]);

        $pdo->commit();

        header("Location: dashboard.php?msg=deleted");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Deletion failed: " . $e->getMessage());
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>