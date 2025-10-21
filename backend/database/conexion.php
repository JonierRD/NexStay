<?php
// Datos de conexión
$host = "127.0.0.1";
$port = 3307;            // ⚠ Puerto correcto de tu MySQL
$dbname = "nexstay_db";
$user = "root";
$password = "12345";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Mensaje para verificar
    echo "✅ Conexión exitosa a la base de datos $dbname";
$stmt = $pdo->query("SELECT * FROM usuarios");
$result = $stmt->fetchAll();
print_r($result);

} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage();
    exit;
}
