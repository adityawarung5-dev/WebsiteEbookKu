<?php
require_once 'config/koneksi.php';

// Harus login
if (!isLoggedIn()) {
    http_response_code(403);
    exit('Forbidden');
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    http_response_code(400);
    exit('Bad Request');
}

$stmt = $conn->prepare("SELECT file_pdf FROM ebooks WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$ebook = $stmt->get_result()->fetch_assoc();

if (!$ebook) {
    http_response_code(404);
    exit('Not Found');
}

$file_path = __DIR__ . '/uploads/ebooks/' . $ebook['file_pdf'];

if (!file_exists($file_path)) {
    http_response_code(404);
    exit('File tidak ditemukan: ' . $ebook['file_pdf']);
}

$file_size = filesize($file_path);

// Kirim header yang benar
header('Content-Type: application/pdf');
header('Content-Length: ' . $file_size);
header('Accept-Ranges: bytes');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Bersihkan buffer
if (ob_get_level()) ob_end_clean();

// Kirim file
readfile($file_path);
exit;