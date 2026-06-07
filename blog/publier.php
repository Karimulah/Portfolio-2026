<?php
session_start();
require 'config/connexion.php';
require 'fonctions.php';
verifier_connexion();

$erreur = '';
$csrf_token = generer_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifier_csrf($_POST['csrf_token'] ?? '')) die('Erreur CSRF');

    $titre = nettoyer($_POST['titre'] ?? '');
    $contenu = nettoyer($_POST['contenu'] ?? '');
    $image_chemin = null;

    if (champ_requis($titre) && champ_requis($contenu)) {
        // Upload image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_name = uniqid('blog_') . '.' . $ext;
                $dest = 'images/articles/' . $new_name;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    $image_chemin = $dest;
                } else {
                    $erreur = "Erreur lors de l'upload de l'image.";
                }
            } else {
                $erreur = "Format d'image non autorisé (jpg, jpeg, png, gif, webp).";
            }
        }

        if (!$erreur) {
            $stmt = $pdo->prepare("INSERT INTO blog_articles (titre, contenu, image_couverture, auteur_id) VALUES (:t, :c, :img, :aut)");
            $stmt->execute([
                't' => $titre,
                'c' => $contenu,
                'img' => $image_chemin,
                'aut' => $_SESSION['utilisateur_id']
            ]);
            $id_article = $pdo->lastInsertId();
            header("Location: article.php?id=" . $id_article);
            exit;
        }
    } else {
        $erreur = "Le titre et le contenu sont obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Publier un article | Blog ESTM</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require 'composants/navigation.php'; ?>
    <main class="container">
        <div style="max-width: 800px; margin: 50px auto; padding: 2rem; background: var(--color-surface); border-radius: 8px;">
            <h1>Publier un nouvel article</h1>
            <?php if ($erreur) echo "<div style='color:red; margin-bottom:1rem;'>$erreur</div>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                
                <div class="form-group mb-2">
                    <label>Titre de l'article *</label>
                    <input type="text" name="titre" required>
                </div>
                
                <div class="form-group mb-2">
                    <label>Image de couverture (Optionnel, formats: jpg, png, webp, gif)</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                
                <div class="form-group mb-2">
                    <label>Contenu de l'article *</label>
                    <textarea name="contenu" required style="min-height: 300px;"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary mt-2">Publier l'article</button>
            </form>
        </div>
    </main>
    <?php require 'composants/pied-de-page.php'; ?>
</body>
</html>