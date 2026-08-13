<?php
require_once __DIR__ . '/../config/config.php';

// Définition des variables pour le header
$titre_page = "Contact | " . SITE_NOM;
$description_page = "Contactez " . SITE_NOM . " sur les sites de Cotonou et Abomey-Calavi.";

// Inclusion du header
require_once __DIR__ . '/../includes/header.php';

// Variables pour les messages
$message_succes = '';
$message_erreur = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier le champ honeypot (anti-spam)
    if (!empty($_POST['website'] ?? '')) {
        http_response_code(403);
        exit('Requête invalide.');
    }
    
    // Vérifier le jeton CSRF
    if (!verifierJetonCsrf()) {
        http_response_code(403);
        exit('Requête invalide (CSRF).');
    }
    
    // Vérifier le rate limiting
    if (!limiterSoumission(basename(__FILE__), 5, 3600)) {
        http_response_code(429);
        exit('Trop de tentatives. Réessayez plus tard.');
    }
    
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sujet = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validation côté serveur
    if (empty($nom)) {
        $message_erreur = 'Le nom est obligatoire.';
    } elseif (empty($email)) {
        $message_erreur = 'L\'email est obligatoire.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_erreur = 'L\'email n\'est pas valide.';
    } elseif (empty($message)) {
        $message_erreur = 'Le message est obligatoire.';
    } else {
        // Insertion dans la base de données avec requête préparée
        try {
            $stmt = $pdo->prepare("INSERT INTO messages_contact (nom, email, sujet, message) VALUES (:nom, :email, :sujet, :message)");
            $stmt->execute([
                'nom' => $nom,
                'email' => $email,
                'sujet' => $sujet,
                'message' => $message
            ]);
            $message_succes = 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.';

            // Réinitialiser les variables pour vider le formulaire
            $nom = '';
            $email = '';
            $sujet = '';
            $message = '';
        } catch (PDOException $e) {
            error_log("Erreur insertion message contact: " . $e->getMessage());
            $message_erreur = 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer plus tard.';
        }
    }
}

// Récupération des contenus dynamiques
$adresse_cotonou = getContenu($pdo, 'global', 'adresse_cotonou');
$adresse_calavi = getContenu($pdo, 'global', 'adresse_calavi');
$telephone_fixe = getContenu($pdo, 'global', 'telephone_fixe');
$telephone_mobile = getContenu($pdo, 'global', 'telephone_mobile');
$email_contact = getContenu($pdo, 'global', 'email_contact');
$horaires = getContenu($pdo, 'global', 'horaires');
?>

<main>
    <!-- Section Hero Contact -->
    <section class="hero hero-page">
        <div class="container">
            <div class="hero-contenu">
                <h1 class="hero-titre">Nous Contacter</h1>
                <p class="hero-soustitre">Retrouvez nos coordonnées et envoyez-nous un message</p>
            </div>
        </div>
    </section>

    <!-- Section Coordonnées -->
    <section class="coordonnees">
        <div class="container">
            <div class="coordonnees-grille">
                <!-- PIGIER-BENIN -->
                <article class="coordonnees-bloc">
                    <h2 class="coordonnees-titre">PIGIER-BENIN</h2>
                    <div class="coordonnees-item">
                        <strong>📍 Adresses</strong>
                        <p>
                            <?= $adresse_cotonou ? echapper($adresse_cotonou) : 'Non renseignée' ?><br>
                            <?= $adresse_calavi ? echapper($adresse_calavi) : 'Non renseignée' ?>
                        </p>
                    </div>
                    <div class="coordonnees-item">
                        <strong>📞 Téléphone fixe</strong>
                        <p><?= $telephone_fixe ? echapper($telephone_fixe) : 'Non renseigné' ?></p>
                    </div>
                    <div class="coordonnees-item">
                        <strong>📱 Mobile / WhatsApp</strong>
                        <p><?= $telephone_mobile ? echapper($telephone_mobile) : 'Non renseigné' ?></p>
                    </div>
                    <div class="coordonnees-item">
                        <strong>✉️ Email</strong>
                        <p>
                            <?php if ($email_contact): ?>
                                <a href="mailto:<?= echapper($email_contact) ?>"><?= echapper($email_contact) ?></a>
                            <?php else: ?>
                                Non renseigné
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="coordonnees-item">
                        <strong>🕒 Horaires</strong>
                        <p><?= $horaires ? echapper($horaires) : 'Non renseignés' ?></p>
                    </div>
                </article>

                <!-- Formulaire de contact -->
                <article class="coordonnees-bloc">
                    <h2 class="coordonnees-titre">Envoyez-nous un message</h2>

                    <?php if ($message_succes): ?>
                        <div class="alert alert-succes">
                            <?= echapper($message_succes) ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($message_erreur): ?>
                        <div class="alert alert-erreur">
                            <?= echapper($message_erreur) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="formulaire">
                        <?= champCsrf() ?>
                        <div style="position: absolute; left: -9999px;" aria-hidden="true">
                            <label>Ne pas remplir ce champ : <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>
                        
                        <div class="formulaire-groupe">
                            <label for="nom" class="formulaire-label">Nom complet <span class="requis">*</span></label>
                            <input
                                type="text"
                                id="nom"
                                name="nom"
                                class="formulaire-input"
                                value="<?= isset($nom) ? echapper($nom) : '' ?>"
                                required
                            >
                        </div>

                        <div class="formulaire-groupe">
                            <label for="email" class="formulaire-label">Email <span class="requis">*</span></label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="formulaire-input"
                                value="<?= isset($email) ? echapper($email) : '' ?>"
                                required
                            >
                        </div>

                        <div class="formulaire-groupe">
                            <label for="sujet" class="formulaire-label">Sujet</label>
                            <input
                                type="text"
                                id="sujet"
                                name="sujet"
                                class="formulaire-input"
                                value="<?= isset($sujet) ? echapper($sujet) : '' ?>"
                            >
                        </div>

                        <div class="formulaire-groupe">
                            <label for="message" class="formulaire-label">Message <span class="requis">*</span></label>
                            <textarea
                                id="message"
                                name="message"
                                class="formulaire-textarea"
                                rows="6"
                                required
                            ><?= isset($message) ? echapper($message) : '' ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primaire">Envoyer le message</button>
                    </form>
                </article>
            </div>
        </div>
    </section>
</main>

<?php
// Inclusion du footer
require_once __DIR__ . '/../includes/footer.php';
?>
