<?php
require_once __DIR__ . '/../config/config.php';

// Vérification obligatoire de l'authentification
verifierAuthentification();

$message_succes = '';
$message_erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !verifierJetonCsrf()) {
    http_response_code(403);
    exit('Jeton de sécurité invalide.');
}

// Changement de statut d'une candidature
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidature_id']) && isset($_POST['nouveau_statut'])) {
    try {
        $stmt = $pdo->prepare("UPDATE candidatures SET statut = :statut WHERE id = :id");
        $stmt->execute([
            'statut' => $_POST['nouveau_statut'],
            'id' => $_POST['candidature_id']
        ]);
        $message_succes = "Statut mis à jour avec succès.";
    } catch (PDOException $e) {
        error_log("Erreur mise à jour statut candidature: " . $e->getMessage());
        $message_erreur = "Erreur lors de la mise à jour du statut.";
    }
}

// Pagination des candidatures
$page_courante = max(1, (int) ($_GET['page'] ?? 1));
$par_page = 15;
$pagination = paginer($pdo, 'candidatures', $page_courante, $par_page, 'date_soumission DESC');
$candidatures = $pagination['donnees'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatures - Administration</title>
    <link rel="icon" href="<?= SITE_URL ?>/assets/images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
    <style>
        .candidature-nouvelle {
            background: #fff3cd !important;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body class="admin-page">
    <?php include __DIR__ . '/nav.php'; ?>

    <main class="admin-main">
        <div class="admin-container">
            <h1 class="admin-titre">Candidatures reçues</h1>

            <?php if ($message_succes): ?>
                <div class="alert alert-succes"><?= echapper($message_succes) ?></div>
            <?php endif; ?>

            <?php if ($message_erreur): ?>
                <div class="alert alert-erreur"><?= echapper($message_erreur) ?></div>
            <?php endif; ?>

            <?php if (empty($candidatures)): ?>
                <div class="dashboard-section">
                    <p class="texte-vide">Aucune candidature reçue pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="candidatures-liste">
                    <?php foreach ($candidatures as $candidature): ?>
                        <div class="candidature-carte <?= $candidature['statut'] === 'nouvelle' ? 'candidature-nouvelle' : '' ?>">
                            <div class="candidature-header">
                                <div class="candidature-info-principale">
                                    <h3><?= echapper($candidature['nom_etudiant']) ?></h3>
                                    <p class="candidature-date">📅 <?= date('d/m/Y à H:i', strtotime($candidature['date_soumission'])) ?></p>
                                </div>
                                <form method="POST" class="candidature-statut-form" onchange="this.submit()">
                                    <input type="hidden" name="candidature_id" value="<?= $candidature['id'] ?>">
                                    <?= champCsrf() ?>
                                    <select name="nouveau_statut" class="statut-select statut-<?= $candidature['statut'] ?>">
                                        <option value="nouvelle" <?= $candidature['statut'] === 'nouvelle' ? 'selected' : '' ?>>🔴 Nouvelle</option>
                                        <option value="vue" <?= $candidature['statut'] === 'vue' ? 'selected' : '' ?>>🟡 Vue</option>
                                        <option value="traitee" <?= $candidature['statut'] === 'traitee' ? 'selected' : '' ?>>🟢 Traitée</option>
                                    </select>
                                    <button type="submit" class="btn-action btn-primary">Mettre à jour</button>
                                </form>
                            </div>

                            <div class="candidature-details">
                                <div class="detail-item">
                                    <strong>Filière :</strong>
                                    <span><?= $candidature['filiere'] ? echapper($candidature['filiere']) : 'Non renseignée' ?></span>
                                </div>

                                <div class="detail-item">
                                    <strong>Email :</strong>
                                    <span><a href="mailto:<?= echapper($candidature['email']) ?>"><?= echapper($candidature['email']) ?></a></span>
                                </div>

                                <div class="detail-item">
                                    <strong>Téléphone :</strong>
                                    <span><?= $candidature['telephone'] ? echapper($candidature['telephone']) : 'Non renseigné' ?></span>
                                </div>

                                <?php if ($candidature['nom_projet']): ?>
                                    <div class="detail-item">
                                        <strong>Nom du projet :</strong>
                                        <span><?= echapper($candidature['nom_projet']) ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="detail-item detail-full">
                                    <strong>Description du projet :</strong>
                                    <p class="description-projet"><?= nl2br(echapper($candidature['description_projet'])) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($pagination['pages'] > 1): ?>
                    <p class="pagination-info">
                        Affichage de <?= count($candidatures) ?> candidature(s) sur <?= $pagination['total'] ?>
                    </p>
                    <?= afficherPagination($pagination, '?') ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    <script src="<?= SITE_URL ?>/assets/js/admin.js"></script>
</body>
</html>
