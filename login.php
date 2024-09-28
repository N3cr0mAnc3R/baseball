<?php
include 'header.php';
include 'db.php'; 
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Benutzer aus der Datenbank abrufen
    $stmt = $pdo->prepare("SELECT password FROM users WHERE name = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Anmeldedaten überprüfen
    if ($user && md5($password) === md5($user['password'])) {
        $_SESSION['loggedin'] = true; // Sitzung Variable setzen
        header('Location: admin.php'); // Zum Admin-Panel weiterleiten
        exit;
    } else {
        $error = "Falscher Benutzername oder Passwort.";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
<div class="container">
    <h1>Einloggen ins Admin-Panel</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST"class="col-12 col-md-4 col-sm-6 container">
<form>
  <div class="mb-3">
    <label for="username" class="form-label">Benutzername</label>
    <input type="text" class="form-control" id="username" name="username">
  </div>
  <div class="mb-3">
    <label for="password" class="form-label">Passwort</label>
    <input type="password" class="form-control" id="password" name="password">
  </div>
  <button type="submit" class="btn btn-primary">Einloggen</button>
</form>

    </form>
</div>
</body>
</html>
