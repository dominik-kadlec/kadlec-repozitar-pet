<?php
session_start();
require 'db.php';

// Získání parametru page, pokud chybí, nastavíme jako výchozí 'home'
$page = $_GET["page"] ?? "home";
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <title>Osobní IT profil - Multipage</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container" style="padding: 0;">
    <nav>
        <a href="?page=home">Domů</a>
        <a href="?page=interests">Zájmy</a>
        <a href="?page=skills">Dovednosti</a>
    </nav>

    <main style="padding: 0 25px 25px 25px;">
        <?php
        switch ($page) {
            case 'home':
                require "pages/home.php";
                break;
            case 'interests':
                require "pages/interests.php";
                break;
            case 'skills':
                require "pages/skills.php";
                break;
            default:
                require "pages/not_found.php";
                break;
        }
        ?>
    </main>
</div>

</body>
</html>