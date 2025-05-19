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
                U.codice, 
                U.nickname, 
                U.nome, 
                U.cognome, 
                U.dataNascita, 
                (
                SELECT COUNT(*) 
                FROM (
                  SELECT DISTINCT b.nome, b.codiceUtente
                  FROM Bacheca b
                  WHERE b.codiceUtente = U.codice
                ) AS cero
                ) AS bachecasCreadas,
                (
                SELECT COUNT(*) 
                FROM (
                  SELECT DISTINCT c.nomeBacheca, c.codUtente
                  FROM UtenteAutorizzatoBacheca c
                  WHERE c.utenteAutorizzato = U.codice
                ) AS uno
                ) AS bachecasAcceso,
                (
                SELECT COUNT(*) 
                FROM (
                  SELECT DISTINCT d.codice
                  FROM Gruppo d
                  WHERE d.creatoDa = U.codice
                ) AS dos
                ) AS gruposCreados,
                (
                SELECT COUNT(*) 
                FROM (
                  SELECT DISTINCT e.codGruppo
                  FROM UtenteAutorizzatoGruppo e
                  WHERE e.codUtente = U.codice
                ) AS tres
                ) AS gruposAcceso
              FROM Utente U
              WHERE 1=1";
          $params = [];
          if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!empty($_POST["codice"])) {
              if (strpos($_POST["codice"], ',') !== false) {
                $values = array_map('trim', explode(',', $_POST["codice"]));
                $placeholders = [];
                foreach ($values as $index => $value) {
                  $placeholder = ":codice_$index";
                  $placeholders[] = $placeholder;
                  $params[$placeholder] = $value;
                }
                $query .= " AND U.codice IN (" . implode(',', $placeholders) . ")";
              } else {
                $query .= " AND U.codice = :codice";
                $params[":codice"] = $_POST["codice"];
              }
            }
            if (!empty($_POST["nickname"])) {
              if (strpos($_POST["nickname"], ',') !== false) {
                $values = array_map('trim', explode(',', $_POST["nickname"]));
                $placeholders = [];
                foreach ($values as $index => $value) {
                  $placeholder = ":nickname_$index";
                  $placeholders[] = $placeholder;
                  $params[$placeholder] = "%$value%";
                }
                $query .= " AND (" . implode(' OR ', array_map(fn($p) => "U.nickname LIKE $p", $placeholders)) . ")";
              } else {
                $query .= " AND U.nickname LIKE :nickname";
                $params[":nickname"] = "%" . $_POST["nickname"] . "%";
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
                $query .= " AND (" . implode(' OR ', array_map(fn($p) => "U.nome LIKE $p", $placeholders)) . ")";
              } else {
                $query .= " AND U.nome LIKE :nome";
                $params[":nome"] = "%" . $_POST["nome"] . "%";
              }
            }
            if (!empty($_POST["cognome"])) {
              if (strpos($_POST["cognome"], ',') !== false) {
                $values = array_map('trim', explode(',', $_POST["cognome"]));
                $placeholders = [];
                foreach ($values as $index => $value) {
                  $placeholder = ":cognome_$index";
                  $placeholders[] = $placeholder;
                  $params[$placeholder] = "%$value%";
                }
                $query .= " AND (" . implode(' OR ', array_map(fn($p) => "U.cognome LIKE $p", $placeholders)) . ")";
              } else {
                $query .= " AND U.cognome LIKE :cognome";
                $params[":cognome"] = "%" . $_POST["cognome"] . "%";
              }
            }
            if (!empty($_POST["dataNascita"])) {
              if (strpos($_POST["dataNascita"], ',') !== false) {
                $values = array_map('trim', explode(',', $_POST["dataNascita"]));
                $placeholders = [];
                foreach ($values as $index => $value) {
                  $placeholder = ":dataNascita_$index";
                  $placeholders[] = $placeholder;
                  $params[$placeholder] = $value;
                }
                $query .= " AND U.dataNascita IN (" . implode(',', $placeholders) . ")";
              } else {
                $query .= " AND U.dataNascita = :dataNascita";
                $params[":dataNascita"] = $_POST["dataNascita"];
              }
            }
          }
          $query .= " GROUP BY U.codice, U.nickname, U.nome, U.cognome, U.dataNascita";


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
          <th style="word-wrap: break-word; white-space: normal; max-width: 150px;">Groups that has created</th>
          <th style="word-wrap: break-word; white-space: normal; max-width: 150px;">Groups to which it has access</th>
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
          <td id="<?php echo $row["codice"]; ?>_dataNascita"> <?php echo date("d/m/Y", strtotime($row["dataNascita"])); ?></td>
          <td id="<?php echo $row["codice"]; ?>_bachecasCreadas">
            <a href="notice_board.php?search_filter=<?php echo urlencode($row["codice"]); ?>"> 
                <?php echo $row["bachecasCreadas"]; ?> 
            </a>
          </td>
          <td id="<?php echo $row["codice"]; ?>_bachecasAcceso">
            <a href="src/php/user-access/user_access.php?search_filter=<?php echo urlencode($row["codice"]); ?>"> 
              <?php echo $row["bachecasAcceso"]; ?> 
            </a>
          </td>
          <td id="<?php echo $row["codice"]; ?>_gruposCreados">
            <a href="group.php?search_filter=<?php echo urlencode($row["codice"]); ?>"> 
              <?php echo $row["gruposCreados"]; ?> 
            </a>
          </td>
          <td id="<?php echo $row["codice"]; ?>_gruposAcceso">
            <a href="src/php/user-access/user_access_g.php?search_filter=<?php echo urlencode($row["codice"]); ?>"> 
              <?php echo $row["gruposAcceso"]; ?> 
            </a>
          </td>
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