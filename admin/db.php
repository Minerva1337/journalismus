<?php
$host = 'localhost:3306';
$user = 'lukas_uni';
$password = 'Mkpi44ja!'; // ggf. anpassen
$database = 'planspiel';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}
?>
