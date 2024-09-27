<?php
include 'header.php';
// Подключение к базе данных
$pdo = new PDO('mysql:host=127.127.126.50;dbname=baseball_club', 'root', '');

// Получение списка игроков с их позициями
$players = $pdo->query("
    SELECT p.id, p.name, r.position 
    FROM players p 
    JOIN ranking r ON p.id = r.player_id
")->fetchAll(PDO::FETCH_ASSOC);

// Преобразование массива для удобного использования в JavaScript
$playersRanking = [];
foreach ($players as $player) {
    $playersRanking[$player['id']] = $player['position'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получение данных из формы
    $player1_id = $_POST['player1'];
    $player2_id = $_POST['player2'];
    $score1 = (int)$_POST['score1'];
    $score2 = (int)$_POST['score2'];

    // Определение победителя и обновление статистики
   
    $winner = $player2_id;
    $loser = $player1_id;
    
    // Обновление статистики игроков
    $pdo->exec("UPDATE players SET points = points + 10, wins = wins + 1 WHERE id = $winner");
    $pdo->exec("UPDATE players SET  points = $score1 + points, losses = losses + 1 WHERE id = $loser");

    // Перемещение победителя на позицию проигравшего
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


    // Сохранение результата игры
    $stmt = $pdo->prepare("INSERT INTO games (player1_id, player2_id, score_player1, score_player2, game_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$player1_id, $player2_id, $score1, $score2]);

    // Перенаправление обратно на страницу рейтинга
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Запись результата игры</title>
    <script>
        // Object to hold players and their positions
        var playersRanking = <?= json_encode($playersRanking) ?>;

        function updatePlayerOptions() {
            var player1 = document.getElementById("player1").value;
            var player1Rank = playersRanking[player1] || null;
            var player2Select = document.getElementById("player2");

            // Enable all options in player2 dropdown first
            for (var i = 0; i < player2Select.options.length; i++) {
                player2Select.options[i].disabled = false;
            }

            // If player1 is selected, filter player2 options based on ranking
            if (player1Rank !== null) {
                for (var i = 0; i < player2Select.options.length; i++) {
                    var player2Value = player2Select.options[i].value;
                    var player2Rank = playersRanking[player2Value] || null;

                    // Disable player2 options if rank difference is greater than 3 or if player2 is the same as player1
                    if (player2Rank === null || Math.abs(player1Rank - player2Rank) > 3 || player2Value === player1) {
                        player2Select.options[i].disabled = true;
                    }
                }
            }
        }

        // Ensure player options are updated on page load
        window.onload = function() {
            updatePlayerOptions();
        }
    </script>
</head>
<body>
    <h1>Запись результата игры</h1>
    <form method="POST">
        <label for="player1">Игрок 1:</label>
        <select name="player1" id="player1" onchange="updatePlayerOptions();">
            <option value="">Выберите игрока 1</option> <!-- Empty option -->
            <?php foreach ($players as $player): ?>
                <option value="<?= $player['id'] ?>"><?= $player['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <label for="score1">Очки игрока 1:</label>
        <input type="number" name="score1" min="0" max="10" required>

        <label for="player2">Игрок 2:</label>
        <select name="player2" id="player2" onchange="updatePlayerOptions();">
            <option value="">Выберите игрока 2</option> <!-- Empty option -->
            <?php foreach ($players as $player): ?>
                <option value="<?= $player['id'] ?>"><?= $player['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <label for="score2">Очки игрока 2:</label>
        <input type="number" id="score2" name="score2" value="10" min="0" max="10" readonly>

        <button type="submit">Сохранить результат</button>
    </form>
</body>
</html>
