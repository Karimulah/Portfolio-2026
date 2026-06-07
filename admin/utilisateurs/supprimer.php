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
    
    if ($id === $_SESSION['admin_id']) {
        die("Vous ne pouvez pas supprimer votre propre compte.");
    }
    
    $stmt = $pdo->prepare("DELETE FROM administrateurs WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

header("Location: index.php");
exit;
?>
