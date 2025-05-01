<?php
$servername = "localhost";
$username = "user";
$password = "1234";
$dbname = "my_arsahosting";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Conexión fallida: " . $e->getMessage());
}
?>