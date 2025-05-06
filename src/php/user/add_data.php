<?php
header('Content-Type: application/json');
include __DIR__ . '/../connection.php';

if (!$conn) {
  echo json_encode(['success' => false, 'error' => 'No DB connection']);
  exit;
}

$query = "SELECT MIN(missing.codice) AS next_code
          FROM (
                SELECT 0 AS codice
                UNION ALL
                SELECT codice + 1
                FROM Utente
          ) AS missing
          WHERE NOT EXISTS (
                SELECT 1 FROM Utente u WHERE u.codice = missing.codice
          );
";

$stmt = $conn->query($query);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$new_codice = $row['next_code'] ?? 1;

$params = [];
$params[':codice'] = $new_codice;
$params[':nickname'] = $_POST['nickname'] != '' ? $_POST['nickname'] : NULL;
$params[':nome'] = $_POST['nome'] != '' ? $_POST['nome'] : NULL;
$params[':cognome'] = $_POST['cognome'] != '' ? $_POST['cognome'] : NULL;
$params[':dataNascita'] = $_POST['dataNascita'] != '' ? $_POST['dataNascita'] : NULL;

$sql = "INSERT INTO Utente (codice, nickname, nome, cognome, dataNascita)
        VALUES (:codice, :nickname, :nome, :cognome, :dataNascita)";
$stmt = $conn->prepare($sql);

if ($stmt->execute($params)) {
  echo json_encode(['success' => true]); // response to client (update_data.js)
} else {
  echo json_encode(['success' => false, 'error' => 'Error in addition']);
}
?>