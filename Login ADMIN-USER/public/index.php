<?php
/**
 * public/index.php — Dashboard User
 */
require_once __DIR__ . '/../includes/auth.php';
requireRole('user'); // redirect ke login jika belum login, 403 jika admin nyasar ke sini

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard User</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: #0f0f0f; color: #e0e0e0; font-family: sans-serif; min-height: 100vh; }

    .topbar {
      background: #1a1a1a; border-bottom: 1px solid #2a2a2a;
      padding: 0 24px; height: 52px;
      display: flex; align-items: center; justify-content: space-between;
    }
    .topbar-brand { color: #4f8ef7; font-weight: 600; font-size: 15px; }
    .topbar-right { display: flex; align-items: center; gap: 12px; font-size: 13px; }
    .badge-user {
      background: #0891b222; color: #67e8f9;
      border: 1px solid #0891b255;
      padding: 2px 10px; border-radius: 20px; font-size: 11px;
    }
    .btn-logout {
      padding: 5px 14px; background: transparent;
      border: 1px solid #333; color: #888;
      border-radius: 6px; cursor: pointer; font-size: 12px;
      text-decoration: none;
    }
    .btn-logout:hover { border-color: #ef5350; color: #ef5350; }

    .main { max-width: 800px; margin: 48px auto; padding: 0 20px; }

    .welcome {
      background: #1a1a1a; border: 1px solid #2a2a2a;
      border-radius: 10px; padding: 32px;
      margin-bottom: 24px;
      border-top: 3px solid #4f8ef7;
    }
    .welcome h1 { font-size: 22px; margin-bottom: 8px; }
    .welcome p  { color: #888; font-size: 14px; }

    .info-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 16px;
    }
    .info-card {
      background: #1a1a1a; border: 1px solid #2a2a2a;
      border-radius: 10px; padding: 20px;
    }
    .info-card .label { font-size: 11px; color: #555; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
    .info-card .value { font-size: 18px; font-weight: 600; color: #e0e0e0; }
    .info-card .value.green { color: #4caf50; }
  </style>
</head>
<body>

<div class="topbar">
  <div class="topbar-brand">Sistem Keamanan</div>
  <div class="topbar-right">
    <span>👤 <?= htmlspecialchars($username) ?></span>
    <span class="badge-user">USER</span>
    <a href="/logout.php" class="btn-logout">⏏ Logout</a>
  </div>
</div>

<div class="main">
  <div class="welcome">
    <h1>Selamat datang, <?= htmlspecialchars($username) ?>!</h1>
    <p>Kamu login sebagai <strong>User</strong>. Halaman admin tidak dapat diakses.</p>
  </div>

  <div class="info-grid">
    <div class="info-card">
      <div class="label">Status Akun</div>
      <div class="value green">● Aktif</div>
    </div>
    <div class="info-card">
      <div class="label">Role</div>
      <div class="value">User</div>
    </div>
    <div class="info-card">
      <div class="label">Sesi Login</div>
      <div class="value" style="font-size:13px;color:#888">
        <?= date('d M Y, H:i') ?>
      </div>
    </div>
  </div>
</div>

</body>
</html>
