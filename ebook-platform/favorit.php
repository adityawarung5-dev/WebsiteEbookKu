<?php
require_once 'config/koneksi.php';
if (!isLoggedIn()) redirect('login.php?redirect=favorit.php');

$user_id = (int)$_SESSION['user_id'];
$ebooks = $conn->query("
    SELECT e.*, k.nama as kategori_nama, k.slug, k.icon
    FROM favorit f
    JOIN ebooks e ON e.id = f.ebook_id
    JOIN kategori k ON k.id = e.kategori_id
    WHERE f.user_id = $user_id
    ORDER BY f.created_at DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Favorit Saya - EbookKu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php require_once 'includes/navbar.php'; ?>

<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
      <h1 class="section-title mb-1">
        <i class="bi bi-heart-fill me-2" style="color:var(--danger)"></i>Favorit Saya
      </h1>
      <p class="section-subtitle mb-0"><?= count($ebooks) ?> ebook tersimpan</p>
      <div class="section-divider mb-0"></div>
    </div>
    <a href="ebook.php" class="btn-outline-main">
      <i class="bi bi-search me-1"></i>Cari Ebook
    </a>
  </div>

  <?php if ($ebooks): ?>
  <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3 g-md-4">
    <?php foreach ($ebooks as $ebook): ?>
    <div class="col" id="fav-card-<?= $ebook['id'] ?>">
      <?php require 'includes/ebook_card.php'; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
  <div class="fav-empty">
    <i class="bi bi-heart" style="font-size:4rem;color:var(--border);display:block"></i>
    <h4 class="fw-bold mt-3">Belum Ada Favorit</h4>
    <p class="text-muted mb-4">Tambahkan ebook ke favorit dengan klik ikon ❤️ pada kartu ebook.</p>
    <a href="ebook.php" class="btn-primary-main">
      <i class="bi bi-book me-2"></i>Jelajahi Ebook
    </a>
  </div>
  <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
<script>
// On favorit page: remove card from DOM when unfavorited
const origToggle = window.toggleFavorit;
window.toggleFavorit = function(id, btn) {
    fetch('favorit_action.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'ebook_id=' + id
    }).then(r=>r.json()).then(data=>{
        if (data.status === 'removed') {
            const card = document.getElementById('fav-card-' + id);
            if (card) {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    card.remove();
                    // Check if empty
                    const grid = document.querySelector('.row');
                    if (grid && !grid.querySelector('.col')) {
                        location.reload();
                    }
                }, 300);
            }
            showToast('Ebook dihapus dari favorit', 'info');
        } else if (data.status === 'added') {
            showToast('❤️ Ebook berhasil ditambahkan ke favorit', 'success');
        }
    });
}
</script>
</body>
</html>
