<?php
require_once("session.php");
require_once("db.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: insertForm.php");
    exit;
}

// proste sprawdzenia obrazka
if (!isset($_FILES['obrazek']) || $_FILES['obrazek']['error'] !== UPLOAD_ERR_OK) {
    die("Błąd uploadu obrazka.");
}

$allowed = ['jpg','jpeg','png','gif','webp'];
$originalName = $_FILES["obrazek"]["name"];
$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
if (!in_array($ext, $allowed)) die("Nieprawidłowy format pliku.");

$targetDir = __DIR__ . "/obrazki/";
if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

// unikalna nazwa
$newName = uniqid('img_', true) . "." . $ext;
$targetFile = $targetDir . $newName;

if (!move_uploaded_file($_FILES["obrazek"]["tmp_name"], $targetFile)) {
    die("Nie udało się zapisać pliku.");
}

// bezpieczne wartości
$nazwa = $conn->real_escape_string($_POST['nazwa']);
$autor = $conn->real_escape_string($_POST['autor']);
$opis = $conn->real_escape_string($_POST['opis']);
$ilosc_stron = (int)$_POST['ilosc_stron'];
$idKategorii = (int)$_POST['idKategorii'];

$stmt = $conn->prepare("INSERT INTO ksiazki (nazwa, autor, opis, ilosc_stron, idKategorii, obrazek) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssiss", $nazwa, $autor, $opis, $ilosc_stron, $idKategorii, $newName);
$stmt->execute();

$conn->close();

header("Location: index.php");
exit;
?>
