<?php
include 'header.php';
$pdo = new PDO('mysql:host=127.127.126.50;dbname=baseball_club', 'root', '');

// Сброс рейтинга
if (isset($_POST['reset'])) {
    $pdo->exec("DELETE FROM games");
    $pdo->exec("UPDATE players SET wins = 0, losses = 0, points = 0");

 // Получаем список всех игроков и сортируем их по ID (или по любому другому критерию)
    $players = $pdo->query("SELECT id FROM players")->fetchAll(PDO::FETCH_ASSOC);

    // Устанавливаем новые позиции для всех игроков на основе порядка их в базе данных
    $position = 1; // Начальная позиция
    foreach ($players as $player) {
        // Обновляем позицию игрока в таблице рейтинга
        $stmt = $pdo->prepare("UPDATE ranking SET position = ? WHERE player_id = ?");
        $stmt->execute([$position, $player['id']]);
        $position++;
    }
}

// Добавление нового игрока
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_player'])) {
    $name = $_POST['name'];
    $stmt = $pdo->prepare("INSERT INTO players (name) VALUES (?)");
    $stmt->execute([$name]);

    // Добавляем игрока в таблицу рейтинга
    $player_id = $pdo->lastInsertId();
// Step 1: Get the current count of rows in the ranking table
$stmt = $pdo->query("SELECT COUNT(*) FROM ranking");
$rank_count = $stmt->fetchColumn();

// Step 2: Insert the new player into the ranking table with the next available position
$position = $rank_count + 1; // The next available position
$pdo->exec("INSERT INTO ranking (player_id, position) VALUES ($player_id, $position)");}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Админ панель</title>
</head>
<body>
    <h1>Админ панель</h1>

    <form method="POST">
        <h2>Сбросить рейтинг</h2>
        <button type="submit" name="reset">Сбросить рейтинг</button>
    </form>

    <form method="POST">
        <h2>Добавить нового игрока</h2>
        <label for="name">Имя игрока:</label>
        <input type="text" name="name" required>
        <button type="submit" name="new_player">Добавить игрока</button>
    </form>

<table>
    <thead>
        <tr>
            <th>Position</th>
            <th>Player</th>
            <th>Games Played</th>
            <th>Actions</th> <!-- New Actions column -->
        </tr>
    </thead>
    <tbody>
        <?php
        // Fetch players from the ranking table along with their names from the players table
        $stmt = $pdo->query("
                SELECT r.position, p.name, p.id,
                       (SELECT COUNT(*) FROM games WHERE player1_id = p.id OR player2_id = p.id) AS games_played
                FROM ranking r
                right JOIN players p ON r.player_id = p.id
                ORDER BY r.position
        ");

        while ($row = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>" . $row['position'] . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>"; // Display player name
            echo "<td>" . $row['games_played'] . "</td>";
            
            // Delete icon
            echo "<td>";
            echo "<a href='delete.php?id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this player?\");'>";
            echo "<i class='fa-solid fa-trash'></i></a>";
            echo "</td>";

            echo "</tr>";
        }
        ?>
    </tbody>
</table>


</body>
</html>
