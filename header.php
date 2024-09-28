<?php
// Get the current page name from the URL
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Baseball Club'; ?></title>
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
<div class="container-fluid">
    <a class="navbar-brand" href="index.php">Baseball Ranking</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : ''; ?>" href="/index.php">Startseite</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'insert.php') ? 'active' : ''; ?>" href="/insert.php">Spieler hinzufügen</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($current_page == 'games.php') ? 'active' : ''; ?>" href="/games.php">Alle Spiele</a>
            </li>
<?php
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
   echo "<li class='nav-item'>
        <a class='nav-link " . (($current_page == 'admin.php') ? 'active' : '') . "' href='admin.php'>Admin</a>
    </li>";

    }
?>

            
        </ul>
<div>
<?php
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        echo "<div class='nav-item'>
                <a class='nav-link' href='logout.php'>Abmelden</a>
            </div>";
    }
    else {
        echo "<div class='nav-item'>
                <a class='nav-link' href='login.php'>Anmelden</a>
            </div>";
    }
?>
</div>
    </div>    </div>
</nav>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
