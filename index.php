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
  <script src="src/js/update_data.js"></script>
</head>
<body>
  <?php include 'src/php/connection.php'; ?>
  <input type="hidden" id="page_name" value="USER">
  <div id="header"></div>
  </div>
  <div class="container">
    <div id="left_layout">
      <nav id="navigation">
        <a href="#" class="selected">User</a>
        <a href="notice_board.php">Notice board</a>
        <a href="multimedia_file.php">Multimedia file</a>
        <a href="group.php">Group</a>
      </nav>
      <div id="search_add_filter">
        <form method="POST" action="">
          User code:<input type="text" name="codice"><br>
          Nickname:<input type="text" name="nickname"><br>
          First name:<input type="text" name="nome"><br>
          Last name:<input type="text" name="cognome"><br>
          Birthday: <input type="date" name="dataNascita"><br><br>
          <input type="submit" value="search user">
          <input type="reset" value="reset"><br><br>
        </form>
        <form method="POST" action="">
          <button type="button" class="add_button" id="add_user_button">
            <img src="media/icons/add_icon.png" alt="add_icon" style="width:50px; height:50px">
          </button>
        </form>
      </div>
    </div>
    <div id="content">

      <?php
      $error = false;
      $query = "SELECT * FROM Utente WHERE 1=1";
      $params = [];

      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["codice"])) {
          $query .= " AND codice = :codice"; // placeholder
          $params[":codice"] = $_POST["codice"];
        }
        if (!empty($_POST["nickname"])) {
          $query .= " AND nickname = :nickname";
          $params[":nickname"] = $_POST["nickname"];
        }
        if (!empty($_POST["nome"])) {
          $query .= " AND nome = :nome";
          $params[":nome"] = $_POST["nome"];
        }
        if (!empty($_POST["cognome"])) {
          $query .= " AND cognome = :cognome";
          $params[":cognome"] = $_POST["cognome"];
        }
        if (!empty($_POST["dataNascita"])) {
          $query .= " AND dataNascita = :dataNascita";
          $params[":dataNascita"] = $_POST["dataNascita"];
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
          <th>Codice</th>
          <th>Nickname</th>
          <th>Name</th>
          <th>Surname</th>
          <th>Birthday</th>
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
          <td id="<?php echo $row["codice"]; ?>_codice"> <?php echo $row["codice"]; ?></td>
          <td id="<?php echo $row["codice"]; ?>_nickname"> <?php echo $row["nickname"]; ?></td>
          <td id="<?php echo $row["codice"]; ?>_nome"> <?php echo $row["nome"]; ?></td>
          <td id="<?php echo $row["codice"]; ?>_cognome"> <?php echo $row["cognome"]; ?></td>
          <td id="<?php echo $row["codice"]; ?>_dataNascita"> <?php echo $row["dataNascita"]; ?></td>
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
  <div id="delete_dialog" title="Delete record">
    <p>Are you sure you want to delete the record?</p>
  </div>
  <div id="edit_dialog" title="Edit record">
    <form action="" method="post">
      User code: <input type="text" name="codice" readonly><br>
      Nickname: <input type="text" name="nickname"><br>
      First name: <input type="text" name="nome"><br>
      Last name: <input type="text" name="cognome"><br>
      Birthday: <input type="date" name="dataNascita"><br>
    </form>
  </div>
  <div id="add_dialog" title="Add record">
    <form action="" method="post">
      Nickname: <input type="text" name="nickname"><br>
      First name: <input type="text" name="nome"><br>
      Last name: <input type="text" name="cognome"><br>
      Birthday: <input type="date" name="dataNascita"><br>
    </form>
  </div>
  <div id="footer"></div>
</body>
</html>
