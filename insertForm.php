<?php
require_once("session.php");
require_once("db.php");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodaj książkę</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php require("menu.php"); ?>

<main class="container">
    <h1>Dodaj nową książkę</h1>
    <form action="insert.php" method="post" enctype="multipart/form-data" class="form-card">
        <p>Obrazek: <input type="file" name="obrazek" accept="image/*" required></p>
        <p>Nazwa: <input type="text" name="nazwa" required></p>
        <p>Autor: <input type="text" name="autor" required></p>
        <p>Ilość stron: <input type="number" name="ilosc_stron" required></p>
        <p>Opis: <textarea name="opis" required></textarea></p>
        <p>Kategoria:
            <select name="idKategorii" required>
                <?php
                $sql = "SELECT id, nazwa FROM kategorie ORDER BY nazwa";
                $result = $conn->query($sql);
                while ($row = $result->fetch_object()) {
                    echo "<option value='".(int)$row->id."'>".htmlspecialchars($row->nazwa)."</option>";
                }
                ?>
            </select>
        </p>
        <p><button type="submit">Dodaj</button></p>
    </form>
    <p><a href="index.php">Powrót</a></p>
</main>
</body>
</html>
<?php $conn->close(); ?>
