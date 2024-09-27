<?php
// Подключение к базе данных
$pdo = new PDO('mysql:host=127.127.126.50;dbname=baseball_club', 'root', '');

if (isset($_GET['id'])) {
    $player_id = $_GET['id'];

    // Prepare and execute the delete query   
    $stmt = $pdo->prepare("DELETE FROM games WHERE player1_id = :player_id or player2_id = :player_id");
    $stmt->execute([':player_id' => $player_id]);

    $stmt = $pdo->prepare("DELETE FROM ranking WHERE player_id = :player_id");
    $stmt->execute([':player_id' => $player_id]);
	$pdo->exec("SET @pos = 0; UPDATE ranking SET position = (@pos := @pos + 1) ORDER BY position;");
  
    $stmt = $pdo->prepare("DELETE FROM players WHERE id = :player_id");
    $stmt->execute([':player_id' => $player_id]);

    // Redirect back to the main page (e.g., index.php or rating.php)
    header("Location: admin.php");
    exit();
} else {
    echo "Player ID not specified.";
}
?>
