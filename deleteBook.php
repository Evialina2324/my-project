<?php
require_once("session.php");
require_once("db.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header("Location: index.php"); exit; }

// Usuń wszystkie recenzje powiązane z książką
$stmt = $conn->prepare("DELETE FROM recenzje WHERE idKsiazki = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Usuń wszystkie ulubione powiązane z książką
$stmt = $conn->prepare("DELETE FROM ulubione WHERE idKsiazki = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Usuń samą książkę
$stmt = $conn->prepare("DELETE FROM ksiazki WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$conn->close();
header("Location: index.php");
exit;
?>
