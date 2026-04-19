<?php
require_once '../config/koneksi.php';
if (!isLoggedIn() || !isAdmin()) redirect('../login.php');

$q    = trim($_GET['q'] ?? '');
$where = $q ? "WHERE e.judul LIKE '%".  $conn->real_escape_string($q) ."%' OR e.penulis LIKE '%". $conn->real_escape_string($q) ."%'" : '';
$ebooks = $conn->query("
    SELECT e.*, k.nama as kategori_nama
    FROM ebooks e JOIN kategori k ON k.id=e.kategori_id
    $where ORDER BY e.created_at DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Ebook - Admin EbookKu</title>
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
      <h5 class="mb-0 fw-bold">Kelola Ebook</h5>
    </div>
    <a href="tambah_ebook.php" class="btn btn-primary rounded-3">
      <i class="bi bi-plus me-1"></i>Tambah Ebook
    </a>
  </div>

  <?php if (isset($_GET['deleted'])): ?>
  <div class="alert alert-success rounded-3 mb-3">Ebook berhasil dihapus.</div>
  <?php endif; ?>

  <div class="card-admin">
    <!-- Search -->
    <form class="d-flex gap-2 mb-3" action="ebook_list.php" method="GET">
      <input type="text" name="q" class="form-control rounded-3" value="<?= e($q) ?>" placeholder="Cari ebook...">
      <button type="submit" class="btn btn-outline-primary rounded-3 px-3"><i class="bi bi-search"></i></button>
      <?php if ($q): ?>
      <a href="ebook_list.php" class="btn btn-outline-secondary rounded-3">Reset</a>
      <?php endif; ?>
    </form>

    <div class="table-responsive">
      <table class="table admin-table table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Cover</th>
            <th>Judul & Penulis</th>
            <th>Kategori</th>
            <th>Views</th>
            <th>Featured</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($ebooks as $i => $e): ?>
          <tr>
            <td class="text-muted"><?= $i + 1 ?></td>
            <td>
              <img src="<?= cover_url($e['cover']) ?>" alt=""
                   style="width:44px;height:58px;object-fit:cover;border-radius:8px;box-shadow:var(--shadow-sm)">
            </td>
            <td>
              <div class="fw-600" style="font-size:.88rem"><?= e($e['judul']) ?></div>
              <small class="text-muted"><?= e($e['penulis']) ?></small>
            </td>
            <td><span class="badge bg-primary-subtle text-primary rounded-pill"><?= e($e['kategori_nama']) ?></span></td>
            <td><?= number_format($e['views']) ?></td>
            <td>
              <?php if ($e['featured']): ?>
              <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Ya</span>
              <?php else: ?>
              <span class="badge bg-light text-muted">Tidak</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="d-flex gap-1 flex-nowrap">
                <a href="../detail_ebook.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-info rounded-2" target="_blank" title="Lihat">
                  <i class="bi bi-eye"></i>
                </a>
                <a href="edit_ebook.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-warning rounded-2" title="Edit">
                  <i class="bi bi-pencil"></i>
                </a>
                <a href="hapus_ebook.php?id=<?= $e['id'] ?>"
                   class="btn btn-sm btn-outline-danger rounded-2" title="Hapus"
                   data-confirm="Hapus ebook '<?= e($e['judul']) ?>'? Tindakan ini tidak dapat dibatalkan.">
                  <i class="bi bi-trash"></i>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($ebooks)): ?>
          <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada ebook ditemukan.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/app.js"></script>
</body>
</html>
