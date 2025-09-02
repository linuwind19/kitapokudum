<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Admin girişi kontrolü
checkAdminLogin();

// Çıkış işlemi
if (isset($_GET['logout'])) {
    logout();
}

// İstatistikleri al
try {
    $db = getDB();
    
    // Toplam kitap sayısı
    $stmt = $db->query("SELECT COUNT(*) as total FROM kitaplar");
    $totalBooks = $stmt->fetch()['total'];
    
    // Bu ay eklenen kitaplar
    $stmt = $db->query("SELECT COUNT(*) as total FROM kitaplar WHERE MONTH(kayit_tarihi) = MONTH(CURRENT_DATE()) AND YEAR(kayit_tarihi) = YEAR(CURRENT_DATE())");
    $thisMonthBooks = $stmt->fetch()['total'];
    
    // Toplam harcama
    $stmt = $db->query("SELECT SUM(alis_fiyati) as total FROM kitaplar");
    $totalSpent = $stmt->fetch()['total'] ?? 0;
    
    // Okunan kitap sayısı
    $stmt = $db->query("SELECT COUNT(*) as total FROM kitaplar WHERE okuma_tarihi IS NOT NULL");
    $readBooks = $stmt->fetch()['total'];
    
    // Son eklenen kitaplar
    $recentBooks = getAllBooks(5);
    
} catch(Exception $e) {
    $totalBooks = $thisMonthBooks = $totalSpent = $readBooks = 0;
    $recentBooks = [];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kitap Kayıt Sistemi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1>Kitap Kayıt Sistemi</h1>
            <div class="user-info">
                <span>Hoş geldiniz, <?php echo htmlspecialchars(getAdminUsername()); ?></span>
                <a href="?logout=1" class="logout-btn">Çıkış</a>
            </div>
        </header>

        <!-- Navigation -->
        <nav class="navigation">
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="add_book.php">Kitap Ekle</a></li>
                <li><a href="list_books.php">Kitap Listesi</a></li>
                <li><a href="reports.php">Raporlar</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- İstatistik Kartları -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <div class="stat-info">
                        <h3><?php echo $totalBooks; ?></h3>
                        <p>Toplam Kitap</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div class="stat-info">
                        <h3><?php echo $thisMonthBooks; ?></h3>
                        <p>Bu Ay Eklenen</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <h3><?php echo formatPrice($totalSpent); ?></h3>
                        <p>Toplam Harcama</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-info">
                        <h3><?php echo $readBooks; ?></h3>
                        <p>Okunan Kitap</p>
                    </div>
                </div>
            </div>

            <!-- Son Eklenen Kitaplar -->
            <div class="recent-books">
                <h2>Son Eklenen Kitaplar</h2>
                <?php if (!empty($recentBooks)): ?>
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Kitap Adı</th>
                                    <th>Yazar</th>
                                    <th>Yayın Evi</th>
                                    <th>Alış Fiyatı</th>
                                    <th>Alış Tarihi</th>
                                    <th>Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentBooks as $book): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($book['kitap_adi']); ?></td>
                                        <td><?php echo htmlspecialchars($book['kitap_yazari']); ?></td>
                                        <td><?php echo htmlspecialchars($book['yayin_evi']); ?></td>
                                        <td><?php echo formatPrice($book['alis_fiyati']); ?></td>
                                        <td><?php echo formatDate($book['alis_tarihi']); ?></td>
                                        <td>
                                            <?php if ($book['okuma_tarihi']): ?>
                                                <span class="status-read">Okundu</span>
                                            <?php else: ?>
                                                <span class="status-unread">Okunmadı</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <a href="list_books.php" class="btn btn-primary">Tüm Kitapları Görüntüle</a>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>Henüz kitap eklenmemiş.</p>
                        <a href="add_book.php" class="btn btn-primary">İlk Kitabı Ekle</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>