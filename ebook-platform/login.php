<?php
require_once 'config/koneksi.php';
if (isLoggedIn()) redirect('index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];

            $redirect = $_GET['redirect'] ?? 'index.php';
            redirect($redirect);
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk - EbookKu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-page">
  <div class="container">
    <div class="auth-card">
      <div class="auth-logo">Ebook<span>Ku</span></div>
      <p class="auth-subtitle">Masuk ke akun Anda untuk membaca ebook</p>

      <?php if ($error): ?>
      <div class="alert alert-danger alert-custom d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="bi bi-exclamation-circle-fill"></i> <?= e($error) ?>
      </div>
      <?php endif; ?>

      <?php if (isset($_GET['registered'])): ?>
      <div class="alert alert-success alert-custom d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-check-circle-fill"></i> Registrasi berhasil! Silakan masuk.
      </div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <div class="mb-3">
          <label class="form-label-custom">Email</label>
          <div class="position-relative">
            <input type="email" name="email" class="form-control-custom ps-4"
                   value="<?= e($_POST['email'] ?? '') ?>"
                   placeholder="email@contoh.com" required>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label-custom">Password</label>
          <div class="position-relative">
            <input type="password" name="password" id="passInput" class="form-control-custom"
                   placeholder="••••••••" required>
            <button type="button" class="btn border-0 bg-transparent position-absolute end-0 top-50 translate-middle-y pe-3"
                    onclick="togglePass()" style="z-index:5">
              <i class="bi bi-eye" id="passIcon" style="color:var(--text-muted)"></i>
            </button>
          </div>
        </div>
        <button type="submit" class="btn-auth mb-3">
          <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
        </button>
      </form>

      <div class="text-center" style="font-size:.88rem;color:var(--text-muted)">
        Belum punya akun? <a href="register.php" style="color:var(--primary);font-weight:600">Daftar Sekarang</a>
      </div>
    </div>
  </div>
</div>
<script>
function togglePass() {
    const inp = document.getElementById('passInput');
    const ico = document.getElementById('passIcon');
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'bi bi-eye-slash';
    } else {
        inp.type = 'password';
        ico.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
