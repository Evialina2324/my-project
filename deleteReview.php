<?php
require_once("session.php");
require_once("db.php");

// Pobranie ID recenzji
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0 || !isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Sprawdzenie, czy recenzja należy do zalogowanego użytkownika
$stmt = $conn->prepare("SELECT idKsiazki, nick FROM recenzje WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    header("Location: index.php");
    exit;
}
$rev = $res->fetch_assoc();

// Tylko autor może usuwać
if ($rev['nick'] !== $_SESSION['login']) {
    echo "Brak uprawnień do usunięcia tej recenzji.";
    exit;
}

// Usuń recenzję
$stmt = $conn->prepare("DELETE FROM recenzje WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$conn->close();
header("Location: details.php?id=" . (int)$rev['idDzbany']);
exit;
?>
