<?php
include 'header.php';
session_start();

// Datenbankverbindung
$pdo = new PDO('mysql:host=127.127.126.50;dbname=baseball_club', 'root', '');

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
    <h1>Einloggen ins Admin-Panel</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="username">Benutzername:</label>
        <input type="text" name="username" required>
        
        <label for="password">Passwort:</label>
        <input type="password" name="password" required>
        
        <button type="submit">Einloggen</button>
    </form>
</body>
</html>
