<?php
// db.php - połączenie z bazą
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "projekt"; // <-- zmienione

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    exit("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>
