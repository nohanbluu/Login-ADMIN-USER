<?php
function getDB() {
    $host     = getenv('MYSQLHOST')     ?: 'mysql.railway.internal';
    $port     = getenv('MYSQLPORT')     ?: '3306';
    $dbname   = getenv('MYSQLDATABASE') ?: 'railway';
    $user     = getenv('MYSQLUSER')     ?: 'root';
    $password = getenv('MYSQLPASSWORD') ?: 'stFmBHfhyMBNAqlFzpwfhpEpnNGrqXEX';

    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("Koneksi DB gagal: " . $e->getMessage());
    }
}
