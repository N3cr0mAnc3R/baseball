<?php
include 'header.php';
include 'db.php'; 


// Wir erhalten die Daten der Spieler und ihre Positionen
$query = $pdo->query("SELECT p.name, r.position, p.wins, p.losses, p.points, p.id
                      FROM ranking r
                      JOIN players p ON p.id = r.player_id
                      ORDER BY r.position ASC");

$players = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Spielerrangliste</title>
</head>
<body>
 <div class="container">
    <h1>Spielerrangliste</h1>

    <table class="table table-bordered table-striped">
        <tr>
            <th>Platz</th>
            <th>Spielername</th>
            <th>Siege</th>
            <th>Niederlagen</th>
            <th>Punkte</th>
        </tr>
        <?php foreach ($players as $player): ?>
            <tr>
                <td><?= $player['position'] ?></td>
		<td><a href="/rating.php/<?= $player['id'] ?>"><?= $player['name'] ?></a></td>
                <td><?= $player['wins'] ?></td>
                <td><?= $player['losses'] ?></td>
                <td><?= $player['points'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>    
</div>
</body>
</html>
