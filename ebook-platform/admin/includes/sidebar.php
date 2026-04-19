<?php
// admin/includes/sidebar.php
$current_admin = basename($_SERVER['PHP_SELF']);
$nav_items = [
    ['dashboard.php', 'bi-speedometer2', 'Dashboard'],
    ['ebook_list.php', 'bi-book', 'Kelola Ebook'],
    ['tambah_ebook.php', 'bi-plus-circle', 'Tambah Ebook'],
    ['kategori.php', 'bi-tags', 'Kategori'],
    ['users.php', 'bi-people', 'Pengguna'],
    ['komentar.php', 'bi-chat-square', 'Komentar'],
];
?>
<div class="admin-sidebar" id="adminSidebar">
  <div class="admin-brand">Ebook<span>Ku</span> <small style="font-size:.65rem;opacity:.5;font-family:sans-serif">Admin</small></div>

  <nav>
    <?php foreach ($nav_items as [$file, $icon, $label]): ?>
    <a href="<?= $file ?>" class="admin-nav-link <?= $current_admin === $file ? 'active' : '' ?>">
      <i class="<?= $icon ?>"></i> <?= $label ?>
    </a>
    <?php endforeach; ?>

    <div style="border-top:1px solid rgba(255,255,255,.08);margin:1rem 0"></div>
    <a href="../index.php" class="admin-nav-link" target="_blank">
      <i class="bi bi-house"></i> Lihat Website
    </a>
    <a href="../logout.php" class="admin-nav-link" style="color:rgba(239,68,68,.7)">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </nav>
</div>
