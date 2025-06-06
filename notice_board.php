<?php
$searchFilter = isset($_GET['search_filter']) ? $_GET['search_filter'] : '';
$jsonData = isset($_GET['json_data']) ? $_GET['json_data'] : null;
$decodedJson = null;

if ($jsonData) {
    $jsonData = urldecode($jsonData);
    $decodedJson = json_decode($jsonData, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $decodedJson = null;
        error_log("Error decoding JSON: " . json_last_error_msg());
        echo "\nJSON Decoding Error: " . json_last_error_msg();
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
    window.onload = function() {
        const searchFilter = "<?php echo $searchFilter; ?>";
        const jsonData = <?php echo $decodedJson ? json_encode($decodedJson) : 'null'; ?>;

        if (searchFilter) {
            $("#search_filter input[name=codiceUtente]").val(searchFilter);
            window.history.replaceState({}, document.title, window.location.pathname);
            $("#search_filter form").submit();
        } else if (jsonData) {
            console.log("Received JSON data:", jsonData);
            if (Array.isArray(jsonData)) {
                console.log("hola");
                let codiceUtenteValues = [];
                let nomeValues = [];

                jsonData.forEach(pair => {
                    if (pair.cod && pair.nom) {
                        console.log(pair.cod + " ----- " + pair.nom);
                        codiceUtenteValues.push(pair.cod);
                        nomeValues.push(pair.nom);
                    }
                });
                const codiceUtenteString = codiceUtenteValues.join(",");
                const nomeString = nomeValues.join(",");
                $("#search_filter input[name=codiceUtente]").val(codiceUtenteString);
                $("#search_filter input[name=nome]").val(nomeString);
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
  <script src="src/js/bacheca/update_data2.js"></script>
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
          <input type="text" name="codiceUtente" style="display: none">
          User Name:<input type="text" name="userName"><br>
          Notice Name:<input type="text" name="nome"><br>
          Creation Date: <input type="text" name="dataCreazione" class="datepicker"><br><br>
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
            if (!empty($_POST["userName"])) {
              if (strpos($_POST["userName"], ',') !== false) {
                $values = array_map('trim', explode(',', $_POST["userName"]));
                $placeholders = [];
                foreach ($values as $index => $value) {
                  $placeholder = ":userName_$index";
                  $placeholders[] = $placeholder;
                  $params[$placeholder] = "%$value%";
                }
                $query .= " AND (" . implode(' OR ', array_map(fn($p) => "U.nome LIKE $p", $placeholders)) . ")";
              } else {
                $query .= " AND U.nome LIKE :userName";
                $params[":userName"] = "%" . $_POST["userName"] . "%";
              }
            }
            if (!empty($_POST["nome"])) {
              if (strpos($_POST["nome"], ',') !== false) {
                  $values = array_map('trim', explode(',', $_POST["nome"]));
                  $placeholders = [];
                  foreach ($values as $index => $value) {
                      $placeholder = ":nome_$index";
                      $placeholders[] = $placeholder;
                      $params[$placeholder] = "%$value%";
                  }
                  $query .= " AND (" . implode(' OR ', array_map(fn($p) => "B.nome LIKE $p", $placeholders)) . ")";
              } else {
                  $query .= " AND B.nome LIKE :nome";
                  $params[":nome"] = "%" . $_POST["nome"] . "%";
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
          <th style="display: none;">User Code</th>
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
          <td id="<?php echo $row["codiceUtente"]; ?>_codiceUtente" style="display: none;">
            <?php echo $row["codiceUtente"]; ?>
          </td>
          <td id="<?php echo $row["codiceUtente"]; ?>_userName">
            <a href="index.php?search_filter=<?php echo urlencode($row['codiceUtente']); ?>"> 
              <?php echo $row["userName"]; ?>
            </a>
          </td>
          <td id="<?php echo $row["codiceUtente"]; ?>_nome"> <?php echo $row["nome"]; ?></td>
          <td id="<?php echo $row["codiceUtente"]; ?>_dataCreazione"> <?php echo date("d/m/Y", strtotime($row["dataCreazione"])); ?></td>
          <td id="<?php echo $row["codiceUtente"]; ?>_numFiles">
          <a href="src/php/file-bacheca/file_bacheca.php?codUtente=<?php echo urlencode($row['codiceUtente']); ?>&nome=<?php echo urlencode($row['nome']); ?>"> 
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
      Creation Date: <input type="text" name="dataCreazione" class="datepicker"><br>
    </form>
  </div>
  <div id="add_dialog" title="Add record" style="display: none;">
    <form action="" method="post">
      User Code<input type="text" name="codiceUtente"><br>
      Notice name: <input type="text" name="nome"><br>
      Creation Date: <input type="text" name="dataCreazione" class="datepicker"><br>
    </form>
  </div>
  <div id="footer"></div>
</body>
</html>
