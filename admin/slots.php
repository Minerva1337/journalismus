
<?php
include 'auth.php';
include 'db.php';

// Slot hinzufügen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
    $stmt = $conn->prepare("INSERT INTO slots (name) VALUES (?)");
    $stmt->bind_param("s", $_POST['name']);
    $stmt->execute();
    header("Location: slots.php");
    exit;
}

// Slot löschen
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM slots WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
    header("Location: slots.php");
    exit;
}

$result = $conn->query("SELECT * FROM slots ORDER BY id ASC");
?>

<h2>Slots verwalten</h2>
<form method="post">
  <label>Neuer Slot: <input name="name" required></label>
  <button type="submit">Hinzufügen</button>
</form>
<table border="1" cellpadding="5" style="margin-top:20px;">
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Aktionen</th>
  </tr>
  <?php while ($row = $result->fetch_assoc()): ?>
  <tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Slot löschen?')">🗑️ Löschen</a></td>
  </tr>
  <?php endwhile; ?>
</table>
