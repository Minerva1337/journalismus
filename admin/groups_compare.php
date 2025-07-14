<?php
include('auth.php');
include('db.php');

// Lade Gruppen aus der DB
$groups_res = $conn->query("SELECT id, name FROM groups");
$groups = [];
while ($row = $groups_res->fetch_assoc()) {
    $groups[] = $row;
}

// Bestimme ausgewählte Gruppe
$selected_group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : null;
$blocks = [];

if ($selected_group_id) {
    $stmt = $conn->prepare("
        SELECT b.name, b.reichweite, b.qualität
        FROM blocks b
        JOIN group_blocks gb ON b.id = gb.block_id
        WHERE gb.group_id = ?
    ");
    $stmt->bind_param("i", $selected_group_id);
    $stmt->execute();
    $blocks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gruppenvergleich</title>
</head>
<body>
<h2>Gruppe vergleichen</h2>

<form method="get">
    <label for="group">Gruppe wählen:</label>
    <select name="group_id" id="group" onchange="this.form.submit()">
        <option value="">-- Gruppe wählen --</option>
        <?php foreach ($groups as $g): ?>
            <option value="<?= $g['id'] ?>" <?= ($g['id'] == $selected_group_id) ? 'selected' : '' ?>>
                <?= htmlspecialchars($g['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($selected_group_id): ?>
    <h3>Blöcke dieser Gruppe</h3>
    <?php if (count($blocks) > 0): ?>
        <table border="1">
            <tr><th>Name</th><th>Reichweite</th><th>Qualität</th></tr>
            <?php
                $sum_r = 0; $sum_q = 0;
                foreach ($blocks as $b):
                    $sum_r += $b['reichweite'];
                    $sum_q += $b['qualität'];
            ?>
            <tr>
                <td><?= htmlspecialchars($b['name']) ?></td>
                <td><?= $b['reichweite'] ?></td>
                <td><?= $b['qualität'] ?></td>
            </tr>
            <?php endforeach; ?>
            <tr style="font-weight: bold;">
                <td>Durchschnitt</td>
                <td><?= round($sum_r / count($blocks), 2) ?></td>
                <td><?= round($sum_q / count($blocks), 2) ?></td>
            </tr>
        </table>
    <?php else: ?>
        <p>Keine Blöcke dieser Gruppe zugewiesen.</p>
    <?php endif; ?>
<?php endif; ?>
</body>
</html>
