<?php
include 'auth.php';
include 'db.php';

$result = $conn->query("SELECT * FROM blocks");
?>
<h2>Blöcke verwalten</h2>
<a href="blocks_add.php">+ Neuen Block erstellen</a>
<table border="1" cellpadding="5">
  <tr>
    <th>Name</th>
    <th>Kosten</th>
    <th>Reichweite</th>
    <th>Qualität</th>
    <th>Aktionen</th>
  </tr>
  <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= $row['kosten'] ?></td>
      <td><?= $row['reichweite'] ?></td>
      <td><?= $row['qualität'] ?></td>
      <td>
        <a href="blocks_edit.php?id=<?= $row['id'] ?>">Bearbeiten</a> |
        <a href="blocks_delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Wirklich löschen?')">Löschen</a>
      </td>
    </tr>
  <?php endwhile; ?>
</table>
