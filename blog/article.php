<?php
session_start();
require 'config/connexion.php';
require 'fonctions.php';

$id_article = (int)($_GET['id'] ?? 0);

// Récupérer l'article
$stmt = $pdo->prepare("
    SELECT a.*, u.prenom, u.nom 
    FROM blog_articles a 
    JOIN blog_utilisateurs u ON a.auteur_id = u.id 
    WHERE a.id = :id
");
$stmt->execute(['id' => $id_article]);
$article = $stmt->fetch();

if (!$article) {
    header("Location: accueil.php");
    exit;
}

$csrf_token = generer_csrf();
$erreur = '';

// Traitement du commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['utilisateur_id'])) {
    if (isset($_POST['action']) && $_POST['action'] === 'commenter') {
        if (!verifier_csrf($_POST['csrf_token'] ?? '')) die('Erreur CSRF');
        $contenu = nettoyer($_POST['contenu'] ?? '');
        if (champ_requis($contenu)) {
            $stmt = $pdo->prepare("INSERT INTO blog_commentaires (article_id, auteur_id, contenu) VALUES (:art, :aut, :cont)");
            $stmt->execute(['art' => $id_article, 'aut' => $_SESSION['utilisateur_id'], 'cont' => $contenu]);
            header("Location: article.php?id=" . $id_article . "#commentaires");
            exit;
        } else {
            $erreur = "Le commentaire ne peut pas être vide.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'supprimer_com') {
        if (!verifier_csrf($_POST['csrf_token'] ?? '')) die('Erreur CSRF');
        $id_com = (int)$_POST['id_com'];
        $stmt = $pdo->prepare("DELETE FROM blog_commentaires WHERE id = :id AND auteur_id = :aut");
        $stmt->execute(['id' => $id_com, 'aut' => $_SESSION['utilisateur_id']]);
        header("Location: article.php?id=" . $id_article . "#commentaires");
        exit;
    }
}

// Récupérer les commentaires
$stmt = $pdo->prepare("
    SELECT c.*, u.prenom, u.nom 
    FROM blog_commentaires c 
    JOIN blog_utilisateurs u ON c.auteur_id = u.id 
    WHERE c.article_id = :id 
    ORDER BY c.date_commentaire ASC
");
$stmt->execute(['id' => $id_article]);
$commentaires = $stmt->fetchAll();

$est_auteur = (isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == $article['auteur_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($article['titre']) ?> | Blog ESTM</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .article-header { text-align: center; margin-bottom: 2rem; }
        .article-cover { width: 100%; max-height: 500px; object-fit: cover; border-radius: 8px; margin-bottom: 2rem; }
        .article-body { font-size: 1.1rem; line-height: 1.8; margin-bottom: 3rem; background: var(--color-surface); padding: 2rem; border-radius: 8px; border: 1px solid var(--color-border); }
        .comment-section { background: var(--color-surface); padding: 2rem; border-radius: 8px; border: 1px solid var(--color-border); }
        .comment-item { border-bottom: 1px solid var(--color-border); padding: 1rem 0; }
        .comment-item:last-child { border-bottom: none; }
        .comment-meta { font-size: 0.85rem; color: var(--color-primary); margin-bottom: 0.5rem; }
        .comment-content { font-size: 0.95rem; }
        .actions-auteur { margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-border); text-align: right; }
    </style>
</head>
<body>
    <?php require 'composants/navigation.php'; ?>
    <main class="container" style="max-width: 800px;">
        <div class="article-header mt-4">
            <h1 style="font-size: 2.5rem; margin-bottom: 1rem;"><?= htmlspecialchars($article['titre']) ?></h1>
            <p style="color: var(--color-primary);">Par <?= htmlspecialchars($article['prenom'] . ' ' . $article['nom']) ?> le <?= date('d/m/Y à H:i', strtotime($article['date_publication'])) ?></p>
        </div>

        <?php if ($article['image_couverture']): ?>
            <img src="<?= htmlspecialchars($article['image_couverture']) ?>" alt="Couverture" class="article-cover">
        <?php endif; ?>

        <div class="article-body">
            <?= nl2br(htmlspecialchars($article['contenu'])) ?>
            
            <?php if ($est_auteur): ?>
                <div class="actions-auteur">
                    <a href="modifier.php?id=<?= $article['id'] ?>" class="btn btn-outline" style="padding: 0.4rem 1rem;">Modifier</a>
                    <form action="supprimer.php" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cet article ?');">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="id" value="<?= $article['id'] ?>">
                        <button type="submit" class="btn" style="background:#ef4444; color:white; padding: 0.4rem 1rem; border:none; cursor:pointer;">Supprimer</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <div id="commentaires" class="comment-section">
            <h2>Commentaires (<?= count($commentaires) ?>)</h2>
            
            <?php if (isset($_SESSION['utilisateur_id'])): ?>
                <form method="POST" style="margin-bottom: 2rem;">
                    <?php if ($erreur) echo "<div style='color:red; margin-bottom:1rem;'>$erreur</div>"; ?>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="action" value="commenter">
                    <div class="form-group">
                        <textarea name="contenu" placeholder="Laissez un commentaire..." required style="min-height: 80px;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-1">Commenter</button>
                </form>
            <?php else: ?>
                <div style="background: var(--color-bg); padding: 1rem; border-radius: 4px; margin-bottom: 2rem; text-align: center;">
                    <a href="connexion.php">Connectez-vous</a> pour laisser un commentaire.
                </div>
            <?php endif; ?>

            <div class="comment-list">
                <?php if (empty($commentaires)): ?>
                    <p>Aucun commentaire pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($commentaires as $c): ?>
                        <div class="comment-item">
                            <div class="comment-meta">
                                <strong><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></strong> - <?= date('d/m/Y H:i', strtotime($c['date_commentaire'])) ?>
                                <?php if (isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == $c['auteur_id']): ?>
                                    <form method="POST" style="display:inline; float:right;" onsubmit="return confirm('Supprimer ce commentaire ?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                        <input type="hidden" name="action" value="supprimer_com">
                                        <input type="hidden" name="id_com" value="<?= $c['id'] ?>">
                                        <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer; font-size:0.8rem; text-decoration:underline;">Supprimer</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <div class="comment-content">
                                <?= nl2br(htmlspecialchars($c['contenu'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <?php require 'composants/pied-de-page.php'; ?>
</body>
</html>