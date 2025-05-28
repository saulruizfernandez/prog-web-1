<?php
  include __DIR__ . '/../connection.php';
  $codice = isset($_GET['search_filter']) ? $_GET['search_filter'] : '';
  $query2 = "SELECT G.codice AS cod
            FROM UtenteAutorizzatoGruppo C
            JOIN Gruppo G ON C.codGruppo = G.codice
            WHERE C.codUtente = :codice;";
  try {
      $stmt2 = $conn->prepare($query2);
      $stmt2->bindParam(':codice', $codice, PDO::PARAM_INT);
      $stmt2->execute();
      $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
      $serializedData = urlencode(json_encode($result2));
      header("Location: ../../../group.php?json_data=$serializedData");
      exit();
  } catch (PDOException $e) {
      echo "Error en la consulta: " . $e->getMessage();
  }
?>