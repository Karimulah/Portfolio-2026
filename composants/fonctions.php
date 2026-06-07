<?php

/**
 * Vérifie qu'un champ n'est pas vide après nettoyage.
 * @param string $valeur  La valeur à vérifier
 * @return bool           true si le champ est valide, false sinon
 */
function champ_requis(string $valeur): bool {
    return !empty(trim($valeur));
}

/**
 * Nettoie une valeur pour l'afficher sans risque dans du HTML.
 * @param string $valeur  La valeur brute
 * @return string         La valeur nettoyée
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
 * Enregistre la visite dans la base de données.
 */
function log_visite($pdo) {
    // Récupération de l'IP, prise en compte des proxy
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'INCONNUE';
    }
    
    $page = basename($_SERVER['PHP_SELF']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO visites (adresse_ip, page) VALUES (:ip, :page)");
        $stmt->execute(['ip' => $ip, 'page' => $page]);
    } catch(PDOException $e) {
        error_log("Erreur de log visite : " . $e->getMessage());
    }
}
?>
