<?php
require_once '../config/koneksi.php';
if (!isLoggedIn() || !isAdmin()) {
    header("Location: /ebook-platform/login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header("Location: /ebook-platform/admin/ebook_list.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM ebooks WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$ebook = $stmt->get_result()->fetch_assoc();

if ($ebook) {
    // Hapus file PDF
    $pdf_path = __DIR__ . '/../uploads/ebooks/' . $ebook['file_pdf'];
    if ($ebook['file_pdf'] && file_exists($pdf_path)) {
        unlink($pdf_path);
    }

    // Hapus file cover
    $cover_path = __DIR__ . '/../uploads/covers/' . $ebook['cover'];
    if ($ebook['cover'] && file_exists($cover_path)) {
        unlink($cover_path);
    }

    // Hapus dari database
    $del = $conn->prepare("DELETE FROM ebooks WHERE id = ?");
    $del->bind_param('i', $id);
    $del->execute();
}

// Redirect langsung pakai path absolut
header("Location: /ebook-platform/admin/ebook_list.php?deleted=1");
exit;