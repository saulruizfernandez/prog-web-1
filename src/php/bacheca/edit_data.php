<?php
header('Content-Type: application/json');
include __DIR__ . '/../connection.php';
if (!$conn) {
    echo json_encode(['success' => false]);
    exit;
}

$codiceUtente = $_POST['codiceUtente'] ?? null;
$nome = trim($_POST['nome'] ?? '');
$dataCreazione = $_POST['dataCreazione'] ?? null;

if ($codiceUtente === null || $nome === '') {
  echo json_encode(['success' => false, 'error' => 'User code and name are required']);
  exit;
}

$check_sql = "SELECT COUNT(*) FROM Bacheca WHERE codiceUtente = :codiceUtente AND nome = :nome";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->execute([
    ':codiceUtente' => $codiceUtente,
    ':nome' => $nome
]);

if ($check_stmt->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'error' => 'Duplicate entry']);
    exit;
}

$params = [];
$params[':codiceUtente'] = intval($_POST['id']);
$params[':nome'] = $_POST['nome'] = $_POST['nome'];
$params[':dataCreazione'] = $_POST['dataCreazione'] != '' ? $_POST['dataCreazione'] : NULL;

$utente_sql = "SELECT COUNT(*) FROM Utente WHERE codice = :codiceUtente";
$utente_stmt = $conn->prepare($utente_sql);
$utente_stmt->execute([':codiceUtente' => $codiceUtente]);

if ($utente_stmt->fetchColumn() == 0) {
    echo json_encode(['success' => false, 'error' => 'The user doesn\'t exist']);
    exit;
}

$sql = "UPDATE Bacheca SET codiceUtente = :codiceUtente, nome = :nome, dataCreazione = :dataCreazione WHERE codiceUtente = :codiceUtente";
$stmt = $conn->prepare($sql); // statement = stmt

if ($stmt->execute($params)) {
  echo json_encode(['success' => true]); // response to client (update_data.js)\
} else {
  echo json_encode(['success' => false, 'error' => 'Database error']);
  http_response_code(500);
}
?>