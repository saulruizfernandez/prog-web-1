<?php
  include __DIR__ . '/../connection.php';
  $rec_param = isset($_GET['codUtente']) ? $_GET['codUtente'] : '';
  $rec_param2 = isset($_GET['nome']) ? $_GET['nome'] : '';
  $query2 = "SELECT P.file AS cod
            FROM FilePubblicatoBacheca P
            JOIN FileMultimediale F ON P.file = F.numero
            WHERE P.codUtente = :codiceUtente AND P.nomeBacheca = :nome";
  try {
      $stmt2 = $conn->prepare($query2);
      $stmt2->bindParam(':codiceUtente', $rec_param, PDO::PARAM_INT);
      $stmt2->bindParam(':nome', $rec_param2, PDO::PARAM_STR);
      $stmt2->execute();
      $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
      $serializedData = urlencode(json_encode($result2));
      header("Location: ../../../multimedia_file.php?json_data=$serializedData");
      exit();
  } catch (PDOException $e) {
      echo "Error en la consulta: " . $e->getMessage();
  }
?>