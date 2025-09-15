<?php
require_once("session.php");
require_once("db.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header("Location: index.php"); exit; }

// średnia ocen
$stmt = $conn->prepare("SELECT AVG(ocena) AS srednia FROM recenzje WHERE idKsiazki = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$sredRes = $stmt->get_result();
$srednia = $sredRes->fetch_object()->srednia ?? 0;

// dane książki
$stmt = $conn->prepare("SELECT k.idKategorii, kat.nazwa AS nazwaKat, k.nazwa, k.autor, k.obrazek, k.opis, k.ilosc_stron 
                        FROM ksiazki k 
                        JOIN kategorie kat ON k.idKategorii = kat.id 
                        WHERE k.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) { header("Location: index.php"); exit; }
$row = $res->fetch_object();

$idUzytkownika = $_SESSION['id'] ?? 0;

// sprawdzenie ulubionych
$added = false;
if ($idUzytkownika) {
    $stmt2 = $conn->prepare("SELECT id FROM ulubione WHERE idKsiazki = ? AND idUzytkownika = ?");
    $stmt2->bind_param("ii", $id, $idUzytkownika);
    $stmt2->execute();
    $added = $stmt2->get_result()->num_rows > 0;
}

$heartFilled = "obrazki/IMG_3888.jpg";
$heartEmpty = "obrazki/IMG_3889.jpg";
$imgSrc = $added ? $heartFilled : $heartEmpty;

// pobranie recenzji
$stmt3 = $conn->prepare("SELECT id, nick, ocena, tresc, data FROM recenzje WHERE idKsiazki = ? ORDER BY data DESC");
$stmt3->bind_param("i", $id);
$stmt3->execute();
$res3 = $stmt3->get_result();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Szczegóły książki - <?= htmlspecialchars($row->nazwa) ?></title>
<link rel="stylesheet" href="style.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="script.js" defer></script>
</head>
<body>
<?php require("menu.php"); ?>

<main class="container">
  <div class="details">
    <div class="details-left">
      <img src="obrazki/<?= htmlspecialchars($row->obrazek ?: 'placeholder.png') ?>" 
           alt="<?= htmlspecialchars($row->nazwa) ?>" class="detail-img">
    </div>
    <div class="details-right">
      <h1><?= htmlspecialchars($row->nazwa) ?></h1>
      <p><strong>Autor:</strong> <?= htmlspecialchars($row->autor) ?></p>
      <p><strong>Kategoria:</strong> 
         <a href="index.php?idKat=<?= (int)$row->idKategorii ?>"><?= htmlspecialchars($row->nazwaKat) ?></a></p>
      <p><strong>Liczba stron:</strong> <?= (int)$row->ilosc_stron ?></p>
      <p><strong>Średnia ocen:</strong> <?= round((float)$srednia, 2) ?>/5</p>

      <img class="fav" data-ksiazka="<?= $id ?>" src="<?= $imgSrc ?>" alt="ulubione" title="Dodaj do ulubionych">

      <?php if ($idUzytkownika): ?>
        <p><a href="deleteBook.php?id=<?= $id ?>" onclick="return confirm('Czy na pewno usunąć książkę? Wszystkie recenzje zostaną usunięte.')">Usuń książkę</a></p>
      <?php endif; ?>

    </div>
  </div>

  <section class="review-add">
    <h2>Dodaj recenzję</h2>
    <form action="insertReview.php" method="post">
        <input type="hidden" name="idKsiazki" value="<?= $id ?>">
        <p>Ocena:
            <select name="ocena">
                <?php for ($i=1; $i<=5; $i++) echo "<option>$i</option>"; ?>
            </select>
        </p>
        <p>Treść: <textarea name="tresc" required></textarea></p>
        <p><input type="submit" value="Dodaj recenzję"></p>
    </form>
  </section>

  <section class="reviews">
    <h2>Recenzje</h2>
    <?php
    if ($res3->num_rows > 0) {
        while ($rev = $res3->fetch_object()) {
            echo "<article class='review'>";
            echo "<header><strong>" . htmlspecialchars($rev->nick) . "</strong> — {$rev->ocena}/5 <span class='rev-date'>{$rev->data}</span></header>";
            echo "<p>" . nl2br(htmlspecialchars($rev->tresc)) . "</p>";

            // link do usunięcia recenzji tylko dla autora
            if (($rev->nick ?? '') === ($_SESSION['login'] ?? '')) {
                echo "<a href='deleteReview.php?id={$rev->id}' onclick='return confirm(\"Czy na pewno usunąć recenzję?\")'>Usuń recenzję</a>";
            }

            echo "</article>";
        }
    } else {
        echo "<p>Brak recenzji.</p>";
    }
    ?>
  </section>

  <p><a href="index.php">Powrót do strony głównej</a></p>
</main>
</body>
</html>
