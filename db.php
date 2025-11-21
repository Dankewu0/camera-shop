<?php
$host = 'localhost';
$db   = 'Camera-Shop-DB';
$user = 'postgres';
$pass = 'dankewu';
$port = "5432";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
    exit;
}
?>
