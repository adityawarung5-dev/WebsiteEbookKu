<?php
require_once '../config/koneksi.php';
if (!isLoggedIn() || !isAdmin()) {
    header("Location: /ebook-platform/login.php");
    exit;
}

$error = $success = '';

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $nama  = trim($_POST['nama'] ?? '');
    $slug  = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $nama));
    $icon  = trim($_POST['icon'] ?? 'bi-book');
    $warna = trim($_POST['warna'] ?? '#6c63ff');
    if (!$nama) {
        $error = 'Nama kategori wajib diisi.';
    } else {
        $stmt = $conn->prepare("INSERT INTO kategori (nama, slug, icon, warna) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $nama, $slug, $icon, $warna);
        $stmt->execute() ? $success = 'Kategori berhasil ditambahkan!' : ($error = 'Gagal menambah kategori.');
    }
}

// Handle delete
if (isset($_GET['hapus'])) {
    $kid = (int)$_GET['hapus'];
    $conn->query("DELETE FROM kategori WHERE id=$kid");
    $success = 'Kategori dihapus.';
}

$kategoris = $conn->query("
    SELECT k.*, COUNT(e.id) as jumlah
    FROM kategori k LEFT JOIN ebooks e ON e.kategori_id=k.id
    GROUP BY k.id ORDER BY k.id
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kategori - Admin EbookKu</title>
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
      <h5 class="mb-0 fw-bold">Kelola Kategori</h5>
    </div>
  </div>

  <?php if ($error): ?>
  <div class="alert alert-danger rounded-3"><?= e($error) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
  <div class="alert alert-success rounded-3"><i class="bi bi-check-circle me-2"></i><?= e($success) ?></div>
  <?php endif; ?>

  <div class="row g-4">
    <!-- Add Form -->
    <div class="col-lg-4">
      <div class="card-admin">
        <h6 class="fw-bold mb-3">Tambah Kategori Baru</h6>
        <form method="POST">
          <input type="hidden" name="add" value="1">
          <div class="mb-3">
            <label class="form-label fw-600 small">Nama Kategori *</label>
            <input type="text" name="nama" class="form-control rounded-3" placeholder="Nama kategori" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-600 small">Bootstrap Icon Class</label>
            <input type="text" name="icon" class="form-control rounded-3" value="bi-book" placeholder="bi-book">
            <small class="text-muted">Cari icon di <a href="https://icons.getbootstrap.com" target="_blank">icons.getbootstrap.com</a></small>
          </div>
          <div class="mb-3">
            <label class="form-label fw-600 small">Warna</label>
            <div class="d-flex gap-2 align-items-center">
              <input type="color" name="warna" value="#6c63ff" class="form-control form-control-color rounded-3" style="width:60px">
              <input type="text" id="colorText" value="#6c63ff" class="form-control rounded-3" style="font-size:.85rem">
            </div>
          </div>
          <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold">
            <i class="bi bi-plus-circle me-2"></i>Tambah Kategori
          </button>
        </form>
      </div>
    </div>

    <!-- List -->
    <div class="col-lg-8">
      <div class="card-admin">
        <h6 class="fw-bold mb-3">Daftar Kategori (<?= count($kategoris) ?>)</h6>
        <div class="table-responsive">
          <table class="table admin-table table-hover">
            <thead>
              <tr>
                <th>Icon</th>
                <th>Nama</th>
                <th>Slug</th>
                <th>Jumlah Ebook</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($kategoris as $k): ?>
              <tr>
                <td>
                  <span style="background:<?= e($k['warna']) ?>22;color:<?= e($k['warna']) ?>;padding:.4rem;border-radius:8px;display:inline-flex">
                    <i class="<?= e($k['icon']) ?> fs-5"></i>
                  </span>
                </td>
                <td class="fw-600"><?= e($k['nama']) ?></td>
                <td><code class="small"><?= e($k['slug']) ?></code></td>
                <td><span class="badge bg-primary-subtle text-primary"><?= $k['jumlah'] ?> ebook</span></td>
                <td>
                  <a href="?hapus=<?= $k['id'] ?>"
                     class="btn btn-sm btn-outline-danger rounded-2"
                     data-confirm="Hapus kategori '<?= e($k['nama']) ?>'? Semua ebook dalam kategori ini juga akan terhapus!">
                    <i class="bi bi-trash"></i>
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/app.js"></script>
<script>
const colorPicker = document.querySelector('input[type="color"]');
const colorText   = document.getElementById('colorText');
if (colorPicker && colorText) {
    colorPicker.addEventListener('input', () => colorText.value = colorPicker.value);
    colorText.addEventListener('input', () => colorPicker.value = colorText.value);
}
</script>
</body>
</html>
