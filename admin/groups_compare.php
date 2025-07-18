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

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Gruppenvergleich</title>
  <link rel="stylesheet" href="/assets/style.css"> <!-- Pfad ggf. anpassen -->
  <style>
    table {
      width: 80%;
      margin: 30px auto;
      border-collapse: collapse;
      background-color: #fff;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }

    th, td {
      border: 1px solid #ccc;
      padding: 12px;
      text-align: center;
    }

    th {
      background-color: var(--primary-color);
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    h2 {
      text-align: center;
      color: var(--secondary-color);
      margin-top: 30px;
    }
  </style>
</head>
<body>

<h2>Gruppenvergleich – Ergebnisse</h2>
<table>
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

</body>
</html>