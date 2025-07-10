<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("INSERT INTO groups (name, budget) VALUES (?, ?)");
    $stmt->bind_param("sd", $_POST['name'], $_POST['budget']);
    $stmt->execute();
}
$result = $conn->query("SELECT * FROM groups");
?>

<h2>Gruppen & Budgets</h2>
<form method="post">
  <label>Gruppenname: <input name="name" required></label>
  <label>Budget: <input name="budget" type="number" step="0.01" required></label>
  <button type="submit">Hinzufügen</button>
</form>

<table border="1" cellpadding="5">
  <tr>
    <th>Name</th>
    <th>Budget</th>
  </tr>
  <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= $row['budget'] ?></td>
    </tr>
  <?php endwhile; ?>
</table>
