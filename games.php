<?php
$title = 'Alle Spiele';
include 'header.php'; // Include your header file for navigation, styles, etc.
session_start();
include 'db.php'; 

// Fetch all games sorted by date in descending order
$query = $pdo->query("
    SELECT g.game_date, p1.name AS player1_name, p2.name AS player2_name, g.score_player1, g.score_player2
    FROM games g
    JOIN players p1 ON g.player1_id = p1.id
    JOIN players p2 ON g.player2_id = p2.id
    ORDER BY g.game_date DESC
");

$games = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Alle Spiele</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>   
 <div class="container">
    <h1>Alle Spiele</h1>

    <table>
        <thead>
            <tr>
                <th>Datum</th>
                <th>Spieler 1</th>
                <th>Punkte Spieler 1</th>
                <th>Punkte Spieler 2</th>      
                <th>Spieler 2</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($games)): ?>
                <tr>
                    <td colspan="5">Keine Spiele gefunden.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($games as $game): ?>
                    <tr>
                        <td>
                            <?php 
                            // Format the game date to dd.MM.yyyy
                            $date = new DateTime($game['game_date']);
                            echo $date->format('d.m.Y'); 
                            ?>
                        </td>
                        <td><?= htmlspecialchars($game['player1_name']) ?></td>
                        <td><?= htmlspecialchars($game['score_player1']) ?></td>
                        <td><?= htmlspecialchars($game['score_player2']) ?></td>
                        <td><?= htmlspecialchars($game['player2_name']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
