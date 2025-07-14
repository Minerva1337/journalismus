<?php
include('../admin/db.php');

// Gruppe auswählen (aus Session oder GET – je nach Spielmechanik)
$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 1;

// Beim Absenden Blöcke speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['block_ids'])) {
    $block_ids = $_POST['block_ids'];

    // Bestehende Zuordnungen der Gruppe löschen
    $stmt_delete = $conn->prepare("DELETE FROM group_blocks WHERE group_id = ?");
    $stmt_delete->bind_param("i", $group_id);
    $stmt_delete->execute();

    // Neue Zuordnungen einfügen
    $stmt_insert = $conn->prepare("INSERT INTO group_blocks (group_id, block_id) VALUES (?, ?)");
    foreach ($block_ids as $block_id) {
        $block_id = intval($block_id);
        $stmt_insert->bind_param("ii", $group_id, $block_id);
        $stmt_insert->execute();
    }

    header("Location: play.php?group_id=" . $group_id);
    exit;
}

// Aktuell zugewiesene Blöcke laden
$sql = "SELECT id FROM group_blocks WHERE group_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $group_id);
$stmt->execute();
$result = $stmt->get_result();
$selected_blocks = [];
while ($row = $result->fetch_assoc()) {
    $selected_blocks[] = $row['id'];
}

// Alle verfügbaren Blöcke laden
$blocks = $conn->query("SELECT * FROM blocks ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Blöcke auswählen</title>
</head>
<body>
<h2>Blöcke für Gruppe <?= $group_id ?> auswählen</h2>

<form method="post">
    <?php foreach ($blocks as $block): ?>
        <label>
            <input type="checkbox" name="block_ids[]" value="<?= $block['id'] ?>"
                <?= in_array($block['id'], $selected_blocks) ? 'checked' : '' ?>>
            <?= htmlspecialchars($block['name']) ?> (RW: <?= $block['reichweite'] ?>, Q: <?= $block['qualität'] ?>)
        </label><br>
    <?php endforeach; ?>
    <br>
    <button type="submit">Ergebnisse berechnen</button>
</form>

</body>
</html>
