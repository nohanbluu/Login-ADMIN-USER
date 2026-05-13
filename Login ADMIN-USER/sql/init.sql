<?php
/**
 * KONFIGURASI DATABASE - Railway MySQL
 * Railway otomatis inject env vars: MYSQLHOST, MYSQLUSER, MYSQLDATABASE, dll.
 */

define('APP_NAME', 'SecureSystem');
define('APP_VERSION', '1.0');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        // Railway inject variabel ini secara otomatis
        $host = getenv('MYSQLHOST')     ?: getenv('DB_HOST')     ?: 'localhost';
        $port = getenv('MYSQLPORT')     ?: getenv('DB_PORT')     ?: '3306';
        $name = getenv('MYSQLDATABASE') ?: getenv('DB_NAME')     ?: 'railway';
        $user = getenv('MYSQLUSER')     ?: getenv('DB_USER')     ?: 'root';
        $pass = getenv('MYSQLPASSWORD') ?: getenv('DB_PASSWORD') ?: 'stFmBHfhyMBNAqlFzpwfhpEpnNGrqXEX';

        $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        initDatabase($pdo);
    }
    return $pdo;
}

function initDatabase(PDO $pdo): void {
    // Buat tabel users jika belum ada
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            username   VARCHAR(50) UNIQUE NOT NULL,
            email      VARCHAR(100) UNIQUE NOT NULL,
            password   VARCHAR(255) NOT NULL,
            role       ENUM('admin','user') NOT NULL DEFAULT 'user',
            status     ENUM('active','inactive') NOT NULL DEFAULT 'active',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_login DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // Buat tabel activity_log (audit trail)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS activity_log (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            user_id    INT NULL,
            username   VARCHAR(50),
            action     VARCHAR(100) NOT NULL,
            ip_address VARCHAR(45),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // Seed akun default jika tabel masih kosong
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($count == 0) {
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)
        ");
        $stmt->execute(['admin', 'admin@example.com', password_hash('Admin@123', PASSWORD_BCRYPT), 'admin']);
        $stmt->execute(['user1', 'user1@example.com', password_hash('User@123',  PASSWORD_BCRYPT), 'user']);
        $stmt->execute(['user2', 'user2@example.com', password_hash('User@123',  PASSWORD_BCRYPT), 'user']);
    }
}
