<?php
session_start();

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php'); // Weiterleitung zur Anmeldeseite, wenn nicht angemeldet
    exit;
}
include 'header.php';
$pdo = new PDO('mysql:host=127.127.126.50;dbname=baseball_club', 'root', '');

// Zurücksetzen des Rankings
if (isset($_POST['reset'])) {
    $pdo->exec("DELETE FROM games");
    $pdo->exec("UPDATE players SET wins = 0, losses = 0, points = 0");

    // Abrufen aller Spieler und Sortieren nach ID (oder einem anderen Kriterium)
    $players = $pdo->query("SELECT id FROM players")->fetchAll(PDO::FETCH_ASSOC);

    // Festlegen neuer Positionen für alle Spieler basierend auf ihrer Reihenfolge in der Datenbank
    $position = 1; // Anfangsposition
    foreach ($players as $player) {
        // Aktualisieren der Position des Spielers in der Rangliste
        $stmt = $pdo->prepare("UPDATE ranking SET position = ? WHERE player_id = ?");
        $stmt->execute([$position, $player['id']]);
        $position++;
    }
}

// Hinzufügen eines neuen Spielers
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_player'])) {
    $name = $_POST['name'];
    $stmt = $pdo->prepare("INSERT INTO players (name) VALUES (?)");
    $stmt->execute([$name]);

    // Spieler zur Rangliste hinzufügen
    $player_id = $pdo->lastInsertId();
    // Schritt 1: Abrufen der aktuellen Anzahl von Zeilen in der Rangliste
    $stmt = $pdo->query("SELECT COUNT(*) FROM ranking");
    $rank_count = $stmt->fetchColumn();

    // Schritt 2: Einfügen des neuen Spielers in die Rangliste mit der nächstverfügbaren Position
    $position = $rank_count + 1; // Die nächstverfügbare Position
    $pdo->exec("INSERT INTO ranking (player_id, position) VALUES ($player_id, $position)");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
</head>
<body>
    <h1>Admin Panel</h1>

    <form method="POST">
        <h2>Ranking zurücksetzen</h2>
        <button type="submit" name="reset">Ranking zurücksetzen</button>
    </form>

    <form method="POST">
        <h2>Neuen Spieler hinzufügen</h2>
        <label for="name">Name des Spielers:</label>
        <input type="text" name="name" required>
        <button type="submit" name="new_player">Spieler hinzufügen</button>
    </form>

<table>
    <thead>
        <tr>
            <th>Position</th>
            <th>Spieler</th>
            <th>Gespielte Spiele</th>
            <th>Aktionen</th> <!-- Neue Aktionen-Spalte -->
        </tr>
    </thead>
    <tbody>
        <?php
        // Abrufen von Spielern aus der Rangliste zusammen mit ihren Namen aus der Spielern-Tabelle
        $stmt = $pdo->query("
                SELECT r.position, p.name, p.id,
                       (SELECT COUNT(*) FROM games WHERE player1_id = p.id OR player2_id = p.id) AS games_played
                FROM ranking r
                RIGHT JOIN players p ON r.player_id = p.id
                ORDER BY r.position
        ");

        while ($row = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>" . $row['position'] . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>"; // Anzeigen des Spielernamens
            echo "<td>" . $row['games_played'] . "</td>";
            
            // Löschen-Symbol
            echo "<td>";
            echo "<a href='delete.php?id=" . $row['id'] . "' onclick='return confirm(\"Sind Sie sicher, dass Sie diesen Spieler löschen möchten?\");'>";
            echo "<i class='fa-solid fa-trash'></i></a>";
            echo "</td>";

            echo "</tr>";
        }
        ?>
    </tbody>
</table>


</body>
</html>
