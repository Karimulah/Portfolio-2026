<?php
session_start();
require '../../config/connexion.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../connexion.php");
    exit;
}

if (isset($_GET['lire'])) {
    $id = (int)$_GET['lire'];
    $stmt = $pdo->prepare("UPDATE messages_contact SET lu = 1 WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: index.php");
    exit;
}

$messages = $pdo->query("SELECT * FROM messages_contact ORDER BY date_envoi DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messages | Admin</title>
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
        .badge-new { background-color: #ef4444; color: white; padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.8rem; }
        .msg-content { background: var(--color-bg); padding: 1rem; border-radius: 4px; margin-top: 0.5rem; border: 1px solid var(--color-border); }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <h2 style="font-size: 1.2rem; margin-bottom: 2rem; padding-left: 1rem;">Admin Portfolio</h2>
            <ul class="sidebar-menu">
                <li><a href="../dashboard.php">Tableau de bord</a></li>
                <li><a href="../projets/index.php">Gérer les projets</a></li>
                <li><a href="index.php" class="active">Messages</a></li>
                <li><a href="../demandes/index.php">Demandes</a></li>
                <li><a href="../utilisateurs/index.php">Administrateurs</a></li>
                <li><a href="../../index.php" target="_blank">Voir le site</a></li>
                <li style="margin-top: 2rem;"><a href="../deconnexion.php" style="color: #ef4444;">Déconnexion</a></li>
            </ul>
        </aside>
        
        <main class="content">
            <h1>Messages de contact</h1>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $m): ?>
                    <tr style="<?= $m['lu'] == 0 ? 'font-weight: bold; background: rgba(99, 102, 241, 0.05);' : '' ?>">
                        <td style="white-space: nowrap;"><?= date('d/m/Y H:i', strtotime($m['date_envoi'])) ?></td>
                        <td><?= htmlspecialchars($m['nom']) ?></td>
                        <td><?= htmlspecialchars($m['email']) ?></td>
                        <td>
                            <div style="max-height: 100px; overflow-y: auto;">
                                <?= nl2br(htmlspecialchars($m['message'])) ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($m['lu'] == 0): ?>
                                <a href="?lire=<?= $m['id'] ?>" class="btn btn-outline" style="padding: 0.3rem 0.6rem; font-size: 0.8rem;">Marquer lu</a>
                            <?php else: ?>
                                <span style="color: var(--color-text-secondary); font-size: 0.9rem;">Lu</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
