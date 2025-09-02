<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Admin girişi kontrolü
checkAdminLogin();

$success_message = '';
$error_message = '';

// Form gönderildiğinde
if ($_POST) {
    // CSRF token kontrolü
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_message = 'Güvenlik hatası! Lütfen sayfayı yenileyin.';
    } else {
        // Form verilerini al ve temizle
        $kitap_adi = cleanInput($_POST['kitap_adi'] ?? '');
        $kitap_yazari = cleanInput($_POST['kitap_yazari'] ?? '');
        $yayin_evi = cleanInput($_POST['yayin_evi'] ?? '');
        $alis_fiyati = cleanInput($_POST['alis_fiyati'] ?? '');
        $alis_tarihi = cleanInput($_POST['alis_tarihi'] ?? '');
        $okuma_tarihi = cleanInput($_POST['okuma_tarihi'] ?? '');
        
        // Validasyon
        $errors = [];
        
        if (empty($kitap_adi)) {
            $errors[] = 'Kitap adı boş olamaz.';
        }
        
        if (empty($kitap_yazari)) {
            $errors[] = 'Kitap yazarı boş olamaz.';
        }
        
        if (empty($yayin_evi)) {
            $errors[] = 'Yayın evi boş olamaz.';
        }
        
        if (empty($alis_fiyati) || !validatePrice($alis_fiyati)) {
            $errors[] = 'Geçerli bir alış fiyatı giriniz.';
        }
        
        if (empty($alis_tarihi) || !validateDate($alis_tarihi)) {
            $errors[] = 'Geçerli bir alış tarihi giriniz.';
        }
        
        if (!empty($okuma_tarihi) && !validateDate($okuma_tarihi)) {
            $errors[] = 'Geçerli bir okuma tarihi giriniz.';
        }
        
        // Okuma tarihi alış tarihinden önce olamaz
        if (!empty($okuma_tarihi) && !empty($alis_tarihi) && $okuma_tarihi < $alis_tarihi) {
            $errors[] = 'Okuma tarihi alış tarihinden önce olamaz.';
        }
        
        if (empty($errors)) {
            // Kitabı veritabanına ekle
            $okuma_tarihi = empty($okuma_tarihi) ? null : $okuma_tarihi;
            
            if (addBook($kitap_adi, $kitap_yazari, $yayin_evi, $alis_fiyati, $alis_tarihi, $okuma_tarihi)) {
                $success_message = 'Kitap başarıyla eklendi!';
                // Formu temizle
                $_POST = [];
            } else {
                $error_message = 'Kitap eklenirken bir hata oluştu.';
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    }
}

// CSRF token oluştur
$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitap Ekle - Kitap Kayıt Sistemi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1>Kitap Kayıt Sistemi</h1>
            <div class="user-info">
                <span>Hoş geldiniz, <?php echo htmlspecialchars(getAdminUsername()); ?></span>
                <a href="dashboard.php?logout=1" class="logout-btn">Çıkış</a>
            </div>
        </header>

        <!-- Navigation -->
        <nav class="navigation">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="add_book.php" class="active">Kitap Ekle</a></li>
                <li><a href="list_books.php">Kitap Listesi</a></li>
                <li><a href="reports.php">Raporlar</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h2>Yeni Kitap Ekle</h2>
                <a href="list_books.php" class="btn btn-secondary">Kitap Listesine Dön</a>
            </div>

            <!-- Mesajlar -->
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Kitap Ekleme Formu -->
            <div class="form-container">
                <form method="POST" action="" class="book-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="kitap_adi">Kitap Adı *</label>
                            <input type="text" id="kitap_adi" name="kitap_adi" 
                                   value="<?php echo htmlspecialchars($_POST['kitap_adi'] ?? ''); ?>" 
                                   required maxlength="255">
                        </div>
                        
                        <div class="form-group">
                            <label for="kitap_yazari">Kitap Yazarı *</label>
                            <input type="text" id="kitap_yazari" name="kitap_yazari" 
                                   value="<?php echo htmlspecialchars($_POST['kitap_yazari'] ?? ''); ?>" 
                                   required maxlength="255">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="yayin_evi">Yayın Evi *</label>
                            <input type="text" id="yayin_evi" name="yayin_evi" 
                                   value="<?php echo htmlspecialchars($_POST['yayin_evi'] ?? ''); ?>" 
                                   required maxlength="255">
                        </div>
                        
                        <div class="form-group">
                            <label for="alis_fiyati">Alış Fiyatı (TL) *</label>
                            <input type="number" id="alis_fiyati" name="alis_fiyati" 
                                   value="<?php echo htmlspecialchars($_POST['alis_fiyati'] ?? ''); ?>" 
                                   step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="alis_tarihi">Alış Tarihi *</label>
                            <input type="date" id="alis_tarihi" name="alis_tarihi" 
                                   value="<?php echo htmlspecialchars($_POST['alis_tarihi'] ?? ''); ?>" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="okuma_tarihi">Okuma Tarihi</label>
                            <input type="date" id="okuma_tarihi" name="okuma_tarihi" 
                                   value="<?php echo htmlspecialchars($_POST['okuma_tarihi'] ?? ''); ?>">
                            <small class="form-help">Kitabı henüz okumadıysanız boş bırakabilirsiniz.</small>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Kitabı Ekle</button>
                        <button type="reset" class="btn btn-secondary">Formu Temizle</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Bugünün tarihini varsayılan olarak ayarla
        document.addEventListener('DOMContentLoaded', function() {
            const alisTarihiInput = document.getElementById('alis_tarihi');
            if (!alisTarihiInput.value) {
                const today = new Date().toISOString().split('T')[0];
                alisTarihiInput.value = today;
            }
        });
    </script>
</body>
</html>