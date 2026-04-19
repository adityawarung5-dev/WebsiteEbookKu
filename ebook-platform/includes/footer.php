<?php // includes/footer.php ?>
<footer class="footer-main">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="footer-brand">Ebook<span>Ku</span></div>
        <p class="footer-desc">Platform baca ebook online terbaik. Ribuan koleksi ebook berkualitas untuk pengembangan diri dan karier Anda.</p>

        <!-- Social Media Icons -->
        <div class="d-flex gap-3 mt-3">

          <!-- Instagram -->
          <a href="https://instagram.com/ebookku" target="_blank" rel="noopener noreferrer"
             class="footer-social-btn" title="Instagram EbookKu">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
              <defs>
                <radialGradient id="ig-grad" cx="30%" cy="107%" r="150%">
                  <stop offset="0%" stop-color="#fdf497"/>
                  <stop offset="5%" stop-color="#fdf497"/>
                  <stop offset="45%" stop-color="#fd5949"/>
                  <stop offset="60%" stop-color="#d6249f"/>
                  <stop offset="90%" stop-color="#285AEB"/>
                </radialGradient>
              </defs>
              <rect width="24" height="24" rx="6" fill="url(#ig-grad)"/>
              <circle cx="12" cy="12" r="4.5" fill="none" stroke="white" stroke-width="1.8"/>
              <circle cx="17.2" cy="6.8" r="1.1" fill="white"/>
              <rect x="3" y="3" width="18" height="18" rx="6" fill="none" stroke="white" stroke-width="1.8"/>
            </svg>
          </a>

          <!-- TikTok -->
          <a href="https://tiktok.com/@ebookku" target="_blank" rel="noopener noreferrer"
             class="footer-social-btn" title="TikTok EbookKu">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
              <rect width="24" height="24" rx="6" fill="#010101"/>
              <path d="M17.5 6.5a3.5 3.5 0 01-3.5-3.5h-2.5v10.8a1.8 1.8 0 11-1.8-1.8c.17 0 .33.02.5.06V9.5a4.3 4.3 0 100 8.6 4.3 4.3 0 004.3-4.3V9.2a6 6 0 003.5 1.1V7.8a3.52 3.52 0 01-0.5-.02z"
                    fill="white"/>
              <path d="M17.5 6.5a3.5 3.5 0 01-3.5-3.5h-2.5v10.8a1.8 1.8 0 11-1.8-1.8c.17 0 .33.02.5.06V9.5a4.3 4.3 0 100 8.6 4.3 4.3 0 004.3-4.3V9.2a6 6 0 003.5 1.1V7.8a3.52 3.52 0 01-0.5-.02z"
                    fill="none" stroke="#69C9D0" stroke-width="0.3"/>
            </svg>
          </a>

          <!-- YouTube -->
          <a href="https://youtube.com/@ebookku" target="_blank" rel="noopener noreferrer"
             class="footer-social-btn" title="YouTube EbookKu">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
              <rect width="24" height="24" rx="6" fill="#FF0000"/>
              <path d="M19.6 8.2s-.2-1.3-.8-1.9c-.7-.8-1.6-.8-2-.8C14.4 5.4 12 5.4 12 5.4s-2.4 0-4.8.1c-.4 0-1.3 0-2 .8-.6.6-.8 1.9-.8 1.9S4.2 9.7 4.2 11v1.2c0 1.3.2 2.8.2 2.8s.2 1.3.8 1.9c.7.8 1.7.7 2.2.8C8.8 17.8 12 17.8 12 17.8s2.4 0 4.8-.2c.4 0 1.3 0 2-.8.6-.6.8-1.9.8-1.9s.2-1.5.2-2.8V11c0-1.3-.2-2.8-.2-2.8z"
                    fill="white" opacity="0.95"/>
              <polygon points="10.2,9.2 10.2,14.8 15.2,12" fill="#FF0000"/>
            </svg>
          </a>

        </div>
      </div>

      <div class="col-6 col-lg-2 offset-lg-2">
        <h6 class="text-white fw-700 mb-3" style="font-size:.85rem;letter-spacing:.5px;text-transform:uppercase;">Menu</h6>
        <a href="<?= base_url('index.php') ?>" class="footer-link">Beranda</a>
        <a href="<?= base_url('ebook.php') ?>" class="footer-link">Semua Ebook</a>
        <a href="<?= base_url('favorit.php') ?>" class="footer-link">Favorit Saya</a>
      </div>

      <div class="col-6 col-lg-2">
        <h6 class="text-white fw-700 mb-3" style="font-size:.85rem;letter-spacing:.5px;text-transform:uppercase;">Kategori</h6>
        <a href="<?= base_url('ebook.php?kategori=motivasi-belajar') ?>" class="footer-link">Motivasi Belajar</a>
        <a href="<?= base_url('ebook.php?kategori=bisnis-online') ?>" class="footer-link">Bisnis Online</a>
        <a href="<?= base_url('ebook.php?kategori=self-development') ?>" class="footer-link">Self Development</a>
        <a href="<?= base_url('ebook.php?kategori=digital-marketing') ?>" class="footer-link">Digital Marketing</a>
      </div>

      <div class="col-lg-2">
        <h6 class="text-white fw-700 mb-3" style="font-size:.85rem;letter-spacing:.5px;text-transform:uppercase;">Akun</h6>
        <a href="<?= base_url('login.php') ?>" class="footer-link">Masuk</a>
        <a href="<?= base_url('register.php') ?>" class="footer-link">Daftar</a>
      </div>
    </div>

    <div class="footer-bottom">
      &copy; <?= date('Y') ?> EbookKu. Dibuat dengan ❤️ untuk para pembaca Indonesia.
      <div class="d-flex justify-content-center gap-3 mt-2">
        <a href="https://instagram.com/ebookku" target="_blank" class="footer-link" style="display:inline;font-size:.8rem">Instagram</a>
        <span style="color:rgba(255,255,255,.2)">·</span>
        <a href="https://tiktok.com/@ebookku" target="_blank" class="footer-link" style="display:inline;font-size:.8rem">TikTok</a>
        <span style="color:rgba(255,255,255,.2)">·</span>
        <a href="https://youtube.com/@ebookku" target="_blank" class="footer-link" style="display:inline;font-size:.8rem">YouTube</a>
      </div>
    </div>
  </div>
</footer>

<style>
.footer-social-btn {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
    transition: all 0.3s ease;
    text-decoration: none;
}

.footer-social-btn:hover {
    transform: translateY(-3px);
    background: rgba(255,255,255,0.18);
    border-color: rgba(255,255,255,0.3);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}
</style>