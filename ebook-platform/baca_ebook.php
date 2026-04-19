<?php
require_once 'config/koneksi.php';
if (!isLoggedIn()) redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('ebook.php');

$stmt = $conn->prepare("SELECT e.*, k.nama as kategori_nama FROM ebooks e JOIN kategori k ON k.id=e.kategori_id WHERE e.id=? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$ebook = $stmt->get_result()->fetch_assoc();
if (!$ebook) redirect('ebook.php');

// Gunakan proxy untuk hindari MIME/204 error
$pdf_url = base_url('pdf_proxy.php?id=' . $ebook['id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>📖 <?= e($ebook['judul']) ?> - EbookKu</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body { overflow-x: hidden; }
    #viewer-wrapper {
      padding-bottom: 70px; /* space for controls */
      background: #404040;
      min-height: calc(100vh - 56px);
    }
    #pdf-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 1rem;
      gap: 8px;
    }
    #pdf-container canvas {
      max-width: 100%;
      box-shadow: 0 2px 16px rgba(0,0,0,0.4);
      display: block;
    }
    .reader-loading {
      color: rgba(255,255,255,0.6);
      text-align: center;
      padding: 3rem;
      font-size: 1rem;
    }
    .reader-error {
      color: #fca5a5;
      text-align: center;
      padding: 3rem;
    }
    #zoom-level {
      color: rgba(255,255,255,0.7);
      font-size: 0.82rem;
      min-width: 50px;
      text-align: center;
    }
    .reader-controls {
      display: flex;
      flex-wrap: wrap;
      gap: 0.5rem;
    }
  </style>
</head>
<body style="background:#404040;margin:0">

<!-- Reader Header -->
<div class="reader-header">
  <div class="container-fluid d-flex align-items-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3 min-w-0">
      <a href="detail_ebook.php?id=<?= $id ?>" class="btn btn-sm btn-outline-secondary border-0"
         style="color:rgba(255,255,255,.7)" title="Kembali">
        <i class="bi bi-arrow-left"></i>
      </a>
      <div class="min-w-0">
        <p class="reader-title text-truncate mb-0"><?= e($ebook['judul']) ?></p>
        <small style="color:rgba(255,255,255,0.45);font-size:.75rem"><?= e($ebook['penulis']) ?></small>
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button class="reader-btn" onclick="zoomOut()" title="Perkecil"><i class="bi bi-zoom-out"></i></button>
      <span id="zoom-level">100%</span>
      <button class="reader-btn" onclick="zoomIn()" title="Perbesar"><i class="bi bi-zoom-in"></i></button>
    </div>
  </div>
</div>

<!-- PDF Viewer -->
<div id="viewer-wrapper">
  <div id="pdf-container">
    <div class="reader-loading" id="loadingMsg">
      <div class="spinner-border text-light mb-3" role="status"></div>
      <br>Memuat ebook...
    </div>
  </div>
</div>

<!-- Bottom Controls -->
<div class="reader-controls" style="position:fixed;bottom:0;left:0;right:0;background:#1a1929;border-top:1px solid rgba(255,255,255,.08);padding:.75rem 1rem;justify-content:center;align-items:center;gap:.75rem;z-index:100">
  <button class="reader-btn" id="btnFirst" onclick="goToPage(1)" title="Halaman Pertama">
    <i class="bi bi-skip-backward-fill"></i>
  </button>
  <button class="reader-btn" id="btnPrev" onclick="prevPage()">
    <i class="bi bi-chevron-left me-1"></i>Prev
  </button>
  <div class="page-info">
    <span id="pageNum">-</span> / <span id="pageCount">-</span>
  </div>
  <button class="reader-btn" id="btnNext" onclick="nextPage()">
    Next <i class="bi bi-chevron-right ms-1"></i>
  </button>
  <button class="reader-btn" id="btnLast" onclick="goToPageLast()" title="Halaman Terakhir">
    <i class="bi bi-skip-forward-fill"></i>
  </button>
  <input type="number" id="pageInput" min="1" placeholder="Hal"
         style="width:65px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:#fff;border-radius:8px;padding:.4rem .6rem;font-size:.82rem;text-align:center"
         onkeydown="if(event.key==='Enter')goToPage(parseInt(this.value))">
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
const PDF_URL = <?= json_encode($pdf_url) ?>;
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

let pdfDoc = null;
let currentPage = 1;
let totalPages = 0;
let scale = 1.2;
let rendering = false;

const container = document.getElementById('pdf-container');
const loadingMsg = document.getElementById('loadingMsg');

async function loadPDF() {
    try {
        const loadingTask = pdfjsLib.getDocument({
            url: PDF_URL,
            withCredentials: true   // ← kirim session cookie ke proxy
        });
        pdfDoc = await loadingTask.promise;
        totalPages = pdfDoc.numPages;
        document.getElementById('pageCount').textContent = totalPages;
        if (loadingMsg) loadingMsg.remove();
        await renderPage(currentPage);
    } catch (err) {
        console.error('PDF Error:', err);
        if (loadingMsg) {
            loadingMsg.className = 'reader-error';
            loadingMsg.innerHTML = `
                <i class="bi bi-exclamation-triangle" style="font-size:2.5rem;display:block;margin-bottom:1rem"></i>
                <strong>Gagal memuat PDF</strong><br>
                <small>Pastikan file PDF tersedia di server.</small><br>
                <small style="opacity:.6">${err.message}</small>`;
        }
    }
}

async function renderPage(num) {
    if (rendering) return;
    rendering = true;

    // Remove all canvases
    container.querySelectorAll('canvas').forEach(c => c.remove());

    const page = await pdfDoc.getPage(num);
    const viewport = page.getViewport({ scale });
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    canvas.height = viewport.height;
    canvas.width = viewport.width;
    container.appendChild(canvas);

    await page.render({ canvasContext: ctx, viewport }).promise;

    currentPage = num;
    document.getElementById('pageNum').textContent = num;
    document.getElementById('pageInput').value = num;

    // Button states
    document.getElementById('btnPrev').disabled = num <= 1;
    document.getElementById('btnFirst').disabled = num <= 1;
    document.getElementById('btnNext').disabled = num >= totalPages;
    document.getElementById('btnLast').disabled = num >= totalPages;

    window.scrollTo({ top: 0, behavior: 'smooth' });
    rendering = false;
}

function prevPage() {
    if (currentPage > 1) renderPage(currentPage - 1);
}

function nextPage() {
    if (currentPage < totalPages) renderPage(currentPage + 1);
}

function goToPage(n) {
    n = parseInt(n);
    if (n >= 1 && n <= totalPages) renderPage(n);
}

function goToPageLast() {
    renderPage(totalPages);
}

function zoomIn() {
    if (scale < 3) {
        scale = Math.round((scale + 0.2) * 10) / 10;
        document.getElementById('zoom-level').textContent = Math.round(scale * 100) + '%';
        renderPage(currentPage);
    }
}

function zoomOut() {
    if (scale > 0.6) {
        scale = Math.round((scale - 0.2) * 10) / 10;
        document.getElementById('zoom-level').textContent = Math.round(scale * 100) + '%';
        renderPage(currentPage);
    }
}

// Keyboard navigation
document.addEventListener('keydown', e => {
    if (e.key === 'ArrowRight' || e.key === 'ArrowDown') nextPage();
    if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   prevPage();
});

loadPDF();
</script>
</body>
</html>
