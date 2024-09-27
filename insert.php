<?php
include 'header.php';
// Verbindung zur Datenbank herstellen
$pdo = new PDO('mysql:host=127.127.126.50;dbname=baseball_club', 'root', '');

// Abrufen der Liste der Spieler mit ihren Positionen
$players = $pdo->query("
    SELECT p.id, p.name, r.position 
    FROM players p 
    JOIN ranking r ON p.id = r.player_id
")->fetchAll(PDO::FETCH_ASSOC);

// Umwandlung des Arrays für die bequeme Verwendung in JavaScript
$playersRanking = [];
foreach ($players as $player) {
    $playersRanking[$player['id']] = $player['position'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Abrufen der Daten aus dem Formular
    $player1_id = $_POST['player1'];
    $player2_id = $_POST['player2'];
    $score1 = (int)$_POST['score1'];
    $score2 = (int)$_POST['score2'];

    // Bestimmung des Gewinners und Aktualisierung der Statistiken
    $winner = $player2_id;
    $loser = $player1_id;
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM games
        WHERE ((player1_id = :player1 AND player2_id = :player2) OR (player1_id = :player2 AND player2_id = :player1))
        AND DATE(game_date) = CURDATE()
    ");
    $stmt->execute([':player1' => $player1_id, ':player2' => $player2_id]);
    $gamesToday = $stmt->fetchColumn();

    if ($gamesToday >= 2) {
        echo "<script>alert('Diese Spieler haben heute bereits 2 Mal gespielt.'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
        return; // Stoppe die Ausführung des Codes, wenn das Spiel-Limit überschritten ist
    }

    // Aktualisierung der Spielerstatistik
    $pdo->exec("UPDATE players SET points = points + 10, wins = wins + 1 WHERE id = $winner");
    $pdo->exec("UPDATE players SET points = $score1 + points, losses = losses + 1 WHERE id = $loser");

    // Verschieben des Gewinners auf die Position des Verlierers
    $stmt = $pdo->prepare("SELECT position FROM ranking WHERE player_id = :loser");
    $stmt->execute([':loser' => $loser]);
    $loser_position = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT position FROM ranking WHERE player_id = :winner");
    $stmt->execute([':winner' => $winner]);
    $winner_position = $stmt->fetchColumn();

    $stmt = $pdo->prepare("UPDATE ranking SET position = :loser_position WHERE player_id = :winner");
    $stmt->execute([':loser_position' => $loser_position, ':winner' => $winner]);

    $stmt = $pdo->prepare("UPDATE ranking SET position = :winner_position WHERE player_id = :loser");
    $stmt->execute([':winner_position' => $winner_position, ':loser' => $loser]);

    // Speichern des Spielergebnisses
    $stmt = $pdo->prepare("INSERT INTO games (player1_id, player2_id, score_player1, score_player2, game_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$player1_id, $player2_id, $score1, $score2]);

    // Weiterleitung zurück zur Ranglisten-Seite
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Spielergebnis eingeben</title>
    <script>
        // Objekt, um Spieler und ihre Positionen zu halten
        var playersRanking = <?= json_encode($playersRanking) ?>;

        function updatePlayerOptions() {
            var player1 = document.getElementById("player1").value;
            var player1Rank = playersRanking[player1] || null;
            var player2Select = document.getElementById("player2");

            // Alle Optionen im Dropdown player2 zunächst aktivieren
            for (var i = 0; i < player2Select.options.length; i++) {
                player2Select.options[i].disabled = false;
            }

            // Wenn player1 ausgewählt ist, filtere die player2 Optionen basierend auf dem Ranking
            if (player1Rank !== null) {
                for (var i = 0; i < player2Select.options.length; i++) {
                    var player2Value = player2Select.options[i].value;
                    var player2Rank = playersRanking[player2Value] || null;

                    // Deaktiviere player2 Optionen, wenn der Rangunterschied größer als 3 ist oder wenn player2 dasselbe ist wie player1
                    if (player2Rank === null || Math.abs(player1Rank - player2Rank) > 3 || player2Value === player1) {
                        player2Select.options[i].disabled = true;
                    }
                }
            }
        }

        // Stelle sicher, dass die Spieleroptionen beim Laden der Seite aktualisiert werden
        window.onload = function() {
            updatePlayerOptions();
        }
    </script>
</head>
<body>
<div class="container">
    <h1>Spielergebnis eingeben</h1>
    <form method="POST">
<div class="row">
<div class="col-5">
        <label for="player1">Spieler 1:</label>
        <select class="form-control" name="player1" id="player1" onchange="updatePlayerOptions();">
            <option value="">Wählen Sie Spieler 1</option> <!-- Leere Option -->
            <?php foreach ($players as $player): ?>
                <option value="<?= $player['id'] ?>"><?= $player['name'] ?></option>
            <?php endforeach; ?>
        </select>


        <label for="score1">Punkte Spieler 1:</label>
        <input class="form-control" type="number" name="score1" min="0" max="10" value="0" required>
</div>
<div class="col-5">
        <label for="player2">Spieler 2:</label>
        <select class="form-control" name="player2" id="player2" onchange="updatePlayerOptions();">
            <option value="">Wählen Sie Spieler 2</option> <!-- Leere Option -->
            <?php foreach ($players as $player): ?>
                <option value="<?= $player['id'] ?>"><?= $player['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <label for="score2">Punkte Spieler 2:</label>
        <input class="form-control" class="form-control" type="number" id="score2" name="score2" value="10" min="0" max="10" readonly>
</div>
<div class="col">
        <button class="btn btn-success" type="submit">Ergebnis speichern</button>
</div></div>
    </form>
</div>
</body>
</html>
