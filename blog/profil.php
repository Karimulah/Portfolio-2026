<?php
session_start();
require 'config/connexion.php';
require 'fonctions.php';
verifier_connexion();

$erreur = '';
$succes = '';
$csrf_token = generer_csrf();
$id_user = $_SESSION['utilisateur_id'];

// Récupérer les infos actuelles
$stmt = $pdo->prepare("SELECT prenom, nom, email FROM blog_utilisateurs WHERE id = :id");
$stmt->execute(['id' => $id_user]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifier_csrf($_POST['csrf_token'] ?? '')) die('Erreur CSRF');

    $prenom = nettoyer($_POST['prenom'] ?? '');
    $nom = nettoyer($_POST['nom'] ?? '');
    $nouveau_mdp = $_POST['nouveau_mdp'] ?? '';

    if (champ_requis($prenom) && champ_requis($nom)) {
        if (!empty($nouveau_mdp)) {
            $hash = password_hash($nouveau_mdp, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE blog_utilisateurs SET prenom = :p, nom = :n, mot_de_passe = :m WHERE id = :id");
            $stmt->execute(['p' => $prenom, 'n' => $nom, 'm' => $hash, 'id' => $id_user]);
        } else {
            $stmt = $pdo->prepare("UPDATE blog_utilisateurs SET prenom = :p, nom = :n WHERE id = :id");
            $stmt->execute(['p' => $prenom, 'n' => $nom, 'id' => $id_user]);
        }
        
        $_SESSION['utilisateur_prenom'] = $prenom;
        $_SESSION['utilisateur_nom'] = $nom;
        $succes = "Profil mis à jour avec succès.";
        $user['prenom'] = $prenom;
        $user['nom'] = $nom;
    } else {
        $erreur = "Le prénom et le nom sont obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil | Blog ESTM</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require 'composants/navigation.php'; ?>
    <main class="container">
        <div style="max-width: 600px; margin: 50px auto; padding: 2rem; background: var(--color-surface); border-radius: 8px;">
            <h1>Mon Profil</h1>
            <?php if ($erreur) echo "<div style='color:red; margin-bottom:1rem;'>$erreur</div>"; ?>
            <?php if ($succes) echo "<div style='color:#10b981; margin-bottom:1rem;'>$succes</div>"; ?>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                
                <div class="form-group mb-2">
                    <label>Adresse email (non modifiable)</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled style="background:#333;">
                </div>
                
                <div class="form-group mb-2">
                    <label>Prénom *</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
                </div>
                
                <div class="form-group mb-2">
                    <label>Nom *</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
                </div>
                
                <div class="form-group mb-2">
                    <label>Nouveau mot de passe (laisser vide pour ne pas modifier)</label>
                    <input type="password" name="nouveau_mdp">
                </div>
                
                <button type="submit" class="btn btn-primary mt-2">Mettre à jour mon profil</button>
            </form>
        </div>
    </main>
    <?php require 'composants/pied-de-page.php'; ?>
</body>
</html>