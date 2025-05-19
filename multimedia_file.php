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
    // Executes the search if the table is linked
    window.onload = function() {
        const searchFilter = "<?php echo $searchFilter; ?>";
        if (searchFilter) {
            $("#search_filter input[name=numero]").val(searchFilter);
            window.history.replaceState({}, document.title, window.location.pathname);
            $("#search_filter form").submit();
        }
    };
  </script>
  <script>
    window.onload = function() {
        const searchFilter = "<?php echo $searchFilter; ?>";
        const nomeBacheca = "<?php echo $nomeBacheca; ?>";
        const jsonData = <?php echo $decodedJson ? json_encode($decodedJson) : 'null'; ?>;

        if (searchFilter) {
            $("#search_filter input[name=codice]").val(searchFilter);
            window.history.replaceState({}, document.title, window.location.pathname);
            $("#search_filter form").submit();
        } else if (jsonData) {
            console.log("Received JSON data:", jsonData);
            if (Array.isArray(jsonData)) {
                console.log(jsonData[0].cod);
                let codiceFileValues = [];
                jsonData.forEach(function(item) {
                  codiceFileValues.push(item.cod);
                });
                const codiceFileString = codiceFileValues.join(",");
                $("#search_filter input[name=numero]").val(codiceFileString);
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
  <script src="src/js/multimedia-file/update_data.js"></script>
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
        <a href="notice_board.php">Notice board</a>
        <a href="multimedia_file.php" class="selected">Multimedia file</a>
        <a href="group.php">Group</a>
      </nav>
      <div id="search_filter">
        <form method="POST" action="">
          <input type="text" name="caricatoda" style="display: none;">
          <input type="text" name="numero" style="display: none;">
          Title:<input type="text" name="title"><br>
          Dimension:<input type="text" name="dimension" step="0.01"><br>
          URL:<input type="text" name="uurl"><br>
          File type:<br>
          <input type="radio" id="video" name="file_t" value="video">
          <label for="video">Video</label><br>
          <input type="radio" id="audio" name="file_t" value="audio">
          <label for="audio">Audio</label><br>
          <input type="radio" id="image" name="file_t" value="image">
          <label for="image">Image</label><br><br>
          <input type="submit" value="search file">
          <input type="reset" value="reset"><br><br>
        </form>
        <div id="contenedor_add_file">
          <button class="add_button" id="add_button_mult_file"><strong>+ Add File</strong></button>
        </div>
      </div>
    </div>
    <div id="content">

      <?php
      $error = false;
      $query = "SELECT * FROM FileMultimediale WHERE 1=1";
      $params = [];
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["caricatoda"])) {
          if (strpos($_POST["caricatoda"], ',') !== false) {
            $values = array_map('trim', explode(',', $_POST["caricatoda"]));
            $placeholders = [];
            foreach ($values as $index => $value) {
              $placeholder = ":caricatoda_$index";
              $placeholders[] = $placeholder;
              $params[$placeholder] = $value;
            }
            $query .= " AND caricatoDa IN (" . implode(',', $placeholders) . ")";
          } else {
            $query .= " AND caricatoDa = :caricatoda";
            $params[":caricatoda"] = $_POST["caricatoda"];
          }
        }
        if (!empty($_POST["numero"])) {
          if (strpos($_POST["numero"], ',') !== false) {
            $values = array_map('trim', explode(',', $_POST["numero"]));
            $placeholders = [];
            foreach ($values as $index => $value) {
              $placeholder = ":numero_$index";
              $placeholders[] = $placeholder;
              $params[$placeholder] = $value;
            }
            $query .= " AND numero IN (" . implode(',', $placeholders) . ")";
          } else {
            $query .= " AND numero = :numero";
            $params[":numero"] = $_POST["numero"];
          }
        }
        if (!empty($_POST["title"])) {
          if (strpos($_POST["title"], ',') !== false) {
            $values = array_map('trim', explode(',', $_POST["title"]));
            $placeholders = [];
            foreach ($values as $index => $value) {
              $placeholder = ":title_$index";
              $placeholders[] = $placeholder;
              $params[$placeholder] = "%$value%";
            }
            $query .= " AND (" . implode(' OR ', array_map(fn($p) => "titolo LIKE $p", $placeholders)) . ")";
          } else {
            $query .= " AND titolo LIKE :title";
            $params[":title"] = "%" . $_POST["title"] . "%";
          }
        }
        if (!empty($_POST["dimension"])) {
          if (strpos($_POST["dimension"], ',') !== false) {
            $values = array_map('trim', explode(',', $_POST["dimension"]));
            $placeholders = [];
            foreach ($values as $index => $value) {
              $placeholder = ":dimension_$index";
              $placeholders[] = $placeholder;
              $params[$placeholder] = "%$value%";
            }
            $query .= " AND (" . implode(' OR ', array_map(fn($p) => "dimensione LIKE $p", $placeholders)) . ")";
          } else {
            $query .= " AND dimensione LIKE :dimension";
            $params[":dimension"] = "%" . $_POST["dimension"] . "%";
          }
        }
        if (!empty($_POST["uurl"])) {
          if (strpos($_POST["uurl"], ',') !== false) {
            $values = array_map('trim', explode(',', $_POST["uurl"]));
            $placeholders = [];
            foreach ($values as $index => $value) {
              $placeholder = ":uurl_$index";
              $placeholders[] = $placeholder;
              $params[$placeholder] = "%$value%";
            }
            $query .= " AND (" . implode(' OR ', array_map(fn($p) => "`URL` LIKE $p", $placeholders)) . ")";
          } else {
            $query .= " AND `URL` LIKE :uurl";
            $params[":uurl"] = "%" . $_POST["uurl"] . "%";
          }
        }
        if (!empty($_POST["file_t"])) {
          if (strpos($_POST["file_t"], ',') !== false) {
            $values = array_map('trim', explode(',', $_POST["file_t"]));
            $placeholders = [];
            foreach ($values as $index => $value) {
              $placeholder = ":file_t_$index";
              $placeholders[] = $placeholder;
              $params[$placeholder] = $value;
            }
            $query .= " AND tipo IN (" . implode(',', $placeholders) . ")";
          } else {
            $query .= " AND tipo = :file_t";
            $params[":file_t"] = $_POST["file_t"];
          }
        }
      }
      $query .= " ORDER BY numero";

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
          <th>Uploaded by</th>
          <th style="display: none;">File number</th>
          <th>Title</th>
          <th>Dimension</th>
          <th>URL</th>
          <th>File type</th>
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
          <td id="<?php echo $row["numero"]; ?>_uploadedby">
            <a href="index.php?search_filter=<?php echo urlencode($row['caricatoDa']); ?>"> 
              <?php
                $query3 = "SELECT U.nome AS nom
                FROM Utente U
                WHERE U.codice = :codice
                LIMIT 1;";
                try {
                    $stmt3 = $conn->prepare($query3);
                    $stmt3->bindParam(':codice', $row['caricatoDa'], PDO::PARAM_INT);
                    $stmt3->execute();
                    $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo "Error en la consulta: " . $e->getMessage();
                }
                echo $result3[0]['nom'];
              ?> 
            </a>
          </td>
          <td id="<?php echo $row["numero"]; ?>_number" style="display: none;"> <?php echo $row["numero"]; ?></td>
          <td id="<?php echo $row["numero"]; ?>_title"> <?php echo $row["titolo"]; ?></td>
          <td id="<?php echo $row["numero"]; ?>_dimension"> <?php echo $row["dimensione"]; ?></td>
          <td id="<?php echo $row["numero"]; ?>_url"> <?php echo $row["URL"]; ?></td>
          <td id="<?php echo $row["numero"]; ?>_filetype"> <?php echo $row["tipo"]; ?></td>
          <td><button class="edit_button" id="<?php echo $row["numero"]; ?>_edit"><img src="media/icons/edit_icon.png" alt="edit_icon" style="width:30px; height:30px"></button></td>
          <td><button class="delete_button" id="<?php echo $row["numero"]; ?>_delete"><img src="media/icons/delete_icon.png" alt="delete_icon" style="width:30px; height:30px"></button></td>
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
      Uploaded by: <input type="text" name="uploadedby" readonly><br>
      File number: <input type="number " name="number" readonly><br>
      Title: <input type="text" name="title"><br>
      Dimension: <input type="number" step="0.01" name="dimension"><br>
      URL: <input type="text" name="uurl"><br>
      File type:
      <select name="filetype" id="filetypeselect">
        <option value="image">image</option>
        <option value="audio">audio</option>
        <option value="video">video</option>
      </select>
    </form>
  </div>
  <div id="add_dialog" title="Add record" style="display: none;">
    <form action="" method="post">
      Uploaded by: <input type="number" name="uploadedby"><br>
      File number: <input type="number " name="number" readonly><br>
      Title: <input type="text" name="title"><br>
      Dimension: <input type="number" step="0.01" name="dimension"><br>
      URL: <input type="text" name="uurl"><br>
      File type:
      <select name="filetype" id="filetypeadd">
        <option value="image">image</option>
        <option value="audio">audio</option>
        <option value="video">video</option>
      </select>
    </form>
  </div>
  <div id="footer"></div>
</body>
</html>