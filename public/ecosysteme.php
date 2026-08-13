<?php
require_once __DIR__ . '/../config/config.php';
// Définition des variables pour le header
$titre_page = "Écosystème | " . SITE_NOM;
$description_page = "L'équipe de direction, les mentors, coachs et experts qui accompagnent les étudiants de " . SITE_NOM . ".";
// Inclusion du header
require_once __DIR__ . '/../includes/header.php';
// Récupération de toutes les personnes actives avec leurs rôles
try {
    $stmt = $pdo->query("
        SELECT p.id, p.nom, p.bio, p.photo_url, p.est_cadre_direction, p.ordre_affichage,
               GROUP_CONCAT(r.libelle ORDER BY r.categorie SEPARATOR ', ') AS roles_liste
        FROM personnes p
        LEFT JOIN personnes_roles pr ON p.id = pr.personne_id
        LEFT JOIN roles r ON pr.role_id = r.id
        WHERE p.actif = TRUE	
        GROUP BY p.id
        ORDER BY p.est_cadre_direction DESC, p.ordre_affichage ASC
    ");
    $personnes = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erreur récupération personnes: " . $e->getMessage());
    $personnes = [];
}
// Séparation en deux groupes
$direction = [];
$mentors_coachs = [];
foreach ($personnes as $personne) {
    if ($personne['est_cadre_direction']) {
        $direction[] = $personne;
    } else {
        $mentors_coachs[] = $personne;
    }
}
?>
<main>
    <!-- Section Hero Écosystème -->
    <section class="hero hero-page">
        <div class="container">
            <div class="hero-contenu">
                <h1 class="hero-titre">L'Écosystème</h1>
                <p class="hero-soustitre">Rencontrez l'équipe qui accompagne les entrepreneurs de demain</p>
            </div>
        </div>
    </section>
    <!-- Section Direction -->
    <?php if (!empty($direction)): ?>
    <section class="ecosysteme-section">
        <div class="container">
            <h2 class="section-titre">Direction</h2>
            <div class="personnes-grille">
                <?php foreach ($direction as $personne): ?>
                    <article class="personne-carte">
                        <div class="personne-infos">
                            <?php if (!empty($personne['roles_liste'])): ?>
                                <p class="personne-role"><?= echapper($personne['roles_liste']) ?></p>
                            <?php endif; ?>
                            <h3 class="personne-nom"><?= echapper($personne['nom']) ?></h3>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <!-- Section Mentors, coachs & experts -->
    <section class="ecosysteme-section ecosysteme-section-alt">
        <div class="container">
            <h2 class="section-titre">Mentors, coachs & experts</h2>
            <?php if (!empty($mentors_coachs)): ?>
                <div class="personnes-grille <?= count($mentors_coachs) === 1 ? 'personnes-grille-centre' : '' ?>">
                    <?php foreach ($mentors_coachs as $personne): ?>
                        <article class="personne-carte">
                            <div class="personne-infos">
                                <?php if (!empty($personne['roles_liste'])): ?>
                                    <p class="personne-role"><?= echapper($personne['roles_liste']) ?></p>
                                <?php endif; ?>
                                <h3 class="personne-nom"><?= echapper($personne['nom']) ?></h3>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <?php /* Texte masqué si besoin: <p class="ecosysteme-note">D'autres mentors rejoindront bientôt l'écosystème.</p> */ ?>
            <?php else: ?>
                <div class="ecosysteme-vide">
                    <p>Les profils des mentors, coachs et experts seront bientôt disponibles.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <!-- Section CTA final -->
    <section class="cta-final">
        <div class="container">
            <h2 class="cta-titre">Rejoignez l'aventure entrepreneuriale</h2>
            <a href="<?= SITE_URL ?>/postuler" class="btn btn-primaire">Postuler maintenant</a>
        </div>
    </section>
</main>
<?php
// Inclusion du footer
require_once __DIR__ . '/../includes/footer.php';
?>
