<?php
session_start();
require 'config/connexion.php';
require 'fonctions.php';
verifier_connexion();

$csrf_token = generer_csrf();
$id_user = $_SESSION['utilisateur_id'];

$stmt = $pdo->prepare("
    SELECT a.id, a.titre, a.date_publication, 
           (SELECT COUNT(*) FROM blog_commentaires c WHERE c.article_id = a.id) as nb_commentaires
    FROM blog_articles a
    WHERE a.auteur_id = :aut
    ORDER BY a.date_publication DESC
");
$stmt->execute(['aut' => $id_user]);
$articles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Articles | Blog ESTM</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require 'composants/navigation.php'; ?>
    <main class="container">
        <div style="margin: 50px auto; padding: 2rem; background: var(--color-surface); border-radius: 8px;">
            <h1>Mes Articles</h1>
            
            <?php if (empty($articles)): ?>
                <p>Vous n'avez pas encore publié d'article. <br><br> <a href="publier.php" class="btn btn-primary">Publier mon premier article</a></p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 1rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--color-border); text-align: left;">
                            <th style="padding: 1rem;">Titre</th>
                            <th style="padding: 1rem;">Date</th>
                            <th style="padding: 1rem;">Commentaires</th>
                            <th style="padding: 1rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $a): ?>
                        <tr style="border-bottom: 1px solid var(--color-border);">
                            <td style="padding: 1rem;">
                                <a href="article.php?id=<?= $a['id'] ?>"><?= htmlspecialchars($a['titre']) ?></a>
                            </td>
                            <td style="padding: 1rem;"><?= date('d/m/Y', strtotime($a['date_publication'])) ?></td>
                            <td style="padding: 1rem;"><?= $a['nb_commentaires'] ?></td>
                            <td style="padding: 1rem;">
                                <a href="modifier.php?id=<?= $a['id'] ?>" class="btn btn-outline" style="padding: 0.3rem 0.8rem; font-size: 0.9rem;">Modifier</a>
                                <form action="supprimer.php" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cet article ?');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                    <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                    <button type="submit" class="btn" style="background:#ef4444; color:white; padding: 0.3rem 0.8rem; font-size: 0.9rem; border:none; cursor:pointer;">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
    <?php require 'composants/pied-de-page.php'; ?>
</body>
</html>