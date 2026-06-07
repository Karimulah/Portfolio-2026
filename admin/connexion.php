<?php
session_start();
require '../config/connexion.php';
require '../composants/fonctions.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$erreur = '';
$csrf_token = generer_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $token_soumis = $_POST['csrf_token'] ?? '';

    if (!verifier_csrf($token_soumis)) {
        die("Erreur de sécurité : Jeton CSRF invalide.");
    }

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id, prenom, mot_de_passe FROM administrateurs WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_prenom'] = $admin['prenom'];
            header("Location: dashboard.php");
            exit;
        } else {
            $erreur = "Identifiants incorrects.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-login { max-width: 400px; margin: 100px auto; padding: 2rem; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: var(--border-radius); }
        .admin-login h1 { text-align: center; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; }
        .form-group input { width: 100%; padding: 0.8rem; border: 1px solid var(--color-border); border-radius: 4px; background-color: var(--color-bg); color: var(--color-text-primary); }
        .error { color: #ef4444; background: rgba(239, 68, 68, 0.1); padding: 0.8rem; border-radius: 4px; margin-bottom: 1rem; text-align: center; }
        .btn-full { width: 100%; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="admin-login">
            <h1>Administration</h1>
            <?php if ($erreur): ?>
                <div class="error"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>
            <form action="connexion.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Se connecter</button>
            </form>
        </div>
    </div>
</body>
</html>
