<?php
session_start();
require 'config/connexion.php';
require 'fonctions.php';

if (isset($_SESSION['utilisateur_id'])) {
    header("Location: accueil.php");
    exit;
}

$erreur = '';
$csrf_token = generer_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifier_csrf($_POST['csrf_token'] ?? '')) die('Erreur CSRF');

    $email = nettoyer($_POST['email'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';

    if (champ_requis($email) && !empty($mdp)) {
        $stmt = $pdo->prepare("SELECT * FROM blog_utilisateurs WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['utilisateur_id'] = $user['id'];
            $_SESSION['utilisateur_prenom'] = $user['prenom'];
            $_SESSION['utilisateur_nom'] = $user['nom'];
            header("Location: accueil.php");
            exit;
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion | Blog ESTM</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require 'composants/navigation.php'; ?>
    <main class="container">
        <div style="max-width: 500px; margin: 50px auto; padding: 2rem; background: var(--color-surface); border-radius: 8px;">
            <h1 class="text-center">Connexion</h1>
            <?php if ($erreur) echo "<div style='color:red; margin-bottom:1rem; text-align:center;'>$erreur</div>"; ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="mot_de_passe" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:1rem;">Se connecter</button>
            </form>
            <p class="text-center mt-2">Pas encore de compte ? <a href="inscription.php">S'inscrire</a></p>
        </div>
    </main>
    <?php require 'composants/pied-de-page.php'; ?>
</body>
</html>