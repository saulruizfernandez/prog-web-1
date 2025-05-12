<?php
$searchFilter = isset($_GET['search_filter']) ? $_GET['search_filter'] : '';
$jsonData = isset($_GET['json_data']) ? $_GET['json_data'] : null;
$decodedJson = null;

if ($jsonData) {
    $jsonData = urldecode($jsonData);
    $decodedJson = json_decode($jsonData, true);

    echo "Decoded JSON:\n";
    print_r($decodedJson); // Use print_r to see the array structure

    if (json_last_error() !== JSON_ERROR_NONE) {
        $decodedJson = null;
        error_log("Error decoding JSON: " . json_last_error_msg());
        echo "\nJSON Decoding Error: " . json_last_error_msg(); // Output the error message
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>arsanet 📡</title>
  <script>
    // Executes the search if the table is linked
    window.onload = function() {
        const searchFilter = "<?php echo $searchFilter; ?>";
        const jsonData = <?php echo $decodedJson ? json_encode($decodedJson) : 'null'; ?>;

        if (searchFilter) {
            // Caso 1: Procesar search_filter
            $("#search_filter input[name=codiceUtente]").val(searchFilter);
            window.history.replaceState({}, document.title, window.location.pathname);
            $("#search_filter form").submit();
        } else if (jsonData) {
            // Caso 2: Procesar json_data
            console.log("Received JSON data:", jsonData);

            if (Array.isArray(jsonData)) {
                console.log("hola");
                let codiceUtenteValues = [];
                let nomeValues = [];

                // Iterar sobre cada par en el array
                jsonData.forEach(pair => {
                    if (pair.cod && pair.nom) {
                        console.log(pair.cod + " ----- " + pair.nom);
                        codiceUtenteValues.push(pair.cod);
                        nomeValues.push(pair.nom);
                    }
                });

                // Convertir los arrays en cadenas separadas por comas
                const codiceUtenteString = codiceUtenteValues.join(",");
                const nomeString = nomeValues.join(",");

                // Asignar las cadenas a los inputs correspondientes
                $("#search_filter input[name=codiceUtente]").val(codiceUtenteString);
                $("#search_filter input[name=nome]").val(nomeString);

                // Limpiar la URL y enviar el formulario
                window.history.replaceState({}, document.title, window.location.pathname);
                $("#search_filter form").submit();
            }
        }
    };
</script>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="stylesheet" href="styles/title.css">
  <script src="lib/jquery-3.7.1.min.js"></script>
  <script src="lib/jquery-ui-1.14.1.custom/jquery-ui.min.js"></script>
  <link rel="stylesheet" href="lib/jquery-ui-1.14.1.custom/jquery-ui.min.css">
  <script src="src/js/header_footer.js"></script>
  <script src="src/js/bacheca/update_data.js"></script>
</head>
<body>
  <?php include 'src/php/connection.php'; ?> 
  <input type="hidden" id="page_name" value="USER">
  <div id="header"></div>
  </div>
  <div class="container">
    <div id="left_layout">
      <nav id="navigation">
        <a href="index.php">User</a>
        <a href="notice_board.php" class="selected">Notice board</a>
        <a href="multimedia_file.php">Multimedia file</a>
        <a href="group.php">Group</a>
      </nav>
      <div id="search_filter">
        <form method="POST" action="">
          <input type="text" name="codiceUtente" style="display: none"><br>
          User Name:<input type="text" name="userName"><br>
          Notice Name:<input type="text" name="nome"><br>
          Creation Date: <input type="date" name="dataCreazione"><br>
          <input type="submit" value="search notice board">
          <input type="reset" value="reset"><br><br>
        </form>
        <div id="contenedor_add_bacheca">
          <button class="add_button" id="add_button_bacheca"><strong>+ Add Notice Board</strong></button>
        </div>
      </div>
    </div>
    <div id="content">

    <?php
      $error = false;
      $query = "SELECT 
            B.codiceUtente, 
            U.nome AS userName, 
            B.nome, 
            B.dataCreazione,
            (SELECT COUNT(*) 
            FROM FilePubblicatoBacheca F 
            WHERE F.codUtente = B.codiceUtente AND F.nomeBacheca = B.nome) AS numFiles
          FROM Bacheca B 
          JOIN Utente U ON B.codiceUtente = U.codice
          WHERE 1=1";
      $params = [];
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
          if (!empty($_POST["codiceUtente"])) {
              if (strpos($_POST["codiceUtente"], ',') !== false) {
                  $values = array_map('trim', explode(',', $_POST["codiceUtente"]));
                  $placeholders = [];
                  foreach ($values as $index => $value) {
                      $placeholder = ":codiceUtente_$index";
                      $placeholders[] = $placeholder;
                      $params[$placeholder] = $value;
                  }
                  $query .= " AND B.codiceUtente IN (" . implode(',', $placeholders) . ")";
              } else {
                  $query .= " AND B.codiceUtente = :codiceUtente";
                  $params[":codiceUtente"] = $_POST["codiceUtente"];
              }
          }
          if (!empty($_POST["nome"])) {
              if (strpos($_POST["nome"], ',') !== false) {
                  $values = array_map('trim', explode(',', $_POST["nome"]));
                  $placeholders = [];
                  foreach ($values as $index => $value) {
                      $placeholder = ":nome_$index";
                      $placeholders[] = $placeholder;
                      $params[$placeholder] = $value;
                  }
                  $query .= " AND B.nome IN (" . implode(',', $placeholders) . ")";
              } else {
                  $query .= " AND B.nome = :nome";
                  $params[":nome"] = $_POST["nome"];
              }
          }
          if (!empty($_POST["dataCreazione"])) {
              if (strpos($_POST["dataCreazione"], ',') !== false) {
                  $values = array_map('trim', explode(',', $_POST["dataCreazione"]));
                  $placeholders = [];
                  foreach ($values as $index => $value) {
                      $placeholder = ":dataCreazione_$index";
                      $placeholders[] = $placeholder;
                      $params[$placeholder] = $value;
                  }
                  $query .= " AND B.dataCreazione IN (" . implode(',', $placeholders) . ")";
              } else {
                  $query .= " AND B.dataCreazione = :dataCreazione";
                  $params[":dataCreazione"] = $_POST["dataCreazione"];
              }
          }
      }
      $query .= " ORDER BY B.codiceUtente";

      try {
        $aux = $conn->prepare($query);
        $aux->execute($params);
        $result = $aux->fetchAll(PDO::FETCH_ASSOC);
      } catch(PDOException$e) {
        echo "DB Error on Query: " . $e->getMessage();
        $error = true;
      }
      if(!$error)
      { 
      ?>

      <table class="table">
        <tr class = "header">
          <th>User Code</th>
          <th>User Name</th>
          <th>Notice Name</th>
          <th>Creation Date</th>
          <th>Number of Files</th>
        </tr>

      <?php
      $i=0;
      foreach($result as $row) {
        $i = $i + 1;
        $classrow = 'class="odd_row"';
        if ($i % 2 == 0) {
          $classrow = 'class="even_row"';
        }
      ?>

        <tr <?php echo $classrow; ?>>
          <td id="<?php echo $row["codiceUtente"]; ?>_codiceUtente">
            <a href="index.php?search_filter=<?php echo urlencode($row['codiceUtente']); ?>"> 
              <?php echo $row["codiceUtente"]; ?>
            </a>
          </td>
          <td id="<?php echo $row["codiceUtente"]; ?>_userName"> <?php echo $row["userName"]; ?></td>
          <td id="<?php echo $row["codiceUtente"]; ?>_nome"> <?php echo $row["nome"]; ?></td>
          <td id="<?php echo $row["codiceUtente"]; ?>_dataCreazione"> <?php echo $row["dataCreazione"]; ?></td>
          <td id="<?php echo $row["codiceUtente"]; ?>_numFiles">
            <a href="src/php/file-bacheca/file_bacheca.php?search_filter=<?php echo urlencode($row['codiceUtente']); ?>"> 
              <?php echo $row["numFiles"]; ?>
            </a>
          </td>
          <td><button class="edit_button" id="<?php echo $row["codiceUtente"]; ?>_edit"><img src="media/icons/edit_icon.png" alt="edit_icon" style="width:30px; height:30px"></button></td>
          <td><button class="delete_button" id="<?php echo $row["codiceUtente"]; ?>_delete"><img src="media/icons/delete_icon.png" alt="delete_icon" style="width:30px; height:30px"></button></td>
        </tr>

      <?php
      }
      ?>

      </table>

      <?php
      }
      ?>

    </div>
  </div>
  <div id="delete_dialog" title="Delete record" style="display: none;">
    <p>Are you sure you want to delete the record?</p>
  </div>
  <div id="edit_dialog" title="Edit record" style="display: none;">
    <form action="" method="post">
      <input type="text" name="codiceUtente" style="display: none"><br>
      Notice name: <input type="text" name="nome"><br>
      Creation Date: <input type="date" name="dataCreazione"><br>
    </form>
  </div>
  <div id="add_dialog" title="Add record" style="display: none;">
    <form action="" method="post">
      <input type="text" name="codiceUtente" style="display: none"><br>
      Notice name: <input type="text" name="nome"><br>
      Creation Date: <input type="date" name="dataCreazione"><br>
    </form>
  </div>
  <div id="footer"></div>
</body>
</html>
