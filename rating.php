<?php
include 'header.php'; // Include header
include 'db.php'; 

// Get player_id from the URL path (e.g., rating.php/1)
$url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($url_path, '/')); // Split the URL by slashes

// Assuming the player_id is the last segment in the URL
$player_id = isset($segments[1]) ? intval($segments[1]) : null;

$games = [];
$player_name = '';

if ($player_id) {
    // Fetch the player's name
    $stmt = $pdo->prepare("SELECT name FROM players WHERE id = :player_id");
    $stmt->execute([':player_id' => $player_id]);
    $player_name = $stmt->fetchColumn();

    // If the player exists, fetch their games
    if ($player_name) {
        $stmt = $pdo->prepare("
            SELECT g.*, p1.name AS player1_name, p2.name AS player2_name 
            FROM games g
            JOIN players p1 ON g.player1_id = p1.id
            JOIN players p2 ON g.player2_id = p2.id
            WHERE g.player1_id = :player_id OR g.player2_id = :player_id
            ORDER BY g.game_date DESC
        ");
        $stmt->execute([':player_id' => $player_id]);
        $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Games for Player</title>
</head>
<body>
<div class="container">
    <h1>Games for <?= htmlspecialchars($player_name) ?></h1>

    <!-- Display Games for Selected Player -->
        <table class="table table-bordered table-striped">
            <tr>
                <th>Date</th>
                <th>Player 1</th>
                <th>Player 2</th>
                <th>Score Player 1</th>
                <th>Score Player 2</th>
            </tr>
            <?php foreach ($games as $game): ?>
                <tr>
                    <td><?= date('d.m.Y', strtotime($game['game_date'])) ?></td>
                    <td><?= htmlspecialchars($game['player1_name']) ?></td>
                    <td><?= htmlspecialchars($game['player2_name']) ?></td>
                    <td><?= $game['score_player1'] ?></td>
                    <td><?= $game['score_player2'] ?></td>
                </tr>
            <?php endforeach; ?>
 <?php if ($player_id && empty($games)): ?>
<tr><td colspan="5" class="bg-secondary text-center text-white">Für den ausgewählten Spieler wurden keine Spiele gefunden.</td></tr>
<?php endif; ?>
        </table>
</div>
</body>
</html>
