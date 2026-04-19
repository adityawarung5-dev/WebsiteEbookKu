-- =============================================
-- DATABASE: ebook_platform
-- =============================================
CREATE DATABASE IF NOT EXISTS ebook_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ebook_platform;

-- ---------------------------------------------
-- TABLE: users
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------
-- TABLE: kategori
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(50) DEFAULT 'bi-book',
    warna VARCHAR(20) DEFAULT '#6c63ff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------
-- TABLE: ebooks
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS ebooks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    penulis VARCHAR(150) NOT NULL,
    deskripsi TEXT,
    kategori_id INT NOT NULL,
    cover VARCHAR(255) DEFAULT NULL,
    file_pdf VARCHAR(255) NOT NULL,
    halaman INT DEFAULT 0,
    tahun YEAR DEFAULT NULL,
    featured TINYINT(1) DEFAULT 0,
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------
-- TABLE: komentar
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS komentar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ebook_id INT NOT NULL,
    user_id INT NOT NULL,
    komentar TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ebook_id) REFERENCES ebooks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------
-- TABLE: favorit
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS favorit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ebook_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_favorit (user_id, ebook_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ebook_id) REFERENCES ebooks(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- SEED DATA
-- =============================================

-- Default Admin & User
INSERT INTO users (nama, email, password, role) VALUES
('Administrator', 'admin@ebookku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Budi Santoso', 'budi@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('Siti Rahayu', 'siti@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');
-- Password for all: password

-- Categories
INSERT INTO kategori (nama, slug, icon, warna) VALUES
('Motivasi Belajar', 'motivasi-belajar', 'bi-lightning-charge-fill', '#f59e0b'),
('Bisnis Online', 'bisnis-online', 'bi-graph-up-arrow', '#10b981'),
('Self Development', 'self-development', 'bi-person-fill-gear', '#6c63ff'),
('Digital Marketing', 'digital-marketing', 'bi-megaphone-fill', '#ef4444');

-- Sample Ebooks
INSERT INTO ebooks (judul, penulis, deskripsi, kategori_id, cover, file_pdf, halaman, tahun, featured, views) VALUES
('The Power of Habit', 'Charles Duhigg', 'Buku tentang kekuatan kebiasaan dan bagaimana mengubah hidup dengan membangun kebiasaan positif. Pelajari ilmu di balik kebiasaan dan bagaimana itu dapat diubah.', 3, 'cover1.jpg', 'sample.pdf', 371, 2012, 1, 1250),
('Zero to One', 'Peter Thiel', 'Catatan tentang startup dan cara membangun masa depan. Buku wajib bagi setiap entrepreneur yang ingin membangun bisnis dari nol.', 2, 'cover2.jpg', 'sample.pdf', 224, 2014, 1, 980),
('Atomic Habits', 'James Clear', 'Cara mudah dan terbukti untuk membangun kebiasaan baik dan menghilangkan kebiasaan buruk. Panduan praktis perubahan hidup 1% setiap hari.', 3, 'cover3.jpg', 'sample.pdf', 320, 2018, 1, 2100),
('Digital Marketing 101', 'Ryan Deiss', 'Panduan lengkap digital marketing untuk pemula hingga profesional. Kuasai SEO, SEM, Social Media, dan Email Marketing.', 4, 'cover4.jpg', 'sample.pdf', 285, 2020, 0, 760),
('Mindset: The New Psychology', 'Carol S. Dweck', 'Temukan bagaimana mindset mempengaruhi semua aspek kehidupan Anda dan bagaimana mengembangkan growth mindset untuk sukses.', 1, 'cover5.jpg', 'sample.pdf', 276, 2006, 1, 1580),
('Dropshipping Mastery', 'Anton Kraly', 'Panduan lengkap membangun bisnis dropshipping yang menguntungkan dari nol. Strategi terbukti untuk sukses di e-commerce.', 2, 'cover6.jpg', 'sample.pdf', 198, 2021, 0, 430),
('Social Media Marketing', 'Dave Kerpen', 'Strategi pemasaran media sosial yang efektif untuk bisnis modern. Pelajari cara membangun brand dan komunitas online.', 4, 'cover7.jpg', 'sample.pdf', 240, 2019, 0, 590),
('Think and Grow Rich', 'Napoleon Hill', 'Klasik motivasi yang telah mengubah jutaan kehidupan. Prinsip-prinsip kekayaan dan kesuksesan yang telah teruji waktu.', 1, 'cover8.jpg', 'sample.pdf', 302, 1937, 1, 3200);

-- Sample Comments
INSERT INTO komentar (ebook_id, user_id, komentar) VALUES
(1, 2, 'Buku yang sangat inspiratif! Mengubah cara pandang saya tentang kebiasaan sehari-hari.'),
(1, 3, 'Rekomendasi banget buat yang mau berubah. Penjelasannya mudah dipahami.'),
(3, 2, 'Atomic Habits adalah buku terbaik yang pernah saya baca tentang produktivitas!'),
(5, 3, 'Mindset Carol Dweck benar-benar membuka wawasan saya. Wajib baca!'),
(2, 2, 'Zero to One memberikan perspektif baru tentang inovasi dan bisnis startup.');

-- Sample Favorites
INSERT INTO favorit (user_id, ebook_id) VALUES
(2, 1), (2, 3), (2, 5),
(3, 2), (3, 4), (3, 8);
