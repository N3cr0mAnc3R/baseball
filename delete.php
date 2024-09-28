<?php
try {
include 'db.php'; 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable error handling
} catch (PDOException $e) {
    echo "Connection error: " . $e->getMessage();
    exit();
}

if (isset($_GET['id'])) {
    $player_id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM games WHERE player1_id = :player_id OR player2_id = :player_id");
        $stmt->execute([':player_id' => $player_id]);

        $stmt = $pdo->prepare("DELETE FROM ranking WHERE player_id = :player_id");
        $stmt->execute([':player_id' => $player_id]);

        $pdo->exec("SET @pos = 0; UPDATE ranking SET position = (@pos := @pos + 1) ORDER BY position;");

        $stmt = $pdo->prepare("DELETE FROM players WHERE id = :player_id");
        $stmt->execute([':player_id' => $player_id]);

        header("Location: admin.php");
        exit();
    } catch (PDOException $e) {
        echo "Delete error: " . $e->getMessage();
        exit();
    }
} else {
    echo "No id presented";
}
?>
