<?php
// Veritabanı bağlantı ayarları
define('DB_HOST', 'localhost');
define('DB_NAME', 'kddbdb');
define('DB_USER', 'kddbuser');
define('DB_PASS', '625000');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    public $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Bağlantı hatası: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}

// Global veritabanı bağlantısı
function getDB() {
    static $database = null;
    if ($database === null) {
        $database = new Database();
    }
    return $database->getConnection();
}
?>