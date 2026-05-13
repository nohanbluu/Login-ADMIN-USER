<?php
/**
 * includes/auth.php
 * Helper: cek login & hak akses (role)
 */

if (session_status() === PHP_SESSION_NONE) session_start();

/** Cek apakah user sudah login */
function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['role']);
}

/** Paksa login — redirect ke login.php jika belum masuk */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

/** Paksa role tertentu — tampilkan 403 jika tidak sesuai */
function requireRole(string $role): void {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        http_response_code(403);
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8">
        <style>body{background:#0f0f0f;color:#e0e0e0;font-family:sans-serif;
        display:flex;align-items:center;justify-content:center;height:100vh;flex-direction:column;gap:12px}
        h1{color:#ef5350;font-size:64px;margin:0}a{color:#4f8ef7}</style></head><body>
        <h1>403</h1><p>Akses ditolak — halaman ini hanya untuk <strong>' . htmlspecialchars($role) . '</strong>.</p>
        <a href="/login.php">← Kembali ke Login</a></body></html>';
        exit;
    }
}

/** Cek apakah user adalah admin */
function isAdmin(): bool {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}
