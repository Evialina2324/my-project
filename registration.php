<?php
session_start();
require_once("db.php");

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $haslo = $_POST['haslo'] ?? '';

    if ($login && $email && $haslo) {
        // Sprawdź czy login już istnieje
        $stmt = $conn->prepare("SELECT id FROM uzytkownicy WHERE login=? OR email=?");
        $stmt->bind_param("ss", $login, $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows > 0) {
            $msg = "Użytkownik z tym loginem lub emailem już istnieje.";
        } else {
            $hash = password_hash($haslo, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO uzytkownicy (login, haslo, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $login, $hash, $email);
            if ($stmt->execute()) {
                $msg = "Zarejestrowano pomyślnie. Możesz się teraz zalogować.";
            } else {
                $msg = "Błąd przy rejestracji.";
            }
        }
    } else {
        $msg = "Wypełnij wszystkie pola.";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Rejestracja</title>
<style>
body { display:flex; justify-content:center; align-items:center; min-height:100vh; margin:0; font-family:Arial,sans-serif; background:#f6f8fa; }
.form-card { background:white; padding:30px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); width:320px; text-align:center; }
.form-card input { width:100%; padding:10px; margin:10px 0; border-radius:6px; border:1px solid #ccc; }
.form-card button { width:100%; padding:10px; border:none; background:#2b7a78; color:white; border-radius:6px; cursor:pointer; }
.form-card button:hover { opacity:0.9; }
.error { color:red; font-size:14px; }
.success { color:green; font-size:14px; }
.muted { font-size:13px; color:#6b7280; margin-top:10px; }
</style>
</head>
<body>
<div class="form-card">
<h1>Rejestracja</h1>
<?php if ($msg) echo "<p class='".(strpos($msg,'pomyślnie')!==false?'success':'error')."'>".htmlspecialchars($msg)."</p>"; ?>
<form method="post" action="">
<input type="text" name="login" placeholder="Login" required>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="haslo" placeholder="Hasło" required>
<button type="submit">Zarejestruj się</button>
</form>
<p class="muted">Masz już konto? <a href="login.php">Zaloguj się</a></p>
</div>
</body>
</html>
