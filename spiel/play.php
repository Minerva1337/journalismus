<?php
session_start();
include '../admin/db.php';

if (!isset($_SESSION['group_id'])) {
    header("Location: index.php");
    exit;
}

$group_id = $_SESSION['group_id'];

// Gruppe laden
$group_stmt = $conn->prepare("SELECT * FROM groups WHERE id = ?");
$group_stmt->bind_param("i", $group_id);
$group_stmt->execute();
$group = $group_stmt->get_result()->fetch_assoc();
$original_budget = $group['budget'];

// Blöcke & Slots laden
$blocks = $conn->query("SELECT * FROM blocks")->fetch_all(MYSQLI_ASSOC);
$slots = $conn->query("SELECT * FROM slots")->fetch_all(MYSQLI_ASSOC);

// Belegungen der Gruppe laden
$belegungen = [];
$res = $conn->prepare("SELECT slot_id, block_id FROM slot_belegungen WHERE group_id = ?");
$res->bind_param("i", $group_id);
$res->execute();
$result = $res->get_result();
while ($row = $result->fetch_assoc()) {
    $belegungen[$row['slot_id']] = $row['block_id'];
}

// Ausschlüsse laden
$exclusion_res = $conn->query("SELECT block_id_1, block_id_2 FROM block_exclusions");
$exclusions = [];
while ($row = $exclusion_res->fetch_assoc()) {
    $exclusions[] = [(int)$row['block_id_1'], (int)$row['block_id_2']];
}

// Blöcke in Map
$block_map = [];
foreach ($blocks as $block) {
    $block_map[$block['id']] = $block;
}

// Restbudget berechnen
$verbrauchtes_budget = 0;
foreach ($belegungen as $block_id) {
    if (isset($block_map[$block_id])) {
        $verbrauchtes_budget += $block_map[$block_id]['kosten'];
    }
}
$verbleibendes_budget = $original_budget - $verbrauchtes_budget;

// Slots strukturieren
$grid = [];
foreach ($slots as $slot) {
    $grid[$slot['row']][$slot['col']] = $slot;
}
ksort($grid);
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <link rel="stylesheet" href="/assets/style.css">
  <meta charset="UTF-8">
  <title>Planspiel</title>
  <style>
    body { font-family: sans-serif; }
    .container { display: flex; gap: 40px; align-items: flex-start; }
    .column { border: 1px solid #aaa; padding: 10px; width: 300px; min-height: 400px; }
    .block { border: 1px solid #ccc; margin: 5px; padding: 10px; background: #f0f0f0; cursor: grab; }
    .slot-table { border-collapse: collapse; }
    .slot-cell { border: 1px solid #aaa; width: 200px; height: 150px; vertical-align: top; padding: 5px; }
    .drag-over { background-color: #e0ffe0; }
  </style>
</head>
<body>

<h2>Willkommen, Gruppe: <?= htmlspecialchars($group['name']) ?> | Budget: <span id="budget" data-raw="<?= $verbleibendes_budget ?>"><?= number_format($verbleibendes_budget, 2, ',', '.') ?></span> €

<div class="container">
  <div class="column" id="backlog">
    <h3>Backlog</h3>
    <?php
      foreach ($blocks as $block) {
          if (!in_array($block['id'], $belegungen)) {
              echo '<div class="block" draggable="true"
                  data-kosten="' . $block['kosten'] . '"
                  data-reichweite="' . $block['reichweite'] . '"
                  data-qualität="' . $block['qualität'] . '"
                  data-id="' . $block['id'] . '">';
              echo '<strong>' . htmlspecialchars($block['name']) . '</strong><br>';
              echo '<em>' . htmlspecialchars($block['description']) . '</em><br>';
              echo 'Kosten: ' . $block['kosten'] . ' €<br>';
              echo 'Reichweite: ' . $block['reichweite'] . '<br>';
              echo 'Qualität: ' . $block['qualität'];
              echo '</div>';
          }
      }
    ?>
  </div>

  <div>
    <h3>Slot-Tabelle</h3>
<div class="slot-container">
  <?php foreach ($slots as $slot): ?>
    <div class="slot" id="slot-<?= $slot['id'] ?>" data-slot-id="<?= $slot['id'] ?>">
      <strong><?= htmlspecialchars($slot['name']) ?></strong>
      <?php
        if (isset($belegungen[$slot['id']])) {
            $block = $block_map[$belegungen[$slot['id']]];
            echo '<div class="block" draggable="true"
                  data-kosten="' . $block['kosten'] . '"
                  data-reichweite="' . $block['reichweite'] . '"
                  data-qualität="' . $block['qualität'] . '"
                  data-id="' . $block['id'] . '">';
            echo '<strong>' . htmlspecialchars($block['name']) . '</strong><br>';
            echo '<em>' . htmlspecialchars($block['description']) . '</em><br>';
            echo 'Kosten: ' . $block['kosten'] . ' €<br>';
            echo 'Reichweite: ' . $block['reichweite'] . '<br>';
            echo 'Qualität: ' . $block['qualität'];
            echo '</div>';
        }
      ?>
    </div>
  <?php endforeach; ?>
</div>
  </div>
</div>

<br>
<form id="auswertungForm" method="post" action="auswertung.php">
  <input type="hidden" name="belegungen" id="belegungenInput">
  <button type="submit">Ergebnis berechnen</button>
</form>

<script>
const blockExclusions = <?= json_encode($exclusions) ?>;

function isCombinationForbidden(newId, existingIds) {
  return existingIds.some(existingId =>
    blockExclusions.some(pair =>
      (pair[0] == newId && pair[1] == existingId) || (pair[1] == newId && pair[0] == existingId)
    )
  );
}

let budget = parseFloat(document.getElementById("budget").dataset.raw);
const budgetDisplay = document.getElementById("budget");
let draggedElement = null;

document.addEventListener("dragstart", function (e) {
  if (e.target.classList.contains("block")) {
    draggedElement = e.target;
    setTimeout(() => e.target.style.display = "none", 0);
  }
});

document.addEventListener("dragend", function () {
  if (draggedElement) {
    draggedElement.style.display = "block";
    draggedElement = null;
  }
});

[...document.querySelectorAll(".slot"), document.getElementById("backlog")].forEach(container => {
  container.addEventListener("dragover", function (e) {
    e.preventDefault();
    this.classList.add("drag-over");
  });

  container.addEventListener("dragleave", function () {
    this.classList.remove("drag-over");
  });

  container.addEventListener("drop", function (e) {
    e.preventDefault();
    this.classList.remove("drag-over");
    if (!draggedElement || draggedElement.parentNode === this) return;

    const blockId = parseInt(draggedElement.dataset.id);
    const from = draggedElement.parentNode;
    const to = this;

    // Prüfe auf verbotene Kombinationen
    const allUsedBlockIds = Array.from(document.querySelectorAll(".slot .block"))
      .map(b => parseInt(b.dataset.id));
    if (isCombinationForbidden(blockId, allUsedBlockIds)) {
      alert("🚫 Diese Blockkombination ist laut Spielregel ausgeschlossen.");
      return;
    }

    const kosten = parseFloat(draggedElement.dataset.kosten);

    if (from.id === "backlog" && to.classList.contains("slot")) {
      if (budget >= kosten) {
        budget -= kosten;
        to.appendChild(draggedElement);
      } else {
        alert("Nicht genug Budget!");
      }
    } else if (from.classList.contains("slot") && to.id === "backlog") {
      budget += kosten;
      to.appendChild(draggedElement);
    }

    budgetDisplay.textContent = budget.toFixed(2);
  });
});

document.getElementById("auswertungForm").addEventListener("submit", function(e) {
  e.preventDefault();

  const belegungen = {};
document.querySelectorAll(".slot").forEach(slot => {
    const block = slot.querySelector(".block");
    if (block) {
      belegungen[slot.dataset.slotId] = block.dataset.id;
    }
  });

  if (Object.keys(belegungen).length === 0) {
    alert("Bitte mindestens einen Block platzieren.");
    return;
  }

  document.getElementById("belegungenInput").value = JSON.stringify(belegungen);
  this.submit();
});
</script>

</body>
</html>