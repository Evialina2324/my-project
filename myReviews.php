<?php
require_once __DIR__ . "/menu.php";
$login = $_SESSION['login'];
$stmt = $conn->prepare("
  SELECT r.ocena, r.tresc, r.data, k.id AS idKsiazki, k.nazwa
  FROM recenzje r JOIN ksiazki k ON k.id = r.idKsiazki
  WHERE r.nick = ?
  ORDER BY r.data DESC
");
$stmt->bind_param("s", $login);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>Moje recenzje</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <h1>Moje recenzje</h1>
  <?php
  if ($res->num_rows > 0) {
      while ($rev = $res->fetch_object()) {
          echo "<article class='review'>";
          echo "<p class='rev-meta'><b>Data:</b> {$rev->data} <b>Książka:</b> <a href='details.php?id={$rev->idKsiazki}'>".htmlspecialchars($rev->nazwa)."</a> <b>Ocena:</b> {$rev->ocena}/5</p>";
          echo "<div class='rev-body'>".nl2br(htmlspecialchars($rev->tresc))."</div>";
          echo "</article>";
      }
  } else {
      echo "<p>Nie dodałaś/eś jeszcze żadnej recenzji.</p>";
  }
  ?>
</div>
</body>
</html>
