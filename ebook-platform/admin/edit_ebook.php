<?php
require_once '../config/koneksi.php';
if (!isLoggedIn() || !isAdmin()) {
    header("Location: /ebook-platform/login.php");
    exit;
}
$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header("Location: /ebook-platform/admin/ebook_list.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM ebooks WHERE id=? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$ebook = $stmt->get_result()->fetch_assoc();
if (!$ebook) redirect('ebook_list.php');

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
    $cover_name  = $ebook['cover'];
    $pdf_name    = $ebook['file_pdf'];

    if (!$judul || !$penulis || !$kategori_id) {
        $error = 'Judul, penulis, dan kategori wajib diisi.';
    } else {
        // Upload cover baru (opsional)
        if (!empty($_FILES['cover']['name'])) {
            $cf   = $_FILES['cover'];
            $cext = strtolower(pathinfo($cf['name'], PATHINFO_EXTENSION));
            if (in_array($cext, ['jpg','jpeg','png','webp']) && $cf['size'] < 5*1024*1024) {
                $cover_name = uniqid('cover_') . '.' . $cext;
                move_uploaded_file($cf['tmp_name'], __DIR__ . '/../uploads/covers/' . $cover_name);
            }
        }

        // Upload PDF baru (opsional)
        if (!empty($_FILES['file_pdf']['name'])) {
            $pf   = $_FILES['file_pdf'];
            $pext = strtolower(pathinfo($pf['name'], PATHINFO_EXTENSION));
            if ($pext === 'pdf' && $pf['size'] < 50*1024*1024) {
                $pdf_name = uniqid('ebook_') . '.pdf';
                move_uploaded_file($pf['tmp_name'], __DIR__ . '/../uploads/ebooks/' . $pdf_name);
            }
        }

        // ✅ FIXED: 10 variabel = type string 'sssissiisi' (10 karakter)
        // s=judul, s=penulis, s=deskripsi, i=kategori_id,
        // s=cover_name, s=pdf_name, i=halaman, i=tahun, i=featured, i=id
        $stmt2 = $conn->prepare("
            UPDATE ebooks 
            SET judul=?, penulis=?, deskripsi=?, kategori_id=?,
                cover=?, file_pdf=?, halaman=?, tahun=?, featured=?
            WHERE id=?
        ");
        $stmt2->bind_param(
            'sssissiiii',
            $judul,
            $penulis,
            $deskripsi,
            $kategori_id,
            $cover_name,
            $pdf_name,
            $halaman,
            $tahun,
            $featured,
            $id
        );

        if ($stmt2->execute()) {
            $success = 'Ebook berhasil diperbarui!';
            // Refresh data ebook agar tampilan ikut update
            $ebook['judul']       = $judul;
            $ebook['penulis']     = $penulis;
            $ebook['deskripsi']   = $deskripsi;
            $ebook['kategori_id'] = $kategori_id;
            $ebook['cover']       = $cover_name;
            $ebook['file_pdf']    = $pdf_name;
            $ebook['halaman']     = $halaman;
            $ebook['tahun']       = $tahun;
            $ebook['featured']    = $featured;
        } else {
            $error = 'Gagal memperbarui ebook: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Ebook - Admin EbookKu</title>
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
        <h5 class="mb-0 fw-bold">Edit Ebook</h5>
        <small class="text-muted"><?= e($ebook['judul']) ?></small>
      </div>
    </div>
    <a href="ebook_list.php" class="btn btn-sm btn-outline-secondary rounded-3">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
  </div>

  <?php if ($error): ?>
  <div class="alert alert-danger rounded-3"><?= e($error) ?></div>
  <?php endif; ?>
  <?php if ($success): ?>
  <div class="alert alert-success rounded-3"><i class="bi bi-check-circle me-2"></i><?= e($success) ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card-admin mb-4">
          <h6 class="fw-bold mb-3">Informasi Ebook</h6>
          <div class="mb-3">
            <label class="form-label fw-600 small">Judul Ebook *</label>
            <input type="text" name="judul" class="form-control rounded-3" value="<?= e($ebook['judul']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-600 small">Penulis *</label>
            <input type="text" name="penulis" class="form-control rounded-3" value="<?= e($ebook['penulis']) ?>" required>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-600 small">Kategori *</label>
              <select name="kategori_id" class="form-select rounded-3" required>
                <?php foreach ($kategoris as $k): ?>
                <option value="<?= $k['id'] ?>" <?= $k['id'] == $ebook['kategori_id'] ? 'selected' : '' ?>>
                  <?= e($k['nama']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-600 small">Halaman</label>
              <input type="number" name="halaman" class="form-control rounded-3" value="<?= $ebook['halaman'] ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-600 small">Tahun</label>
              <input type="number" name="tahun" class="form-control rounded-3" value="<?= $ebook['tahun'] ?>">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-600 small">Deskripsi</label>
            <textarea name="deskripsi" class="form-control rounded-3" rows="5"><?= e($ebook['deskripsi']) ?></textarea>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="featured" id="featured" <?= $ebook['featured'] ? 'checked' : '' ?>>
            <label class="form-check-label fw-500 small" for="featured">⭐ Tampilkan di beranda (Featured)</label>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card-admin mb-4">
          <h6 class="fw-bold mb-3">Cover Ebook</h6>
          <div class="text-center mb-3">
            <img id="coverPreview" src="<?= cover_url($ebook['cover']) ?>" alt="Cover"
                 style="max-width:160px;border-radius:12px;box-shadow:var(--shadow)">
          </div>
          <input type="file" name="cover" id="coverInput" class="form-control rounded-3" accept="image/*">
          <small class="text-muted">Kosongkan jika tidak ingin mengganti cover</small>
        </div>
        <div class="card-admin mb-4">
          <h6 class="fw-bold mb-3">File PDF</h6>
          <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded-2" style="background:var(--surface-3)">
            <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
            <small class="text-muted text-truncate"><?= e($ebook['file_pdf']) ?></small>
          </div>
          <input type="file" name="file_pdf" class="form-control rounded-3" accept=".pdf">
          <small class="text-muted">Kosongkan jika tidak ingin mengganti PDF</small>
        </div>
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-warning rounded-3 fw-bold">
            <i class="bi bi-save me-2"></i>Simpan Perubahan
          </button>
          <a href="../detail_ebook.php?id=<?= $id ?>" class="btn btn-outline-info rounded-3" target="_blank">
            <i class="bi bi-eye me-2"></i>Preview Ebook
          </a>
        </div>
      </div>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/app.js"></script>
</body>
</html>
