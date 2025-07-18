<?php
include '../admin/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['group_id'] = $_POST['group_id'];
    header("Location: play.php");
    exit;
}

$groups = $conn->query("SELECT * FROM groups");
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Gruppe wählen</title>
  <link rel="stylesheet" href="../assets/style.css"> <!-- ggf. Pfad anpassen -->
  <style>
    .group-select-container {
      max-width: 500px;
      margin: 80px auto;
      padding: 30px;
      background-color: #fff;
      border: 2px solid var(--primary-color);
      border-radius: 12px;
      text-align: center;
      box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
    }

    .group-select-container h2 {
      color: var(--secondary-color);
      margin-bottom: 20px;
    }

    select {
      padding: 10px;
      width: 100%;
      font-size: 1em;
      border-radius: 6px;
      border: 1px solid #ccc;
      margin-bottom: 20px;
    }

    button {
      background-color: var(--primary-color);
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      font-size: 1em;
      cursor: pointer;
    }

    button:hover {
      background-color: #d070e0;
    }
  </style>
</head>
<body>

<div class="group-select-container">
  <h2>🎯 Gruppe auswählen</h2>
  <form method="post">
    <select name="group_id" required>
      <option value="">-- Gruppe wählen --</option>
      <?php while ($g = $groups->fetch_assoc()): ?>
        <option value="<?= $g['id'] ?>">
          <?= htmlspecialchars($g['name']) ?> (Budget: <?= number_format($g['budget'], 2) ?> €)
        </option>
      <?php endwhile; ?>
    </select>
    <br>
    <button type="submit">🎮 Spiel starten</button>
  </form>
</div>

</body>
</html>