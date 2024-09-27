<?php
// Подключение к базе данных
try {
    $pdo = new PDO('mysql:host=127.127.126.50;dbname=baseball_club', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable error handling
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
    exit();
}

if (isset($_GET['id'])) {
    $player_id = $_GET['id'];

    try {
        // Удаление записей об играх, связанных с игроком
        $stmt = $pdo->prepare("DELETE FROM games WHERE player1_id = :player_id OR player2_id = :player_id");
        $stmt->execute([':player_id' => $player_id]);

        // Удаление игрока из рейтинга
        $stmt = $pdo->prepare("DELETE FROM ranking WHERE player_id = :player_id");
        $stmt->execute([':player_id' => $player_id]);

        // Сброс позиций игроков после удаления
        $pdo->exec("SET @pos = 0; UPDATE ranking SET position = (@pos := @pos + 1) ORDER BY position;");

        // Удаление игрока из таблицы игроков
        $stmt = $pdo->prepare("DELETE FROM players WHERE id = :player_id");
        $stmt->execute([':player_id' => $player_id]);

        // Перенаправление обратно на админскую панель
        header("Location: admin.php");
        exit();
    } catch (PDOException $e) {
        echo "Ошибка при удалении: " . $e->getMessage();
        exit();
    }
} else {
    echo "ID игрока не указан.";
}
?>
