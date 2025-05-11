<?php
$searchFilter = isset($_GET['search_filter']) ? $_GET['search_filter'] : '';
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
            $("#search_filter input[name=codice]").val(searchFilter);
            window.history.replaceState({}, document.title, window.location.pathname);
            $("#search_filter form").submit();
        }
    };
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/styles.css">
  <script src="lib/jquery-3.7.1.min.js"></script>
  <script src="lib/jquery-ui-1.14.1.custom/jquery-ui.min.js"></script>
  <link rel="stylesheet" href="lib/jquery-ui-1.14.1.custom/jquery-ui.min.css">
  <script src="src/js/header_footer.js"></script>
  <script src="src/js/user/update_data.js"></script>
</head>
<body>
  <?php include 'src/php/connection.php'; ?>
  <input type="hidden" id="page_name" value="USER">
  <div id="header"></div>
  </div>
  <div class="container">
    <div id="left_layout">
      <nav id="navigation">
        <a href="index.php" class="selected">User</a>
        <a href="notice_board.php">Notice board</a>
        <a href="multimedia_file.php">Multimedia file</a>
        <a href="group.php">Group</a>
      </nav>
      <div id="search_filter">
        <form method="POST" action="">
          <input type="text" name="codice" style="display: none;">
          Nickname:<input type="text" name="nickname"><br>
          Name:<input type="text" name="nome"><br>
          Surname:<input type="text" name="cognome"><br>
          Birthday: <input type="date" name="dataNascita"><br><br>
          <input type="submit" value="search user">
          <input type="reset" value="reset"><br><br>
        </form>
        <div id="contenedor_add_user">
          <button class="add_button" id="add_button_user"><b>+ Add User</b></button>
        </div>
        
      </div>
    </div>
    <div id="content">

      <?php
      $error = false;
      $query = "SELECT 
                  u.codice, 
                  u.nickname, 
                  u.nome, 
                  u.cognome, 
                  u.dataNascita, 
                  (
                    SELECT COUNT(*) 
                    FROM (
                      SELECT DISTINCT b2.nome, b2.codiceUtente
                      FROM Bacheca b2
                      WHERE b2.codiceUtente = u.codice
                        AND b2.nome IS NOT NULL 
                        AND b2.codiceUtente IS NOT NULL
                    ) AS dist_bachecas
                  ) AS bachecasCreadas,
                  (
                    SELECT COUNT(*) 
                    FROM (
                      SELECT DISTINCT c2.nomeBacheca, c2.codUtente
                      FROM UtenteAutorizzatoBacheca c2
                      WHERE c2.utenteAutorizzato = u.codice
                        AND c2.nomeBacheca IS NOT NULL 
                        AND c2.codUtente IS NOT NULL
                    ) AS dist_autorizzate
                  ) AS bachecasAcceso
                FROM Utente u
                WHERE 1=1;
                ";
      $params = [];

      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["codice"])) {
          $query .= " AND u.codice = :codice"; // placeholder
          $params[":codice"] = $_POST["codice"];
        }
        if (!empty($_POST["nickname"])) {
          $query .= " AND u.nickname = :nickname";
          $params[":nickname"] = $_POST["nickname"];
        }
        if (!empty($_POST["nome"])) {
          $query .= " AND u.nome = :nome";
          $params[":nome"] = $_POST["nome"];
        }
        if (!empty($_POST["cognome"])) {
          $query .= " AND u.cognome = :cognome";
          $params[":cognome"] = $_POST["cognome"];
        }
        if (!empty($_POST["dataNascita"])) {
          $query .= " AND u.dataNascita = :dataNascita";
          $params[":dataNascita"] = $_POST["dataNascita"];
        }
      }
      $query .= " GROUP BY u.codice, u.nickname, u.nome, u.cognome, u.dataNascita";

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
          <th>Code</th>
          <th>Nickname</th>
          <th>Name</th>
          <th>Surname</th>
          <th>Birthday</th>
          <th style="word-wrap: break-word; white-space: normal; max-width: 150px;">Noticeboards that has created</th>
          <th style="word-wrap: break-word; white-space: normal; max-width: 150px;">Noticeboards to which it has access</th>
          <!-- <th>Groups that has created</th>
          <th>Groups to which it has access</th> -->
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
          <td id="<?php echo $row["codice"]; ?>_bachecasCreadas"> <?php echo $row["bachecasCreadas"]; ?></td>
          <td id="<?php echo $row["codice"]; ?>_bachecasAcceso"> <?php echo $row["bachecasAcceso"]; ?></td>
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
      User code: <input type="text" name="codice" readonly><br>
      Nickname: <input type="text" name="nickname"><br>
      First name: <input type="text" name="nome"><br>
      Last name: <input type="text" name="cognome"><br>
      Birthday: <input type="date" name="dataNascita"><br>
    </form>
  </div>
  <div id="add_dialog" title="Add record" style="display: none;">
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
