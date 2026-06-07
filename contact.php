<?php
session_start();
require 'config/connexion.php';
require 'composants/fonctions.php';
log_visite($pdo);

$erreurs_contact = [];
$succes_contact = false;
$nom_contact = '';
$email_contact = '';
$message_contact = '';

$erreurs_projet = [];
$succes_projet = false;
$demande_projet = [];
$nom_projet = '';
$email_projet = '';
$type_projet = '';
$budget_projet = '';
$desc_projet = '';

// Générer le jeton CSRF pour les formulaires
$csrf_token = generer_csrf();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_type = $_POST['form_type'] ?? '';
    $token_soumis = $_POST['csrf_token'] ?? '';

    // Vérification globale du jeton CSRF
    if (!verifier_csrf($token_soumis)) {
        die("Erreur de sécurité : Jeton CSRF invalide.");
    }

    if ($form_type === 'contact') {
        $nom_contact     = nettoyer($_POST['nom'] ?? '');
        $email_contact   = nettoyer($_POST['email'] ?? '');
        $message_contact = nettoyer($_POST['message'] ?? '');

        if (!champ_requis($nom_contact))     $erreurs_contact['nom'] = 'Le nom est obligatoire.';
        if (!filter_var($email_contact, FILTER_VALIDATE_EMAIL)) $erreurs_contact['email'] = 'Ldresse e-mail est invalide.';
        if (!champ_requis($message_contact)) $erreurs_contact['message'] = 'Le message ne peut pas être vide.';

        if (empty($erreurs_contact)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO messages_contact (nom, email, message) VALUES (:nom, :email, :message)");
                $stmt->execute([
                    'nom' => $nom_contact,
                    'email' => $email_contact,
                    'message' => $message_contact
                ]);
                $succes_contact = true;
                // Vider les champs après succès
                $nom_contact = ''; $email_contact = ''; $message_contact = '';
            } catch (PDOException $e) {
                error_log("Erreur insertion contact : " . $e->getMessage());
                $erreurs_contact['general'] = "Une erreur est survenue lors de l'envoi du message.";
            }
        }
    } 
    elseif ($form_type === 'projet') {
        $nom_projet     = nettoyer($_POST['project_name'] ?? '');
        $email_projet   = nettoyer($_POST['project_email'] ?? '');
        $type_projet    = nettoyer($_POST['project_type'] ?? '');
        $budget_projet  = nettoyer($_POST['budget'] ?? '');
        $desc_projet    = nettoyer($_POST['project_desc'] ?? '');

        if (!champ_requis($nom_projet))     $erreurs_projet['project_name'] = 'Le nom complet est obligatoire.';
        if (!filter_var($email_projet, FILTER_VALIDATE_EMAIL)) $erreurs_projet['project_email'] = 'Ldresse e-mail est invalide.';
        if (!champ_requis($type_projet))    $erreurs_projet['project_type'] = 'Le type de besoin est obligatoire.';
        if (!champ_requis($desc_projet))    $erreurs_projet['project_desc'] = 'La description est obligatoire.';

        if (empty($erreurs_projet)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO demandes_projet (nom, email, type_projet, description, budget) VALUES (:nom, :email, :type_projet, :description, :budget)");
                $stmt->execute([
                    'nom' => $nom_projet,
                    'email' => $email_projet,
                    'type_projet' => $type_projet,
                    'description' => $desc_projet,
                    'budget' => $budget_projet ?: null
                ]);
                $succes_projet = true;
                $demande_projet = [
                    'nom' => $nom_projet,
                    'email' => $email_projet,
                    'type_projet' => $type_projet,
                    'budget' => $budget_projet,
                    'description' => $desc_projet
                ];
                // Vider les champs après succès
                $nom_projet = ''; $email_projet = ''; $type_projet = ''; $budget_projet = ''; $desc_projet = '';
            } catch (PDOException $e) {
                error_log("Erreur insertion demande projet : " . $e->getMessage());
                $erreurs_projet['general'] = "Une erreur est survenue lors de l'envoi de la demande.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | Abdou Diatta</title>
    <meta name="description" content="Contactez Abdou Diatta pour vos projets en développement et administration système.">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .contact-container { display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-xl); }
        .form-section { background-color: var(--color-surface); padding: var(--spacing-lg); border-radius: var(--border-radius); border: 1px solid var(--color-border); }
        .form-group { margin-bottom: var(--spacing-md); }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 0.8rem; border: 1px solid var(--color-border); border-radius: 4px; background-color: var(--color-bg); color: var(--color-text-primary); font-family: inherit; }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2); }
        .form-group textarea { min-height: 120px; resize: vertical; }
        .error-msg { color: #ef4444; font-size: 0.85rem; margin-top: 0.3rem; display: block; }
        .success-box { background-color: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; color: #10b981; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; }
        .recap-box { background-color: var(--color-bg); border: 1px solid var(--color-border); padding: 1rem; border-radius: 4px; margin-top: 1rem; }
        .recap-box ul { list-style-type: none; padding: 0; }
        .recap-box li { margin-bottom: 0.5rem; }
        @media screen and (max-width: 900px) { .contact-container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <?php require 'composants/navigation.php'; ?>

    <main>
        <section class="container">
            <div class="text-center mb-4">
                <h1>Travaillons Ensemble</h1>
                <p>Je suis actuellement à la recherche d'un stage ou d'un projet freelance. N'hésitez pas à m'écrire.</p>
            </div>

            <div class="contact-container">
                <!-- Formulaire de Contact Simple -->
                <div class="form-section">
                    <h2>Envoyer un message</h2>
                    <p class="mb-2">Pour toute question ou proposition de stage.</p>
                    
                    <?php if (isset($erreurs_contact['general'])): ?>
                        <div class="error-msg" style="margin-bottom: 1rem;"><?= $erreurs_contact['general'] ?></div>
                    <?php endif; ?>

                    <?php if ($succes_contact): ?>
                        <div class="success-box">
                            <strong>Message envoyé avec succès !</strong><br>
                            Merci de m'avoir contacté, je vous répondrai dans les plus brefs délais.
                        </div>
                    <?php endif; ?>

                    <form action="contact.php" method="POST">
                        <input type="hidden" name="form_type" value="contact">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        
                        <div class="form-group">
                            <label for="name">Nom complet *</label>
                            <input type="text" id="name" name="nom" value="<?= htmlspecialchars($nom_contact) ?>" placeholder="Votre nom">
                            <?php if (isset($erreurs_contact['nom'])): ?>
                                <span class="error-msg"><?= $erreurs_contact['nom'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="email">Adresse e-mail *</label>
                            <input type="text" id="email" name="email" value="<?= htmlspecialchars($email_contact) ?>" placeholder="vous@exemple.com">
                            <?php if (isset($erreurs_contact['email'])): ?>
                                <span class="error-msg"><?= $erreurs_contact['email'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" placeholder="Votre message..."><?= htmlspecialchars($message_contact) ?></textarea>
                            <?php if (isset($erreurs_contact['message'])): ?>
                                <span class="error-msg"><?= $erreurs_contact['message'] ?></span>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary mt-1">Envoyer le message</button>
                    </form>
                </div>

                <!-- Formulaire de Demande de Prestation (Projet) -->
                <div class="form-section">
                    <h2>Demande de Prestation</h2>
                    <p class="mb-2">Décrivez votre besoin en infrastructure ou développement.</p>

                    <?php if (isset($erreurs_projet['general'])): ?>
                        <div class="error-msg" style="margin-bottom: 1rem;"><?= $erreurs_projet['general'] ?></div>
                    <?php endif; ?>

                    <?php if ($succes_projet): ?>
                        <div class="success-box">
                            <strong>Demande soumise avec succès !</strong><br>
                            Voici un récapitulatif de votre besoin :
                            <div class="recap-box">
                                <ul>
                                    <li><strong>Nom :</strong> <?= htmlspecialchars($demande_projet['nom']) ?></li>
                                    <li><strong>Email :</strong> <?= htmlspecialchars($demande_projet['email']) ?></li>
                                    <li><strong>Type :</strong> <?= htmlspecialchars($demande_projet['type_projet']) ?></li>
                                    <li><strong>Budget :</strong> <?= htmlspecialchars($demande_projet['budget'] ?: 'Non défini') ?></li>
                                    <li><strong>Description :</strong><br><?= nl2br(htmlspecialchars($demande_projet['description'])) ?></li>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form action="contact.php" method="POST">
                        <input type="hidden" name="form_type" value="projet">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                        <div class="form-group">
                            <label for="project-name">Nom complet *</label>
                            <input type="text" id="project-name" name="project_name" value="<?= htmlspecialchars($nom_projet) ?>" placeholder="Votre nom">
                            <?php if (isset($erreurs_projet['project_name'])): ?>
                                <span class="error-msg"><?= $erreurs_projet['project_name'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="project-email">E-mail de contact *</label>
                            <input type="text" id="project-email" name="project_email" value="<?= htmlspecialchars($email_projet) ?>" placeholder="vous@exemple.com">
                            <?php if (isset($erreurs_projet['project_email'])): ?>
                                <span class="error-msg"><?= $erreurs_projet['project_email'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="project-type">Type de besoin *</label>
                            <select id="project-type" name="project_type">
                                <option value="">-- Sélectionnez une option --</option>
                                <option value="reseau" <?= $type_projet === 'reseau' ? 'selected' : '' ?>>Mise en place / Audit Réseau</option>
                                <option value="sysadmin" <?= $type_projet === 'sysadmin' ? 'selected' : '' ?>>Administration Serveur (Linux/Windows)</option>
                                <option value="dev" <?= $type_projet === 'dev' ? 'selected' : '' ?>>Développement Web (HTML/CSS/JS/PHP)</option>
                                <option value="cyber" <?= $type_projet === 'cyber' ? 'selected' : '' ?>>Conseil en Cybersécurité</option>
                                <option value="autre" <?= $type_projet === 'autre' ? 'selected' : '' ?>>Autre</option>
                            </select>
                            <?php if (isset($erreurs_projet['project_type'])): ?>
                                <span class="error-msg"><?= $erreurs_projet['project_type'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="budget">Budget estimatif</label>
                            <select id="budget" name="budget">
                                <option value="">-- Non défini --</option>
                                <option value="small" <?= $budget_projet === 'small' ? 'selected' : '' ?>>Moins de 1000€</option>
                                <option value="medium" <?= $budget_projet === 'medium' ? 'selected' : '' ?>>1000€ - 3000€</option>
                                <option value="large" <?= $budget_projet === 'large' ? 'selected' : '' ?>>3000€ - 10000€</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="project-desc">Description du besoin *</label>
                            <textarea id="project-desc" name="project_desc" placeholder="Description détaillée de l'infrastructure ou de l'application souhaitée..."><?= htmlspecialchars($desc_projet) ?></textarea>
                            <?php if (isset($erreurs_projet['project_desc'])): ?>
                                <span class="error-msg"><?= $erreurs_projet['project_desc'] ?></span>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-outline mt-1">Soumettre la demande</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <?php require 'composants/pied-de-page.php'; ?>

</body>
</html>
