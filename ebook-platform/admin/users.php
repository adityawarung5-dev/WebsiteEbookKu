<?php
require_once '../config/koneksi.php';
if (!isLoggedIn() || !isAdmin()) {
    header("Location: /ebook-platform/login.php");
    exit;
}

$success = '';
// Toggle role
if (isset($_GET['toggle_role'])) {
    $uid = (int)$_GET['toggle_role'];
    if ($uid !== (int)$_SESSION['user_id']) { // Can't change own role
        $u = $conn->query("SELECT role FROM users WHERE id=$uid")->fetch_assoc();
        $new_role = ($u['role'] === 'admin') ? 'user' : 'admin';
        $conn->query("UPDATE users SET role='$new_role' WHERE id=$uid");
        $success = 'Role pengguna berhasil diubah.';
    }
}
// Delete user
if (isset($_GET['hapus'])) {
    $uid = (int)$_GET['hapus'];
    if ($uid !== (int)$_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE id=$uid");
        $success = 'Pengguna berhasil dihapus.';
    }
}

$users = $conn->query("
    SELECT u.*, 
           COUNT(DISTINCT f.id) as fav_count,
           COUNT(DISTINCT k.id) as comment_count
    FROM users u
    LEFT JOIN favorit f ON f.user_id=u.id
    LEFT JOIN komentar k ON k.user_id=u.id
    GROUP BY u.id ORDER BY u.created_at DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Pengguna - Admin EbookKu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php require_once 'includes/sidebar.php'; ?>

<div class="admin-main">
  <div class="admin-topbar">
    <div class="d-flex align-items-center gap-3">
      <button id="sidebarToggle" class="btn btn-sm border-0 d-lg-none"><i class="bi bi-list fs-4"></i></button>
      <h5 class="mb-0 fw-bold">Kelola Pengguna</h5>
    </div>
    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
      Total: <?= count($users) ?> pengguna
    </span>
  </div>

  <?php if ($success): ?>
  <div class="alert alert-success rounded-3"><i class="bi bi-check-circle me-2"></i><?= e($success) ?></div>
  <?php endif; ?>

  <div class="card-admin">
    <div class="table-responsive">
      <table class="table admin-table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Pengguna</th>
            <th>Email</th>
            <th>Role</th>
            <th>Favorit</th>
            <th>Komentar</th>
            <th>Bergabung</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $i => $u): ?>
          <tr>
            <td class="text-muted"><?= $i + 1 ?></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="comment-avatar" style="width:34px;height:34px;font-size:.8rem;flex-shrink:0">
                  <?= strtoupper(substr($u['nama'], 0, 1)) ?>
                </div>
                <span class="fw-600" style="font-size:.88rem"><?= e($u['nama']) ?></span>
                <?php if ($u['id'] == $_SESSION['user_id']): ?>
                <span class="badge bg-info-subtle text-info">Anda</span>
                <?php endif; ?>
              </div>
            </td>
            <td style="font-size:.85rem"><?= e($u['email']) ?></td>
            <td>
              <span class="badge <?= $u['role'] === 'admin' ? 'bg-warning text-dark' : 'bg-success' ?>">
                <?= $u['role'] ?>
              </span>
            </td>
            <td><?= $u['fav_count'] ?></td>
            <td><?= $u['comment_count'] ?></td>
            <td style="font-size:.82rem;color:var(--text-muted)">
              <?= date('d M Y', strtotime($u['created_at'])) ?>
            </td>
            <td>
              <div class="d-flex gap-1">
                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                <a href="?toggle_role=<?= $u['id'] ?>"
                   class="btn btn-sm btn-outline-warning rounded-2"
                   title="Ubah Role"
                   data-confirm="Ubah role '<?= e($u['nama']) ?>' menjadi <?= $u['role']==='admin'?'user':'admin' ?>?">
                  <i class="bi bi-arrow-repeat"></i>
                </a>
                <a href="?hapus=<?= $u['id'] ?>"
                   class="btn btn-sm btn-outline-danger rounded-2"
                   title="Hapus"
                   data-confirm="Hapus pengguna '<?= e($u['nama']) ?>'?">
                  <i class="bi bi-trash"></i>
                </a>
                <?php else: ?>
                <span class="text-muted" style="font-size:.78rem">—</span>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/app.js"></script>
</body>
</html>
