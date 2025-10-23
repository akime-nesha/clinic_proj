<?php
require_once 'config.php';

if (isset($_SESSION['user']) && !empty($_SESSION['user']['id'])) {
    // verify user actually exists in database
    $check = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $check->execute([$_SESSION['user']['id']]);
    if ($check->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip) VALUES (?,?,?)");
        $stmt->execute([$_SESSION['user']['id'], 'Logged out', $_SERVER['REMOTE_ADDR'] ?? '']);
    }
}

session_destroy();
header('Location: login.php');
exit;
