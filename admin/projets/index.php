<?php
session_start();
require '../../config/connexion.php';
require '../../composants/fonctions.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../connexion.php");
    exit;
}

$projets = $pdo->query("SELECT * FROM projets ORDER BY date_creation DESC")->fetchAll();
$csrf_token = generer_csrf();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Projets | Admin</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        .admin-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: var(--color-surface); border-right: 1px solid var(--color-border); padding: 2rem 1rem; }
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu li { margin-bottom: 0.5rem; }
        .sidebar-menu a { display: block; padding: 0.8rem 1rem; color: var(--color-text-primary); text-decoration: none; border-radius: 4px; transition: background 0.2s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background-color: var(--color-primary); color: white; }
        .content { flex: 1; padding: 2rem; background-color: var(--color-bg); }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 1rem; background: var(--color-surface); border: 1px solid var(--color-border); }
        .data-table th, .data-table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--color-border); }
        .data-table th { background-color: rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <h2 style="font-size: 1.2rem; margin-bottom: 2rem; padding-left: 1rem;">Admin Portfolio</h2>
            <ul class="sidebar-menu">
                <li><a href="../dashboard.php">Tableau de bord</a></li>
                <li><a href="index.php" class="active">Gérer les projets</a></li>
                <li><a href="../messages/index.php">Messages</a></li>
                <li><a href="../demandes/index.php">Demandes</a></li>
                <li><a href="../utilisateurs/index.php">Administrateurs</a></li>
                <li><a href="../../index.php" target="_blank">Voir le site</a></li>
                <li style="margin-top: 2rem;"><a href="../deconnexion.php" style="color: #ef4444;">Déconnexion</a></li>
            </ul>
        </aside>
        
        <main class="content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h1>Gestion des Projets</h1>
                <a href="creer.php" class="btn btn-primary">Ajouter un projet</a>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Technologies</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projets as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['titre']) ?></td>
                        <td><?= htmlspecialchars($p['technologies']) ?></td>
                        <td><?= date('d/m/Y', strtotime($p['date_creation'])) ?></td>
                        <td>
                            <a href="modifier.php?id=<?= $p['id'] ?>" class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Modifier</a>
                            <form action="supprimer.php" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce projet définitivement ?');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" class="btn" style="background:#ef4444; color:white; padding: 0.3rem 0.6rem; border:none; border-radius:4px; cursor:pointer;">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
