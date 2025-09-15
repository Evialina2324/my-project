<?php
require_once __DIR__ . "/session.php";
require_once __DIR__ . "/db.php";

if (!isset($_POST['idKsiazki'])) { header("Location: index.php"); exit; }
$idKsiazki = (int)$_POST['idKsiazki'];
$ocena = (int)$_POST['ocena'];
$tresc = trim($_POST['tresc']);
$nick = $_SESSION['login'] ?? 'Anon';

if ($tresc === '') { header("Location: details.php?id=$idKsiazki"); exit; }

$stmt = $conn->prepare("INSERT INTO recenzje (idKsiazki, nick, ocena, tresc) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isis", $idKsiazki, $nick, $ocena, $tresc);
$stmt->execute();
$conn->close();

header("Location: details.php?id=$idKsiazki");
exit;
?>
