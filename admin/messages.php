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

// Marquer un message comme lu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id']) && isset($_POST['action'])) {
    try {
        if ($_POST['action'] === 'marquer_lu') {
            $stmt = $pdo->prepare("UPDATE messages_contact SET lu = TRUE WHERE id = :id");
            $stmt->execute(['id' => $_POST['message_id']]);
            $message_succes = "Message marqué comme lu.";
        } elseif ($_POST['action'] === 'marquer_non_lu') {
            $stmt = $pdo->prepare("UPDATE messages_contact SET lu = FALSE WHERE id = :id");
            $stmt->execute(['id' => $_POST['message_id']]);
            $message_succes = "Message marqué comme non lu.";
        }
    } catch (PDOException $e) {
        error_log("Erreur mise à jour message: " . $e->getMessage());
        $message_erreur = "Erreur lors de la mise à jour du message.";
    }
}

// Pagination des messages
$page_courante = max(1, (int) ($_GET['page'] ?? 1));
$par_page = 15;
$pagination = paginer($pdo, 'messages_contact', $page_courante, $par_page, 'date_envoi DESC');
$messages = $pagination['donnees'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Administration</title>
    <link rel="icon" href="<?= SITE_URL ?>/assets/images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
    <style>
        .message-non-lu {
            background: #e3f2fd !important;
            border-left: 4px solid #2196f3;
            font-weight: 600;
        }
    </style>
</head>
<body class="admin-page">
    <?php include __DIR__ . '/nav.php'; ?>

    <main class="admin-main">
        <div class="admin-container">
            <h1 class="admin-titre">Messages de contact</h1>

            <?php if ($message_succes): ?>
                <div class="alert alert-succes"><?= echapper($message_succes) ?></div>
            <?php endif; ?>

            <?php if ($message_erreur): ?>
                <div class="alert alert-erreur"><?= echapper($message_erreur) ?></div>
            <?php endif; ?>

            <?php if (empty($messages)): ?>
                <div class="dashboard-section">
                    <p class="texte-vide">Aucun message reçu pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="messages-liste">
                    <?php foreach ($messages as $message): ?>
                        <div class="message-carte <?= !$message['lu'] ? 'message-non-lu' : '' ?>">
                            <div class="message-header">
                                <div class="message-info-principale">
                                    <h3><?= echapper($message['nom']) ?></h3>
                                    <p class="message-date">📅 <?= date('d/m/Y à H:i', strtotime($message['date_envoi'])) ?></p>
                                </div>
                                <form method="POST" class="message-action-form">
                                    <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                                    <?= champCsrf() ?>
                                    <?php if ($message['lu']): ?>
                                        <button type="submit" name="action" value="marquer_non_lu" class="btn-action btn-secondary">Marquer non lu</button>
                                    <?php else: ?>
                                        <button type="submit" name="action" value="marquer_lu" class="btn-action btn-primary">Marquer lu</button>
                                    <?php endif; ?>
                                </form>
                            </div>

                            <div class="message-details">
                                <div class="detail-item">
                                    <strong>Email :</strong>
                                    <span><a href="mailto:<?= echapper($message['email']) ?>"><?= echapper($message['email']) ?></a></span>
                                </div>

                                <?php if ($message['sujet']): ?>
                                    <div class="detail-item">
                                        <strong>Sujet :</strong>
                                        <span><?= echapper($message['sujet']) ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="detail-item detail-full">
                                    <strong>Message :</strong>
                                    <p class="message-contenu"><?= nl2br(echapper($message['message'])) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($pagination['pages'] > 1): ?>
                    <p class="pagination-info">
                        Affichage de <?= count($messages) ?> message(s) sur <?= $pagination['total'] ?>
                    </p>
                    <?= afficherPagination($pagination, '?') ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    <script src="<?= SITE_URL ?>/assets/js/admin.js"></script>
</body>
</html>
