<?php
session_start();
require 'config/connexion.php';
require 'fonctions.php';
verifier_connexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifier_csrf($_POST['csrf_token'] ?? '')) die('Erreur CSRF');

    $id_article = (int)($_POST['id'] ?? 0);
    $id_user = $_SESSION['utilisateur_id'];

    // Vérifier propriété et récupérer l'image pour suppression
    $stmt = $pdo->prepare("SELECT image_couverture FROM blog_articles WHERE id = :id AND auteur_id = :aut");
    $stmt->execute(['id' => $id_article, 'aut' => $id_user]);
    $article = $stmt->fetch();

    if ($article) {
        if ($article['image_couverture'] && file_exists($article['image_couverture'])) {
            unlink($article['image_couverture']);
        }
        $stmt = $pdo->prepare("DELETE FROM blog_articles WHERE id = :id");
        $stmt->execute(['id' => $id_article]);
    }
}
header("Location: mes-articles.php");
exit;
?>