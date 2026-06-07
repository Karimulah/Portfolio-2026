<?php
session_start();
require 'config/connexion.php';
require 'fonctions.php';
verifier_connexion();

$id_article = (int)($_GET['id'] ?? 0);
$id_user = $_SESSION['utilisateur_id'];

// Vérifier que l'article appartient à l'utilisateur connecté
$stmt = $pdo->prepare("SELECT * FROM blog_articles WHERE id = :id AND auteur_id = :aut");
$stmt->execute(['id' => $id_article, 'aut' => $id_user]);
$article = $stmt->fetch();

if (!$article) {
    header("Location: mes-articles.php");
    exit;
}

$erreur = '';
$csrf_token = generer_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifier_csrf($_POST['csrf_token'] ?? '')) die('Erreur CSRF');

    $titre = nettoyer($_POST['titre'] ?? '');
    $contenu = nettoyer($_POST['contenu'] ?? '');
    $image_chemin = $article['image_couverture'];

    if (champ_requis($titre) && champ_requis($contenu)) {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $new_name = uniqid('blog_') . '.' . $ext;
                $dest = 'images/articles/' . $new_name;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                    // Supprimer l'ancienne image
                    if ($article['image_couverture'] && file_exists($article['image_couverture'])) {
                        unlink($article['image_couverture']);
                    }
                    $image_chemin = $dest;
                } else {
                    $erreur = "Erreur d'upload.";
                }
            } else {
                $erreur = "Format d'image non autorisé.";
            }
        }

        if (!$erreur) {
            $stmt = $pdo->prepare("UPDATE blog_articles SET titre = :t, contenu = :c, image_couverture = :img WHERE id = :id");
            $stmt->execute([
                't' => $titre,
                'c' => $contenu,
                'img' => $image_chemin,
                'id' => $id_article
            ]);
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
    <title>Modifier un article | Blog ESTM</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require 'composants/navigation.php'; ?>
    <main class="container">
        <div style="max-width: 800px; margin: 50px auto; padding: 2rem; background: var(--color-surface); border-radius: 8px;">
            <h1>Modifier l'article</h1>
            <?php if ($erreur) echo "<div style='color:red; margin-bottom:1rem;'>$erreur</div>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                
                <div class="form-group mb-2">
                    <label>Titre de l'article *</label>
                    <input type="text" name="titre" value="<?= htmlspecialchars($article['titre']) ?>" required>
                </div>
                
                <div class="form-group mb-2">
                    <label>Image de couverture (Laisser vide pour conserver l'actuelle)</label><br>
                    <?php if ($article['image_couverture']): ?>
                        <img src="<?= htmlspecialchars($article['image_couverture']) ?>" alt="Couverture" style="height: 100px; margin-bottom:0.5rem;"><br>
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*">
                </div>
                
                <div class="form-group mb-2">
                    <label>Contenu de l'article *</label>
                    <textarea name="contenu" required style="min-height: 300px;"><?= htmlspecialchars($article['contenu']) ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary mt-2">Mettre à jour l'article</button>
            </form>
        </div>
    </main>
    <?php require 'composants/pied-de-page.php'; ?>
</body>
</html>