<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= $pageTitle ?? 'App' ?></title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: sans-serif; background: #0f0f0f; color: #e0e0e0; padding: 20px; }
    :root {
      --border: #333; --text-muted: #888; --cyan: #0ff;
      --yellow: #ff0; --primary: #4f8ef7;
    }
    .page-header { margin-bottom: 20px; }
    .card { background: #1a1a1a; border: 1px solid var(--border); border-radius: 8px; padding: 16px; margin-bottom: 16px; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid var(--border); font-size: 14px; }
    th { color: var(--text-muted); font-weight: 600; }
    .btn { padding: 6px 12px; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; border: none; font-size: 13px; }
    .btn-primary { background: var(--primary); color: #fff; }
    .btn-danger { background: #c0392b; color: #fff; }
    .btn-sm { padding: 3px 8px; font-size: 12px; }
    .alert { padding: 10px 14px; border-radius: 6px; margin-bottom: 12px; }
    .alert-success { background: #1a3a1a; border: 1px solid #2e7d32; color: #81c784; }
    .alert-danger { background: #3a1a1a; border: 1px solid #c62828; color: #ef9a9a; }
    .badge { padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .badge-active { background: #1b5e20; color: #a5d6a7; }
    .badge-inactive { background: #4a1a1a; color: #ef9a9a; }
    .badge-role { padding: 2px 8px; border-radius: 10px; font-size: 11px; }
    .badge-admin { background: #1a237e; color: #90caf9; }
    .badge-user { background: #1a3a1a; color: #a5d6a7; }
    .mono { font-family: monospace; }
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.7); z-index:100; align-items:center; justify-content:center; }
    .modal-overlay.show { display:flex; }
    .modal-box { background:#1a1a1a; border:1px solid var(--border); border-radius:8px; padding:24px; width:400px; max-width:90vw; }
    .modal-title { font-size:16px; font-weight:700; margin-bottom:16px; }
    .form-group { margin-bottom:12px; }
    .form-label { display:block; font-size:12px; color:var(--text-muted); margin-bottom:4px; }
    .form-control { width:100%; padding:8px 10px; background:#0f0f0f; border:1px solid var(--border); border-radius:4px; color:#e0e0e0; font-size:13px; }
    nav { background:#1a1a1a; border-bottom:1px solid var(--border); padding:12px 20px; margin:-20px -20px 20px; display:flex; gap:16px; align-items:center; }
    nav a { color:var(--text-muted); text-decoration:none; font-size:13px; }
    nav a.active { color:var(--cyan); }
  </style>
</head>
<body>
<nav>
  <strong style="color:var(--cyan)">Noor Ahmad Naufal</strong>
  <a href="/admin/pages/users.php" class="<?= ($activeMenu??'') === 'users' ? 'active' : '' ?>">Users</a>
  <a href="/logout.php" style="margin-left:auto">Logout</a>
</nav>
