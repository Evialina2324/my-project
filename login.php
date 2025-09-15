<?php
session_start();
require_once("db.php");

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $haslo = $_POST['haslo'] ?? '';

    if ($login && $haslo) {
        $stmt = $conn->prepare("SELECT id, haslo FROM uzytkownicy WHERE login=?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $user = $res->fetch_assoc();
            if (password_verify($haslo, $user['haslo'])) {
                $_SESSION['login'] = $login;
                $_SESSION['id'] = $user['id'];
                header("Location: index.php");
                exit;
            } else {
                $msg = "Nieprawidłowy login lub hasło.";
            }
        } else {
            $msg = "Nieprawidłowy login lub hasło.";
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
<title>Logowanie</title>
<style>
body { display:flex; justify-content:center; align-items:center; min-height:100vh; margin:0; font-family:Arial,sans-serif; background:#f6f8fa; }
.form-card { background:white; padding:30px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.1); width:320px; text-align:center; }
.form-card input { width:100%; padding:10px; margin:10px 0; border-radius:6px; border:1px solid #ccc; }
.form-card button { width:100%; padding:10px; border:none; background:#2b7a78; color:white; border-radius:6px; cursor:pointer; }
.form-card button:hover { opacity:0.9; }
.error { color:red; font-size:14px; }
.muted { font-size:13px; color:#6b7280; margin-top:10px; }
</style>
</head>
<body>
<div class="form-card">
<h1>Zaloguj się</h1>
<?php if ($msg) echo "<p class='error'>".htmlspecialchars($msg)."</p>"; ?>
<form method="post" action="">
<input type="text" name="login" placeholder="Login" required autofocus>
<input type="password" name="haslo" placeholder="Hasło" required>
<button type="submit">Zaloguj</button>
</form>
<p class="muted">Nie masz konta? <a href="registration.php">Zarejestruj się</a></p>
</div>
</body>
</html>
