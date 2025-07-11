
<?php
session_start();
include '../admin/db.php';

if (!isset($_SESSION['group_id'])) {
    header("Location: index.php");
    exit;
}

// Gruppe laden
$group_id = $_SESSION['group_id'];
$group_stmt = $conn->prepare("SELECT * FROM groups WHERE id = ?");
$group_stmt->bind_param("i", $group_id);
$group_stmt->execute();
$group = $group_stmt->get_result()->fetch_assoc();

// Blöcke und Slots laden
$blocks = $conn->query("SELECT * FROM blocks")->fetch_all(MYSQLI_ASSOC);
$slots = $conn->query("SELECT * FROM slots ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Planspiel</title>
  <style>
    body { font-family: sans-serif; }
    .container { display: flex; gap: 40px; }
    .column, .slot { border: 1px solid #aaa; padding: 10px; width: 300px; min-height: 150px; margin-top: 10px; }
    .block { border: 1px solid #ccc; margin: 5px; padding: 10px; background: #f0f0f0; cursor: grab; }
    .drag-over { background-color: #e0ffe0; }
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
           data-reichweite="<?= $block['reichweite'] ?>"
           data-qualität="<?= $block['qualität'] ?>"
           data-id="<?= $block['id'] ?>">
        <strong><?= htmlspecialchars($block['name']) ?></strong><br>
        <em><?= htmlspecialchars($block['description']) ?></em><br>
        Kosten: <?= $block['kosten'] ?> €<br>
        Reichweite: <?= $block['reichweite'] ?><br>
        Qualität: <?= $block['qualität'] ?>
      </div>
    <?php endforeach; ?>
  </div>

  <div>
    <h3>🧩 Deine Slots</h3>
    <?php foreach ($slots as $slot): ?>
      <div class="slot" id="slot-<?= $slot['id'] ?>" data-slot-id="<?= $slot['id'] ?>">
        <strong><?= htmlspecialchars($slot['name']) ?></strong>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<br>
<form id="auswertungForm" method="post" action="auswertung.php">
  <input type="hidden" name="selected_blocks[]" id="selectedBlocksInput" value="">
  <button type="submit">📈 Ergebnis berechnen</button>
</form>

<script>
let budget = parseFloat(document.getElementById("budget").textContent);
const budgetDisplay = document.getElementById("budget");

let draggedElement = null;

document.addEventListener("dragstart", function (e) {
  if (e.target.classList.contains("block")) {
    draggedElement = e.target;
    setTimeout(() => e.target.style.display = "none", 0);
  }
});

document.addEventListener("dragend", function (e) {
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

    const kosten = parseFloat(draggedElement.dataset.kosten);
    const from = draggedElement.parentNode;
    const to = this;

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
  const selected = Array.from(document.querySelectorAll(".slot .block"))
    .map(b => b.getAttribute("data-id"));

  if (selected.length === 0) {
    alert("Bitte wähle mindestens einen Block aus.");
    e.preventDefault();
    return;
  }

  const form = this;
  selected.forEach(id => {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "selected_blocks[]";
    input.value = id;
    form.appendChild(input);
  });
});
</script>

</body>
</html>
