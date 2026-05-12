<?php
require_once __DIR__ . '/includes/db.php';
session_start();

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        $db->prepare("UPDATE users SET last_login=NOW() WHERE id=?")->execute([$user['id']]);
        header('Location: /admin/pages/users.php');
        exit;
    }
    $err = 'Username atau password salah!';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <style>
    body { background:#0f0f0f; color:#e0e0e0; font-family:sans-serif; display:flex; align-items:center; justify-content:center; height:100vh; }
    .box { background:#1a1a1a; border:1px solid #333; border-radius:8px; padding:32px; width:320px; }
    h2 { margin-bottom:20px; color:#4f8ef7; }
    input { width:100%; padding:8px 10px; background:#0f0f0f; border:1px solid #333; border-radius:4px; color:#e0e0e0; margin-bottom:12px; font-size:13px; }
    button { width:100%; padding:10px; background:#4f8ef7; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:14px; }
    .err { color:#ef9a9a; font-size:13px; margin-bottom:10px; }
  </style>
</head>
<body>
<div class="box">
  <h2>Login</h2>
  <?php if ($err): ?><div class="err">!!!<?= htmlspecialchars($err) ?></div><?php endif; ?>
  <form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Masuk</button>
  </form>
</div>
</body>
</html>
