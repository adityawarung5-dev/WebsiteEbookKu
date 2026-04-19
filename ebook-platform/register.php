<?php
require_once 'config/koneksi.php';
if (isLoggedIn()) redirect('index.php');

$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (!$nama || !$email || !$password || !$confirm) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Password dan konfirmasi tidak cocok.';
    } else {
        // Check email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Email sudah terdaftar.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $conn->prepare("INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
            $stmt2->bind_param('sss', $nama, $email, $hashed);
            if ($stmt2->execute()) {
                redirect('login.php?registered=1');
            } else {
                $error = 'Gagal mendaftar. Coba lagi.';
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
  <title>Daftar - EbookKu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="container">
    <div class="auth-card">
      <div class="auth-logo">Ebook<span>Ku</span></div>
      <p class="auth-subtitle">Buat akun gratis dan mulai baca ebook</p>

      <?php if ($error): ?>
      <div class="alert alert-danger alert-custom d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-exclamation-circle-fill"></i> <?= e($error) ?>
      </div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <div class="mb-3">
          <label class="form-label-custom">Nama Lengkap</label>
          <input type="text" name="nama" class="form-control-custom"
                 value="<?= e($_POST['nama'] ?? '') ?>" placeholder="Nama Anda" required>
        </div>
        <div class="mb-3">
          <label class="form-label-custom">Email</label>
          <input type="email" name="email" class="form-control-custom"
                 value="<?= e($_POST['email'] ?? '') ?>" placeholder="email@contoh.com" required>
        </div>
        <div class="mb-3">
          <label class="form-label-custom">Password</label>
          <div class="position-relative">
            <input type="password" name="password" id="passInput" class="form-control-custom"
                   placeholder="Minimal 6 karakter" required>
            <button type="button" class="btn border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y pe-3"
                    onclick="togglePass('passInput','ico1')" style="z-index:5">
              <i class="bi bi-eye" id="ico1" style="color:var(--text-muted)"></i>
            </button>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label-custom">Konfirmasi Password</label>
          <div class="position-relative">
            <input type="password" name="confirm_password" id="passInput2" class="form-control-custom"
                   placeholder="Ulangi password" required>
            <button type="button" class="btn border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y pe-3"
                    onclick="togglePass('passInput2','ico2')" style="z-index:5">
              <i class="bi bi-eye" id="ico2" style="color:var(--text-muted)"></i>
            </button>
          </div>
        </div>
        <button type="submit" class="btn-auth mb-3">
          <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
        </button>
      </form>

      <div class="text-center" style="font-size:.88rem;color:var(--text-muted)">
        Sudah punya akun? <a href="login.php" style="color:var(--primary);font-weight:600">Masuk</a>
      </div>
    </div>
  </div>
</div>
<script>
function togglePass(id, iconId) {
    const inp = document.getElementById(id);
    const ico = document.getElementById(iconId);
    if (inp.type === 'password') {
        inp.type = 'text'; ico.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password'; ico.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
