<?php session_start(); ?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
</head>
<body>
  <h2>Admin Login</h2>
  <?php if (isset($_SESSION['error'])): ?>
    <p style="color:red"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
  <?php endif; ?>
  <form action="check_login.php" method="post">
    <label>Benutzername: <input name="username" required></label><br>
    <label>Passwort: <input name="password" type="password" required></label><br><br>
    <button type="submit">Einloggen</button>
  </form>
</body>
</html>
