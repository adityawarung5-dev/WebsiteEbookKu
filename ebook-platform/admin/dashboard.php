<?php
require_once '../config/koneksi.php';
if (!isLoggedIn() || !isAdmin()) redirect('../login.php');

$stats = [
    'ebooks'   => $conn->query("SELECT COUNT(*) c FROM ebooks")->fetch_assoc()['c'],
    'users'    => $conn->query("SELECT COUNT(*) c FROM users WHERE role='user'")->fetch_assoc()['c'],
    'comments' => $conn->query("SELECT COUNT(*) c FROM komentar")->fetch_assoc()['c'],
    'favorit'  => $conn->query("SELECT COUNT(*) c FROM favorit")->fetch_assoc()['c'],
];

$recent_ebooks = $conn->query("
    SELECT e.*, k.nama as kategori_nama FROM ebooks e
    JOIN kategori k ON k.id=e.kategori_id
    ORDER BY e.created_at DESC LIMIT 8
")->fetch_all(MYSQLI_ASSOC);

$recent_users = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - EbookKu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Sidebar -->
<?php require_once 'includes/sidebar.php'; ?>

<!-- Main -->
<div class="admin-main">
  <!-- Topbar -->
  <div class="admin-topbar">
    <div class="d-flex align-items-center gap-3">
      <button id="sidebarToggle" class="btn btn-sm border-0 d-lg-none">
        <i class="bi bi-list fs-4"></i>
      </button>
      <div>
        <h5 class="mb-0 fw-bold">Dashboard</h5>
        <small class="text-muted">Selamat datang, <?= e($_SESSION['nama']) ?>!</small>
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="../index.php" class="btn btn-sm btn-outline-secondary rounded-3" target="_blank">
        <i class="bi bi-box-arrow-up-right me-1"></i>Lihat Site
      </a>
      <a href="../logout.php" class="btn btn-sm btn-outline-danger rounded-3">
        <i class="bi bi-box-arrow-right me-1"></i>Logout
      </a>
    </div>
  </div>

  <!-- Stats -->
  <div class="row g-3 mb-4">
    <?php
    $stat_items = [
        ['ebooks',   'bi-book-fill',      '#ede9ff','#6c63ff', 'Total Ebook'],
        ['users',    'bi-people-fill',    '#ecfdf5','#10b981', 'Total Pengguna'],
        ['comments', 'bi-chat-fill',      '#fff8e6','#f59e0b', 'Total Komentar'],
        ['favorit',  'bi-heart-fill',     '#fef2f2','#ef4444', 'Total Favorit'],
    ];
    foreach ($stat_items as [$key, $icon, $bg, $clr, $label]):
    ?>
    <div class="col-6 col-xl-3">
      <div class="stat-card">
        <div class="stat-icon" style="background:<?= $bg ?>;color:<?= $clr ?>">
          <i class="<?= $icon ?>"></i>
        </div>
        <div>
          <div class="stat-number"><?= number_format($stats[$key]) ?></div>
          <div class="stat-label"><?= $label ?></div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="row g-4">
    <!-- Recent Ebooks -->
    <div class="col-lg-8">
      <div class="card-admin">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h6 class="fw-bold mb-0">Ebook Terbaru</h6>
          <a href="ebook_list.php" class="btn btn-sm btn-outline-primary rounded-3">Lihat Semua</a>
        </div>
        <div class="table-responsive">
          <table class="table admin-table table-hover align-middle">
            <thead>
              <tr>
                <th>Cover</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Views</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recent_ebooks as $e): ?>
              <tr>
                <td>
                  <img src="<?= cover_url($e['cover']) ?>" alt=""
                       style="width:40px;height:52px;object-fit:cover;border-radius:6px">
                </td>
                <td>
                  <div class="fw-600" style="font-size:.88rem;max-width:200px" class="text-truncate">
                    <?= e($e['judul']) ?>
                  </div>
                  <small class="text-muted"><?= e($e['penulis']) ?></small>
                </td>
                <td><span class="badge bg-primary-subtle text-primary"><?= e($e['kategori_nama']) ?></span></td>
                <td><?= number_format($e['views']) ?></td>
                <td>
                  <div class="d-flex gap-1">
                    <a href="../detail_ebook.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-info rounded-2" target="_blank">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="edit_ebook.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-warning rounded-2">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="hapus_ebook.php?id=<?= $e['id'] ?>"
                       class="btn btn-sm btn-outline-danger rounded-2"
                       data-confirm="Hapus ebook '<?= e($e['judul']) ?>'?">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Recent Users -->
    <div class="col-lg-4">
      <div class="card-admin">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h6 class="fw-bold mb-0">Pengguna Terbaru</h6>
          <a href="users.php" class="btn btn-sm btn-outline-primary rounded-3">Semua</a>
        </div>
        <?php foreach ($recent_users as $u): ?>
        <div class="d-flex align-items-center gap-3 py-2 border-bottom">
          <div class="comment-avatar" style="width:36px;height:36px;font-size:.8rem;flex-shrink:0">
            <?= strtoupper(substr($u['nama'], 0, 1)) ?>
          </div>
          <div class="min-w-0">
            <div class="fw-600 text-truncate" style="font-size:.88rem"><?= e($u['nama']) ?></div>
            <small class="text-muted text-truncate d-block"><?= e($u['email']) ?></small>
          </div>
          <span class="badge ms-auto <?= $u['role'] === 'admin' ? 'bg-warning text-dark' : 'bg-success' ?>">
            <?= $u['role'] ?>
          </span>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Quick Links -->
      <div class="card-admin mt-3">
        <h6 class="fw-bold mb-3">Aksi Cepat</h6>
        <div class="d-grid gap-2">
          <a href="tambah_ebook.php" class="btn btn-primary rounded-3">
            <i class="bi bi-plus-circle me-2"></i>Tambah Ebook
          </a>
          <a href="kategori.php" class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-tags me-2"></i>Kelola Kategori
          </a>
          <a href="komentar.php" class="btn btn-outline-secondary rounded-3">
            <i class="bi bi-chat-square me-2"></i>Moderasi Komentar
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/app.js"></script>
</body>
</html>
