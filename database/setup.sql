CREATE TABLE IF NOT EXISTS kitaplar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kitap_adi VARCHAR(255) NOT NULL,
    kitap_yazari VARCHAR(255) NOT NULL,
    yayin_evi VARCHAR(255) NOT NULL,
    alis_fiyati DECIMAL(10,2) NOT NULL,
    alis_tarihi DATE NOT NULL,
    okuma_tarihi DATE NULL,
    kayit_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_kitap_adi (kitap_adi),
    INDEX idx_yazar (kitap_yazari),
    INDEX idx_yayin_evi (yayin_evi),
    INDEX idx_alis_tarihi (alis_tarihi),
    INDEX idx_okuma_tarihi (okuma_tarihi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin kullanıcıları tablosu (gelecekte genişletilebilir)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan admin kullanıcısını ekle (yayla/mitos19)
INSERT INTO admin_users (username, password_hash) VALUES 
('yayla', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi') -- mitos19
ON DUPLICATE KEY UPDATE username = username;

-- Sistem ayarları tablosu
CREATE TABLE IF NOT EXISTS sistem_ayarlari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ayar_adi VARCHAR(100) NOT NULL UNIQUE,
    ayar_degeri TEXT,
    aciklama TEXT,
    guncelleme_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan sistem ayarları
INSERT INTO sistem_ayarlari (ayar_adi, ayar_degeri, aciklama) VALUES 
('site_baslik', 'Kitap Kayıt Sistemi', 'Site başlığı'),
('sayfa_basina_kayit', '20', 'Listeleme sayfalarında gösterilecek kayıt sayısı'),
('varsayilan_para_birimi', 'TL', 'Fiyatlar için varsayılan para birimi')
ON DUPLICATE KEY UPDATE ayar_adi = ayar_adi;

-- Örnek kitap verileri (test için)
INSERT INTO kitaplar (kitap_adi, kitap_yazari, yayin_evi, alis_fiyati, alis_tarihi, okuma_tarihi) VALUES 
('Suç ve Ceza', 'Fyodor Dostoyevski', 'İş Bankası Kültür Yayınları', 25.50, '2024-01-15', '2024-02-10'),
('1984', 'George Orwell', 'Can Yayınları', 18.75, '2024-01-20', NULL),
('Simyacı', 'Paulo Coelho', 'Alfa Yayınları', 22.00, '2024-02-05', '2024-02-25'),
('Satranç', 'Stefan Zweig', 'Türkiye İş Bankası Kültür Yayınları', 15.25, '2024-02-10', NULL),
('Kürk Mantolu Madonna', 'Sabahattin Ali', 'Yapı Kredi Yayınları', 12.50, '2024-01-25', '2024-02-15')
ON DUPLICATE KEY UPDATE id = id;