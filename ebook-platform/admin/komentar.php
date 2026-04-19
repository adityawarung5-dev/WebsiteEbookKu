<?php
require_once '../config/koneksi.php';
if (!isLoggedIn() || !isAdmin()) {
    header("Location: /ebook-platform/login.php");
    exit;
}

$success = '';
if (isset($_GET['hapus'])) {
    $kid = (int)$_GET['hapus'];
    $conn->query("DELETE FROM komentar WHERE id=$kid");
    $success = 'Komentar berhasil dihapus.';
}

$comments = $conn->query("
    SELECT k.*, u.nama as user_nama, e.judul as ebook_judul, e.id as ebook_id
    FROM komentar k
    JOIN users u ON u.id=k.user_id
    JOIN ebooks e ON e.id=k.ebook_id
    ORDER BY k.created_at DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Moderasi Komentar - Admin EbookKu</title>
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
      <h5 class="mb-0 fw-bold">Moderasi Komentar</h5>
    </div>
    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
      <?= count($comments) ?> komentar
    </span>
  </div>

  <?php if ($success): ?>
  <div class="alert alert-success rounded-3"><i class="bi bi-check-circle me-2"></i><?= e($success) ?></div>
  <?php endif; ?>

  <div class="card-admin">
    <?php if ($comments): ?>
    <div class="table-responsive">
      <table class="table admin-table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Pengguna</th>
            <th>Ebook</th>
            <th>Komentar</th>
            <th>Waktu</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($comments as $i => $c): ?>
          <tr>
            <td class="text-muted"><?= $i + 1 ?></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="comment-avatar" style="width:32px;height:32px;font-size:.75rem;flex-shrink:0">
                  <?= strtoupper(substr($c['user_nama'], 0, 1)) ?>
                </div>
                <span style="font-size:.85rem;font-weight:600"><?= e($c['user_nama']) ?></span>
              </div>
            </td>
            <td>
              <a href="../detail_ebook.php?id=<?= $c['ebook_id'] ?>" target="_blank"
                 style="color:var(--primary);font-size:.85rem;text-decoration:none;font-weight:500">
                <?= e(mb_strimwidth($c['ebook_judul'], 0, 35, '...')) ?>
              </a>
            </td>
            <td style="max-width:280px;font-size:.85rem;color:var(--text-muted)">
              <?= e(mb_strimwidth($c['komentar'], 0, 80, '...')) ?>
            </td>
            <td style="font-size:.8rem;color:var(--text-muted);white-space:nowrap">
              <?= time_ago($c['created_at']) ?>
            </td>
            <td>
              <a href="?hapus=<?= $c['id'] ?>"
                 class="btn btn-sm btn-outline-danger rounded-2"
                 data-confirm="Hapus komentar ini?">
                <i class="bi bi-trash"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="text-center py-5 text-muted">
      <i class="bi bi-chat-square" style="font-size:3rem;display:block;margin-bottom:1rem;opacity:.3"></i>
      Belum ada komentar.
    </div>
    <?php endif; ?>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/app.js"></script>
</body>
</html>
