<?php
include('auth.php');
include('db.php');

// Beispiel: Vergleich der Gruppen nach Durchschnittsreichweite & Qualität
$sql = "SELECT g.name, AVG(b.reichweite) AS avg_reichweite, AVG(b.qualität) AS avg_qualität
        FROM groups g
        JOIN group_blocks gb ON g.id = gb.group_id
        JOIN blocks b ON gb.block_id = b.id
        GROUP BY g.name";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head><title>Gruppenvergleich</title></head>
<body>
<h2>Gruppenvergleich</h2>
<table border='1'>
<tr><th>Gruppe</th><th>Ø Reichweite</th><th>Ø Qualität</th></tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= round($row['avg_reichweite'], 2) ?></td>
    <td><?= round($row['avg_qualität'], 2) ?></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
