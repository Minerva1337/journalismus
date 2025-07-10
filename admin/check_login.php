<?php
session_start();
include 'db.php';

// Beispiel-Admin-Zugangsdaten (besser: aus DB!)
$admin_user = 'admin';
$admin_pass = 'geheim123'; // besser: Passwort-Hash + DB später!

$user = $_POST['username'];
$pass = $_POST['password'];

if ($user === $admin_user && $pass === $admin_pass) {
    $_SESSION['admin'] = true;
    header("Location: dashboard.php");
    exit;
} else {
    $_SESSION['error'] = "Falsche Zugangsdaten!";
    header("Location: login.php");
    exit;
}
