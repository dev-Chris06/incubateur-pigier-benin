<?php
require_once __DIR__ . '/../config/config.php';

// Vérification obligatoire de l'authentification
verifierAuthentification();

// Récupération des statistiques du dashboard
try {
    // Nombre de nouvelles candidatures
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM candidatures WHERE statut = 'nouvelle'");
    $nb_nouvelles_candidatures = $stmt->fetch()['total'];

    // Nombre de messages non lus
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM messages_contact WHERE lu = FALSE");
    $nb_messages_non_lus = $stmt->fetch()['total'];

    // Les 3 prochains événements à venir
    $stmt = $pdo->query("SELECT * FROM evenements WHERE date_evenement >= CURDATE() ORDER BY date_evenement ASC LIMIT 3");
    $prochains_evenements = $stmt->fetchAll();

    // Statistiques globales
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM candidatures");
    $nb_total_candidatures = $stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM personnes WHERE actif = TRUE");
    $nb_personnes_actives = $stmt->fetch()['total'];

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pech_editions");
    $nb_editions_pech = $stmt->fetch()['total'];

} catch (PDOException $e) {
    error_log("Erreur récupération stats dashboard: " . $e->getMessage());
    $nb_nouvelles_candidatures = 0;
    $nb_messages_non_lus = 0;
    $prochains_evenements = [];
    $nb_total_candidatures = 0;
    $nb_personnes_actives = 0;
    $nb_editions_pech = 0;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administration <?= SITE_NOM ?></title>
    <link rel="icon" href="<?= SITE_URL ?>/assets/images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-page">
    <?php include __DIR__ . '/nav.php'; ?>

    <main class="admin-main">
        <div class="admin-container">
            <h1 class="admin-titre">Dashboard</h1>

            <!-- Statistiques principales -->
            <div class="stats-grille">
                <div class="stat-carte stat-carte-urgent">
                    <div class="stat-icone">📬</div>
                    <div class="stat-contenu">
                        <div class="stat-nombre"><?= $nb_nouvelles_candidatures ?></div>
                        <div class="stat-label">Nouvelle<?= $nb_nouvelles_candidatures > 1 ? 's' : '' ?> candidature<?= $nb_nouvelles_candidatures > 1 ? 's' : '' ?></div>
                        <a href="<?= SITE_URL ?>/admin/candidatures.php" class="stat-lien">Voir les candidatures →</a>
                    </div>
                </div>

                <div class="stat-carte stat-carte-urgent">
                    <div class="stat-icone">✉️</div>
                    <div class="stat-contenu">
                        <div class="stat-nombre"><?= $nb_messages_non_lus ?></div>
                        <div class="stat-label">Message<?= $nb_messages_non_lus > 1 ? 's' : '' ?> non lu<?= $nb_messages_non_lus > 1 ? 's' : '' ?></div>
                        <a href="<?= SITE_URL ?>/admin/messages.php" class="stat-lien">Voir les messages →</a>
                    </div>
                </div>

                <div class="stat-carte">
                    <div class="stat-icone">👥</div>
                    <div class="stat-contenu">
                        <div class="stat-nombre"><?= $nb_personnes_actives ?></div>
                        <div class="stat-label">Personne<?= $nb_personnes_actives > 1 ? 's' : '' ?> active<?= $nb_personnes_actives > 1 ? 's' : '' ?></div>
                        <a href="<?= SITE_URL ?>/admin/gerer.php?table=personnes" class="stat-lien">Gérer →</a>
                    </div>
                </div>

                <div class="stat-carte">
                    <div class="stat-icone">🏆</div>
                    <div class="stat-contenu">
                        <div class="stat-nombre"><?= $nb_editions_pech ?></div>
                        <div class="stat-label">Édition<?= $nb_editions_pech > 1 ? 's' : '' ?> PECH</div>
                        <a href="<?= SITE_URL ?>/admin/gerer.php?table=pech_editions" class="stat-lien">Gérer →</a>
                    </div>
                </div>
            </div>

            <!-- Prochains événements -->
            <div class="dashboard-section">
                <h2 class="section-titre-admin">Prochains événements</h2>
                <?php if (!empty($prochains_evenements)): ?>
                    <div class="evenements-liste">
                        <?php foreach ($prochains_evenements as $evt): ?>
                            <div class="evenement-item">
                                <div class="evenement-date-badge">
                                    <?= $evt['date_evenement'] ? date('d/m/Y', strtotime($evt['date_evenement'])) : 'N/A' ?>
                                </div>
                                <div class="evenement-details">
                                    <h3><?= echapper($evt['titre']) ?></h3>
                                    <p><?= echapper($evt['lieu']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?= SITE_URL ?>/admin/gerer.php?table=evenements" class="btn btn-secondaire">Gérer tous les événements</a>
                <?php else: ?>
                    <p class="texte-vide">Aucun événement à venir pour le moment.</p>
                    <a href="<?= SITE_URL ?>/admin/gerer.php?table=evenements" class="btn btn-primaire">Créer un événement</a>
                <?php endif; ?>
            </div>

            <!-- Liens rapides -->
            <div class="dashboard-section">
                <h2 class="section-titre-admin">Gestion du contenu</h2>
                <div class="liens-rapides">
                    <a href="<?= SITE_URL ?>/admin/gerer.php?table=personnes" class="lien-rapide">
                        <span class="lien-icone">👥</span>
                        <span class="lien-label">Personnes (équipe)</span>
                    </a>
                    <a href="<?= SITE_URL ?>/admin/gerer.php?table=roles" class="lien-rapide">
                        <span class="lien-icone">🏷️</span>
                        <span class="lien-label">Rôles</span>
                    </a>
                    <a href="<?= SITE_URL ?>/admin/gerer.php?table=pech_editions" class="lien-rapide">
                        <span class="lien-icone">🏆</span>
                        <span class="lien-label">Éditions PECH</span>
                    </a>
                    <a href="<?= SITE_URL ?>/admin/gerer.php?table=pech_realisations" class="lien-rapide">
                        <span class="lien-icone">💡</span>
                        <span class="lien-label">Projets PECH</span>
                    </a>
                    <a href="<?= SITE_URL ?>/admin/gerer.php?table=evenements" class="lien-rapide">
                        <span class="lien-icone">📅</span>
                        <span class="lien-label">Événements</span>
                    </a>
                    <a href="<?= SITE_URL ?>/admin/gerer.php?table=contenu_statique" class="lien-rapide">
                        <span class="lien-icone">📝</span>
                        <span class="lien-label">Contenu du site</span>
                    </a>
                    <a href="<?= SITE_URL ?>/admin/gerer.php?table=candidature_infos" class="lien-rapide">
                        <span class="lien-icone">📨</span>
                        <span class="lien-label">Ouverture candidatures</span>
                    </a>
                </div>
            </div>
        </div>
    </main>
    <script src="<?= SITE_URL ?>/assets/js/admin.js"></script>
</body>
</html>
