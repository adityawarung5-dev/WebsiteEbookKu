<?php
require_once '../config/koneksi.php';
if (!isLoggedIn() || !isAdmin()) redirect('../login.php');

$kategoris = $conn->query("SELECT * FROM kategori ORDER BY nama")->fetch_all(MYSQLI_ASSOC);
$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul       = trim($_POST['judul'] ?? '');
    $penulis     = trim($_POST['penulis'] ?? '');
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $kategori_id = (int)($_POST['kategori_id'] ?? 0);
    $halaman     = (int)($_POST['halaman'] ?? 0);
    $tahun       = !empty($_POST['tahun']) ? (int)$_POST['tahun'] : null;
    $featured    = isset($_POST['featured']) ? 1 : 0;

    if (!$judul || !$penulis || !$kategori_id) {
        $error = 'Judul, penulis, dan kategori wajib diisi.';
    } elseif (empty($_FILES['file_pdf']['name'])) {
        $error = 'File PDF wajib diupload.';
    } else {
        $pdf_file = $_FILES['file_pdf'];
        $pdf_ext  = strtolower(pathinfo($pdf_file['name'], PATHINFO_EXTENSION));

        if ($pdf_ext !== 'pdf') {
            $error = 'File harus berformat PDF.';
        } elseif ($pdf_file['size'] > 50 * 1024 * 1024) {
            $error = 'Ukuran PDF maksimal 50MB.';
        } else {
            $pdf_name  = uniqid('ebook_') . '.pdf';
            $pdf_dest  = __DIR__ . '/../uploads/ebooks/' . $pdf_name;
            $cover_name = '';

            // Upload cover (opsional)
            if (!empty($_FILES['cover']['name'])) {
                $cover_file = $_FILES['cover'];
                $cover_ext  = strtolower(pathinfo($cover_file['name'], PATHINFO_EXTENSION));
                $allowed    = ['jpg', 'jpeg', 'png', 'webp'];

                if (!in_array($cover_ext, $allowed)) {
                    $error = 'Cover harus berformat JPG/PNG/WebP.';
                } elseif ($cover_file['size'] > 5 * 1024 * 1024) {
                    $error = 'Ukuran cover maksimal 5MB.';
                } else {
                    $cover_name = uniqid('cover_') . '.' . $cover_ext;
                    move_uploaded_file($cover_file['tmp_name'], __DIR__ . '/../uploads/covers/' . $cover_name);
                }
            }

            if (!$error) {
                if (move_uploaded_file($pdf_file['tmp_name'], $pdf_dest)) {
                    // ✅ FIXED: type string = 9 karakter untuk 9 variabel
                    // s=judul, s=penulis, s=deskripsi, i=kategori_id,
                    // s=cover_name, s=pdf_name, i=halaman, i=tahun, i=featured
                    $stmt = $conn->prepare("
                        INSERT INTO ebooks 
                            (judul, penulis, deskripsi, kategori_id, cover, file_pdf, halaman, tahun, featured)
                        VALUES 
                            (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->bind_param(
                        'sssissiis',
                        // s  s       s          i             s            s         i        i      s
                        $judul, $penulis, $deskripsi, $kategori_id, $cover_name, $pdf_name, $halaman, $tahun, $featured
                    );

                    if ($stmt->execute()) {
                        $new_id  = $conn->insert_id;
                        $success = 'Ebook berhasil ditambahkan! <a href="../detail_ebook.php?id=' . $new_id . '" target="_blank">Lihat Ebook</a>';
                    } else {
                        $error = 'Gagal menyimpan ke database: ' . $conn->error;
                    }
                } else {
                    $error = 'Gagal mengupload PDF. Periksa permission folder uploads/ebooks/';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Ebook - Admin EbookKu</title>
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
      <div>
        <h5 class="mb-0 fw-bold">Tambah Ebook</h5>
        <small class="text-muted">Upload ebook baru</small>
      </div>
    </div>
    <a href="ebook_list.php" class="btn btn-sm btn-outline-secondary rounded-3">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
  </div>

  <?php if ($error): ?>
  <div class="alert alert-danger rounded-3 d-flex align-items-center gap-2"><?= $error ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
  <div class="alert alert-success rounded-3 d-flex align-items-center gap-2">
    <i class="bi bi-check-circle-fill me-2"></i><?= $success ?>
  </div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card-admin mb-4">
          <h6 class="fw-bold mb-3">Informasi Ebook</h6>

          <div class="mb-3">
            <label class="form-label fw-600 small">Judul Ebook *</label>
            <input type="text" name="judul" class="form-control rounded-3"
                   value="<?= e($_POST['judul'] ?? '') ?>" placeholder="Judul ebook" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-600 small">Penulis *</label>
            <input type="text" name="penulis" class="form-control rounded-3"
                   value="<?= e($_POST['penulis'] ?? '') ?>" placeholder="Nama penulis" required>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-600 small">Kategori *</label>
              <select name="kategori_id" class="form-select rounded-3" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategoris as $k): ?>
                <option value="<?= $k['id'] ?>" <?= (($_POST['kategori_id']??'') == $k['id']) ? 'selected' : '' ?>>
                  <?= e($k['nama']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-600 small">Jumlah Halaman</label>
              <input type="number" name="halaman" class="form-control rounded-3" min="0"
                     value="<?= (int)($_POST['halaman'] ?? 0) ?>" placeholder="0">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-600 small">Tahun Terbit</label>
              <input type="number" name="tahun" class="form-control rounded-3" min="1900" max="<?= date('Y') ?>"
                     value="<?= (int)($_POST['tahun'] ?? date('Y')) ?>" placeholder="<?= date('Y') ?>">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-600 small">Deskripsi</label>
            <textarea name="deskripsi" class="form-control rounded-3" rows="5"
                      placeholder="Deskripsi singkat tentang ebook ini..."><?= e($_POST['deskripsi'] ?? '') ?></textarea>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="featured" id="featured"
                   <?= isset($_POST['featured']) ? 'checked' : '' ?>>
            <label class="form-check-label fw-500 small" for="featured">
              ⭐ Tampilkan di beranda (Featured)
            </label>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <!-- Cover Upload -->
        <div class="card-admin mb-4">
          <h6 class="fw-bold mb-3">Cover Ebook</h6>
          <div class="text-center mb-3">
            <img id="coverPreview" src="../assets/images/default-cover.jpg" alt="Preview"
                 style="max-width:160px;border-radius:12px;box-shadow:var(--shadow);display:block;margin:0 auto">
          </div>
          <input type="file" name="cover" id="coverInput" class="form-control rounded-3"
                 accept="image/jpeg,image/png,image/webp">
          <small class="text-muted mt-1 d-block">JPG/PNG/WebP, max 5MB</small>
        </div>

        <!-- PDF Upload -->
        <div class="card-admin mb-4">
          <h6 class="fw-bold mb-3">File PDF *</h6>
          <div class="border-2 border-dashed rounded-3 p-3 text-center mb-2"
               style="border-color:var(--border);cursor:pointer"
               onclick="document.getElementById('pdfInput').click()">
            <i class="bi bi-file-earmark-pdf" style="font-size:2.5rem;color:var(--danger)"></i>
            <p class="text-muted small mt-2 mb-0">Klik untuk pilih file PDF</p>
            <small class="text-muted">Maksimal 50MB</small>
          </div>
          <input type="file" name="file_pdf" id="pdfInput" class="form-control rounded-3"
                 accept=".pdf" required>
          <div id="pdfFileName" class="text-muted small mt-1"></div>
        </div>

        <div class="d-grid">
          <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-bold">
            <i class="bi bi-upload me-2"></i>Upload Ebook
          </button>
        </div>
      </div>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/app.js"></script>
<script>
document.getElementById('pdfInput').addEventListener('change', function() {
    const f = this.files[0];
    if (f) document.getElementById('pdfFileName').textContent = '📄 ' + f.name;
});
</script>
</body>
</html>
