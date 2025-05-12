<?php
  include __DIR__ . '/../connection.php';
  $rec_param = isset($_GET['search_filter']) ? $_GET['search_filter'] : '';
  $query2 = "SELECT F.numero AS cod
            FROM FileAssociatoGruppo C
            JOIN FileMultimediale F ON C.file = F.numero
            WHERE C.codGruppo = :codice;";
  try {
      $stmt2 = $conn->prepare($query2);
      $stmt2->bindParam(':codice', $rec_param, PDO::PARAM_INT);
      $stmt2->execute();
      $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
      $serializedData = urlencode(json_encode($result2));
      header("Location: ../../../multimedia_file.php?json_data=$serializedData");
      exit();
  } catch (PDOException $e) {
      echo "Error en la consulta: " . $e->getMessage();
  }
?>