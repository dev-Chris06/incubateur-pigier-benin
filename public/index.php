<?php
require_once __DIR__ . '/../config/config.php';

// Définition des variables pour le header
$titre_page = "Accueil | " . SITE_NOM;
$description_page = SITE_NOM . ", le premier incubateur étudiant du Bénin, hébergé par PIGIER Bénin.";

// Inclusion du header
require_once __DIR__ . '/../includes/header.php';

// Récupération des contenus dynamiques
$hero_titre = getContenu($pdo, 'accueil', 'hero_titre');
$hero_soustitre = getContenu($pdo, 'accueil', 'hero_soustitre');
$chiffre_cle = getContenu($pdo, 'accueil', 'chiffre_cle');
?>

<main>
    <!-- Section Hero -->
   <section class="hero">
        <div class="container hero-grille">
            <div class="hero-contenu">
                <h1 class="hero-titre"><?= $hero_titre ? echapper($hero_titre) : 'Le 1er incubateur universitaire dédié aux étudiants de PIGIER-BÉNIN' ?></h1>
                <p class="hero-soustitre"><?= $hero_soustitre ? echapper($hero_soustitre) : 'Un espace exclusif pour transformer vos idées en entreprises.' ?></p>
                <div class="hero-cta">
                    <a href="<?= SITE_URL ?>/public/postuler.php" class="btn btn-primaire">Postuler</a>
                    <a href="#accueil-programme" class="btn btn-secondaire">Découvrir le programme</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="<?= SITE_URL ?>/assets/images/pech-laureats.jpg" alt="Lauréats PECH 2026" />
            </div>
        </div>
    </section>

    <!-- Section Pourquoi cet incubateur -->
    <section class="pourquoi">
        <div class="container">
            <h2 class="section-titre">Pourquoi cet incubateur ?</h2>
            <div class="cartes-grille">
                <article class="carte">
                    <div class="carte-icone">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c0 1.657 2.686 3 6 3s6-1.343 6-3v-5"/>
                        </svg>
                    </div>
                    <h3 class="carte-titre">100% étudiants</h3>
                    <p class="carte-description">Le seul incubateur au Bénin exclusivement dédié aux étudiants universitaires.</p>
                </article>

                <article class="carte">
                    <div class="carte-icone">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="8" width="18" height="4" rx="1"/>
                            <path d="M12 8v13M19 12v7a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-7"/>
                            <path d="M7.5 8a2.5 2.5 0 0 1 0-5C11 3 12 8 12 8s1-5 4.5-5a2.5 2.5 0 0 1 0 5"/>
                        </svg>
                    </div>
                    <h3 class="carte-titre">Gratuit</h3>
                    <p class="carte-description">Un avantage offert par PIGIER à ses étudiants, sans surcoût.</p>
                </article>

                <article class="carte">
                    <div class="carte-icone">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/>
                            <path d="M12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/>
                            <path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/>
                            <path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/>
                        </svg>
                    </div>
                    <h3 class="carte-titre">Accompagnement complet</h3>
                    <p class="carte-description">Coaching, mentorat, formation et appui réglementaire.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- Section Programme -->
    <section id="accueil-programme" class="accueil-section">
        <div class="container">
            <h2 class="section-titre">Le programme</h2>
            <p class="apercu-description">Un accompagnement complet pour transformer votre idée en entreprise.</p>
            <div class="programme-grille">
                <div class="accueil-contenu">
                    <div class="accueil-icone">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="8" y="2" width="8" height="4" rx="1"/>
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                            <path d="M9 12h6M9 16h6M9 8h1"/>
                        </svg>
                    </div>
                    <h3 class="accueil-titre">Candidature & sélection</h3>
                    <p class="accueil-texte">Les étudiants porteurs d'une idée déposent leur dossier de candidature. Une première sélection retient les projets les plus prometteurs pour la suite du parcours.</p>
                </div>
                <div class="accueil-contenu">
                    <div class="accueil-icone">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                    </div>
                    <h3 class="accueil-titre">Formation & mentorat</h3>
                    <p class="accueil-texte">Sur plusieurs mois, les candidats bénéficient de formations, d'ateliers pratiques et de séances de coaching avec des mentors et des entrepreneurs experts pour affiner leur projet, avec une sélection progressive à chaque étape.</p>
                </div>
                <div class="accueil-contenu">
                    <div class="accueil-icone">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 21h8M12 17v4"/>
                            <path d="M7 4h10v6a5 5 0 0 1-10 0V4z"/>
                            <path d="M17 6h2a2 2 0 0 1 0 4h-1M7 6H5a2 2 0 0 0 0 4h1"/>
                        </svg>
                    </div>
                    <h3 class="accueil-titre">Finale devant jury</h3>
                    <p class="accueil-texte">Les finalistes présentent leur projet devant un jury qui délibère et désigne les lauréats de l'édition.</p>
                </div>
                <div class="accueil-contenu">
                    <div class="carte-icone">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/>
                            <path d="M12 15l-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/>
                            <path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/>
                            <path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/>
                        </svg>
                    </div>
                    <h3 class="accueil-titre">Incubation & lancement</h3>
                    <p class="accueil-texte">Les meilleurs projets peuvent ensuite intégrer une phase d'incubation pour finaliser leur solution avant son lancement sur le marché.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Section Chiffre clé -->
    <section class="chiffre-cle">
        <div class="container">
            <p class="chiffre-texte"><?= $chiffre_cle ? echapper($chiffre_cle) : '2 éditions du PECH, 12 projets finalistes, 6 lauréats en 2026' ?></p>
        </div>
    </section>

    <!-- Section Le PECH -->
    <section class="apercu-pech">
        <div class="container">
            <h2 class="section-titre">Le PECH</h2>
            <p class="apercu-description">Le Pigier Entrepreneurship Challenge est le concours annuel d'incubation et de financement qui propulse les initiatives entrepreneuriales des étudiants.</p>
            <div class="apercu-cta">
                <a href="<?= SITE_URL ?>/public/pech.php" class="btn btn-primaire">Voir les projets accompagnés</a>
            </div>
        </div>
    </section>

    <!-- Section CTA final -->
    <section class="cta-final">
        <div class="container">
            <h2 class="cta-titre">Une idée de projet ? Rejoignez l'incubateur.</h2>
            <a href="<?= SITE_URL ?>/public/postuler.php" class="btn btn-primaire">Postuler</a>
        </div>
    </section>
</main>

<?php
// Inclusion du footer
require_once __DIR__ . '/../includes/footer.php';
?>
