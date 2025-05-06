<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
include __DIR__ . '/../connection.php';

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'No DB connection']);
    exit;
}

$codiceUtente = $_POST['codiceUtente'] ?? null;
$nome = trim($_POST['nome'] ?? '');
$dataCreazione = $_POST['dataCreazione'] ?? null;

if ($nome === '') {
  echo json_encode(['success' => false, 'error' => 'Notice board name is required']);
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
$params[':codiceUtente'] = $_POST['codiceUtente'];
$params[':nome'] = $_POST['nome'];
$params[':dataCreazione'] = $_POST['dataCreazione'] != '' ? $_POST['dataCreazione'] : NULL;


$utente_sql = "SELECT COUNT(*) FROM Utente WHERE codice = :codiceUtente";
$utente_stmt = $conn->prepare($utente_sql);
$utente_stmt->execute([':codiceUtente' => $codiceUtente]);

if ($utente_stmt->fetchColumn() == 0) {
    echo json_encode(['success' => false, 'error' => 'The user doesn\'t exist']);
    exit;
}

$sql = "INSERT INTO Bacheca (codiceUtente, nome, dataCreazione)
               VALUES (:codiceUtente, :nome, :dataCreazione)";
$stmt = $conn->prepare($sql);

if ($stmt->execute($params)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Error in addition']);
    http_response_code(500);
}
?>