<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("INSERT INTO blocks (name, description, base_value, multiplier, max_budget, category) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddds", $_POST['name'], $_POST['description'], $_POST['base_value'], $_POST['multiplier'], $_POST['max_budget'], $_POST['category']);
    $stmt->execute();
    header("Location: blocks.php");
    exit;
}
?>

<h2>Neuen Block hinzufügen</h2>
<form method="post">
  <label>Name: <input name="name" required></label><br>
  <label>Beschreibung: <textarea name="description"></textarea></label><br>
  <label>Basiswert: <input type="number" name="base_value" step="0.01" required></label><br>
  <label>Multiplikator: <input type="number" name="multiplier" step="0.01" required></label><br>
  <label>Max Budget: <input type="number" name="max_budget" required></label><br>
  <label>Kategorie: <input name="category"></label><br><br>
  <button type="submit">Speichern</button>
</form>
