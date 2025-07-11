
<?php
session_start();
include '../admin/db.php';

if (!isset($_POST['selected_blocks']) || !is_array($_POST['selected_blocks'])) {
    echo "Keine Auswahl übermittelt.";
    exit;
}

$block_ids = $_POST['selected_blocks'];
$placeholders = implode(',', array_fill(0, count($block_ids), '?'));

$types = str_repeat('i', count($block_ids));
$stmt = $conn->prepare("SELECT * FROM blocks WHERE id IN ($placeholders)");
$stmt->bind_param($types, ...$block_ids);
$stmt->execute();
$result = $stmt->get_result();

$blocks = [];
while ($row = $result->fetch_assoc()) {
    $blocks[] = $row;
}

// Berechnung
$kosten = 0;
$reichweite = 0;
$qualitätSumme = 0;

foreach ($blocks as $b) {
    $kosten += $b['kosten'];
    $reichweite += $b['reichweite'];
    $qualitätSumme += $b['qualität'];
}

$anzahl = count($blocks);
$durchschnittQualität = $anzahl > 0 ? $qualitätSumme / $anzahl : 0;
$qualitätsfaktor = $durchschnittQualität / 10;
$umsatz = ($reichweite * $qualitätsfaktor) - $kosten;
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Auswertung</title>
</head>
<body>
  <h2>📊 Auswertung</h2>

  <p>Gewählte Blöcke: <?= $anzahl ?></p>
  <p>💰 Gesamtkosten: <?= number_format($kosten, 2) ?> €</p>
  <p>📡 Gesamtreichweite: <?= $reichweite ?></p>
  <p>🎯 Durchschnittliche Qualität: <?= number_format($durchschnittQualität, 2) ?> / 10</p>
  <p><strong>📈 Ergebnis (Umsatz): <?= number_format($umsatz, 2) ?> €</strong></p>

  <a href="play.php">🔁 Zurück zum Spiel</a>
</body>
</html>
