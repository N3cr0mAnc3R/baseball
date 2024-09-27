<?php
include 'header.php';
// Подключение к базе данных
$pdo = new PDO('mysql:host=127.127.126.50;dbname=baseball_club', 'root', '');

// Получаем данные игроков и их позиции
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
    <title>Рейтинг игроков</title>
</head>
<body>
    <h1>Рейтинг игроков</h1>
    <table border="1">
        <tr>
            <th>Место</th>
            <th>Имя игрока</th>
            <th>Победы</th>
            <th>Поражения</th>
            <th>Очки</th>
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
