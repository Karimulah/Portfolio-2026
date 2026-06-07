<?php
session_start();
require '../../config/connexion.php';
require '../../composants/fonctions.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../connexion.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifier_csrf($_POST['csrf_token'] ?? '')) die('Erreur CSRF');
    $id = (int)$_POST['id'];
    
    // On pourrait supprimer l'image du serveur ici avant de delete la ligne
    $stmt = $pdo->prepare("SELECT image FROM projets WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $projet = $stmt->fetch();
    
    if ($projet && $projet['image']) {
        $chemin_img = '../../' . $projet['image'];
        if (file_exists($chemin_img)) {
            unlink($chemin_img);
        }
    }
    
    $stmt = $pdo->prepare("DELETE FROM projets WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

header("Location: index.php");
exit;
?>
