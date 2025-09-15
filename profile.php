<?php
require_once("session.php");
require_once("db.php");

$msg = '';
$hasloMsg = '';
$idUzytkownika = $_SESSION['id'] ?? 0;

// Pobierz dane użytkownika
$stmt = $conn->prepare("SELECT login, email, kolor_tla FROM uzytkownicy WHERE id = ?");
$stmt->bind_param("i", $idUzytkownika);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    header("Location: index.php");
    exit;
}
$user = $res->fetch_assoc();

// Zmiana koloru tła
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_color'])) {
    $color = $_POST['kolor_tla'] ?? '#f6f8fa';
    $upd = $conn->prepare("UPDATE uzytkownicy SET kolor_tla = ? WHERE id = ?");
    $upd->bind_param("si", $color, $idUzytkownika);
    if ($upd->execute()) {
        $msg = "Kolor tła został zmieniony pomyślnie.";
        $user['kolor_tla'] = $color;
        $_SESSION['kolor_tla'] = $color; // globalna zmiana
    } else {
        $msg = "Błąd przy zmianie koloru tła.";
    }
}

// Zmiana hasła
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $repeat = $_POST['repeat_password'] ?? '';

    if ($current && $new && $repeat) {
        if ($new !== $repeat) {
            $hasloMsg = "Nowe hasła nie są takie same.";
        } else {
            $stmtPwd = $conn->prepare("SELECT haslo FROM uzytkownicy WHERE id = ?");
            $stmtPwd->bind_param("i", $idUzytkownika);
            $stmtPwd->execute();
            $resPwd = $stmtPwd->get_result();
            $rowPwd = $resPwd->fetch_assoc();

            if ($rowPwd && password_verify($current, $rowPwd['haslo'])) {
                $newHash = password_hash($new, PASSWORD_DEFAULT);
                $updPwd = $conn->prepare("UPDATE uzytkownicy SET haslo = ? WHERE id = ?");
                $updPwd->bind_param("si", $newHash, $idUzytkownika);
                if ($updPwd->execute()) {
                    $hasloMsg = "Hasło zostało zmienione pomyślnie.";
                } else {
                    $hasloMsg = "Błąd podczas zmiany hasła.";
                }
            } else {
                $hasloMsg = "Aktualne hasło jest nieprawidłowe.";
            }
        }
    } else {
        $hasloMsg = "Wypełnij wszystkie pola.";
    }
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Mój Profil</title>
<link rel="stylesheet" href="style.css">
<style>
body { background-color: <?= htmlspecialchars($user['kolor_tla'] ?? '#f6f8fa') ?>; }
.form-card { max-width:500px; margin:20px auto; padding:20px; border-radius:10px; background:white; box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.form-card input, .form-card select { width:100%; padding:8px; margin:6px 0; border:1px solid #ccc; border-radius:6px;}
.form-card button { padding:10px; width:100%; border:none; border-radius:6px; background:#2b7a78; color:white; cursor:pointer; margin-top:10px;}
.form-card button:hover { opacity:0.9;}
.success { color:green; }
.error { color:red; }
h1 { text-align:center; }
</style>
</head>
<body>
<?php require("menu.php"); ?>

<div class="form-card">
<h1>Mój Profil</h1>

<p><strong>Login:</strong> <?= htmlspecialchars($user['login']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

<!-- Zmiana koloru tła -->
<h2>Personalizacja koloru tła</h2>
<?php if($msg): ?><p class="success"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
<form method="post" action="">
    <input type="hidden" name="change_color" value="1">
    <input type="color" name="kolor_tla" value="<?= htmlspecialchars($user['kolor_tla'] ?? '#f6f8fa') ?>">
    <button type="submit">Zmień kolor</button>
</form>

<!-- Zmiana hasła -->
<h2>Zmiana hasła</h2>
<?php if($hasloMsg): ?><p class="<?= strpos($hasloMsg,'pomyślnie')!==false ? 'success' : 'error' ?>"><?= htmlspecialchars($hasloMsg) ?></p><?php endif; ?>
<form method="post" action="">
    <input type="hidden" name="change_password" value="1">
    <input type="password" name="current_password" placeholder="Aktualne hasło" required>
    <input type="password" name="new_password" placeholder="Nowe hasło" required>
    <input type="password" name="repeat_password" placeholder="Powtórz nowe hasło" required>
    <button type="submit">Zmień hasło</button>
</form>
</div>

</body>
</html>
