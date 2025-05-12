<?php
  include __DIR__ . '/../connection.php';
  $rec_param = isset($_GET['search_filter']) ? $_GET['search_filter'] : '';
  $nome_bacheca = isset($_GET['nome_bacheca']) ? $_GET['nome_bacheca'] : '';
  $query2 = "SELECT F.file
            FROM FilePubblicatoBacheca F
            JOIN FileMultimediale M ON F.file = M.numero
            WHERE F.codUtente = :codiceUtente AND F.nomeBacheca = :nome;";
  try {
      $stmt2 = $conn->prepare($query2);
      $stmt2->bindParam(':codiceUtente', $rec_param, PDO::PARAM_INT);
      $stmt2->bindParam(':nome', $nome_bacheca, PDO::PARAM_STR);
      $stmt2->execute();
      $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
      $serializedData = urlencode(json_encode($result2));
      header("Location: ../../../multimedia_file.php?json_data=$serializedData");
      exit();
  } catch (PDOException $e) {
      echo "Error en la consulta: " . $e->getMessage();
  }
?>