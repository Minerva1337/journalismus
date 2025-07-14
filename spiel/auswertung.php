
<?php
session_start();
include '../admin/db.php';

if (!isset($_SESSION['group_id']) || !isset($_POST['belegungen'])) {
    echo "Ungültiger Zugriff.";
    exit;
}

$group_id = $_SESSION['group_id'];
$belegungen = json_decode($_POST['belegungen'], true);

if (!is_array($belegungen)) {
    echo "Ungültige Belegungsdaten.";
    exit;
}

// Bestehende Belegungen löschen
$stmt = $conn->prepare("DELETE FROM slot_belegungen WHERE group_id = ?");
if (!$stmt) {
    die("Fehler beim DELETE-Prepare: " . $conn->error);
}
$stmt->bind_param("i", $group_id);
$stmt->execute();

// Neue Belegungen einfügen
foreach ($belegungen as $slot_id => $block_id) {
    $stmt = $conn->prepare("INSERT INTO slot_belegungen (group_id, slot_id, block_id) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Fehler beim INSERT-Prepare: " . $conn->error);
    }
    $stmt->bind_param("iii", $group_id, $slot_id, $block_id);
    $stmt->execute();
}

// Alle verwendeten Blöcke laden
$block_ids = array_values($belegungen);
$placeholders = implode(',', array_fill(0, count($block_ids), '?'));
$types = str_repeat('i', count($block_ids));
$stmt = $conn->prepare("SELECT * FROM blocks WHERE id IN ($placeholders)");
if (!$stmt) {
    die("Fehler beim Block-SELECT: " . $conn->error);
}
$stmt->bind_param($types, ...$block_ids);
$stmt->execute();
$result = $stmt->get_result();

$blocks = [];
while ($row = $result->fetch_assoc()) {
    $blocks[] = $row;
}

// Ergebnis berechnen
$kosten = $reichweite = $qual_summe = 0;
foreach ($blocks as $b) {
    $kosten += $b['kosten'];
    $reichweite += $b['reichweite'];
    $qual_summe += $b['qualität'];
}
$anzahl = count($blocks);
$qual_durchschnitt = $anzahl ? $qual_summe / $anzahl : 0;
$qual_faktor = $qual_durchschnitt / 10;
$umsatz = ($reichweite * $qual_faktor) - $kosten;

// Ergebnis speichern
$stmt = $conn->prepare("REPLACE INTO ergebnisse (group_id, umsatz, reichweite, qualitaet_durchschnitt) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    die("Fehler beim Ergebnis-INSERT: " . $conn->error);
}
$stmt->bind_param("iddi", $group_id, $umsatz, $reichweite, $qual_durchschnitt);
$stmt->execute();
?>
<!DOCTYPE html>
<html lang="de">
<head><meta charset="UTF-8"><title>Ergebnis</title></head>
<body>
<h2>📊 Ergebnis</h2>
<p>💰 Umsatz: <?= number_format($umsatz, 2) ?> €</p>
<p>📡 Reichweite: <?= $reichweite ?></p>
<p>🎯 Qualität (∅): <?= number_format($qual_durchschnitt, 2) ?>/10</p>
<a href="play.php">🔁 Zurück zum Spiel</a>
</body>
</html>
