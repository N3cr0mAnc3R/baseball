<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baseball Ranking</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
 <style>
        .container > h1 {
		margin-bottom: 2rem;
		margin-top: 1rem;
        }
    </style>

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">Baseball Ranking</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Startseite</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="insert.php">Spieler hinzufügen</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="games.php">Alle Spiele</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="rating.php">Ranglisten anzeigen</a>
            </li>

<?php
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        echo "<li class='nav-item'>
                <a class='nav-link' href='admin.php'>Admin</a>
            </li> 
            <li class='nav-item'>
                <a class='nav-link' href='logout.php'>Abmelden</a>
            </li>";
    }
    else {
        echo "<li class='nav-item'>
                <a class='nav-link' href='login.php'>Anmelden</a>
            </li>";
    }
?>
            
        </ul>
    </div>
</nav>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
