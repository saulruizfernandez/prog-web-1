<?php
header('Content-Type: application/json');
include __DIR__ . '/connection.php';
if (!$conn) {
    echo json_encode(['success' => false]);
    exit;
}

$params = [];
$params[':codice'] = intval($_POST['id']);
$params[':nickname'] = $_POST['nickname'];
$params[':nome'] = $_POST['nome'];
$params[':cognome'] = $_POST['cognome'];
$params[':dataNascita'] = $_POST['dataNascita'];

$sql = "UPDATE Utente SET nickname = :nickname, nome = :nome, cognome = :cognome, dataNascita = :dataNascita WHERE codice = :codice";
$stmt = $conn->prepare($sql); // statement = stmt

if ($stmt->execute($params)) {
  echo json_encode(['success' => true]); // response to client (update_data.js)
} else {
  echo json_encode(['success' => false, 'error' => 'Database error']);
  http_response_code(500);
}
?>