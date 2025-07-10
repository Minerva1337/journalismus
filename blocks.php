<?php
include 'db.php';
$result = $conn->query("SELECT * FROM blocks");
?>
<h2>Blöcke verwalten</h2>
<a href="blocks_add.php">+ Neuen Block erstellen</a>
<table border="1" cellpadding="5">
  <tr>
    <th>Name</th>
    <th>Kategorie</th>
    <th>Basiswert</th>
    <th>Faktor</th>
    <th>Max Budget</th>
    <th>Aktionen</th>
  </tr>
  <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= htmlspecialchars($row['category']) ?></td>
      <td><?= $row['base_value'] ?></td>
      <td><?= $row['multiplier'] ?></td>
      <td><?= $row['max_budget'] ?></td>
      <td>
        <a href="blocks_edit.php?id=<?= $row['id'] ?>">Bearbeiten</a> |
        <a href="blocks_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Wirklich löschen?')">Löschen</a>
      </td>
    </tr>
  <?php endwhile; ?>
</table>
