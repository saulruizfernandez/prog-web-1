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
  <script src="src/js/group/update_data.js"></script>
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
        <a href="multimedia_file.php">Multimedia file</a>
        <a href="group.php" class="selected">Group</a>
      </nav>
      <div id="search_filter">
        <form method="POST" action="">
          CreatedBy:<input type="number" name="creatoDa"><br>
          Code:<input type="number" name="numero"><br>
          Name:<input type="text" name="nome"><br>
          CreationDate:<input type="date" name="creazioneData"><br>
          <input type="submit" value="search group">
          <input type="reset" value="reset"><br><br>
        </form>
        <div id="contenedor_add_group">
          <button class="add_button" id="add_button_group"><strong>+ Add Group</strong></button>
        </div>
      </div>
    </div>
    <div id="content">

      <?php
      $error = false;
      $query = "SELECT * FROM Gruppo WHERE 1=1";
      $params = [];

      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["creatoDa"])) {
          $query .= " AND creatoDa = :creatoDa"; // placeholder
          $params[":creatoDa"] = $_POST["creatoDa"];
        }
        if (!empty($_POST["numero"])) {
          $query .= " AND codice = :numero";
          $params[":numero"] = $_POST["numero"];
        }
        if (!empty($_POST["nome"])) {
          $query .= " AND nome = :nome";
          $params[":nome"] = $_POST["nome"];
        }
        if (!empty($_POST["creazioneData"])) {
          $query .= " AND dataCreazione = :creazioneData";
          $params[":creazioneData"] = $_POST["creazioneData"];
        }
      }
      $query .= " ORDER BY codice";

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
          <th>Created by</th>
          <th>Code</th>
          <th>Name</th>
          <th>Date creation</th>
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
          <td id="<?php echo $row["codice"]; ?>_createdby">
            <a href="index.php?search_filter=<?php echo urlencode($row['creatoDa']); ?>"> 
              <?php echo $row["creatoDa"]; ?>
            </a>
          </td>
          <td id="<?php echo $row["codice"]; ?>_codice"> <?php echo $row["codice"]; ?></td>
          <td id="<?php echo $row["codice"]; ?>_nome"> <?php echo $row["nome"]; ?></td>
          <td id="<?php echo $row["codice"]; ?>_dataCreazione"> <?php echo $row["dataCreazione"]; ?></td>
          <td><button class="edit_button" id="<?php echo $row["codice"]; ?>_edit"><img src="media/icons/edit_icon.png" alt="edit_icon" style="width:30px; height:30px"></button></td>
          <td><button class="delete_button" id="<?php echo $row["codice"]; ?>_delete"><img src="media/icons/delete_icon.png" alt="delete_icon" style="width:30px; height:30px"></button></td>
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
      Created by: <input type="number" name="createdby"><br>
      Code: <input type="number " name="code" readonly><br>
      Name: <input type="text" name="name"><br>
      Creation date: <input type="date" name="creationdate"><br>
    </form>
  </div>
  <div id="add_dialog" title="Add record" style="display: none;">
    <form action="" method="post">
      Created by: <input type="number" name="createdby"><br>
      Code: <input type="number " name="code" readonly><br>
      Name: <input type="text" name="name"><br>
      Creation date: <input type="date" name="creationdate"><br>
    </form>
  </div>
  <div id="footer"></div>
</body>
</html>
