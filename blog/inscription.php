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

    $prenom = nettoyer($_POST['prenom'] ?? '');
    $nom = nettoyer($_POST['nom'] ?? '');
    $email = nettoyer($_POST['email'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';
    $mdp_conf = $_POST['mot_de_passe_conf'] ?? '';

    if (champ_requis($prenom) && champ_requis($nom) && champ_requis($email) && !empty($mdp)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if ($mdp === $mdp_conf) {
                // Vérifier email unique
                $stmt = $pdo->prepare("SELECT id FROM blog_utilisateurs WHERE email = :email");
                $stmt->execute(['email' => $email]);
                if ($stmt->fetch()) {
                    $erreur = "Cette adresse email est déjà utilisée.";
                } else {
                    $hash = password_hash($mdp, PASSWORD_BCRYPT);
                    try {
                        $stmt = $pdo->prepare("INSERT INTO blog_utilisateurs (prenom, nom, email, mot_de_passe) VALUES (:prenom, :nom, :email, :mdp)");
                        $stmt->execute(['prenom' => $prenom, 'nom' => $nom, 'email' => $email, 'mdp' => $hash]);
                        
                        // Auto login
                        $_SESSION['utilisateur_id'] = $pdo->lastInsertId();
                        $_SESSION['utilisateur_prenom'] = $prenom;
                        $_SESSION['utilisateur_nom'] = $nom;
                        session_regenerate_id(true);
                        
                        header("Location: accueil.php");
                        exit;
                    } catch(PDOException $e) {
                        $erreur = "Erreur base de données.";
                        error_log($e->getMessage());
                    }
                }
            } else {
                $erreur = "Les mots de passe ne correspondent pas.";
            }
        } else {
            $erreur = "L'adresse email n'est pas valide.";
        }
    } else {
        $erreur = "Tous les champs sont obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription | Blog ESTM</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require 'composants/navigation.php'; ?>
    <main class="container">
        <div style="max-width: 500px; margin: 50px auto; padding: 2rem; background: var(--color-surface); border-radius: 8px;">
            <h1 class="text-center">Inscription</h1>
            <?php if ($erreur) echo "<div style='color:red; margin-bottom:1rem; text-align:center;'>$erreur</div>"; ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" required>
                </div>
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="mot_de_passe" required>
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="mot_de_passe_conf" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:1rem;">S'inscrire</button>
            </form>
            <p class="text-center mt-2">Déjà inscrit ? <a href="connexion.php">Se connecter</a></p>
        </div>
    </main>
    <?php require 'composants/pied-de-page.php'; ?>
</body>
</html>