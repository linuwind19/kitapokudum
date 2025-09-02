# Kitap Kayıt Sistemi

Linux web sunucusunda çalışan PHP tabanlı kitap kayıt ve takip otomasyonu.

## Özellikler

- ✅ Admin giriş sistemi
- ✅ Kitap ekleme, düzenleme, silme
- ✅ Kitap listeleme ve arama
- ✅ Aylık ve yıllık raporlar
- ✅ Responsive tasarım
- ✅ Güvenli form işlemleri
- ✅ Session yönetimi

## Sistem Gereksinimleri

- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Apache/Nginx web sunucusu
- PDO MySQL extension

## Kurulum

### 1. Dosyaları Sunucuya Yükleyin

Tüm proje dosyalarını web sunucunuzun document root klasörüne yükleyin.

### 2. Veritabanını Oluşturun

```sql
-- MySQL'de aşağıdaki komutu çalıştırın:
source /path/to/database/setup.sql
```

Veya `database/setup.sql` dosyasının içeriğini MySQL'de manuel olarak çalıştırın.

### 3. Veritabanı Bağlantı Ayarları

`config/database.php` dosyasındaki veritabanı bilgilerini düzenleyin:

```php
define('DB_HOST', 'localhost');     // Veritabanı sunucusu
define('DB_NAME', 'kitap_kayit_db'); // Veritabanı adı
define('DB_USER', 'root');           // Kullanıcı adı
define('DB_PASS', '');               // Şifre
```

### 4. Dosya İzinleri

Linux sunucusunda gerekli izinleri verin:

```bash
chmod -R 755 /path/to/project
chown -R www-data:www-data /path/to/project
```

## Admin Giriş Bilgileri

- **Kullanıcı Adı:** yayla
- **Şifre:** mitos19

## Kullanım

### Kitap Ekleme

1. Admin paneline giriş yapın
2. "Kitap Ekle" menüsüne tıklayın
3. Kitap bilgilerini doldurun:
   - Kitap Adı (zorunlu)
   - Kitap Yazarı (zorunlu)
   - Yayın Evi (zorunlu)
   - Alış Fiyatı (zorunlu)
   - Alış Tarihi (zorunlu)
   - Okuma Tarihi (opsiyonel)

### Kitap Yönetimi

- **Listeleme:** "Kitap Listesi" menüsünden tüm kitapları görüntüleyin
- **Arama:** Kitap adı, yazar veya yayın evine göre arama yapın
- **Düzenleme:** Kitap satırındaki düzenle butonuna tıklayın
- **Silme:** Kitap satırındaki sil butonuna tıklayın

### Raporlar

- **Aylık Rapor:** Belirli bir ay için kitap ve harcama raporu
- **Yıllık Rapor:** Belirli bir yıl için toplam istatistikler
- **Yazdırma:** Raporları yazdırabilirsiniz

## Güvenlik Özellikleri

- ✅ CSRF token koruması
- ✅ SQL injection koruması (PDO prepared statements)
- ✅ XSS koruması (htmlspecialchars)
- ✅ Session güvenliği
- ✅ Input validasyonu
- ✅ Admin yetkilendirme kontrolü

## Veritabanı Yapısı

### kitaplar tablosu
- `id` - Birincil anahtar
- `kitap_adi` - Kitap adı
- `kitap_yazari` - Yazar adı
- `yayin_evi` - Yayın evi
- `alis_fiyati` - Alış fiyatı (DECIMAL)
- `alis_tarihi` - Alış tarihi
- `okuma_tarihi` - Okuma tarihi (NULL olabilir)
- `kayit_tarihi` - Kayıt zamanı
- `guncelleme_tarihi` - Son güncelleme zamanı

## Dosya Yapısı

```
kitapkaydet2025/
├── index.php              # Giriş sayfası
├── config/
│   └── database.php       # Veritabanı bağlantısı
├── includes/
│   ├── auth.php          # Yetkilendirme fonksiyonları
│   └── functions.php     # Ortak fonksiyonlar
├── pages/
│   ├── dashboard.php     # Ana sayfa
│   ├── add_book.php      # Kitap ekleme
│   ├── list_books.php    # Kitap listeleme
│   ├── edit_book.php     # Kitap düzenleme
│   └── reports.php       # Raporlar
├── assets/
│   └── css/
│       └── style.css     # CSS stilleri
├── database/
│   └── setup.sql         # Veritabanı kurulum scripti
└── README.md             # Bu dosya
```

### Sorun Giderme

### Veritabanı Bağlantı Hatası
- `config/database.php` dosyasındaki bilgileri kontrol edin
- MySQL servisinin çalıştığından emin olun
- Kullanıcı izinlerini kontrol edin

### Session Hatası
- PHP session ayarlarını kontrol edin
- Sunucuda session klasörü izinlerini kontrol edin

### CSS/JS Yüklenmiyor
- Dosya yollarını kontrol edin
- Web sunucusu static dosya servis ayarlarını kontrol edin

## Geliştirici Notları

- Tüm formlar CSRF token ile korunmaktadır
- Veritabanı işlemleri PDO ile prepared statements kullanır
- Responsive tasarım mobil uyumludur
- Print CSS ile raporlar yazdırılabilir

## Lisans

Bu proje açık kaynak kodludur ve MIT lisansı altında dağıtılmaktadır.

## İletişim

Sorularınız için sistem yöneticisi ile iletişime geçin.

---

**Not:** Üretim ortamında kullanmadan önce güvenlik ayarlarını gözden geçirin ve gerekli güncellemeleri yapın.