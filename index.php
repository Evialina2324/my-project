<?php
require_once("session.php");
require_once("db.php");

// Pobranie filtrów
$search = $_GET['search'] ?? '';
$idKat = isset($_GET['idKat']) ? (int)$_GET['idKat'] : 0;
$minRating = $_GET['min_rating'] ?? '';
$sort = $_GET['sort'] ?? 'nazwa';

// Budowanie WHERE
$where = [];
$params = [];
$types = '';

if ($search !== '') {
    $where[] = 'd.nazwa LIKE ?';
    $params[] = '%' . $search . '%';
    $types .= 's';
}
if ($idKat > 0) {
    $where[] = 'd.idKategorii = ?';
    $params[] = $idKat;
    $types .= 'i';
}
if ($minRating !== '') {
    $where[] = '(SELECT AVG(ocena) FROM recenzje r WHERE r.idKsiazki = d.id) >= ?';
    $params[] = (float)$minRating;
    $types .= 'd';
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Sortowanie
$orderBy = match($sort) {
    'nazwa' => 'd.nazwa ASC',
    'popularnosc' => '(SELECT COUNT(*) FROM ulubione u WHERE u.idKsiazki = d.id) DESC',
    'srednia' => '(SELECT AVG(ocena) FROM recenzje r WHERE r.idKsiazki = d.id) DESC',
    default => 'd.nazwa ASC'
};

// Pobranie książek
$sql = "SELECT d.id, d.nazwa, d.obrazek FROM ksiazki d $whereSQL ORDER BY $orderBy";
$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Pobranie kategorii do menu
$kategorie = $conn->query("SELECT id, nazwa FROM kategorie ORDER BY nazwa");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Książki</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php require("menu.php"); ?>

<main class="container">

<!-- Filtr i wyszukiwarka -->
<form method="get" action="">
    <input type="text" name="search" placeholder="Szukaj po nazwie" value="<?= htmlspecialchars($search) ?>">

    <select name="idKat">
        <option value="0">Wszystkie kategorie</option>
        <?php while($rowKat = $kategorie->fetch_object()): ?>
            <option value="<?= (int)$rowKat->id ?>" <?= $idKat === (int)$rowKat->id ? 'selected' : '' ?>>
                <?= htmlspecialchars($rowKat->nazwa) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <input type="number" step="0.1" name="min_rating" placeholder="Min ocena" value="<?= htmlspecialchars($minRating) ?>">

    <select name="sort">
        <option value="nazwa" <?= $sort === 'nazwa' ? 'selected' : '' ?>>Nazwa</option>
        <option value="popularnosc" <?= $sort === 'popularnosc' ? 'selected' : '' ?>>Popularność</option>
        <option value="srednia" <?= $sort === 'srednia' ? 'selected' : '' ?>>Średnia ocena</option>
    </select>

    <button type="submit">Filtruj</button>
</form>

<!-- Lista książek -->
<?php
if ($result->num_rows > 0) {
    echo "<div class='grid-list'>";
    while ($row = $result->fetch_object()) {
        $img = htmlspecialchars($row->obrazek ?: 'placeholder.png');
        echo "<article class='card'>";
        echo "<a href='details.php?id={$row->id}'><img src='obrazki/{$img}' class='card-img' alt=''></a>";
        echo "<h3 class='card-title'><a href='details.php?id={$row->id}'>" . htmlspecialchars($row->nazwa) . "</a></h3>";
        echo "</article>";
    }
    echo "</div>";
} else {
    echo "<p>Nie znaleziono książek spełniających kryteria.</p>";
}
$conn->close();
?>
</main>
</body>
</html>
