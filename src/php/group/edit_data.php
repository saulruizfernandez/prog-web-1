<?php
header('Content-Type: application/json');
include __DIR__ . '/../connection.php';

if (!$conn) {
  echo json_encode(['success' => false, 'error' => 'No DB connection']);
  exit;
}

$createdby = $_POST['creatoDa'] ?? null;
if ($createdby) {
    $checkUserQuery = "SELECT COUNT(*) AS user_exists FROM Utente WHERE codice = :createdby";
    $checkUserStmt = $conn->prepare($checkUserQuery);
    $checkUserStmt->execute([':createdby' => $createdby]);
    $userExists = $checkUserStmt->fetch(PDO::FETCH_ASSOC)['user_exists'];

    if (!$userExists) {
        echo json_encode(['success' => false, 'error' => 'The user doesn\'t exist']);
        exit;
    }
}

$params = [];
$params[':codice'] = intval($_POST['codice']);
$params[':creatoDa'] = $_POST['creatoDa'] != '' ? $_POST['creatoDa'] : NULL;
$params[':nome'] = $_POST['nome'] != '' ? $_POST['nome'] : NULL;
$params[':dataCreazione'] = $_POST['dataCreazione'] != '' ? $_POST['dataCreazione'] : NULL;

$sql = "UPDATE Gruppo SET creatoDa = :creatoDa, nome = :nome, dataCreazione = :dataCreazione WHERE codice = :codice";
$stmt = $conn->prepare($sql);

if ($stmt->execute($params)) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'error' => 'Error in edit']);
}
?>