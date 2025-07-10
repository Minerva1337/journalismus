<?php
include '../admin/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['group_id'] = $_POST['group_id'];
    header("Location: play.php");
    exit;
}

$groups = $conn->query("SELECT * FROM groups");
?>
<!DOCTYPE html>
<html lang="de">
<head><meta charset="UTF-8"><title>Gruppe wählen</title></head>
<body>
  <h2>Gruppe auswählen</h2>
  <form method="post">
    <select name="group_id" required>
      <option value="">-- Gruppe wählen --</option>
      <?php while ($g = $groups->fetch_assoc()): ?>
        <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?> (Budget: <?= $g['budget'] ?>)</option>
      <?php endwhile; ?>
    </select>
    <button type="submit">Spiel starten</button>
  </form>
</body>
</html>
