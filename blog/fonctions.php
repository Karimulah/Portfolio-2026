<?php

/**
 * Vérifie qu'un champ n'est pas vide après nettoyage.
 */
function champ_requis(string $valeur): bool {
    return !empty(trim($valeur));
}

/**
 * Nettoie une valeur pour l'afficher sans risque dans du HTML.
 */
function nettoyer(string $valeur): string {
    return htmlspecialchars(trim($valeur));
}

/**
 * Génère un jeton CSRF et le stocke en session.
 */
function generer_csrf(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie la validité d'un jeton CSRF.
 */
function verifier_csrf(string $token): bool {
    if (empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirige vers la page de connexion si l'utilisateur n'est pas connecté.
 */
function verifier_connexion() {
    if (!isset($_SESSION['utilisateur_id'])) {
        header('Location: connexion.php');
        exit;
    }
}
?>
