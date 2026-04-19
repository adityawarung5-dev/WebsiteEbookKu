<?php
require_once 'config/koneksi.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['status' => 'login']);
    exit;
}

$ebook_id = (int)($_POST['ebook_id'] ?? 0);
$user_id  = (int)$_SESSION['user_id'];

if (!$ebook_id) {
    echo json_encode(['status' => 'error', 'msg' => 'Invalid ebook']);
    exit;
}

// Check if already favorited
$stmt = $conn->prepare("SELECT id FROM favorit WHERE user_id=? AND ebook_id=? LIMIT 1");
$stmt->bind_param('ii', $user_id, $ebook_id);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();

if ($existing) {
    $del = $conn->prepare("DELETE FROM favorit WHERE user_id=? AND ebook_id=?");
    $del->bind_param('ii', $user_id, $ebook_id);
    $del->execute();
    echo json_encode(['status' => 'removed']);
} else {
    $ins = $conn->prepare("INSERT INTO favorit (user_id, ebook_id) VALUES (?,?)");
    $ins->bind_param('ii', $user_id, $ebook_id);
    $ins->execute();
    echo json_encode(['status' => 'added']);
}
