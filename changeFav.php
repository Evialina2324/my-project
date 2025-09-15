<?php
require_once("session.php");
require_once("db.php");

header('Content-Type: text/plain; charset=utf-8');

if (!isset($_SESSION['id'])) {
    echo "błąd";
    exit;
}

$idKsiazki = isset($_POST['idKsiazki']) ? (int)$_POST['idKsiazki'] : 0;
$idUzytkownika = (int)$_SESSION['id'];
if ($idKsiazki <= 0) { echo "błąd"; exit; }

$stmt = $conn->prepare("SELECT id FROM ulubione WHERE idKsiazki = ? AND idUzytkownika = ?");
$stmt->bind_param("ii", $idKsiazki, $idUzytkownika);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 1) {
    $id = $res->fetch_object()->id;
    $del = $conn->prepare("DELETE FROM ulubione WHERE id = ?");
    $del->bind_param("i", $id);
    $ok = $del->execute();
} else {
    $ins = $conn->prepare("INSERT INTO ulubione (idKsiazki, idUzytkownika) VALUES (?, ?)");
    $ins->bind_param("ii", $idKsiazki, $idUzytkownika);
    $ok = $ins->execute();
}

if ($ok) echo "sukces";
else echo "błąd";
$conn->close();
?>
