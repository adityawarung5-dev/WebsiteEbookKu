<?php
require_once 'config/koneksi.php';

// Fetch categories with count
$kategoris = $conn->query("
    SELECT k.*, COUNT(e.id) as jumlah
    FROM kategori k
    LEFT JOIN ebooks e ON e.kategori_id = k.id
    GROUP BY k.id ORDER BY k.id
")->fetch_all(MYSQLI_ASSOC);

// Fetch featured/latest ebooks
$featured_ebooks = $conn->query("
    SELECT e.*, k.nama as kategori_nama, k.slug, k.icon
    FROM ebooks e
    JOIN kategori k ON k.id = e.kategori_id
    ORDER BY e.featured DESC, e.created_at DESC
    LIMIT 8
")->fetch_all(MYSQLI_ASSOC);

// Stats
$total_ebooks = $conn->query("SELECT COUNT(*) as c FROM ebooks")->fetch_assoc()['c'];
$total_users  = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='user'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EbookKu - Platform Baca Ebook Online</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php require_once 'includes/navbar.php'; ?>

<!-- ===================== HERO ===================== -->
<section class="hero-section">
  <div class="container position-relative" style="z-index:1">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="hero-badge">
          <i class="bi bi-stars"></i> Platform Ebook Terbaik
        </div>
        <h1 class="hero-title">
          Baca <span class="highlight">Ribuan</span> Ebook Berkualitas Online
        </h1>
        <p class="hero-subtitle">
          Akses koleksi ebook pilihan dalam berbagai kategori. Belajar, berkembang, dan raih kesuksesan bersama EbookKu.
        </p>

        <!-- Search Bar -->
        <form action="ebook.php" method="GET">
          <div class="search-bar-wrap">
            <i class="bi bi-search" style="color:rgba(255,255,255,.5)"></i>
            <input type="text" name="q" placeholder="Cari ebook, penulis, kategori..." id="searchHero" autocomplete="off">
            <button type="submit" class="btn-search">
              <i class="bi bi-search me-1"></i>Cari
            </button>
          </div>
        </form>

        <div class="hero-stats">
          <div class="hero-stat">
            <div class="hero-stat-number"><?= $total_ebooks ?>+</div>
            <div class="hero-stat-label">Koleksi Ebook</div>
          </div>
          <div class="hero-stat">
            <div class="hero-stat-number"><?= $total_users ?>+</div>
            <div class="hero-stat-label">Pengguna Aktif</div>
          </div>
          <div class="hero-stat">
            <div class="hero-stat-number">4</div>
            <div class="hero-stat-label">Kategori</div>
          </div>
        </div>
      </div>
      <div class="col-lg-6 d-none d-lg-flex justify-content-center">
        <div style="position:relative;display:inline-block">
          <div style="
            width:320px;height:400px;
            background:linear-gradient(135deg,rgba(108,99,255,.3),rgba(245,158,11,.2));
            border-radius:24px;border:1px solid rgba(255,255,255,.1);
            display:flex;align-items:center;justify-content:center;
            font-size:8rem;
          "><img src="logo1.png" width="500px" height="500px"></div>
          <div style="
            position:absolute;top:-16px;right:-16px;
            background:var(--secondary);color:#fff;
            border-radius:12px;padding:.6rem 1rem;
            font-weight:700;font-size:.85rem;
            box-shadow:0 8px 24px rgba(245,158,11,.4);
          ">✨ Gratis Baca!</div>
          <div style="
            position:absolute;bottom:-16px;left:-16px;
            background:rgba(255,255,255,.15);backdrop-filter:blur(10px);
            border:1px solid rgba(255,255,255,.2);color:#fff;
            border-radius:12px;padding:.6rem 1rem;
            font-size:.82rem;
          ">📖 <?= $total_ebooks ?> Ebook Tersedia</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===================== CATEGORIES ===================== -->
<section class="section-padding bg-surface">
  <div class="container">
    <div class="text-center mb-4">
      <h2 class="section-title">Kategori Ebook</h2>
      <p class="section-subtitle">Temukan ebook sesuai minat dan kebutuhanmu</p>
      <div class="section-divider mx-auto"></div>
    </div>
    <div class="row g-3 justify-content-center">
      <?php
      $cat_styles = [
          ['#fff8e6','#f59e0b'],
          ['#ecfdf5','#10b981'],
          ['#ede9ff','#6c63ff'],
          ['#fef2f2','#ef4444'],
      ];
      foreach ($kategoris as $i => $k):
          [$bg, $clr] = $cat_styles[$i % 4];
      ?>
      <div class="col-6 col-md-3">
        <a href="ebook.php?kategori=<?= e($k['slug']) ?>" class="category-card"
           style="--cat-color:<?= $clr ?>;--cat-bg:<?= $bg ?>">
          <div class="category-icon"><i class="<?= e($k['icon']) ?>"></i></div>
          <div class="category-name"><?= e($k['nama']) ?></div>
          <div class="category-count"><?= $k['jumlah'] ?> ebook</div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===================== LATEST EBOOKS ===================== -->
<section class="section-padding">
  <div class="container">
    <div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
      <div>
        <h2 class="section-title mb-1">Ebook Terbaru & Terpopuler</h2>
        <p class="section-subtitle mb-0">Koleksi pilihan terbaik untuk Anda</p>
        <div class="section-divider mb-0"></div>
      </div>
      <a href="ebook.php" class="btn-outline-main">
        Lihat Semua <i class="bi bi-arrow-right"></i>
      </a>
    </div>

    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3 g-md-4">
      <?php foreach ($featured_ebooks as $ebook): ?>
      <div class="col">
        <?php require 'includes/ebook_card.php'; ?>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if (empty($featured_ebooks)): ?>
    <div class="text-center py-5">
      <i class="bi bi-book" style="font-size:3rem;color:var(--border)"></i>
      <p class="text-muted mt-3">Belum ada ebook tersedia.</p>
      <?php if (isAdmin()): ?>
      <a href="admin/tambah_ebook.php" class="btn-primary-main">Upload Ebook Pertama</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ===================== CTA BANNER ===================== -->
<?php if (!isLoggedIn()): ?>
<section class="py-5" style="background:linear-gradient(135deg,var(--primary),#4338ca)">
  <div class="container text-center">
    <h2 class="font-display text-white fw-bold mb-3" style="font-size:2rem">
      Siap Mulai Membaca? 📚
    </h2>
    <p class="text-white mb-4" style="opacity:.8">
      Daftar gratis sekarang dan akses ribuan ebook berkualitas
    </p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="register.php" class="btn btn-warning btn-lg fw-bold px-4 rounded-3">
        <i class="bi bi-person-plus me-2"></i>Daftar Gratis
      </a>
      <a href="login.php" class="btn btn-outline-light btn-lg fw-semibold px-4 rounded-3">
        Sudah punya akun? Masuk
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
