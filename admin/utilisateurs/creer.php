<?php
session_start();
require '../../config/connexion.php';
require '../../composants/fonctions.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../connexion.php");
    exit;
}

$erreur = '';
$csrf_token = generer_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifier_csrf($_POST['csrf_token'] ?? '')) die('Erreur CSRF');

    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($prenom) && !empty($nom) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        try {
            $stmt = $pdo->prepare("INSERT INTO administrateurs (prenom, nom, email, mot_de_passe) VALUES (:prenom, :nom, :email, :mdp)");
            $stmt->execute(['prenom'=>$prenom, 'nom'=>$nom, 'email'=>$email, 'mdp'=>$hash]);
            header("Location: index.php");
            exit;
        } catch(PDOException $e) {
            $erreur = "Erreur ou email déjà utilisé.";
        }
    } else {
        $erreur = "Veuillez remplir correctement tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div style="max-width: 500px; margin: 50px auto; padding: 2rem; background: var(--color-surface); border-radius: 8px;">
        <h2>Ajouter un administrateur</h2>
        <?php if ($erreur) echo "<div style='color:red; margin-bottom:1rem;'>$erreur</div>"; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div style="margin-bottom:1rem;">
                <label>Prénom</label>
                <input type="text" name="prenom" style="width:100%; padding:0.5rem;" required>
            </div>
            <div style="margin-bottom:1rem;">
                <label>Nom</label>
                <input type="text" name="nom" style="width:100%; padding:0.5rem;" required>
            </div>
            <div style="margin-bottom:1rem;">
                <label>Email</label>
                <input type="email" name="email" style="width:100%; padding:0.5rem;" required>
            </div>
            <div style="margin-bottom:1rem;">
                <label>Mot de passe</label>
                <input type="password" name="password" style="width:100%; padding:0.5rem;" required>
            </div>
            <button type="submit" class="btn btn-primary">Créer</button>
            <a href="index.php" style="margin-left: 1rem;">Annuler</a>
        </form>
    </div>
</body>
</html>
