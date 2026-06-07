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

    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $technologies = trim($_POST['technologies'] ?? '');
    $lien = trim($_POST['lien'] ?? '');
    $image_path = null;

    if (!empty($titre) && !empty($description) && !empty($technologies)) {
        
        // Gestion de l'upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['image']['tmp_name'];
            $name = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            
            if (in_array($ext, $allowed)) {
                $new_name = uniqid('proj_') . '.' . $ext;
                $dest = '../../images/projets/' . $new_name;
                if (move_uploaded_file($tmp_name, $dest)) {
                    $image_path = 'images/projets/' . $new_name;
                } else {
                    $erreur = "Erreur lors de l'upload de l'image.";
                }
            } else {
                $erreur = "Format d'image non autorisé (seulement jpg, jpeg, png, webp, gif).";
            }
        }

        if (empty($erreur)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO projets (titre, description, technologies, image, lien) VALUES (:titre, :desc, :tech, :img, :lien)");
                $stmt->execute([
                    'titre' => $titre,
                    'desc' => $description,
                    'tech' => $technologies,
                    'img' => $image_path,
                    'lien' => $lien ?: null
                ]);
                header("Location: index.php");
                exit;
            } catch(PDOException $e) {
                $erreur = "Erreur base de données.";
                error_log($e->getMessage());
            }
        }
    } else {
        $erreur = "Veuillez remplir les champs obligatoires (Titre, Description, Technologies).";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un projet</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        .form-group input[type="text"], .form-group input[type="url"], .form-group textarea { width: 100%; padding: 0.8rem; border: 1px solid var(--color-border); border-radius: 4px; }
        .form-group textarea { min-height: 150px; }
    </style>
</head>
<body>
    <div style="max-width: 800px; margin: 50px auto; padding: 2rem; background: var(--color-surface); border-radius: 8px;">
        <h2>Créer un nouveau projet</h2>
        <?php if ($erreur) echo "<div style='color:red; margin-bottom:1rem;'>$erreur</div>"; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            
            <div class="form-group">
                <label>Titre *</label>
                <input type="text" name="titre" required>
            </div>
            
            <div class="form-group">
                <label>Description *</label>
                <textarea name="description" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Technologies (séparées par des virgules) *</label>
                <input type="text" name="technologies" placeholder="ex: HTML, CSS, PHP" required>
            </div>
            
            <div class="form-group">
                <label>Lien vers le projet (optionnel)</label>
                <input type="url" name="lien" placeholder="https://...">
            </div>
            
            <div class="form-group">
                <label>Image du projet (optionnel)</label>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif">
                <small style="display:block; margin-top:0.3rem;">Formats acceptés : JPG, PNG, WEBP, GIF.</small>
            </div>

            <button type="submit" class="btn btn-primary">Créer le projet</button>
            <a href="index.php" style="margin-left: 1rem;">Annuler</a>
        </form>
    </div>
</body>
</html>
