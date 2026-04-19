<?php
// includes/navbar.php
$current = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-main navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="<?= base_url('index.php') ?>">
      Ebook<span>Ku</span>
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto gap-1">
        <li class="nav-item">
          <a class="nav-link <?= $current === 'index.php' ? 'active' : '' ?>" href="<?= base_url('index.php') ?>">
            <i class="bi bi-house me-1"></i>Beranda
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current === 'ebook.php' ? 'active' : '' ?>" href="<?= base_url('ebook.php') ?>">
            <i class="bi bi-book me-1"></i>Ebook
          </a>
        </li>
        <?php if (isLoggedIn()): ?>
        <li class="nav-item">
          <a class="nav-link <?= $current === 'favorit.php' ? 'active' : '' ?>" href="<?= base_url('favorit.php') ?>">
            <i class="bi bi-heart me-1"></i>Favorit
          </a>
        </li>
        <?php endif; ?>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <?php if (isLoggedIn()): ?>
          <?php if (isAdmin()): ?>
          <a href="<?= base_url('admin/dashboard.php') ?>" class="nav-link" title="Admin Panel">
            <i class="bi bi-shield-check text-warning"></i>
          </a>
          <?php endif; ?>
          <div class="dropdown">
            <button class="btn btn-nav-register dropdown-toggle" type="button" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle me-1"></i><?= e($_SESSION['nama']) ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
              <li><span class="dropdown-item-text small text-muted"><?= e($_SESSION['email']) ?></span></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="<?= base_url('favorit.php') ?>"><i class="bi bi-heart me-2 text-danger"></i>Favorit Saya</a></li>
              <?php if (isAdmin()): ?>
              <li><a class="dropdown-item" href="<?= base_url('admin/dashboard.php') ?>"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Admin</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="<?= base_url('logout.php') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="<?= base_url('login.php') ?>" class="nav-link btn-nav-login">Masuk</a>
          <a href="<?= base_url('register.php') ?>" class="nav-link btn-nav-register">Daftar</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- Toast Container -->
<div class="toast-container-top" id="toastContainer"></div>
