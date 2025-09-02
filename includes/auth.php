<?php
// Session kontrolü ve yetkilendirme fonksiyonları

// Session başlat (eğer başlatılmamışsa)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Admin girişi kontrolü
 */
function checkAdminLogin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: ../index.php');
        exit();
    }
}

/**
 * Güvenli çıkış fonksiyonu
 */
function logout() {
    session_start();
    session_unset();
    session_destroy();
    header('Location: ../index.php');
    exit();
}

/**
 * Admin kullanıcı adını al
 */
function getAdminUsername() {
    return $_SESSION['admin_username'] ?? 'Admin';
}

/**
 * CSRF token oluştur
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF token doğrula
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Güvenli input temizleme
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Tarih formatını kontrol et
 */
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Fiyat formatını kontrol et
 */
function validatePrice($price) {
    return is_numeric($price) && $price >= 0;
}
?>