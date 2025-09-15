<?php
require_once("session.php");
require_once("db.php");

$idUzytkownika = (int)$_SESSION["id"];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Ulubione książki</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require("menu.php"); ?>

<main class="container">
<h1>Twoje ulubione książki</h1>

<?php
$stmt = $conn->prepare("SELECT k.id, k.nazwa, k.obrazek 
                        FROM ksiazki k 
                        JOIN ulubione u ON u.idKsiazki = k.id 
                        WHERE u.idUzytkownika = ?");
$stmt->bind_param("i", $idUzytkownika);
$stmt->execute();
$resultKsiazka = $stmt->get_result();

if ($resultKsiazka->num_rows > 0) {
    echo "<div class='grid-list'>";
    while ($row = $resultKsiazka->fetch_object()) {
        $img = htmlspecialchars($row->obrazek ?: 'placeholder.png');
        echo "<article class='card'>";
        echo "<a href='details.php?id={$row->id}'><img src='obrazki/{$img}' class='card-img' alt=''></a>";
        echo "<h3 class='card-title'><a href='details.php?id={$row->id}'>" . htmlspecialchars($row->nazwa) . "</a></h3>";
        echo "</article>";
    }
    echo "</div>";
} else {
    echo "<p>Nie masz jeszcze żadnych ulubionych książek.</p>";
}

$conn->close();
?>
</main>
</body>
</html>
