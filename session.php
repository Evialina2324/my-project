<?php
// session.php - zarządzanie sesją i globalnymi ustawieniami
session_start();
require_once("db.php");

// Jeśli nie ustawiono, domyślny kolor tła
if (!isset($_SESSION['kolor_tla'])) {
    $_SESSION['kolor_tla'] = '#f6f8fa'; // domyślny kolor
}

// Sprawdzenie, czy dostęp do strony wymaga zalogowania
if (!defined('ALLOW_ANON') || !ALLOW_ANON) {
    if (!isset($_SESSION['id']) || $_SESSION['id'] <= 0) {
        // Jeśli użytkownik nie jest zalogowany, przekierowanie na logowanie
        header("Location: login.php");
        exit;
    }

    // Pobranie koloru tła z bazy dla zalogowanego użytkownika
    $idUzytkownika = (int)$_SESSION['id'];
    $stmt = $conn->prepare("SELECT kolor_tla FROM uzytkownicy WHERE id = ?");
    $stmt->bind_param("i", $idUzytkownika);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (!empty($user['kolor_tla'])) {
            $_SESSION['kolor_tla'] = $user['kolor_tla'];
        }
    }
}

// Funkcja dodawania alertu
function add_alert($message, $type = 'success') {
    if (!isset($_SESSION['alerts'])) {
        $_SESSION['alerts'] = [];
    }
    $_SESSION['alerts'][] = ['msg' => $message, 'type' => $type];
}

// Funkcja pobrania i wyczyszczenia alertów
function get_alerts() {
    $alerts = $_SESSION['alerts'] ?? [];
    unset($_SESSION['alerts']);
    return $alerts;
}

// Inne session checks (np. logowanie)
$user_id = $_SESSION['id'] ?? null;
$kolor_tla = $_SESSION['kolor_tla'] ?? '#f6f8fa';
?>
