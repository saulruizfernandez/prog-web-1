<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>arsanet 📡</title>
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
          Uploaded by:<input type="text" name="caricatoda"><br>
          File number:<input type="number" name="numero"><br>
          Title:<input type="text" name="title"><br>
          Dimension:<input type="number" name="dimension" step="0.01"><br>
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
          $query .= " AND caricatoDa = :caricatoda"; // placeholder
          $params[":caricatoda"] = $_POST["caricatoda"];
        }
        if (!empty($_POST["numero"])) {
          $query .= " AND numero = :numero";
          $params[":numero"] = $_POST["numero"];
        }
        if (!empty($_POST["title"])) {
          $query .= " AND titolo = :title";
          $params[":title"] = $_POST["title"];
        }
        if (!empty($_POST["dimension"])) {
          $query .= " AND dimensione = :dimension";
          $params[":dimension"] = $_POST["dimension"];
        }
        if (!empty($_POST["uurl"])) {
          $query .= " AND `URL` = :uurl";
          $params[":uurl"] = $_POST["uurl"];
        }
        if (!empty($_POST["file_t"])) {
          $query .= " AND tipo = :file_t";
          $params[":file_t"] = $_POST["file_t"];
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
          <th>File number</th>
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
          <td id="<?php echo $row["numero"]; ?>_uploadedby"> <?php echo $row["caricatoDa"]; ?></td>
          <td id="<?php echo $row["numero"]; ?>_number"> <?php echo $row["numero"]; ?></td>
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
      Uploaded by: <input type="text" name="uploadedby"><br>
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
