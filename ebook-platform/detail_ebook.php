<?php
require_once 'config/koneksi.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('ebook.php');

$stmt = $conn->prepare("
    SELECT e.*, k.nama as kategori_nama, k.slug, k.icon, k.warna
    FROM ebooks e JOIN kategori k ON k.id=e.kategori_id
    WHERE e.id=? LIMIT 1
");
$stmt->bind_param('i', $id);
$stmt->execute();
$ebook = $stmt->get_result()->fetch_assoc();
if (!$ebook) redirect('ebook.php');

// Update views
$conn->query("UPDATE ebooks SET views=views+1 WHERE id=$id");

// Handle comment submit
$comment_error = $comment_success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['komentar'])) {
    if (!isLoggedIn()) {
        $comment_error = 'Anda harus login untuk berkomentar.';
    } else {
        $komentar = trim($_POST['komentar']);
        if (!$komentar) {
            $comment_error = 'Komentar tidak boleh kosong.';
        } else {
            $uid = (int)$_SESSION['user_id'];
            $stmt2 = $conn->prepare("INSERT INTO komentar (ebook_id, user_id, komentar) VALUES (?,?,?)");
            $stmt2->bind_param('iis', $id, $uid, $komentar);
            $stmt2->execute();
            $comment_success = 'Komentar berhasil ditambahkan!';
        }
    }
}

// Fetch comments
$comments = $conn->query("
    SELECT k.*, u.nama as user_nama
    FROM komentar k JOIN users u ON u.id=k.user_id
    WHERE k.ebook_id=$id ORDER BY k.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

// Check favorite
$is_fav = false;
if (isLoggedIn()) {
    $uid = (int)$_SESSION['user_id'];
    $r = $conn->query("SELECT id FROM favorit WHERE user_id=$uid AND ebook_id=$id LIMIT 1");
    $is_fav = $r && $r->num_rows > 0;
}

// Related ebooks
$kat_id = (int)$ebook['kategori_id'];
$related = $conn->query("
    SELECT e.*, k.nama as kategori_nama, k.slug, k.icon
    FROM ebooks e JOIN kategori k ON k.id=e.kategori_id
    WHERE e.kategori_id=$kat_id AND e.id!=$id
    ORDER BY RAND() LIMIT 4
")->fetch_all(MYSQLI_ASSOC);

$badge_class_map = [
    'motivasi-belajar' => 'badge-motivasi',
    'bisnis-online'    => 'badge-bisnis',
    'self-development' => 'badge-self',
    'digital-marketing'=> 'badge-digital',
];
$badge_class = $badge_class_map[$ebook['slug']] ?? 'badge-self';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($ebook['judul']) ?> - EbookKu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php require_once 'includes/navbar.php'; ?>

<div class="container py-4">
  <!-- Breadcrumb -->
  <nav class="mb-4" style="font-size:.85rem">
    <a href="index.php" style="color:var(--text-muted);text-decoration:none">Beranda</a>
    <span class="mx-2 text-muted">/</span>
    <a href="ebook.php" style="color:var(--text-muted);text-decoration:none">Ebook</a>
    <span class="mx-2 text-muted">/</span>
    <span style="color:var(--primary)"><?= e($ebook['judul']) ?></span>
  </nav>

  <div class="row g-4 g-lg-5">
    <!-- Cover -->
    <div class="col-lg-3 text-center text-lg-start">
      <img src="<?= cover_url($ebook['cover']) ?>" alt="<?= e($ebook['judul']) ?>"
           class="detail-cover img-fluid mb-3">
      <div class="d-grid gap-2">
        <?php if (isLoggedIn()): ?>
        <a href="baca_ebook.php?id=<?= $ebook['id'] ?>" class="btn-primary-main justify-content-center">
          <i class="bi bi-book-open-fill"></i> Baca Sekarang
        </a>
        <?php else: ?>
        <a href="login.php?redirect=baca_ebook.php?id=<?= $ebook['id'] ?>" class="btn-primary-main justify-content-center">
          <i class="bi bi-lock-fill"></i> Login untuk Membaca
        </a>
        <?php endif; ?>
        <button class="btn-outline-main justify-content-center <?= $is_fav ? 'active' : '' ?>"
                id="favBtn" onclick="toggleFavorit(<?= $id ?>, this)">
          <i class="bi <?= $is_fav ? 'bi-heart-fill' : 'bi-heart' ?>" style="color:<?= $is_fav ? 'var(--danger)' : '' ?>"></i>
          <?= $is_fav ? 'Hapus Favorit' : 'Tambah Favorit' ?>
        </button>
      </div>
    </div>

    <!-- Info -->
    <div class="col-lg-9">
      <span class="ebook-badge <?= $badge_class ?> mb-2">
        <i class="<?= e($ebook['icon']) ?>"></i> <?= e($ebook['kategori_nama']) ?>
      </span>
      <h1 class="font-display fw-bold mb-1" style="font-size:clamp(1.5rem,3vw,2.2rem)">
        <?= e($ebook['judul']) ?>
      </h1>
      <p class="text-muted mb-3"><i class="bi bi-person me-1"></i><?= e($ebook['penulis']) ?></p>

      <!-- Meta -->
      <div class="card-admin mb-3">
        <div class="detail-meta-item">
          <span class="detail-meta-label"><i class="bi bi-layers me-2 text-primary"></i>Kategori</span>
          <span class="detail-meta-value"><?= e($ebook['kategori_nama']) ?></span>
        </div>
        <?php if ($ebook['halaman']): ?>
        <div class="detail-meta-item">
          <span class="detail-meta-label"><i class="bi bi-file-text me-2 text-primary"></i>Halaman</span>
          <span class="detail-meta-value"><?= $ebook['halaman'] ?> halaman</span>
        </div>
        <?php endif; ?>
        <?php if ($ebook['tahun']): ?>
        <div class="detail-meta-item">
          <span class="detail-meta-label"><i class="bi bi-calendar me-2 text-primary"></i>Tahun</span>
          <span class="detail-meta-value"><?= $ebook['tahun'] ?></span>
        </div>
        <?php endif; ?>
        <div class="detail-meta-item">
          <span class="detail-meta-label"><i class="bi bi-eye me-2 text-primary"></i>Dilihat</span>
          <span class="detail-meta-value"><?= number_format($ebook['views']) ?> kali</span>
        </div>
        <div class="detail-meta-item">
          <span class="detail-meta-label"><i class="bi bi-chat me-2 text-primary"></i>Komentar</span>
          <span class="detail-meta-value"><?= count($comments) ?> komentar</span>
        </div>
      </div>

      <!-- Description -->
      <?php if ($ebook['deskripsi']): ?>
      <div class="card-admin mb-3">
        <h5 class="fw-bold mb-3" style="font-family:'Fraunces',serif">Deskripsi</h5>
        <p style="color:var(--text-muted);line-height:1.8;font-size:.93rem"><?= nl2br(e($ebook['deskripsi'])) ?></p>
      </div>
      <?php endif; ?>

      <!-- Comments Section -->
      <div class="card-admin">
        <h5 class="fw-bold mb-3" style="font-family:'Fraunces',serif">
          <i class="bi bi-chat-square-text me-2" style="color:var(--primary)"></i>
          Komentar (<?= count($comments) ?>)
        </h5>

        <?php if ($comment_error): ?>
        <div class="alert alert-danger alert-custom mb-3"><?= e($comment_error) ?></div>
        <?php endif; ?>
        <?php if ($comment_success): ?>
        <div class="alert alert-success alert-custom mb-3"><?= e($comment_success) ?></div>
        <?php endif; ?>

        <!-- Comment Form -->
        <?php if (isLoggedIn()): ?>
        <form method="POST" class="mb-4">
          <div class="d-flex gap-3 align-items-start">
            <div class="comment-avatar flex-shrink-0">
              <?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?>
            </div>
            <div class="flex-grow-1">
              <textarea name="komentar" rows="3" class="form-control-custom w-100"
                        placeholder="Tulis komentar Anda..." style="resize:vertical"></textarea>
              <button type="submit" class="btn-primary-main mt-2" style="padding:.5rem 1.4rem">
                <i class="bi bi-send me-1"></i>Kirim
              </button>
            </div>
          </div>
        </form>
        <?php else: ?>
        <div class="alert alert-custom mb-3" style="background:var(--primary-light);color:var(--primary)">
          <i class="bi bi-info-circle me-2"></i>
          <a href="login.php" style="color:var(--primary);font-weight:600">Login</a> untuk menulis komentar.
        </div>
        <?php endif; ?>

        <!-- Comments List -->
        <?php if ($comments): ?>
          <?php foreach ($comments as $c): ?>
          <div class="comment-item">
            <div class="d-flex align-items-center gap-3 mb-2">
              <div class="comment-avatar">
                <?= strtoupper(substr($c['user_nama'], 0, 1)) ?>
              </div>
              <div>
                <div class="comment-username"><?= e($c['user_nama']) ?></div>
                <div class="comment-time"><?= time_ago($c['created_at']) ?></div>
              </div>
            </div>
            <p class="comment-text mb-0"><?= nl2br(e($c['komentar'])) ?></p>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center text-muted py-3">Belum ada komentar. Jadilah yang pertama!</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Related Ebooks -->
  <?php if ($related): ?>
  <div class="mt-5">
    <h3 class="section-title mb-1">Ebook Terkait</h3>
    <div class="section-divider"></div>
    <div class="row row-cols-2 row-cols-md-4 g-3">
      <?php foreach ($related as $ebook): ?>
      <div class="col">
        <?php require 'includes/ebook_card.php'; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script>
// Update fav button text after toggle
const origToggle = toggleFavorit;
window.toggleFavorit = function(id, btn) {
    fetch('favorit_action.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'ebook_id=' + id
    }).then(r=>r.json()).then(data=>{
        if (data.status === 'login') { window.location.href = 'login.php'; return; }
        const icon = btn.querySelector('i');
        if (data.status === 'added') {
            icon.className = 'bi bi-heart-fill';
            icon.style.color = 'var(--danger)';
            btn.innerHTML = icon.outerHTML + ' Hapus Favorit';
            showToast('❤️ Ebook berhasil ditambahkan ke favorit', 'success');
        } else {
            icon.className = 'bi bi-heart';
            icon.style.color = '';
            btn.innerHTML = icon.outerHTML + ' Tambah Favorit';
            showToast('Ebook dihapus dari favorit', 'info');
        }
    });
}
</script>
</body>
</html>
