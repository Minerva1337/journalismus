
<?php
include 'auth.php';
include 'db.php';

// Ausschlüsse hinzufügen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['block1'], $_POST['block2'])) {
    $b1 = min($_POST['block1'], $_POST['block2']);
    $b2 = max($_POST['block1'], $_POST['block2']);
    if ($b1 !== $b2) {
        $stmt = $conn->prepare("INSERT IGNORE INTO block_exclusions (block_id_1, block_id_2) VALUES (?, ?)");
        $stmt->bind_param("ii", $b1, $b2);
        $stmt->execute();
    }
}

// Ausschluss löschen
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM block_exclusions WHERE id = $id");
}

// Daten laden
$blocks = $conn->query("SELECT * FROM blocks ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$exclusions = $conn->query("SELECT e.id, b1.name AS name1, b2.name AS name2
                            FROM block_exclusions e
                            JOIN blocks b1 ON e.block_id_1 = b1.id
                            JOIN blocks b2 ON e.block_id_2 = b2.id
                            ORDER BY b1.name, b2.name")->fetch_all(MYSQLI_ASSOC);
?>

<h2>🛑 Block-Kombinationen ausschließen</h2>

<form method="post">
    <label>Block 1:
        <select name="block1">
            <?php foreach ($blocks as $b): ?>
                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Block 2:
        <select name="block2">
            <?php foreach ($blocks as $b): ?>
                <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <button type="submit">➕ Hinzufügen</button>
</form>

<h3>❌ Bestehende Ausschlüsse</h3>
<table border="1" cellpadding="5">
    <tr><th>Block 1</th><th>Block 2</th><th>Aktion</th></tr>
    <?php foreach ($exclusions as $e): ?>
        <tr>
            <td><?= htmlspecialchars($e['name1']) ?></td>
            <td><?= htmlspecialchars($e['name2']) ?></td>
            <td><a href="?delete=<?= $e['id'] ?>" onclick="return confirm('Löschen?')">🗑️ Löschen</a></td>
        </tr>
    <?php endforeach; ?>
</table>
