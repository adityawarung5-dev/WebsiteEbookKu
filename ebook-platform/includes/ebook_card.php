<?php
// includes/ebook_card.php
// Required: $ebook array, $conn, optional: $user_id
if (!isset($ebook)) return;

$badge_class_map = [
    'motivasi-belajar' => 'badge-motivasi',
    'bisnis-online'    => 'badge-bisnis',
    'self-development' => 'badge-self',
    'digital-marketing'=> 'badge-digital',
];
$badge_class = $badge_class_map[$ebook['slug'] ?? ''] ?? 'badge-self';

$is_fav = false;
$user_id = $_SESSION['user_id'] ?? null;
if ($user_id) {
    $eid = (int)$ebook['id'];
    $uid = (int)$user_id;
    $r = $conn->query("SELECT id FROM favorit WHERE user_id=$uid AND ebook_id=$eid LIMIT 1");
    $is_fav = $r && $r->num_rows > 0;
}
?>
<div class="ebook-card h-100">
  <div class="ebook-card-cover">
    <img src="<?= cover_url($ebook['cover']) ?>" alt="<?= e($ebook['judul']) ?>" loading="lazy">
    <div class="ebook-card-overlay">
      <a href="<?= base_url('detail_ebook.php?id=' . $ebook['id']) ?>" class="btn-read">
        <i class="bi bi-eye me-1"></i>Lihat Detail
      </a>
    </div>
    <button
      class="btn-favorite <?= $is_fav ? 'active' : '' ?>"
      onclick="toggleFavorit(<?= $ebook['id'] ?>, this)"
      title="<?= $is_fav ? 'Hapus dari Favorit' : 'Tambah ke Favorit' ?>">
      <i class="bi <?= $is_fav ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
    </button>
  </div>
  <div class="ebook-card-body">
    <span class="ebook-badge <?= $badge_class ?>">
      <i class="<?= e($ebook['icon'] ?? 'bi-book') ?>"></i>
      <?= e($ebook['kategori_nama'] ?? 'Umum') ?>
    </span>
    <h3 class="ebook-title"><?= e($ebook['judul']) ?></h3>
    <p class="ebook-author"><i class="bi bi-person me-1"></i><?= e($ebook['penulis']) ?></p>
    <div class="ebook-meta">
      <span class="ebook-views">
        <i class="bi bi-eye"></i> <?= number_format($ebook['views']) ?>
      </span>
      <a href="<?= base_url('detail_ebook.php?id=' . $ebook['id']) ?>" class="btn-detail">
        Detail <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
  </div>
</div>
