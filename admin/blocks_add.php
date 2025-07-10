<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("INSERT INTO blocks (name, kosten, reichweite, qualität, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sddds", $_POST['name'], $_POST['kosten'], $_POST['reichweite'], $_POST['qualität'], $_POST['description']);

    $stmt->execute();
    header("Location: blocks.php");
    exit;
}
?>

<h2>Neuen Block hinzufügen</h2>
<form method="post">
  <label>Name: <input name="name" required></label><br>
  <label>Beschreibung: <textarea name="description"></textarea></label><br>
  <label>Kosten: <input name="kosten" type="number" step="0.01" required></label><br>
  <label>Reichweite: <input name="reichweite" type="number" required></label><br>
  <label>Qualität: <input name="qualität" type="number" min="1" max="10" required></label><br>
  <button type="submit">Speichern</button>
</form>
