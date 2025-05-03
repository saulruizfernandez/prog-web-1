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

$query = "SELECT MIN(missing.codice) AS next_code
          FROM (
                SELECT 0 AS codice
                UNION ALL
                SELECT codice + 1
                FROM Gruppo
          ) AS missing
          WHERE NOT EXISTS (
                SELECT 1 FROM Gruppo g WHERE g.codice = missing.codice
          );
";

$stmt = $conn->query($query);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$new_codice = $row['next_code'] ?? 1;

$params = [];
$params[':creatoDa'] = $_POST['creatoDa'] != '' ? $_POST['creatoDa'] : NULL;
$params[':codice'] = $new_codice;
$params[':nome'] = $_POST['nome'] != '' ? $_POST['nome'] : NULL;
$params[':dataCreazione'] = $_POST['dataCreazione'] != '' ? $_POST['dataCreazione'] : NULL;

$sql = "INSERT INTO Gruppo (creatoDa, codice, nome, dataCreazione)
        VALUES (:creatoDa, :codice, :nome, :dataCreazione)";
$stmt = $conn->prepare($sql);

if ($stmt->execute($params)) {
  echo json_encode(['success' => true]); // response to client (update_data.js)
} else {
  echo json_encode(['success' => false, 'error' => 'Database error']);
  http_response_code(500);
}
?>