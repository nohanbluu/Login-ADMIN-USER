<?php
/**
 * login.php
 * - Redirect ke /admin/pages/users.php jika admin
 * - Redirect ke /public/index.php jika user biasa
 */
require_once __DIR__ . '/includes/db.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Sudah login? langsung redirect
if (!empty($_SESSION['role'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? '/admin/pages/users.php' : '/public/index.php'));
    exit;
}

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $err = 'Username dan password wajib diisi!';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true); // cegah session fixation

            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            $db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")
               ->execute([$user['id']]);

            // Redirect berdasarkan role
            $redirect = ($user['role'] === 'admin') ? '/admin/pages/users.php' : '/public/index.php';
            header("Location: $redirect");
            exit;
        }

        $err = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login — Sistem Keamanan</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      background: #0f0f0f;
      color: #e0e0e0;
      font-family: sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }
    .box {
      background: #1a1a1a;
      border: 1px solid #333;
      border-radius: 8px;
      padding: 32px;
      width: 320px;
    }
    h2 { margin-bottom: 20px; color: #4f8ef7; }
    label { font-size: 12px; color: #888; display: block; margin-bottom: 4px; }
    input {
      width: 100%;
      padding: 8px 10px;
      background: #0f0f0f;
      border: 1px solid #333;
      border-radius: 4px;
      color: #e0e0e0;
      margin-bottom: 14px;
      font-size: 13px;
    }
    button {
      width: 100%;
      padding: 10px;
      background: #4f8ef7;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }
    button:hover { background: #3a7de8; }
    .err {
      color: #ef9a9a;
      font-size: 13px;
      margin-bottom: 12px;
      background: #ef9a9a15;
      border: 1px solid #ef9a9a40;
      border-radius: 4px;
      padding: 8px 10px;
    }
    .hint {
      margin-top: 20px;
      font-size: 11px;
      color: #555;
      background: #111;
      border: 1px solid #2a2a2a;
      border-radius: 4px;
      padding: 10px;
      line-height: 1.8;
      font-family: monospace;
    }
    .hint strong { color: #4f8ef7; }
  </style>
</head>
<body>
<div class="box">
  <h2>Login</h2>

  <?php if ($err): ?>
    <div class="err">?!<?= htmlspecialchars($err) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label>Username</label>
    <input type="text" name="username"
           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
           placeholder="Masukkan username"
           autocomplete="username" required>

    <label>Password</label>
    <input type="password" name="password"
           placeholder="Masukkan password"
           autocomplete="current-password" required>

    <button type="submit">Masuk</button>
  </form>

  <div class="hint">
    <strong>Demo:</strong><br>
    Admin → admin / Admin@123<br>
    User &nbsp;→ user1 / User@123
  </div>
</div>
</body>
</html>
