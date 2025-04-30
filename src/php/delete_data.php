<?php
header('Content-Type: application/json');
include __DIR__ . '/connection.php';
if (!$conn) {
    echo json_encode(['success' => false]);
    exit;
}

$record_delete_id = intval($_POST['id']);
$sql = "DELETE FROM Utente WHERE codice = :id";
$stmt = $conn->prepare($sql); // statement = stmt
$stmt->bindValue(':id', $record_delete_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(['success' => true]); // response to client (update_data.js)
} else {
    echo json_encode(['success' => false]);
}
?>
