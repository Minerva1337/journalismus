<?php
include 'auth.php';
include 'db.php';

$id = $_GET['id'] ?? null;

// Daten laden
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $conn->prepare("SELECT * FROM blocks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $block = $stmt->get_result()->fetch_assoc();
    if (!$block) {
        echo "Block nicht gefunden.";
        exit;
    }
}

// Daten aktualisieren
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE blocks SET name = ?, description = ?, kosten = ?, reichweite = ?, qualität = ? WHERE id = ?");
    $stmt->bind_param("ssddii", $_POST['name'], $_POST['description'], $_POST['kosten'], $_POST['reichweite'], $_POST['qualität'], $_POST['id']);

    $stmt->execute();
    header("Location: blocks.php");
    exit;
}
?>

<h2>Block bearbeiten</h2>
<form method="post">
  <input type="hidden" name="id" value="<?= $block['id'] ?>">
  <label>Name: <input name="name" value="<?= htmlspecialchars($block['name']) ?>" required></label><br>
  <label>Beschreibung: <textarea name="description"><?= htmlspecialchars($block['description']) ?></textarea></label><br>
  <label>Kosten: <input name="kosten" type="number" step="0.01" value="<?= $block['kosten'] ?>" required></label><br>
    <label>Reichweite: <input name="reichweite" type="number" value="<?= $block['reichweite'] ?>" required></label><br>
    <label>Qualität: <input name="qualität" type="number" min="-100" max="100" value="<?= $block['qualität'] ?>" required></label><br>

  <button type="submit">Änderungen speichern</button>
</form>
<a href="blocks.php">⬅ Zurück</a>
