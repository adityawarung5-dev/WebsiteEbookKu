<?php
require_once 'config/koneksi.php';

$q        = trim($_GET['q'] ?? '');
$kat_slug = trim($_GET['kategori'] ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 12;
$offset   = ($page - 1) * $per_page;

// Build query
$where_parts = [];
$params = [];
$types  = '';

if ($q) {
    $where_parts[] = "(e.judul LIKE ? OR e.penulis LIKE ? OR e.deskripsi LIKE ?)";
    $like = "%$q%";
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= 'sss';
}

if ($kat_slug) {
    $where_parts[] = "k.slug = ?";
    $params[] = $kat_slug;
    $types .= 's';
}

$where_sql = $where_parts ? 'WHERE ' . implode(' AND ', $where_parts) : '';

$count_sql = "SELECT COUNT(*) as c FROM ebooks e JOIN kategori k ON k.id=e.kategori_id $where_sql";
$stmt = $conn->prepare($count_sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['c'];
$total_pages = ceil($total / $per_page);

$data_sql = "SELECT e.*, k.nama as kategori_nama, k.slug, k.icon
             FROM ebooks e JOIN kategori k ON k.id=e.kategori_id
             $where_sql ORDER BY e.featured DESC, e.created_at DESC
             LIMIT $per_page OFFSET $offset";
$stmt2 = $conn->prepare($data_sql);
if ($params) $stmt2->bind_param($types, ...$params);
$stmt2->execute();
$ebooks = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

$kategoris = $conn->query("SELECT * FROM kategori ORDER BY nama")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $q ? 'Hasil: ' . e($q) : 'Semua Ebook' ?> - EbookKu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php require_once 'includes/navbar.php'; ?>

<div class="container py-4">

  <!-- Header -->
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
      <h1 class="section-title mb-1">
        <?php if ($q): ?>
          Hasil pencarian: "<span style="color:var(--primary)"><?= e($q) ?></span>"
        <?php elseif ($kat_slug): ?>
          <?= e(array_column($kategoris, 'nama', 'slug')[$kat_slug] ?? 'Kategori') ?>
        <?php else: ?>
          Semua Ebook
        <?php endif; ?>
      </h1>
      <p class="section-subtitle mb-0"><?= number_format($total) ?> ebook ditemukan</p>
    </div>
    <!-- Search -->
    <form action="ebook.php" method="GET" class="d-flex gap-2">
      <?php if ($kat_slug): ?>
        <input type="hidden" name="kategori" value="<?= e($kat_slug) ?>">
      <?php endif; ?>
      <input type="text" name="q" value="<?= e($q) ?>"
             class="form-control rounded-3 border-0 shadow-sm"
             style="min-width:220px;font-family:inherit"
             placeholder="Cari ebook...">
      <button type="submit" class="btn btn-primary rounded-3 px-3">
        <i class="bi bi-search"></i>
      </button>
    </form>
  </div>

  <!-- Filter Bar -->
  <div class="filter-bar">
    <span class="filter-label"><i class="bi bi-funnel me-1"></i>Kategori:</span>
    <button class="filter-btn <?= !$kat_slug ? 'active' : '' ?>" data-cat="all">Semua</button>
    <?php foreach ($kategoris as $k): ?>
    <button class="filter-btn <?= $kat_slug === $k['slug'] ? 'active' : '' ?>" data-cat="<?= e($k['slug']) ?>">
      <i class="<?= e($k['icon']) ?> me-1"></i><?= e($k['nama']) ?>
    </button>
    <?php endforeach; ?>
  </div>

  <!-- Grid -->
  <?php if ($ebooks): ?>
  <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3 g-md-4">
    <?php foreach ($ebooks as $ebook): ?>
    <div class="col">
      <?php require 'includes/ebook_card.php'; ?>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Pagination -->
  <?php if ($total_pages > 1): ?>
  <nav class="mt-5 d-flex justify-content-center">
    <ul class="pagination gap-1">
      <?php if ($page > 1): ?>
      <li class="page-item">
        <a class="page-link rounded-3" href="?<?= http_build_query(array_merge($_GET, ['page' => $page-1])) ?>">
          <i class="bi bi-chevron-left"></i>
        </a>
      </li>
      <?php endif; ?>
      <?php for ($p = max(1,$page-2); $p <= min($total_pages,$page+2); $p++): ?>
      <li class="page-item <?= $p == $page ? 'active' : '' ?>">
        <a class="page-link rounded-3 fw-600" href="?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>">
          <?= $p ?>
        </a>
      </li>
      <?php endfor; ?>
      <?php if ($page < $total_pages): ?>
      <li class="page-item">
        <a class="page-link rounded-3" href="?<?= http_build_query(array_merge($_GET, ['page' => $page+1])) ?>">
          <i class="bi bi-chevron-right"></i>
        </a>
      </li>
      <?php endif; ?>
    </ul>
  </nav>
  <?php endif; ?>

  <?php else: ?>
  <div class="fav-empty">
    <i class="bi bi-search"></i>
    <h4 class="fw-700">Ebook Tidak Ditemukan</h4>
    <p class="mb-3">Coba kata kunci lain atau hapus filter.</p>
    <a href="ebook.php" class="btn-primary-main">Lihat Semua Ebook</a>
  </div>
  <?php endif; ?>

</div>

<?php require_once 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
