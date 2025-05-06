<?php
header('Content-Type: application/json');
include __DIR__ . '/../connection.php';

if (!$conn) {
  echo json_encode(['success' => false, 'error' => 'No DB connection']);
  exit;
}

$params = [];
$params[':codice'] = intval($_POST['id']);
$params[':nickname'] = $_POST['nickname'] != '' ? $_POST['nickname'] : NULL;
$params[':nome'] = $_POST['nome'] != '' ? $_POST['nome'] : NULL;
$params[':cognome'] = $_POST['cognome'] != '' ? $_POST['cognome'] : NULL;
$params[':dataNascita'] = $_POST['dataNascita'] != '' ? $_POST['dataNascita'] : NULL;

$sql = "UPDATE Utente SET nickname = :nickname, nome = :nome, cognome = :cognome, dataNascita = :dataNascita WHERE codice = :codice";
$stmt = $conn->prepare($sql);

if ($stmt->execute($params)) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'error' => 'Error in edit']);
  http_response_code(500);
}
?>