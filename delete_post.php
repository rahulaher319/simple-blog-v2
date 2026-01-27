<?php
require 'includes/db.php';
session_start();

// Security: Only logged-in users can access this script
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    try {
        // Start a transaction: If one part fails, none of it happens
        $pdo->beginTransaction();

        // Step 1: Delete all likes associated with this post
        $delLikes = $pdo->prepare("DELETE FROM likes WHERE post_id = ?");
        $delLikes->execute([$post_id]);

        // Step 2: Delete all comments associated with this post
        $delComments = $pdo->prepare("DELETE FROM comments WHERE post_id = ?");
        $delComments->execute([$post_id]);

        // Step 3: Delete the post itself, but only if it belongs to the logged-in user
        $stmt = $pdo->prepare("DELETE FROM posts WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$post_id, $user_id]);

        // Finalize the changes
        $pdo->commit();

        header("Location: dashboard.php?msg=deleted");
        exit();

    } catch (Exception $e) {
        // If there's an error, rollback to keep data safe
        $pdo->rollBack();
        die("Deletion failed: " . $e->getMessage());
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>