<?php
header('Content-Type: application/json');
include __DIR__ . '/../connection.php';

if (!$conn) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'error' => 'No DB connection']);
    exit;
}

// Validate input
if (!isset($_POST['id']) || !is_numeric($_POST['id']) || !isset($_POST['nome']) || trim($_POST['nome']) === '') {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
    exit;
}

$record_delete_id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
$record_delete_name = filter_var(trim($_POST['nome']), FILTER_SANITIZE_STRING);

try {
    $sql = "DELETE FROM Bacheca WHERE codiceUtente = :id AND nome = :nome";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':id', $record_delete_id, PDO::PARAM_INT);
    $stmt->bindValue(':nome', $record_delete_name, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        $errorInfo = $stmt->errorInfo();
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'error' => 'Error in deletion: ' . $errorInfo[2]]);
    }
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>