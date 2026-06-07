<?php
session_start();
require '../config/connexion.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: connexion.php");
    exit;
}

// Statistiques
$nb_projets = $pdo->query("SELECT COUNT(*) FROM projets")->fetchColumn();
$nb_messages = $pdo->query("SELECT COUNT(*) FROM messages_contact WHERE lu = 0")->fetchColumn();
$nb_demandes = $pdo->query("SELECT COUNT(*) FROM demandes_projet WHERE lu = 0")->fetchColumn();

// 5 dernières visites
$visites = $pdo->query("SELECT adresse_ip, page, date_visite FROM visites ORDER BY date_visite DESC LIMIT 5")->fetchAll();

// 5 dernières demandes
$dernieres_demandes = $pdo->query("SELECT id, nom, type_projet, date_demande, lu FROM demandes_projet ORDER BY date_demande DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord | Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: var(--color-surface); border-right: 1px solid var(--color-border); padding: 2rem 1rem; }
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu li { margin-bottom: 0.5rem; }
        .sidebar-menu a { display: block; padding: 0.8rem 1rem; color: var(--color-text-primary); text-decoration: none; border-radius: 4px; transition: background 0.2s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background-color: var(--color-primary); color: white; }
        .content { flex: 1; padding: 2rem; background-color: var(--color-bg); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card { background: var(--color-surface); padding: 1.5rem; border-radius: var(--border-radius); border: 1px solid var(--color-border); text-align: center; }
        .stat-number { font-size: 2.5rem; font-weight: bold; color: var(--color-primary); margin-top: 0.5rem; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 1rem; background: var(--color-surface); border: 1px solid var(--color-border); }
        .data-table th, .data-table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--color-border); }
        .data-table th { background-color: rgba(0,0,0,0.05); }
        .badge { padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.8rem; }
        .badge-new { background-color: #ef4444; color: white; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; }
        @media screen and (max-width: 900px) { .admin-layout { flex-direction: column; } .sidebar { width: 100%; } .grid-2 { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <h2 style="font-size: 1.2rem; margin-bottom: 2rem; padding-left: 1rem;">Admin Portfolio</h2>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active">Tableau de bord</a></li>
                <li><a href="projets/index.php">Gérer les projets</a></li>
                <li><a href="messages/index.php">Messages</a></li>
                <li><a href="demandes/index.php">Demandes</a></li>
                <li><a href="utilisateurs/index.php">Administrateurs</a></li>
                <li><a href="../index.php" target="_blank">Voir le site</a></li>
                <li style="margin-top: 2rem;"><a href="deconnexion.php" style="color: #ef4444;">Déconnexion</a></li>
            </ul>
        </aside>
        
        <main class="content">
            <h1>Bonjour, <?= htmlspecialchars($_SESSION['admin_prenom']) ?> 👋</h1>
            <p>Voici un aperçu de l'activité de votre portfolio.</p>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Projets Publiés</h3>
                    <div class="stat-number"><?= $nb_projets ?></div>
                </div>
                <div class="stat-card">
                    <h3>Messages Non Lus</h3>
                    <div class="stat-number"><?= $nb_messages ?></div>
                </div>
                <div class="stat-card">
                    <h3>Demandes Non Lues</h3>
                    <div class="stat-number"><?= $nb_demandes ?></div>
                </div>
            </div>

            <div class="grid-2">
                <div>
                    <h2>Dernières Visites</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Adresse IP</th>
                                <th>Page</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visites as $v): ?>
                            <tr>
                                <td><?= htmlspecialchars($v['adresse_ip']) ?></td>
                                <td><?= htmlspecialchars($v['page']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($v['date_visite'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div>
                    <h2>Dernières Demandes</h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dernieres_demandes as $d): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($d['nom']) ?>
                                    <?php if ($d['lu'] == 0): ?>
                                        <span class="badge badge-new">Nouveau</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($d['type_projet']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($d['date_demande'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
