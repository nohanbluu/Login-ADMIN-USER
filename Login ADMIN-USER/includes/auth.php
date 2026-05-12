<?php
require_once __DIR__ . '/db.php';

session_start();

function requireRole(string $role): void {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== $role) {
        header('Location: /login.php');
        exit;
    }
}

function logActivity(int $userId, string $username, string $action): void {
    // Log ke file
    $log = date('Y-m-d H:i:s') . " | user:$username($userId) | $action\n";
    file_put_contents(__DIR__ . '/../logs/activity.log', $log, FILE_APPEND);
}

function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
