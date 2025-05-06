<?php
header('Content-Type: application/json');
include __DIR__ . '/../connection.php';

if (!$conn) {
  echo json_encode(['success' => false, 'error' => 'No DB connection']);
  exit;
}

$record_delete_id = intval($_POST['filenumber']);
$sql = "DELETE FROM FileMultimediale WHERE numero = :filenumber";
$stmt = $conn->prepare($sql); // statement = stmt
$stmt->bindValue(':filenumber', $record_delete_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'error' => 'Error in deletion']);
}
?>
