<?php
session_start();
require '../../config/connexion.php';
require '../../composants/fonctions.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../connexion.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE id = :id");
$stmt->execute(['id' => $id]);
$admin = $stmt->fetch();

if (!$admin) {
    header("Location: index.php");
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

    if (!empty($prenom) && !empty($nom) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE administrateurs SET prenom=:prenom, nom=:nom, email=:email, mot_de_passe=:mdp WHERE id=:id");
            $stmt->execute(['prenom'=>$prenom, 'nom'=>$nom, 'email'=>$email, 'mdp'=>$hash, 'id'=>$id]);
        } else {
            $stmt = $pdo->prepare("UPDATE administrateurs SET prenom=:prenom, nom=:nom, email=:email WHERE id=:id");
            $stmt->execute(['prenom'=>$prenom, 'nom'=>$nom, 'email'=>$email, 'id'=>$id]);
        }
        header("Location: index.php");
        exit;
    } else {
        $erreur = "Veuillez remplir correctement les champs obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div style="max-width: 500px; margin: 50px auto; padding: 2rem; background: var(--color-surface); border-radius: 8px;">
        <h2>Modifier un administrateur</h2>
        <?php if ($erreur) echo "<div style='color:red; margin-bottom:1rem;'>$erreur</div>"; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <div style="margin-bottom:1rem;">
                <label>Prénom</label>
                <input type="text" name="prenom" style="width:100%; padding:0.5rem;" value="<?= htmlspecialchars($admin['prenom']) ?>" required>
            </div>
            <div style="margin-bottom:1rem;">
                <label>Nom</label>
                <input type="text" name="nom" style="width:100%; padding:0.5rem;" value="<?= htmlspecialchars($admin['nom']) ?>" required>
            </div>
            <div style="margin-bottom:1rem;">
                <label>Email</label>
                <input type="email" name="email" style="width:100%; padding:0.5rem;" value="<?= htmlspecialchars($admin['email']) ?>" required>
            </div>
            <div style="margin-bottom:1rem;">
                <label>Nouveau mot de passe (laisser vide pour ne pas modifier)</label>
                <input type="password" name="password" style="width:100%; padding:0.5rem;">
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
            <a href="index.php" style="margin-left: 1rem;">Annuler</a>
        </form>
    </div>
</body>
</html>
