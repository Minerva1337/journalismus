<?php
include 'auth.php'; // Zugangskontrolle
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: sans-serif; background: #f9f9f9; margin: 0; padding: 20px; }
        h1 { color: #333; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 10px; }
        a { text-decoration: none; color: #0066cc; font-size: 1.1em; }
        a:hover { text-decoration: underline; }
        .section { margin-bottom: 30px; }
    </style>
</head>
<body>

<h1>🛠️ Admin Center – Übersicht</h1>

<div class="section">
    <h2>📦 Blöcke</h2>
    <ul>
        <li><a href="blocks.php">Alle Blöcke verwalten</a></li>
        <li><a href="blocks_add.php">+ Neuen Block hinzufügen</a></li>
    </ul>
</div>

<div class="section">
    <h2>🧩 Slots</h2>
    <ul>
        <li><a href="slots.php">Alle Slots verwalten</a></li>
        <li><a href="slots_add.php">+ Neuen Slot hinzufügen</a></li>
    </ul>
</div>

<div class="section">
    <h2>🚫 Block-Ausschlüsse</h2>
    <ul>
        <li><a href="exclusions.php">Inkompatible Block-Kombinationen verwalten</a></li>
    </ul>
</div>

<div class="section">
    <h2>👥 Gruppen</h2>
    <ul>
        <li><a href="groups.php">Gruppen verwalten</a></li>
        <li><a href="groups_compare.php">📊 Gruppen vergleichen</a></li>
    </ul>
</div>

<div class="section">
    <h2>💰 Ergebnisse</h2>
    <ul>
        <li><a href="profit_overview.php">Übersicht: Ergebnisse & Budgets</a></li>
    </ul>
</div>

<div class="section">
    <ul>
        <li><a href="logout.php">🔓 Logout</a></li>
    </ul>
</div>

</body>
</html>