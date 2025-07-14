
<?php
include 'auth.php';
include 'db.php';

$result = $conn->query("
  SELECT g.name, e.umsatz, e.reichweite, e.qualitaet_durchschnitt
  FROM groups g
  LEFT JOIN ergebnisse e ON g.id = e.group_id
  ORDER BY e.umsatz DESC
");
?>

<h2>📊 Gruppenvergleich – Ergebnisse</h2>
<table border="1" cellpadding="5">
  <tr>
    <th>Gruppe</th>
    <th>Umsatz (€)</th>
    <th>Reichweite</th>
    <th>∅ Qualität</th>
  </tr>
  <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= number_format($row['umsatz'], 2) ?></td>
      <td><?= $row['reichweite'] ?></td>
      <td><?= number_format($row['qualitaet_durchschnitt'], 2) ?></td>
    </tr>
  <?php endwhile; ?>
</table>
