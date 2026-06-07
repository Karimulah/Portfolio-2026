<?php
session_start();
require 'config/connexion.php';
require 'fonctions.php';

$stmt = $pdo->query("
    SELECT a.id, a.titre, a.contenu, a.image_couverture, a.date_publication, u.prenom, u.nom,
           (SELECT COUNT(*) FROM blog_commentaires c WHERE c.article_id = a.id) as nb_commentaires
    FROM blog_articles a
    JOIN blog_utilisateurs u ON a.auteur_id = u.id
    ORDER BY a.date_publication DESC
");
$articles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil | Blog ESTM</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .blog-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 2rem; margin-top: 2rem; }
        .article-card { background: var(--color-surface); border: 1px solid var(--color-border); border-radius: 8px; overflow: hidden; transition: transform 0.2s; display: flex; flex-direction: column; }
        .article-card:hover { transform: translateY(-5px); border-color: var(--color-primary); }
        .article-img { width: 100%; height: 200px; object-fit: cover; }
        .article-img-placeholder { width: 100%; height: 200px; background: linear-gradient(45deg, #1e3c72, #2a5298); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; }
        .article-content { padding: 1.5rem; flex: 1; display: flex; flex-direction: column; }
        .article-title { margin-bottom: 0.5rem; font-size: 1.25rem; }
        .article-title a { color: var(--color-text-primary); text-decoration: none; }
        .article-title a:hover { color: var(--color-primary); }
        .article-meta { font-size: 0.85rem; color: var(--color-primary); margin-bottom: 1rem; }
        .article-excerpt { font-size: 0.95rem; color: var(--color-text-secondary); margin-bottom: 1.5rem; flex: 1; }
        .article-footer { display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; border-top: 1px solid var(--color-border); padding-top: 1rem; margin-top: auto; }
    </style>
</head>
<body>
    <?php require 'composants/navigation.php'; ?>
    <main class="container">
        <h1 class="text-center mt-4">Le Blog de l'ESTM</h1>
        <p class="text-center">Actualités, tutoriels et partages de la communauté.</p>
        
        <?php if (empty($articles)): ?>
            <div style="text-align: center; margin-top: 3rem; padding: 3rem; background: var(--color-surface); border-radius: 8px;">
                <h2>Aucun article pour le moment !</h2>
                <p>Soyez le premier à publier sur le blog de l'ESTM.</p>
                <?php if (isset($_SESSION['utilisateur_id'])): ?>
                    <a href="publier.php" class="btn btn-primary mt-2">Publier un article</a>
                <?php else: ?>
                    <a href="connexion.php" class="btn btn-primary mt-2">Connectez-vous pour publier</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="blog-grid">
                <?php foreach ($articles as $a): ?>
                    <article class="article-card">
                        <?php if ($a['image_couverture']): ?>
                            <img src="<?= htmlspecialchars($a['image_couverture']) ?>" alt="Couverture" class="article-img">
                        <?php else: ?>
                            <div class="article-img-placeholder">Blog ESTM</div>
                        <?php endif; ?>
                        
                        <div class="article-content">
                            <h2 class="article-title">
                                <a href="article.php?id=<?= $a['id'] ?>"><?= htmlspecialchars($a['titre']) ?></a>
                            </h2>
                            <div class="article-meta">
                                Publié par <?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?> le <?= date('d/m/Y', strtotime($a['date_publication'])) ?>
                            </div>
                            <div class="article-excerpt">
                                <?php 
                                    $extrait = mb_substr($a['contenu'], 0, 150);
                                    if (mb_strlen($a['contenu']) > 150) $extrait .= "...";
                                    echo nl2br(htmlspecialchars($extrait));
                                ?>
                            </div>
                            <div class="article-footer">
                                <span><?= $a['nb_commentaires'] ?> commentaire(s)</span>
                                <a href="article.php?id=<?= $a['id'] ?>" style="font-weight: bold;">Lire la suite →</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
    <?php require 'composants/pied-de-page.php'; ?>
</body>
</html>