<?php
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "my_arsahosting";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en conexión: " . $e->getMessage());
}
?>
