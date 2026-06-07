<?php
// Récupérer le nom du fichier actuellement chargé
$page_courante = basename($_SERVER["PHP_SELF"]);
?>
<header>
    <div class="container nav-container">
        <a href="index.php" class="logo">Abdou<span>Diatta.</span></a>
        <nav>
            <ul class="nav-links">
                <li><a href="index.php" <?php if ($page_courante === "index.php") echo "class=\"active\""; ?>>Accueil</a></li>
                <li><a href="projets.php" <?php if ($page_courante === "projets.php") echo "class=\"active\""; ?>>Projets</a></li>
                <li><a href="contact.php" <?php if ($page_courante === "contact.php") echo "class=\"active\""; ?>>Contact</a></li>
            </ul>
        </nav>
    </div>
</header>
