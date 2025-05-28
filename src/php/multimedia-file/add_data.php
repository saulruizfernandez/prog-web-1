<?php
header('Content-Type: application/json');
include __DIR__ . '/../connection.php';

if (!$conn) {
  echo json_encode(['success' => false, 'error' => 'No DB connection']);
  exit;
}

$uploadedby = $_POST['uploadedby'] ?? null;
if ($uploadedby) {
    $checkUserQuery = "SELECT COUNT(*) AS user_exists FROM Utente WHERE codice = :uploadedby";
    $checkUserStmt = $conn->prepare($checkUserQuery);
    $checkUserStmt->execute([':uploadedby' => $uploadedby]);
    $userExists = $checkUserStmt->fetch(PDO::FETCH_ASSOC)['user_exists'];

    if (!$userExists) {
        echo json_encode(['success' => false, 'error' => 'The user doesn\'t exist']);
        exit;
    }
}

$query = "SELECT MIN(missing.numero) AS next_code
          FROM (
                SELECT 1 AS numero
                UNION ALL
                SELECT numero + 1
                FROM FileMultimediale
          ) AS missing
          WHERE NOT EXISTS (
                SELECT 1 FROM FileMultimediale fm WHERE fm.numero = missing.numero
          );
";

$stmt = $conn->query($query);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$new_codice = $row['next_code'] ?? 1;

$params = [];
$params[':filenumber'] = $new_codice;
$params[':uploadedby'] = $_POST['uploadedby'] != '' ? $_POST['uploadedby'] : NULL;
$params[':title'] = $_POST['title'] != '' ? $_POST['title'] : NULL;
$params[':dimension'] = $_POST['dimension'] != '' ? $_POST['dimension'] : NULL;
$params[':uurl'] = $_POST['uurl'] != '' ? $_POST['uurl'] : NULL;
$params[':filetype'] = $_POST['filetype'] != '' ? $_POST['filetype'] : NULL;

$sql = "INSERT INTO FileMultimediale (caricatoDa, numero, titolo, dimensione, `URL`, tipo)
        VALUES (:uploadedby, :filenumber, :title, :dimension, :uurl, :filetype)";
$stmt = $conn->prepare($sql);

if ($stmt->execute($params)) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'error' => 'Error in addition']);
}
?>