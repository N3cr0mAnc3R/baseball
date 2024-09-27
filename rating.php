<?php
include 'header.php';
// Verbindung zur Datenbank herstellen
$pdo = new PDO('mysql:host=127.127.126.50;dbname=baseball_club', 'root', '');

// Abrufen der Spieler und ihrer Positionen
$query = $pdo->query("SELECT p.name, r.position, p.wins, p.losses, p.points
                      FROM ranking r
                      JOIN players p ON p.id = r.player_id
                      ORDER BY r.position ASC");

$players = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Spieler-Ranking</title>
</head>
<body>
    <h1>Spieler-Ranking</h1>
    <table border="1">
        <tr>
            <th>Platz</th>
            <th>Name des Spielers</th>
            <th>Siege</th>
            <th>Niederlagen</th>
            <th>Punkte</th>
        </tr>
        <?php foreach ($players as $player): ?>
            <tr>
                <td><?= $player['position'] ?></td>
                <td><?= $player['name'] ?></td>
                <td><?= $player['wins'] ?></td>
                <td><?= $player['losses'] ?></td>
                <td><?= $player['points'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
