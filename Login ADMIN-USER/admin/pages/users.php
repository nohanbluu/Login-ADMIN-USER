<?php
/**
 * KELOLA USER (CRUD)
 * Hanya bisa diakses oleh role = admin
 */
$pageTitle  = 'Kelola Pengguna';
$activeMenu = 'users';

require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin'); // Hanya admin!

$db  = getDB();
$msg = '';
$err = '';

// ── HAPUS USER ──
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $targetId = (int)$_GET['delete'];
    if ($targetId === (int)$_SESSION['user_id']) {
        $err = 'Tidak bisa menghapus akun sendiri!';
    } else {
        $db->prepare("DELETE FROM users WHERE id = ?")->execute([$targetId]);
        logActivity($_SESSION['user_id'], $_SESSION['username'], "DELETE_USER:$targetId");
        $msg = 'User berhasil dihapus.';
    }
}

// ── TOGGLE STATUS ──
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $targetId = (int)$_GET['toggle'];
    $current = $db->prepare("SELECT status FROM users WHERE id=?");
    $current->execute([$targetId]);
    $row = $current->fetch();
    if ($row) {
        $newStatus = $row['status'] === 'active' ? 'inactive' : 'active';
        $db->prepare("UPDATE users SET status=? WHERE id=?")->execute([$newStatus, $targetId]);
        logActivity($_SESSION['user_id'], $_SESSION['username'], "TOGGLE_STATUS:$targetId:$newStatus");
        $msg = "Status user diubah ke <strong>$newStatus</strong>.";
    }
}

// ── TAMBAH / EDIT USER ──
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action   = $_POST['action'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $role     = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? '';
    $editId   = (int)($_POST['edit_id'] ?? 0);

    if (empty($username) || empty($email)) {
        $err = 'Username dan email wajib diisi!';
    } elseif (!in_array($role, ['admin','user'])) {
        $err = 'Role tidak valid!';
    } elseif ($action === 'add') {
        if (empty($password)) { $err = 'Password wajib diisi untuk user baru!'; }
        else {
            try {
                $db->prepare("INSERT INTO users (username,email,password,role) VALUES (?,?,?,?)")
                   ->execute([$username, $email, password_hash($password, PASSWORD_BCRYPT), $role]);
                logActivity($_SESSION['user_id'], $_SESSION['username'], "ADD_USER:$username");
                $msg = "User <strong>$username</strong> berhasil ditambahkan.";
            } catch (PDOException $e) {
                $err = 'Username atau email sudah digunakan!';
            }
        }
    } elseif ($action === 'edit' && $editId > 0) {
        try {
            if (!empty($password)) {
                $db->prepare("UPDATE users SET username=?,email=?,role=?,password=? WHERE id=?")
                   ->execute([$username, $email, $role, password_hash($password, PASSWORD_BCRYPT), $editId]);
            } else {
                $db->prepare("UPDATE users SET username=?,email=?,role=? WHERE id=?")
                   ->execute([$username, $email, $role, $editId]);
            }
            logActivity($_SESSION['user_id'], $_SESSION['username'], "EDIT_USER:$editId");
            $msg = "User berhasil diperbarui.";
        } catch (PDOException $e) {
            $err = 'Username atau email sudah digunakan!';
        }
    }
}

// Ambil semua user
$users = $db->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

// Ambil user untuk edit (jika ada param ?edit=id)
$editUser = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $editUser = $stmt->fetch();
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start">
  <div>
    <h1>Kelola Pengguna</h1>
    <p>Tambah, edit, dan hapus akun pengguna sistem.</p>
  </div>
  <button class="btn btn-primary" onclick="document.getElementById('modalAdd').classList.add('show')">
    + Tambah User
  </button>
</div>

<?php if ($msg): ?><div class="alert alert-success">Success <?= $msg ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger">!? <?= e($err) ?></div><?php endif; ?>

<!-- TABEL USER -->
<div class="card">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Login Terakhir</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td class="mono" style="color:var(--text-muted)"><?= $u['id'] ?></td>
          <td><strong><?= e($u['username']) ?></strong>
            <?php if ($u['id'] == $_SESSION['user_id']): ?>
              <span style="font-size:10px;color:var(--cyan)"> (kamu)</span>
            <?php endif; ?>
          </td>
          <td class="mono"><?= e($u['email']) ?></td>
          <td>
            <span class="badge-role badge-<?= e($u['role']) ?>"><?= strtoupper($u['role']) ?></span>
          </td>
          <td>
            <span class="badge badge-<?= $u['status'] === 'active' ? 'active' : 'inactive' ?>">
              <?= $u['status'] ?>
            </span>
          </td>
          <td style="color:var(--text-muted);font-size:12px">
            <?= $u['last_login'] ? e($u['last_login']) : '-' ?>
          </td>
          <td>
            <a href="?edit=<?= $u['id'] ?>" class="btn btn-sm"
               style="border:1px solid var(--border);color:var(--text-muted)">✏️</a>
            <a href="?toggle=<?= $u['id'] ?>" class="btn btn-sm"
               style="border:1px solid var(--border);color:var(--yellow)"
               onclick="return confirm('Ubah status user ini?')">⟳</a>
            <?php if ($u['id'] != $_SESSION['user_id']): ?>
            <a href="?delete=<?= $u['id'] ?>" class="btn btn-sm btn-danger"
               onclick="return confirm('Hapus user <?= e($u['username']) ?>?')">🗑️</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL TAMBAH USER -->
<div class="modal-overlay" id="modalAdd">
  <div class="modal-box">
    <div class="modal-title">➕ Tambah User Baru</div>
    <form method="POST">
      <input type="hidden" name="action" value="add">
      <div class="form-group">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label">Role</label>
        <select name="role" class="form-control">
          <option value="user">User</option>
          <option value="admin">Admin</option>
        </select>
      </div>
      <div style="display:flex;gap:8px">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn"
          style="border:1px solid var(--border);color:var(--text-muted)"
          onclick="document.getElementById('modalAdd').classList.remove('show')">Batal</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL EDIT USER -->
<?php if ($editUser): ?>
<div class="modal-overlay show" id="modalEdit">
  <div class="modal-box">
    <div class="modal-title">✏️ Edit User: <?= e($editUser['username']) ?></div>
    <form method="POST">
      <input type="hidden" name="action" value="edit">
      <input type="hidden" name="edit_id" value="<?= $editUser['id'] ?>">
      <div class="form-group">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" value="<?= e($editUser['username']) ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= e($editUser['email']) ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password Baru <span style="color:var(--text-muted)">(kosongkan jika tidak diubah)</span></label>
        <input type="password" name="password" class="form-control">
      </div>
      <div class="form-group">
        <label class="form-label">Role</label>
        <select name="role" class="form-control">
          <option value="user" <?= $editUser['role']==='user'?'selected':'' ?>>User</option>
          <option value="admin" <?= $editUser['role']==='admin'?'selected':'' ?>>Admin</option>
        </select>
      </div>
      <div style="display:flex;gap:8px">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="users.php" class="btn" style="border:1px solid var(--border);color:var(--text-muted)">Batal</a>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
