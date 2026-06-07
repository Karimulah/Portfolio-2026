<?php
$page_courante = basename($_SERVER["PHP_SELF"]);
?>
<header>
    <div class="container nav-container">
        <a href="accueil.php" class="logo">Blog<span>ESTM.</span></a>
        <nav>
            <ul class="nav-links">
                <li><a href="accueil.php" <?php if ($page_courante === "accueil.php") echo "class=\"active\""; ?>>Accueil Blog</a></li>
                <?php if (isset($_SESSION['utilisateur_id'])): ?>
                    <li><a href="publier.php" <?php if ($page_courante === "publier.php") echo "class=\"active\""; ?>>Publier</a></li>
                    <li><a href="mes-articles.php" <?php if ($page_courante === "mes-articles.php") echo "class=\"active\""; ?>>Mes Articles</a></li>
                    <li><a href="profil.php" <?php if ($page_courante === "profil.php") echo "class=\"active\""; ?>>Profil (<?= htmlspecialchars($_SESSION['utilisateur_prenom']) ?>)</a></li>
                    <li><a href="deconnexion.php" style="color: var(--color-error);">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="connexion.php" <?php if ($page_courante === "connexion.php") echo "class=\"active\""; ?>>Connexion</a></li>
                    <li><a href="inscription.php" <?php if ($page_courante === "inscription.php") echo "class=\"active\""; ?>>Inscription</a></li>
                <?php endif; ?>
                <li><a href="../index.php">Retour au Portfolio</a></li>
            </ul>
        </nav>
    </div>
</header>
