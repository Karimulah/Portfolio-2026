<?php
session_start();
require 'config/connexion.php';
require 'composants/fonctions.php';
log_visite($pdo);

// Filtrage par mot-clé
$mot_cle = nettoyer($_GET['q'] ?? '');
$resultats = [];

try {
    if ($mot_cle !== '') {
        $stmt = $pdo->prepare("SELECT * FROM projets WHERE titre LIKE :search OR description LIKE :search ORDER BY date_creation DESC");
        $stmt->execute(['search' => '%' . $mot_cle . '%']);
    } else {
        $stmt = $pdo->query("SELECT * FROM projets ORDER BY date_creation DESC");
    }
    $resultats = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erreur lors de la récupération des projets : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Projets | Abdou Diatta</title>
    <meta name="description" content="Découvrez les projets académiques et personnels d'Abdou Diatta.">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .projects-header { text-align: center; margin-bottom: var(--spacing-xl); }
        .search-form { max-width: 600px; margin: 0 auto var(--spacing-lg); display: flex; gap: var(--spacing-sm); }
        .search-form input { flex: 1; }
        .projects-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: var(--spacing-lg); }
        .project-card { background-color: var(--color-surface); border-radius: var(--border-radius); overflow: hidden; border: 1px solid var(--color-border); transition: transform var(--transition-fast), border-color var(--transition-fast); display: flex; flex-direction: column; }
        .project-card:hover { transform: translateY(-5px); border-color: var(--color-primary); }
        .project-img { width: 100%; height: 180px; object-fit: cover; background-color: var(--color-bg); border-bottom: 1px solid var(--color-border); }
        .project-content { padding: var(--spacing-md); flex: 1; display: flex; flex-direction: column; }
        .project-title { margin-bottom: 0.2rem; font-size: 1.25rem; }
        .project-date { font-size: 0.85rem; color: var(--color-primary); margin-bottom: var(--spacing-sm); display: block; }
        .project-desc { font-size: 0.95rem; flex: 1; color: var(--color-text-secondary); margin-bottom: var(--spacing-md); }
        .project-tags { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: auto; }
        .tag { background-color: var(--color-bg); color: var(--color-text-primary); padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem; font-weight: 500; border: 1px solid var(--color-border); }
    </style>
</head>
<body>

    <?php require 'composants/navigation.php'; ?>

    <main>
        <section class="container">
            <div class="projects-header">
                <h1>Projets Académiques & Personnels</h1>
                <p>Voici quelques travaux pratiques et projets que j'ai réalisés.</p>
            </div>

            <form class="search-form" action="projets.php" method="GET">
                <input type="search" name="q" value="<?= htmlspecialchars($mot_cle) ?>" placeholder="Rechercher (ex: Réseau, LAN...)" aria-label="Rechercher un projet">
                <button type="submit" class="btn btn-primary">Filtrer</button>
            </form>

            <div class="projects-grid">
                <?php foreach ($resultats as $projet) : ?>
                    <article class="project-card">
                        <?php if (!empty($projet['image'])): ?>
                            <img src="<?= htmlspecialchars($projet['image']) ?>" alt="<?= htmlspecialchars($projet['titre']) ?>" class="project-img">
                        <?php else: 
                            $gradients = [
                                'linear-gradient(45deg, #1e3c72, #2a5298)',
                                'linear-gradient(45deg, #cc2b5e, #753a88)',
                                'linear-gradient(135deg, #11998e, #38ef7d)',
                                'linear-gradient(135deg, #fc4a1a, #f7b733)',
                                'linear-gradient(135deg, #000046, #1CB5E0)'
                            ];
                            $index = (int)$projet['id'] % count($gradients);
                            $bg = $gradients[$index];
                        ?>
                            <div class="project-img" style="background: <?= $bg ?>; display: flex; align-items: center; justify-content: center; text-align: center; padding: 1rem;">
                                <span style="color: rgba(255,255,255,0.9); font-family: var(--font-heading); font-size: 1.1rem; font-weight: bold;"><?= htmlspecialchars($projet['titre']) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="project-content">
                            <h3 class="project-title"><?= htmlspecialchars($projet['titre']) ?></h3>
                            <span class="project-date">Créé le <?= date('d/m/Y', strtotime($projet['date_creation'])) ?></span>
                            <p class="project-desc"><?= nl2br(htmlspecialchars($projet['description'])) ?></p>
                            <div class="project-tags">
                                <?php 
                                $techs = explode(',', $projet['technologies']);
                                foreach ($techs as $tech) : 
                                    $tech = trim($tech);
                                    if (!empty($tech)):
                                ?>
                                    <span class="tag"><?= htmlspecialchars($tech) ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                            <?php if (!empty($projet['lien'])): ?>
                                <div style="margin-top: 1rem;">
                                    <a href="<?= htmlspecialchars($projet['lien']) ?>" target="_blank" class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Voir le lien</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if (empty($resultats)) : ?>
                <p style="text-align: center; margin-top: 2rem;">Aucun projet ne correspond à votre recherche ou la base de données est vide.</p>
            <?php endif; ?>
        </section>
    </main>

    <?php require 'composants/pied-de-page.php'; ?>

</body>
</html>
