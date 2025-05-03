<?php
header('Content-Type: application/json');
include __DIR__ . '/../connection.php';
if (!$conn) {
    echo json_encode(['success' => false]);
    exit;
}

$createdby = $_POST['creatoDa'] ?? null;
if ($uploadedby) {
    $checkUserQuery = "SELECT COUNT(*) AS user_exists FROM Utente WHERE codice = :createdby";
    $checkUserStmt = $conn->prepare($checkUserQuery);
    $checkUserStmt->execute([':createdby' => $createdby]);
    $userExists = $checkUserStmt->fetch(PDO::FETCH_ASSOC)['user_exists'];

    if (!$userExists) {
        echo json_encode(['success' => false, 'error' => 'The user does not exist']);
        exit;
    }
}

$params = [];
$params[':codice'] = intval($_POST['codice']);
$params[':creatoDa'] = $_POST['creatoDa'] != '' ? $_POST['creatoDa'] : NULL;
$params[':nome'] = $_POST['nome'] != '' ? $_POST['nome'] : NULL;
$params[':dataCreazione'] = $_POST['dataCreazione'] != '' ? $_POST['dataCreazione'] : NULL;

$sql = "UPDATE Gruppo SET creatoDa = :creatoDa, nome = :nome, dataCreazione = :dataCreazione WHERE codice = :codice";
$stmt = $conn->prepare($sql); // statement = stmt

if ($stmt->execute($params)) {
  echo json_encode(['success' => true]); // response to client (update_data.js)
} else {
  echo json_encode(['success' => false, 'error' => 'Database error']);
  http_response_code(500);
}
?>