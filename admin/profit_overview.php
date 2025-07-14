<?php
include('auth.php');
include('db.php');

// Einnahmenformel: basis * reichweite * qualität
$sql = "SELECT b.name, b.reichweite, b.qualität, b.kosten, (b.reichweite * b.qualität) AS einnahme
        FROM blocks b";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head><title>Gewinnübersicht</title></head>
<body>
<h2>Gewinnübersicht</h2>
<table border='1'>
<tr><th>Name</th><th>Reichweite</th><th>Qualität</th><th>Kosten</th><th>Einnahme</th></tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= $row['reichweite'] ?></td>
    <td><?= $row['qualität'] ?></td>
    <td><?= number_format($row['kosten'], 2) ?> €</td>
    <td><?= number_format($row['einnahme'], 2) ?> €</td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
