<?php
require_once __DIR__ . '/../config/config.php';

// Définition des variables pour le header
$titre_page = "Le PECH | " . SITE_NOM;
$description_page = "Le Pigier Entrepreneurship Challenge, le concours d'incubation et de financement de " . SITE_NOM . ".";

// Inclusion du header
require_once __DIR__ . '/../includes/header.php';

// Récupération de toutes les éditions du PECH
try {
    $stmt = $pdo->query("SELECT * FROM pech_editions ORDER BY numero_edition DESC");
    $editions = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erreur récupération éditions PECH: " . $e->getMessage());
    $editions = [];
}
?>

<main>
    <!-- Section Hero PECH -->
    <section class="hero hero-page">
        <div class="container">
            <div class="hero-contenu">
                <img src="<?= SITE_URL ?>/assets/images/pech.png" alt="Logo PECH" class="hero-logo">
                <h1 class="hero-titre">Le PECH</h1>
                <p class="hero-soustitre">Pigier Entrepreneurship Challenge</p>
            </div>
        </div>
    </section>

    <!-- Section Introduction -->
    <section class="pech-intro">
        <div class="container">
            <h2 class="section-titre">Le PECH — Pigier Entrepreneurship Challenge</h2>
            <p class="pech-intro-texte">PECH est une compétition initié par la direction générale de l'école PIGIER-BENIN pour cultiver l'esprit entrepreneurial et promouvoir l'auto-emploi des apprenants.</p>
        </div>
    </section>

    <!-- Liste des éditions -->
    <?php if (!empty($editions)): ?>
        <?php foreach ($editions as $index => $edition): ?>
            <section class="pech-edition <?= $index % 2 === 1 ? 'pech-edition-alt' : '' ?>">
                <div class="container">
                    <!-- En-tête de l'édition -->
                    <div class="edition-header">
                        <h3 class="edition-titre">
                            Édition <?= echapper($edition['numero_edition']) ?>
                            <?php if ($edition['annee_academique']): ?>
                                — <?= echapper($edition['annee_academique']) ?>
                            <?php endif; ?>
                        </h3>

                        <!-- Dates -->
                        <?php if ($edition['statut'] === 'a_venir'): ?>
                            <p class="edition-dates">📅 Dates à venir</p>
                        <?php elseif ($edition['date_debut'] && $edition['date_fin']): ?>
                            <p class="edition-dates">
                                📅 <?= formaterDateFr($edition['date_debut']) ?> - <?= formaterDateFr($edition['date_fin']) ?>
                            </p>
                        <?php endif; ?>

                        <!-- Statistiques -->
                        <div class="edition-stats">
                            <?php if ($edition['nb_finalistes']): ?>
                                <span class="stat-badge">👥 <?= echapper($edition['nb_finalistes']) ?> finaliste<?= $edition['nb_finalistes'] > 1 ? 's' : '' ?></span>
                            <?php endif; ?>
                            <?php if ($edition['nb_laureats']): ?>
                                <span class="stat-badge stat-badge-laureat">🏆 <?= echapper($edition['nb_laureats']) ?> lauréat<?= $edition['nb_laureats'] > 1 ? 's' : '' ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Description -->
                        <?php if ($edition['description']): ?>
                            <p class="edition-description"><?= echapper($edition['description']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Réalisations de cette édition -->
                    <?php
                    try {
                        $stmt_realisations = $pdo->prepare("
                            SELECT * FROM pech_realisations
                            WHERE edition_id = :edition_id
                            ORDER BY est_laureat DESC, ordre_affichage ASC, titre_projet ASC
                        ");
                        $stmt_realisations->execute(['edition_id' => $edition['id']]);
                        $realisations = $stmt_realisations->fetchAll();
                    } catch (PDOException $e) {
                        error_log("Erreur récupération réalisations: " . $e->getMessage());
                        $realisations = [];
                    }
                    ?>

                    <?php if (!empty($realisations)): ?>
                        <div class="realisations-grille">
                            <?php foreach ($realisations as $realisation): ?>
                                <article class="realisation-carte <?= $realisation['est_laureat'] ? 'realisation-laureat' : '' ?>">
                                    <?php if ($realisation['est_laureat']): ?>
                                        <div class="badge-laureat">🏆 Lauréat</div>
                                    <?php endif; ?>

                                    <div class="realisation-visuel-container">
                                        <?php if ($realisation['visuel_url']): ?>
                                            <img src="<?= SITE_URL . echapper($realisation['visuel_url']) ?>" alt="Visuel du projet <?= echapper($realisation['titre_projet']) ?>" class="realisation-visuel">
                                        <?php else: ?>
                                            <img src="<?= SITE_URL ?>/assets/images/projet-placeholder.png" alt="Visuel du projet <?= echapper($realisation['titre_projet']) ?>" class="realisation-visuel">
                                        <?php endif; ?>
                                    </div>

                                    <div class="realisation-contenu">
                                        <h4 class="realisation-titre"><?= echapper($realisation['titre_projet']) ?></h4>

                                        <?php if ($realisation['secteur']): ?>
                                            <p class="realisation-secteur">📍 <?= echapper($realisation['secteur']) ?></p>
                                        <?php endif; ?>

                                        <?php if ($realisation['description']): ?>
                                            <p class="realisation-description"><?= echapper($realisation['description']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="realisations-vide">
                            <p>Les projets de cette édition seront annoncés prochainement.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <section class="pech-vide">
            <div class="container">
                <p>Les informations sur les éditions du PECH seront bientôt disponibles.</p>
            </div>
        </section>
    <?php endif; ?>

    <!-- Section CTA final -->
    <section class="cta-final">
        <div class="container">
            <h2 class="cta-titre">Participez à la prochaine édition du PECH</h2>
            <a href="<?= SITE_URL ?>/postuler" class="btn btn-primaire">Postuler maintenant</a>
        </div>
    </section>
</main>

<?php
// Inclusion du footer
require_once __DIR__ . '/../includes/footer.php';
?>
