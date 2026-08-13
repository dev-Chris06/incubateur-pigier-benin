<?php
require_once __DIR__ . '/../config/config.php';

// Rediriger vers le dashboard si déjà connecté
if (estConnecte()) {
    rediriger(SITE_URL . '/admin/dashboard.php');
}

$message_erreur = '';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !verifierJetonCsrf()) { http_response_code(403); exit('Jeton de sécurité invalide.'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (empty($email) || empty($mot_de_passe)) {
        $message_erreur = 'Veuillez remplir tous les champs.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, mot_de_passe_hash FROM admins WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($mot_de_passe, $admin['mot_de_passe_hash'])) {
                // Connexion réussie
                $_SESSION['admin_id'] = $admin['id'];
                rediriger(SITE_URL . '/admin/dashboard.php');
            } else {
                // Identifiants incorrects (message générique pour la sécurité)
                $message_erreur = 'Identifiants incorrects.';
            }
        } catch (PDOException $e) {
            error_log("Erreur connexion admin: " . $e->getMessage());
            $message_erreur = 'Une erreur est survenue. Veuillez réessayer.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Administration <?= SITE_NOM ?></title>
    <link rel="icon" href="<?= SITE_URL ?>/assets/images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Administration</h1>
                <p><?= SITE_NOM ?></p>
            </div>

            <?php if ($message_erreur): ?>
                <div class="alert alert-erreur">
                    <?= echapper($message_erreur) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="login-form">
                <?= champCsrf() ?>
                <div class="form-groupe">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        required
                        autofocus
                    >
                </div>

                <div class="form-groupe">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input
                        type="password"
                        id="mot_de_passe"
                        name="mot_de_passe"
                        class="form-input"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primaire btn-block">Se connecter</button>
            </form>

            <div class="login-footer">
                <a href="<?= SITE_URL ?>/public/index.php">← Retour au site</a>
            </div>
        </div>
    </div>
</body>
</html>
