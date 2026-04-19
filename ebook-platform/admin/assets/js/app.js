/* =============================================
   assets/js/app.js
   EbookKu - Main JavaScript
   ============================================= */

// ---- TOAST NOTIFICATION ----
function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;

    const icons = {
        success: '<i class="bi bi-check-circle-fill me-2"></i>',
        danger:  '<i class="bi bi-x-circle-fill me-2"></i>',
        info:    '<i class="bi bi-info-circle-fill me-2"></i>',
    };

    const toastEl = document.createElement('div');
    toastEl.className = `toast toast-custom show align-items-center text-bg-${type} border-0 mb-2`;
    toastEl.setAttribute('role', 'alert');
    toastEl.innerHTML = `
        <div class="d-flex align-items-center px-1">
            ${icons[type] || ''}
            <div class="toast-body ps-0">${message}</div>
            <button type="button" class="btn-close btn-close-white ms-auto me-2" onclick="this.closest('.toast').remove()"></button>
        </div>`;
    toastContainer.appendChild(toastEl);
    setTimeout(() => toastEl.remove(), 3500);
}

// ---- FAVORITE TOGGLE ----
function toggleFavorit(ebookId, btn) {
    if (!btn) return;

    fetch('favorit_action.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'ebook_id=' + encodeURIComponent(ebookId)
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'login') {
            window.location.href = 'login.php?redirect=ebook.php';
            return;
        }
        const icon = btn.querySelector('i');
        if (data.status === 'added') {
            btn.classList.add('active');
            if (icon) { icon.className = 'bi bi-heart-fill'; }
            btn.classList.add('pulse-heart');
            showToast('❤️ Ebook berhasil ditambahkan ke favorit', 'success');
        } else if (data.status === 'removed') {
            btn.classList.remove('active');
            if (icon) { icon.className = 'bi bi-heart'; }
            showToast('Ebook dihapus dari favorit', 'info');
        }
        setTimeout(() => btn.classList.remove('pulse-heart'), 400);
    })
    .catch(() => showToast('Terjadi kesalahan. Coba lagi.', 'danger'));
}

// ---- SEARCH LIVE ----
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    let debounce;
    searchInput.addEventListener('input', () => {
        clearTimeout(debounce);
        debounce = setTimeout(() => {
            const q = searchInput.value.trim();
            if (q.length > 0) {
                window.location.href = `ebook.php?q=${encodeURIComponent(q)}`;
            }
        }, 600);
    });
}

// ---- CATEGORY FILTER ----
document.querySelectorAll('.filter-btn[data-cat]').forEach(btn => {
    btn.addEventListener('click', () => {
        const cat = btn.dataset.cat;
        const url = new URL(window.location.href);
        if (cat === 'all') {
            url.searchParams.delete('kategori');
        } else {
            url.searchParams.set('kategori', cat);
        }
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });
});

// ---- CONFIRM DELETE ----
document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
        const msg = el.dataset.confirm || 'Yakin ingin menghapus?';
        if (!confirm(msg)) e.preventDefault();
    });
});

// ---- ADMIN MOBILE SIDEBAR ----
const sidebarToggle = document.getElementById('sidebarToggle');
const adminSidebar = document.getElementById('adminSidebar');
if (sidebarToggle && adminSidebar) {
    sidebarToggle.addEventListener('click', () => adminSidebar.classList.toggle('show'));
}

// ---- COVER PREVIEW ----
const coverInput = document.getElementById('coverInput');
const coverPreview = document.getElementById('coverPreview');
if (coverInput && coverPreview) {
    coverInput.addEventListener('change', e => {
        const file = e.target.files[0];
        if (file) {
            const url = URL.createObjectURL(file);
            coverPreview.src = url;
            coverPreview.style.display = 'block';
        }
    });
}

// ---- SMOOTH SCROLL ----
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// ---- NAVBAR SCROLL EFFECT ----
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar-main');
    if (navbar) {
        if (window.scrollY > 20) {
            navbar.style.boxShadow = '0 2px 20px rgba(108,99,255,0.12)';
        } else {
            navbar.style.boxShadow = 'none';
        }
    }
});

// ---- ANIMATE CARDS ON SCROLL ----
const observerOptions = { threshold: 0.1 };
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in-up');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

document.querySelectorAll('.ebook-card, .category-card, .stat-card').forEach(el => {
    el.style.opacity = '0';
    observer.observe(el);
});
