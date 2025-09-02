<?php
// Ortak fonksiyonlar

require_once '../config/database.php';

/**
 * Kitap ekleme fonksiyonu
 */
function addBook($kitap_adi, $kitap_yazari, $yayin_evi, $alis_fiyati, $alis_tarihi, $okuma_tarihi = null) {
    try {
        $db = getDB();
        $sql = "INSERT INTO kitaplar (kitap_adi, kitap_yazari, yayin_evi, alis_fiyati, alis_tarihi, okuma_tarihi) 
                VALUES (:kitap_adi, :kitap_yazari, :yayin_evi, :alis_fiyati, :alis_tarihi, :okuma_tarihi)";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':kitap_adi', $kitap_adi);
        $stmt->bindParam(':kitap_yazari', $kitap_yazari);
        $stmt->bindParam(':yayin_evi', $yayin_evi);
        $stmt->bindParam(':alis_fiyati', $alis_fiyati);
        $stmt->bindParam(':alis_tarihi', $alis_tarihi);
        $stmt->bindParam(':okuma_tarihi', $okuma_tarihi);
        
        return $stmt->execute();
    } catch(PDOException $e) {
        error_log("Kitap ekleme hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * Kitap güncelleme fonksiyonu
 */
function updateBook($id, $kitap_adi, $kitap_yazari, $yayin_evi, $alis_fiyati, $alis_tarihi, $okuma_tarihi = null) {
    try {
        $db = getDB();
        $sql = "UPDATE kitaplar SET 
                kitap_adi = :kitap_adi, 
                kitap_yazari = :kitap_yazari, 
                yayin_evi = :yayin_evi, 
                alis_fiyati = :alis_fiyati, 
                alis_tarihi = :alis_tarihi, 
                okuma_tarihi = :okuma_tarihi 
                WHERE id = :id";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':kitap_adi', $kitap_adi);
        $stmt->bindParam(':kitap_yazari', $kitap_yazari);
        $stmt->bindParam(':yayin_evi', $yayin_evi);
        $stmt->bindParam(':alis_fiyati', $alis_fiyati);
        $stmt->bindParam(':alis_tarihi', $alis_tarihi);
        $stmt->bindParam(':okuma_tarihi', $okuma_tarihi);
        
        return $stmt->execute();
    } catch(PDOException $e) {
        error_log("Kitap güncelleme hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * Kitap silme fonksiyonu
 */
function deleteBook($id) {
    try {
        $db = getDB();
        $sql = "DELETE FROM kitaplar WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    } catch(PDOException $e) {
        error_log("Kitap silme hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * Kitap detayını getir
 */
function getBook($id) {
    try {
        $db = getDB();
        $sql = "SELECT * FROM kitaplar WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    } catch(PDOException $e) {
        error_log("Kitap getirme hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * Tüm kitapları listele
 */
function getAllBooks($limit = null, $offset = 0, $search = '') {
    try {
        $db = getDB();
        $sql = "SELECT * FROM kitaplar";
        
        if (!empty($search)) {
            $sql .= " WHERE kitap_adi LIKE :search OR kitap_yazari LIKE :search OR yayin_evi LIKE :search";
        }
        
        $sql .= " ORDER BY kayit_tarihi DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $db->prepare($sql);
        
        if (!empty($search)) {
            $searchTerm = '%' . $search . '%';
            $stmt->bindParam(':search', $searchTerm);
        }
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Kitap listeleme hatası: " . $e->getMessage());
        return [];
    }
}

/**
 * Toplam kitap sayısını getir
 */
function getTotalBooksCount($search = '') {
    try {
        $db = getDB();
        $sql = "SELECT COUNT(*) as total FROM kitaplar";
        
        if (!empty($search)) {
            $sql .= " WHERE kitap_adi LIKE :search OR kitap_yazari LIKE :search OR yayin_evi LIKE :search";
        }
        
        $stmt = $db->prepare($sql);
        
        if (!empty($search)) {
            $searchTerm = '%' . $search . '%';
            $stmt->bindParam(':search', $searchTerm);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    } catch(PDOException $e) {
        error_log("Kitap sayısı getirme hatası: " . $e->getMessage());
        return 0;
    }
}

/**
 * Aylık rapor verilerini getir
 */
function getMonthlyReport($year, $month) {
    try {
        $db = getDB();
        $sql = "SELECT 
                COUNT(*) as toplam_kitap,
                SUM(alis_fiyati) as toplam_harcama,
                AVG(alis_fiyati) as ortalama_fiyat,
                COUNT(CASE WHEN okuma_tarihi IS NOT NULL THEN 1 END) as okunan_kitap
                FROM kitaplar 
                WHERE YEAR(alis_tarihi) = :year AND MONTH(alis_tarihi) = :month";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':month', $month);
        $stmt->execute();
        
        return $stmt->fetch();
    } catch(PDOException $e) {
        error_log("Aylık rapor hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * Yıllık rapor verilerini getir
 */
function getYearlyReport($year) {
    try {
        $db = getDB();
        $sql = "SELECT 
                COUNT(*) as toplam_kitap,
                SUM(alis_fiyati) as toplam_harcama,
                AVG(alis_fiyati) as ortalama_fiyat,
                COUNT(CASE WHEN okuma_tarihi IS NOT NULL THEN 1 END) as okunan_kitap
                FROM kitaplar 
                WHERE YEAR(alis_tarihi) = :year";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        
        return $stmt->fetch();
    } catch(PDOException $e) {
        error_log("Yıllık rapor hatası: " . $e->getMessage());
        return false;
    }
}

/**
 * Tarih formatını düzenle
 */
function formatDate($date, $format = 'd.m.Y') {
    if (empty($date)) return '-';
    return date($format, strtotime($date));
}

/**
 * Fiyat formatını düzenle
 */
function formatPrice($price) {
    return number_format($price, 2, ',', '.') . ' TL';
}
?>