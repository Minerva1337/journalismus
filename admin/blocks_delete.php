<?php
include 'auth.php';
include 'db.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo "Ungültige ID.";
    exit;
}

$stmt = $conn->prepare("DELETE FROM blocks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: blocks.php");
exit;
