<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Admin girişi kontrolü
checkAdminLogin();

$success_message = '';
$error_message = '';

// Silme işlemi
if (isset($_GET['delete']) && isset($_GET['csrf_token'])) {
    if (validateCSRFToken($_GET['csrf_token'])) {
        $book_id = (int)$_GET['delete'];
        if (deleteBook($book_id)) {
            $success_message = 'Kitap başarıyla silindi.';
        } else {
            $error_message = 'Kitap silinirken bir hata oluştu.';
        }
    } else {
        $error_message = 'Güvenlik hatası!';
    }
}

// Sayfalama ve arama parametreleri
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;
$search = cleanInput($_GET['search'] ?? '');

// Kitapları getir
$books = getAllBooks($per_page, $offset, $search);
$total_books = getTotalBooksCount($search);
$total_pages = ceil($total_books / $per_page);

// CSRF token
$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitap Listesi - Kitap Kayıt Sistemi</title>
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
                <li><a href="list_books.php" class="active">Kitap Listesi</a></li>
                <li><a href="reports.php">Raporlar</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h2>Kitap Listesi</h2>
                <a href="add_book.php" class="btn btn-primary">Yeni Kitap Ekle</a>
            </div>

            <!-- Mesajlar -->
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Arama Formu -->
            <div class="search-container">
                <form method="GET" action="" class="search-form">
                    <div class="search-group">
                        <input type="text" name="search" placeholder="Kitap adı, yazar veya yayın evi ara..." 
                               value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                        <button type="submit" class="btn btn-primary">Ara</button>
                        <?php if ($search): ?>
                            <a href="list_books.php" class="btn btn-secondary">Temizle</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Kitap Listesi -->
            <?php if (!empty($books)): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kitap Adı</th>
                                <th>Yazar</th>
                                <th>Yayın Evi</th>
                                <th>Alış Fiyatı</th>
                                <th>Alış Tarihi</th>
                                <th>Okuma Tarihi</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?php echo $book['id']; ?></td>
                                    <td class="book-title"><?php echo htmlspecialchars($book['kitap_adi']); ?></td>
                                    <td><?php echo htmlspecialchars($book['kitap_yazari']); ?></td>
                                    <td><?php echo htmlspecialchars($book['yayin_evi']); ?></td>
                                    <td><?php echo formatPrice($book['alis_fiyati']); ?></td>
                                    <td><?php echo formatDate($book['alis_tarihi']); ?></td>
                                    <td><?php echo formatDate($book['okuma_tarihi']); ?></td>
                                    <td>
                                        <?php if ($book['okuma_tarihi']): ?>
                                            <span class="status-read">Okundu</span>
                                        <?php else: ?>
                                            <span class="status-unread">Okunmadı</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions">
                                        <a href="edit_book.php?id=<?php echo $book['id']; ?>" 
                                           class="btn btn-sm btn-primary" title="Düzenle">✏️</a>
                                        <a href="?delete=<?php echo $book['id']; ?>&csrf_token=<?php echo $csrf_token; ?>" 
                                           class="btn btn-sm btn-danger" title="Sil"
                                           onclick="return confirm('Bu kitabı silmek istediğinizden emin misiniz?')">🗑️</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Sayfalama -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo ($page - 1); ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                               class="btn btn-secondary">« Önceki</a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="btn btn-primary current"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                                   class="btn btn-secondary"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo ($page + 1); ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                               class="btn btn-secondary">Sonraki »</a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="pagination-info">
                        Toplam <?php echo $total_books; ?> kitaptan <?php echo (($page - 1) * $per_page + 1); ?>-<?php echo min($page * $per_page, $total_books); ?> arası gösteriliyor
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="empty-state">
                    <?php if ($search): ?>
                        <p>"<?php echo htmlspecialchars($search); ?>" araması için sonuç bulunamadı.</p>
                        <a href="list_books.php" class="btn btn-secondary">Tüm Kitapları Görüntüle</a>
                    <?php else: ?>
                        <p>Henüz kitap eklenmemiş.</p>
                        <a href="add_book.php" class="btn btn-primary">İlk Kitabı Ekle</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>