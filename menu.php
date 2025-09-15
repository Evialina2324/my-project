<?php
require_once("db.php");
if (session_status() === PHP_SESSION_NONE) session_start();

$sql = "SELECT id, nazwa FROM kategorie ORDER BY nazwa";
$resultKat = $conn->query($sql);
?>
<body style="background-color: <?= htmlspecialchars($_SESSION['kolor_tla'] ?? '#f6f8fa') ?>;">
<nav class="main-nav">
    <div class="nav-top">
        <a class="brand" href="index.php">Książki</a>
        <div class="user-actions">
            <?php if (isset($_SESSION["login"])): ?>
                <span>Witaj, <?= htmlspecialchars($_SESSION["login"]) ?></span>
                <a href="profile.php">Mój profil</a>
                <a href="favourites.php">Ulubione</a>
                <a href="myReviews.php">Moje recenzje</a>
                <a href="logout.php" class="btn-logout">Wyloguj</a>
            <?php else: ?>
                <a href="login.php">Zaloguj</a>
                <a href="registration.php">Zarejestruj</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="nav-add">
        <a href="insertForm.php" class="btn-add">Dodaj nową książkę</a>
    </div>

    <div class="nav-categories">
        <?php while($kat = $resultKat->fetch_object()): ?>
            <a href="index.php?idKat=<?= $kat->id ?>"><?= htmlspecialchars($kat->nazwa) ?></a>
        <?php endwhile; ?>
    </div>
</nav>
