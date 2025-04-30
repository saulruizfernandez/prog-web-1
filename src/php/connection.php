<?php
$servername = "localhost";
$username = "user";
$password = "1234";

try {
    $conn = new PDO("mysql:host=$servername;dbname=my_arsahosting", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
