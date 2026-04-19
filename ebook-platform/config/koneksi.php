<?php
// =============================================
// config/koneksi.php
// Database Configuration
// =============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ebook_platform');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:2rem;color:red;">
        <h2>❌ Koneksi Database Gagal</h2>
        <p>' . $conn->connect_error . '</p>
    </div>');
}

$conn->set_charset('utf8mb4');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =============================================
// BASE URL — Fix untuk XAMPP Windows
// =============================================
define('BASE_PATH', '/ebook-platform'); // Sesuaikan jika nama folder berbeda

function base_url($path = '') {
    $path = ltrim($path, '/');
    return BASE_PATH . ($path ? '/' . $path : '');
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    // Jika url relatif (tidak diawali http), tambahkan base
    if (!preg_match('#^https?://#', $url)) {
        $url = base_url(ltrim($url, '/'));
    }
    header("Location: $url");
    exit;
}

function e($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function cover_url($cover) {
    if ($cover && file_exists(__DIR__ . '/../uploads/covers/' . $cover)) {
        return base_url('uploads/covers/' . $cover);
    }
    return base_url('assets/images/default-cover.jpg');
}

function time_ago($datetime) {
    $now  = new DateTime();
    $ago  = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->y > 0) return $diff->y . ' tahun lalu';
    if ($diff->m > 0) return $diff->m . ' bulan lalu';
    if ($diff->d > 0) return $diff->d . ' hari lalu';
    if ($diff->h > 0) return $diff->h . ' jam lalu';
    if ($diff->i > 0) return $diff->i . ' menit lalu';
    return 'Baru saja';
}