<?php
require_once __DIR__ . '/../config/config.php';

// Définition des variables pour le header
$titre_page = "Actualités | " . SITE_NOM;
$description_page = "Les événements et actualités de " . SITE_NOM . ".";

// Inclusion du header
require_once __DIR__ . '/../includes/header.php';

// Récupération de tous les événements
try {
    $stmt = $pdo->query("SELECT * FROM evenements ORDER BY date_evenement ASC");
    $evenements = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erreur récupération événements: " . $e->getMessage());
    $evenements = [];
}

// Séparer les événements à venir et passés
$date_aujourdhui = date('Y-m-d');
$evenements_a_venir = [];
$evenements_passes = [];

foreach ($evenements as $evenement) {
    if ($evenement['date_evenement'] && $evenement['date_evenement'] >= $date_aujourdhui) {
        $evenements_a_venir[] = $evenement;
    } else {
        $evenements_passes[] = $evenement;
    }
}
?>

<main>
    <!-- Section Hero Actualités -->
    <section class="hero hero-page">
        <div class="container">
            <div class="hero-contenu">
                <h1 class="hero-titre">Actualités</h1>
                <p class="hero-soustitre">Événements et rendez-vous de l'incubateur</p>
            </div>
        </div>
    </section>

    <!-- Section Événements à venir -->
    <section class="evenements-section">
        <div class="container">
            <h2 class="section-titre">Événements à venir</h2>

            <?php if (!empty($evenements_a_venir)): ?>
                <div class="evenements-grille">
                    <?php foreach ($evenements_a_venir as $evenement): ?>
                        <article class="evenement-carte">
                            <div class="evenement-header">
                                <h3 class="evenement-titre"><?= echapper($evenement['titre']) ?></h3>

                                <?php
                                $lieu_class = '';
                                $lieu_icone = '';
                                $lieu_label = '';

                                switch ($evenement['lieu']) {
                                    case 'calavi':
                                        $lieu_class = 'lieu-calavi';
                                        $lieu_icone = '📍';
                                        $lieu_label = 'Calavi';
                                        break;
                                    case 'cotonou':
                                        $lieu_class = 'lieu-cotonou';
                                        $lieu_icone = '📍';
                                        $lieu_label = 'Cotonou';
                                        break;
                                    case 'autre':
                                        $lieu_class = 'lieu-autre';
                                        $lieu_icone = '📍';
                                        $lieu_label = 'Autre lieu';
                                        break;
                                }
                                ?>

                                <span class="evenement-lieu <?= $lieu_class ?>">
                                    <?= $lieu_icone ?> <?= $lieu_label ?>
                                </span>
                            </div>

                            <?php if ($evenement['date_evenement']): ?>
                                <p class="evenement-date">📅 <?= formaterDateFr($evenement['date_evenement']) ?></p>
                            <?php endif; ?>

                            <?php if ($evenement['description']): ?>
                                <p class="evenement-description"><?= echapper($evenement['description']) ?></p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="evenements-vide">
                    <p>Aucun événement à venir pour le moment. Revenez bientôt !</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Section Événements passés -->
    <?php if (!empty($evenements_passes)): ?>
        <section class="evenements-section evenements-section-passes">
            <div class="container">
                <h2 class="section-titre">Événements passés</h2>

                <div class="evenements-grille">
                    <?php foreach ($evenements_passes as $evenement): ?>
                        <article class="evenement-carte evenement-carte-passe">
                            <div class="evenement-header">
                                <h3 class="evenement-titre"><?= echapper($evenement['titre']) ?></h3>

                                <?php
                                $lieu_class = '';
                                $lieu_icone = '';
                                $lieu_label = '';

                                switch ($evenement['lieu']) {
                                    case 'calavi':
                                        $lieu_class = 'lieu-calavi';
                                        $lieu_icone = '📍';
                                        $lieu_label = 'Calavi';
                                        break;
                                    case 'cotonou':
                                        $lieu_class = 'lieu-cotonou';
                                        $lieu_icone = '📍';
                                        $lieu_label = 'Cotonou';
                                        break;
                                    case 'autre':
                                        $lieu_class = 'lieu-autre';
                                        $lieu_icone = '📍';
                                        $lieu_label = 'Autre lieu';
                                        break;
                                }
                                ?>

                                <span class="evenement-lieu <?= $lieu_class ?>">
                                    <?= $lieu_icone ?> <?= $lieu_label ?>
                                </span>
                            </div>

                            <?php if ($evenement['date_evenement']): ?>
                                <p class="evenement-date">📅 <?= formaterDateFr($evenement['date_evenement']) ?></p>
                            <?php endif; ?>

                            <?php if ($evenement['description']): ?>
                                <p class="evenement-description"><?= echapper($evenement['description']) ?></p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Section CTA final -->
    <section class="cta-final">
        <div class="container">
            <h2 class="cta-titre">Rejoignez l'incubateur</h2>
            <a href="<?= SITE_URL ?>/postuler" class="btn btn-primaire">Postuler maintenant</a>
        </div>
    </section>
</main>

<?php
// Inclusion du footer
require_once __DIR__ . '/../includes/footer.php';
?>
