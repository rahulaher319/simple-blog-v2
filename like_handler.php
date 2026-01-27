<?php
require 'includes/db.php';
session_start();

if (isset($_GET['post_id']) && isset($_SESSION['user_id'])) {
    $post_id = $_GET['post_id'];
    $user_id = $_SESSION['user_id'];

    // Toggle logic: If like exists, delete it. If not, add it.
    $check = $pdo->prepare("SELECT * FROM likes WHERE post_id = ? AND user_id = ?");
    $check->execute([$post_id, $user_id]);

    if ($check->fetch()) {
        $pdo->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?")->execute([$post_id, $user_id]);
    } else {
        $pdo->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)")->execute([$post_id, $user_id]);
    }
    header("Location: view_post.php?id=$post_id");
}