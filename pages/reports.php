<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Admin girişi kontrolü
checkAdminLogin();

// Varsayılan değerler
$current_year = date('Y');
$current_month = date('n');

// Form parametreleri
$report_type = $_GET['type'] ?? 'monthly';
$selected_year = (int)($_GET['year'] ?? $current_year);
$selected_month = (int)($_GET['month'] ?? $current_month);

// Rapor verilerini al
$report_data = null;
$monthly_details = [];

if ($report_type === 'monthly') {
    $report_data = getMonthlyReport($selected_year, $selected_month);
    
    // Aylık detay verileri
    try {
        $db = getDB();
        $sql = "SELECT * FROM kitaplar 
                WHERE YEAR(alis_tarihi) = :year AND MONTH(alis_tarihi) = :month 
                ORDER BY alis_tarihi DESC";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':year', $selected_year);
        $stmt->bindParam(':month', $selected_month);
        $stmt->execute();
        $monthly_details = $stmt->fetchAll();
    } catch(Exception $e) {
        $monthly_details = [];
    }
} else {
    $report_data = getYearlyReport($selected_year);
    
    // Yıllık aylık dağılım
    try {
        $db = getDB();
        $sql = "SELECT 
                MONTH(alis_tarihi) as ay,
                COUNT(*) as kitap_sayisi,
                SUM(alis_fiyati) as toplam_harcama
                FROM kitaplar 
                WHERE YEAR(alis_tarihi) = :year 
                GROUP BY MONTH(alis_tarihi)
                ORDER BY MONTH(alis_tarihi)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':year', $selected_year);
        $stmt->execute();
        $monthly_breakdown = $stmt->fetchAll();
    } catch(Exception $e) {
        $monthly_breakdown = [];
    }
}

// Ay isimleri
$month_names = [
    1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
    5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
    9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raporlar - Kitap Kayıt Sistemi</title>
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
                <li><a href="add_book.php">Kitap Ekle</a></li>
                <li><a href="list_books.php">Kitap Listesi</a></li>
                <li><a href="reports.php" class="active">Raporlar</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h2>Raporlar</h2>
            </div>

            <!-- Rapor Filtreleri -->
            <div class="report-filters">
                <form method="GET" action="" class="filter-form">
                    <div class="filter-group">
                        <label for="type">Rapor Türü:</label>
                        <select name="type" id="type" onchange="toggleMonthField()">
                            <option value="monthly" <?php echo $report_type === 'monthly' ? 'selected' : ''; ?>>Aylık Rapor</option>
                            <option value="yearly" <?php echo $report_type === 'yearly' ? 'selected' : ''; ?>>Yıllık Rapor</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="year">Yıl:</label>
                        <select name="year" id="year">
                            <?php for ($y = $current_year; $y >= $current_year - 5; $y--): ?>
                                <option value="<?php echo $y; ?>" <?php echo $y === $selected_year ? 'selected' : ''; ?>>
                                    <?php echo $y; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group" id="month-group" <?php echo $report_type === 'yearly' ? 'style="display:none;"' : ''; ?>>
                        <label for="month">Ay:</label>
                        <select name="month" id="month">
                            <?php foreach ($month_names as $num => $name): ?>
                                <option value="<?php echo $num; ?>" <?php echo $num === $selected_month ? 'selected' : ''; ?>>
                                    <?php echo $name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Raporu Göster</button>
                </form>
            </div>

            <!-- Rapor Sonuçları -->
            <?php if ($report_data): ?>
                <div class="report-results">
                    <div class="report-header">
                        <h3>
                            <?php if ($report_type === 'monthly'): ?>
                                <?php echo $month_names[$selected_month] . ' ' . $selected_year; ?> Aylık Raporu
                            <?php else: ?>
                                <?php echo $selected_year; ?> Yıllık Raporu
                            <?php endif; ?>
                        </h3>
                        <button onclick="window.print()" class="btn btn-secondary">Yazdır</button>
                    </div>

                    <!-- Özet İstatistikler -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon">📚</div>
                            <div class="stat-info">
                                <h3><?php echo $report_data['toplam_kitap']; ?></h3>
                                <p>Toplam Kitap</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">💰</div>
                            <div class="stat-info">
                                <h3><?php echo formatPrice($report_data['toplam_harcama'] ?? 0); ?></h3>
                                <p>Toplam Harcama</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">📊</div>
                            <div class="stat-info">
                                <h3><?php echo formatPrice($report_data['ortalama_fiyat'] ?? 0); ?></h3>
                                <p>Ortalama Fiyat</p>
                            </div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon">✅</div>
                            <div class="stat-info">
                                <h3><?php echo $report_data['okunan_kitap']; ?></h3>
                                <p>Okunan Kitap</p>
                            </div>
                        </div>
                    </div>

                    <!-- Aylık Rapor Detayları -->
                    <?php if ($report_type === 'monthly' && !empty($monthly_details)): ?>
                        <div class="report-details">
                            <h4>Aylık Detaylar</h4>
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
                                        <?php foreach ($monthly_details as $book): ?>
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
                        </div>
                    <?php endif; ?>

                    <!-- Yıllık Rapor Aylık Dağılım -->
                    <?php if ($report_type === 'yearly' && !empty($monthly_breakdown)): ?>
                        <div class="report-details">
                            <h4>Aylık Dağılım</h4>
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Ay</th>
                                            <th>Kitap Sayısı</th>
                                            <th>Toplam Harcama</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($monthly_breakdown as $month_data): ?>
                                            <tr>
                                                <td><?php echo $month_names[$month_data['ay']]; ?></td>
                                                <td><?php echo $month_data['kitap_sayisi']; ?></td>
                                                <td><?php echo formatPrice($month_data['toplam_harcama']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>Seçilen dönem için veri bulunamadı.</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function toggleMonthField() {
            const typeSelect = document.getElementById('type');
            const monthGroup = document.getElementById('month-group');
            
            if (typeSelect.value === 'yearly') {
                monthGroup.style.display = 'none';
            } else {
                monthGroup.style.display = 'block';
            }
        }
    </script>
</body>
</html>