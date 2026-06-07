<?php
session_start();
require 'config/connexion.php';
require 'composants/fonctions.php';
log_visite($pdo);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abdou Diatta | Portfolio</title>
    <meta name="description" content="Portfolio d'Abdou Diatta, Etudiant en Génie Logiciel et Administration Réseaux à Dakar.">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .hero { min-height: 80vh; display: flex; align-items: center; justify-content: center; text-align: center; background: radial-gradient(circle at center, var(--color-surface) 0%, var(--color-bg) 100%); }
        .hero h1 { font-size: 4rem; margin-bottom: 0.5rem; }
        .hero .highlight { color: var(--color-primary); }
        .hero p { font-size: 1.25rem; max-width: 600px; margin: 0 auto 2rem; }
        .skills-section { display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-xl); }
        .skills-grid { display: flex; flex-wrap: wrap; gap: var(--spacing-sm); }
        .skill-tag { background-color: var(--color-surface); padding: 0.5rem 1rem; border-radius: 20px; border: 1px solid var(--color-border); font-weight: 500; color: var(--color-text-primary); }
        .timeline { position: relative; max-width: 800px; margin: 0 auto; padding: var(--spacing-lg) 0; }
        .timeline::after { content: ''; position: absolute; width: 2px; background-color: var(--color-primary); top: 0; bottom: 0; left: 50%; margin-left: -1px; }
        .timeline-item { padding: 10px 40px; position: relative; background-color: inherit; width: 50%; }
        .timeline-item::after { content: ''; position: absolute; width: 20px; height: 20px; right: -10px; background-color: var(--color-bg); border: 4px solid var(--color-primary); top: 15px; border-radius: 50%; z-index: 1; }
        .left { left: 0; }
        .right { left: 50%; }
        .right::after { left: -10px; }
        .timeline-content { padding: var(--spacing-lg); background-color: var(--color-surface); border-radius: var(--border-radius); border: 1px solid var(--color-border); }
        .timeline-date { color: var(--color-primary); font-weight: bold; margin-bottom: var(--spacing-sm); display: block; }
        @media screen and (max-width: 768px) {
            .hero h1 { font-size: 2.5rem; }
            .skills-section { grid-template-columns: 1fr; }
            .timeline::after { left: 31px; }
            .timeline-item { width: 100%; padding-left: 70px; padding-right: 25px; }
            .timeline-item::after { left: 21px; }
            .right { left: 0%; }
        }
    </style>
</head>
<body>

    <?php require 'composants/navigation.php'; ?>

    <main>
        <section class="hero">
            <div class="container">
                <h1>Bonjour, je suis <span class="highlight">Abdou Diatta</span></h1>
                <p>Étudiant en Génie Logiciel & Administration Réseaux. Passionné par les technologies réseau, la cybersécurité et l'administration des systèmes informatiques.</p>
                <div>
                    <a href="projets.php" class="btn btn-primary">Voir mes projets</a>
                    <a href="contact.php" class="btn btn-outline" style="margin-left: 10px;">Me contacter</a>
                </div>
            </div>
        </section>

        <section id="about" class="container">
            <div class="text-center mb-4">
                <h2>À propos de moi</h2>
                <p>Mon parcours et mes ambitions.</p>
            </div>
            <div style="max-width: 900px; margin: 0 auto; background: var(--color-surface); padding: 2rem; border-radius: var(--border-radius); border: 1px solid var(--color-border); display: flex; gap: 2rem; align-items: center; flex-wrap: wrap;">
                <div style="flex: 1 1 250px; text-align: center;">
                    <img src="images/profile.jpg" alt="Abdou Diatta" style="width: 100%; max-width: 300px; border-radius: var(--border-radius); border: 3px solid var(--color-primary); object-fit: cover;">
                </div>
                <div style="flex: 2 1 400px; text-align: justify;">
                    <p>Etudiant en Licence 2 - Génie Logiciel et Administration Réseau à l'Ecole Supérieure de Technologie et de Management (ESTM) de Dakar, je suis passionné par les technologies réseau, la cybersécurité et l'administration des systèmes informatiques.</p>
                    <p>Rigoureux, curieux et motivé, je recherche activement un stage de formation professionnelle afin de consolider mes acquis théoriques par une expérience pratique en entreprise. Ma capacité d'apprentissage rapide, mon sens de l'organisation et ma curiosité technique constituent des atouts solides pour contribuer efficacement au sein d'une équipe IT.</p>
                </div>
            </div>
        </section>

        <section id="skills" class="container">
            <div class="text-center mb-4">
                <h2>Mes Compétences</h2>
                <p>Les technologies et outils que je maîtrise.</p>
            </div>
            
            <div class="skills-section">
                <div>
                    <h3 class="mb-2">IT & Administration Réseau</h3>
                    <div class="skills-grid">
                        <span class="skill-tag">Modèle OSI / TCP-IP</span>
                        <span class="skill-tag">Adressage IP / VLSM</span>
                        <span class="skill-tag">Config. Switch & Routeur</span>
                        <span class="skill-tag">Huawei CLI</span>
                        <span class="skill-tag">Windows Server</span>
                        <span class="skill-tag">Linux</span>
                        <span class="skill-tag">Virtualisation</span>
                        <span class="skill-tag">Cybersécurité</span>
                    </div>
                </div>
                <div>
                    <h3 class="mb-2">Programmation & Développement</h3>
                    <div class="skills-grid">
                        <span class="skill-tag">HTML / CSS</span>
                        <span class="skill-tag">JavaScript</span>
                        <span class="skill-tag">PHP</span>
                        <span class="skill-tag">SQL</span>
                        <span class="skill-tag">Java</span>
                        <span class="skill-tag">Python</span>
                        <span class="skill-tag">Algèbre relationnelle</span>
                    </div>
                </div>
                <div>
                    <h3 class="mb-2">Savoir-être</h3>
                    <div class="skills-grid">
                        <span class="skill-tag">Travail en équipe</span>
                        <span class="skill-tag">Curiosité</span>
                        <span class="skill-tag">Organisation</span>
                        <span class="skill-tag">Adaptabilité</span>
                        <span class="skill-tag">Analyse</span>
                    </div>
                </div>
                <div>
                    <h3 class="mb-2">Langues</h3>
                    <div class="skills-grid">
                        <span class="skill-tag">Français (Courant)</span>
                        <span class="skill-tag">Anglais (Technique)</span>
                    </div>
                </div>
            </div>
        </section>

        <section id="experience" class="container">
            <div class="text-center mb-4">
                <h2>Formation & Certifications</h2>
                <p>Mon cursus scolaire et mes certifications.</p>
            </div>
            <div class="timeline">
                <div class="timeline-item left">
                    <div class="timeline-content">
                        <span class="timeline-date">En cours</span>
                        <h3>Licence 2 - Génie Logiciel & Administration Réseau</h3>
                        <p>Ecole Supérieure de Technologie et de Management (ESTM) | Dakar, Sénégal.</p>
                    </div>
                </div>
                <div class="timeline-item right">
                    <div class="timeline-content">
                        <span class="timeline-date">Obtenu</span>
                        <h3>Baccalauréat Scientifique - Série S2</h3>
                        <p>Diplôme d'études secondaires avec spécialisation en sciences exactes.</p>
                    </div>
                </div>
                <div class="timeline-item left">
                    <div class="timeline-content">
                        <span class="timeline-date">Formations Continues</span>
                        <h3>Certifications diverses</h3>
                        <ul style="margin-left: 20px; margin-top: 10px; color: var(--color-text-secondary);">
                            <li>Administration réseau - Formation pratique</li>
                            <li>Introduction à la Cybersécurité</li>
                            <li>Cisco Packet Tracer - Fondamentaux</li>
                            <li>Fondamentaux Linux</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php require 'composants/pied-de-page.php'; ?>

</body>
</html>