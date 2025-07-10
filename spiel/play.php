<?php
session_start();
include '../admin/db.php';

if (!isset($_SESSION['group_id'])) {
    header("Location: index.php");
    exit;
}

// Hole Budget der aktuellen Gruppe
$group_id = $_SESSION['group_id'];
$group_stmt = $conn->prepare("SELECT * FROM groups WHERE id = ?");
$group_stmt->bind_param("i", $group_id);
$group_stmt->execute();
$group = $group_stmt->get_result()->fetch_assoc();

// Hole alle Blöcke
$blocks = $conn->query("SELECT * FROM blocks")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Planspiel</title>
  <style>
    body { font-family: sans-serif; }
    .container { display: flex; gap: 40px; }
    .column { border: 1px solid #aaa; padding: 10px; width: 300px; min-height: 400px; }
    .block { border: 1px solid #ccc; margin: 5px; padding: 10px; background: #f0f0f0; cursor: grab; }
    .block.dragging { opacity: 0.5; }
    .info { margin-bottom: 10px; }
  </style>
</head>
<body>

<h2>Willkommen, Gruppe: <?= htmlspecialchars($group['name']) ?> | Budget: <span id="budget"><?= $group['budget'] ?></span> €</h2>

<div class="container">
  <div class="column" id="backlog">
    <h3>📦 Backlog</h3>
    <?php foreach ($blocks as $block): ?>
      <div class="block" draggable="true"
           data-kosten="<?= $block['kosten'] ?>"
           data-id="<?= $block['id'] ?>">
        <strong><?= htmlspecialchars($block['name']) ?></strong><br>
        <em><?= htmlspecialchars($block['description']) ?></em><br>
        Kosten: <?= $block['kosten'] ?> €<br>
        Reichweite: <?= $block['reichweite'] ?><br>
        Qualität: <?= $block['qualität'] ?>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="column" id="slots">
    <h3>🧩 Deine Auswahl</h3>
    <!-- Hier landen die ausgewählten Blöcke -->
  </div>
</div>

<script>
let budget = parseFloat(document.getElementById("budget").textContent);
const backlog = document.getElementById("backlog");
const slots = document.getElementById("slots");

let dragged = null;

document.querySelectorAll(".block").forEach(block => {
  block.addEventListener("dragstart", (e) => {
    dragged = block;
    block.classList.add("dragging");
  });

  block.addEventListener("dragend", () => {
    dragged = null;
    document.querySelectorAll(".block").forEach(b => b.classList.remove("dragging"));
  });
});

[backlog, slots].forEach(container => {
  container.addEventListener("dragover", e => e.preventDefault());

  container.addEventListener("drop", e => {
    e.preventDefault();
    if (!dragged) return;

    const kosten = parseFloat(dragged.getAttribute("data-kosten"));
    const from = dragged.parentElement;
    const to = container;

    // Block wird aus Backlog gezogen -> Budget verringern
    if (from.id === "backlog" && to.id === "slots") {
      if (budget >= kosten) {
        to.appendChild(dragged);
        budget -= kosten;
      } else {
        alert("Nicht genug Budget!");
        return;
      }
    }

    // Block wird zurück ins Backlog geschoben -> Budget zurückerstatten
    else if (from.id === "slots" && to.id === "backlog") {
      to.appendChild(dragged);
      budget += kosten;
    }

    document.getElementById("budget").textContent = budget.toFixed(2);
  });
});
<br><br>
<button onclick="berechneErgebnis()">📈 Ergebnis berechnen</button>

<div id="auswertung" style="margin-top: 20px; border-top: 1px solid #ccc; padding-top: 10px;"></div>

<script>
function berechneErgebnis() {
  const slots = document.querySelectorAll("#slots .block");
  let kosten = 0, reichweite = 0, qualitätSumme = 0;

  if (slots.length === 0) {
    alert("Bitte wähle mindestens einen Block aus.");
    return;
  }

  slots.forEach(block => {
    const k = parseFloat(block.dataset.kosten);
    const r = parseInt(block.dataset.reichweite);
    const q = parseInt(block.dataset.qualität);

    kosten += k;
    reichweite += r;
    qualitätSumme += q;
  });

  const durchschnittQualität = qualitätSumme / slots.length;
  const qualitätsfaktor = durchschnittQualität / 10;
  const umsatz = (reichweite * qualitätsfaktor) - kosten;

  document.getElementById("auswertung").innerHTML = `
    <h3>📊 Auswertung</h3>
    <p>✅ Anzahl ausgewählter Blöcke: ${slots.length}</p>
    <p>💰 Gesamtkosten: ${kosten.toFixed(2)} €</p>
    <p>📡 Gesamtreichweite: ${reichweite}</p>
    <p>🎯 Durchschnittliche Qualität: ${durchschnittQualität.toFixed(2)} / 10</p>
    <p><strong>📈 Umsatz (Ergebnis): ${umsatz.toFixed(2)} €</strong></p>
  `;
}
</script>


</body>
</html>
