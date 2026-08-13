<?php
require_once __DIR__ . '/../config/config.php';

// Définition des variables pour le header
$titre_page = "Postuler - " . SITE_NOM;
$description_page = "Rejoignez " . SITE_NOM . " et faites financer votre projet entrepreneurial.";

// Inclusion du header
require_once __DIR__ . '/../includes/header.php';

// Variables pour les messages
$message_succes = '';
$message_erreur = '';

// Récupération des informations sur les candidatures
try {
    $stmt = $pdo->query("SELECT * FROM candidature_infos LIMIT 1");
    $candidature_info = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Erreur récupération candidature_infos: " . $e->getMessage());
    $candidature_info = null;
}

$candidatures_ouvertes = $candidature_info && $candidature_info['candidatures_ouvertes'];

// Traitement du formulaire (seulement si les candidatures sont ouvertes)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!verifierJetonCsrf() || !empty($_POST['website'] ?? ''))) {
    http_response_code(403);
    exit('Requête invalide.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !limiterSoumission(basename(__FILE__), 5, 3600)) {
    http_response_code(429);
    exit('Trop de tentatives. Réessayez plus tard.');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $candidatures_ouvertes) {
    $nom_etudiant = trim($_POST['nom_etudiant'] ?? '');
    $filiere = trim($_POST['filiere'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $nom_projet = trim($_POST['nom_projet'] ?? '');
    $description_projet = trim($_POST['description_projet'] ?? '');

    // Validation côté serveur
    if (empty($nom_etudiant)) {
        $message_erreur = 'Le nom complet est obligatoire.';
    } elseif (empty($email)) {
        $message_erreur = 'L\'email est obligatoire.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message_erreur = 'L\'email n\'est pas valide.';
    } elseif (empty($description_projet)) {
        $message_erreur = 'La description du projet est obligatoire.';
    } else {
        // Insertion dans la base de données avec requête préparée
        try {
            $stmt = $pdo->prepare("INSERT INTO candidatures (nom_etudiant, filiere, email, telephone, nom_projet, description_projet) VALUES (:nom_etudiant, :filiere, :email, :telephone, :nom_projet, :description_projet)");
            $stmt->execute([
                'nom_etudiant' => $nom_etudiant,
                'filiere' => $filiere,
                'email' => $email,
                'telephone' => $telephone,
                'nom_projet' => $nom_projet,
                'description_projet' => $description_projet
            ]);
            $message_succes = 'Votre candidature a bien été reçue, l\'équipe de l\'incubateur vous recontactera.';

            // Réinitialiser les variables pour vider le formulaire
            $nom_etudiant = '';
            $filiere = '';
            $email = '';
            $telephone = '';
            $nom_projet = '';
            $description_projet = '';
        } catch (PDOException $e) {
            error_log("Erreur insertion candidature: " . $e->getMessage());
            $message_erreur = 'Une erreur est survenue lors de l\'envoi de votre candidature. Veuillez réessayer plus tard.';
        }
    }
}
?>

<main>
    <!-- Section Hero Postuler -->
    <section class="hero hero-page">
        <div class="container">
            <div class="hero-contenu">
                <h1 class="hero-titre">Postuler</h1>
                <p class="hero-soustitre">Rejoignez l'incubateur et transformez votre idée en entreprise</p>
            </div>
        </div>
    </section>

    <!-- Section Formulaire ou Message -->
    <section class="postuler-section">
        <div class="container">
            <?php if ($candidatures_ouvertes): ?>
                <!-- Candidatures ouvertes : afficher le formulaire -->
                <div class="postuler-info">
                    <h2 class="section-titre">Candidatures ouvertes</h2>

                    <?php if ($candidature_info['edition_ouverte']): ?>
                        <p class="postuler-edition">📌 Édition en cours : <strong><?= echapper($candidature_info['edition_ouverte']) ?></strong></p>
                    <?php endif; ?>

                    <?php if ($candidature_info['date_ouverture'] && $candidature_info['date_cloture']): ?>
                        <p class="postuler-dates">
                            📅 Période de candidature : du <?= formaterDateFr($candidature_info['date_ouverture']) ?> au <?= formaterDateFr($candidature_info['date_cloture']) ?>
                        </p>
                    <?php endif; ?>

                    <?php if ($candidature_info['criteres']): ?>
                        <div class="postuler-criteres">
                            <h3>Critères d'éligibilité :</h3>
                            <p><?= nl2br(echapper($candidature_info['criteres'])) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($message_succes): ?>
                    <div class="alert alert-succes">
                        <?= echapper($message_succes) ?>
                    </div>
                <?php endif; ?>

                    <?= champCsrf() ?>
                    <div style="position:absolute;left:-9999px" aria-hidden="true"><label>Site web<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
                <?php if ($message_erreur): ?>
                    <div class="alert alert-erreur">
                        <?= echapper($message_erreur) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="formulaire">
                    <div class="formulaire-groupe">
                        <label for="nom_etudiant" class="formulaire-label">Nom complet <span class="requis">*</span></label>
                        <input
                            type="text"
                            id="nom_etudiant"
                            name="nom_etudiant"
                            class="formulaire-input"
                            value="<?= isset($nom_etudiant) ? echapper($nom_etudiant) : '' ?>"
                            required
                        >
                    </div>

                    <div class="formulaire-groupe">
                        <label for="filiere" class="formulaire-label">Filière</label>
                        <input
                            type="text"
                            id="filiere"
                            name="filiere"
                            class="formulaire-input"
                            value="<?= isset($filiere) ? echapper($filiere) : '' ?>"
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
                        <label for="telephone" class="formulaire-label">Téléphone</label>
                        <input
                            type="tel"
                            id="telephone"
                            name="telephone"
                            class="formulaire-input"
                            value="<?= isset($telephone) ? echapper($telephone) : '' ?>"
                        >
                    </div>

                    <div class="formulaire-groupe">
                        <label for="nom_projet" class="formulaire-label">Nom du projet</label>
                        <input
                            type="text"
                            id="nom_projet"
                            name="nom_projet"
                            class="formulaire-input"
                            value="<?= isset($nom_projet) ? echapper($nom_projet) : '' ?>"
                        >
                    </div>

                    <div class="formulaire-groupe">
                        <label for="description_projet" class="formulaire-label">Description du projet <span class="requis">*</span></label>
                        <textarea
                            id="description_projet"
                            name="description_projet"
                            class="formulaire-textarea"
                            rows="8"
                            required
                        ><?= isset($description_projet) ? echapper($description_projet) : '' ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primaire">Envoyer ma candidature</button>
                </form>

            <?php else: ?>
                <!-- Candidatures fermées : message d'attente -->
                <div class="postuler-ferme">
                    <h2 class="section-titre">Candidatures actuellement fermées</h2>
                    <p class="postuler-message">Les candidatures ne sont pas encore ouvertes pour l'édition à venir. Revenez bientôt !</p>

                    <?php if ($candidature_info && $candidature_info['criteres']): ?>
                        <div class="postuler-criteres">
                            <h3>Critères d'éligibilité (pour information) :</h3>
                            <p><?= nl2br(echapper($candidature_info['criteres'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="postuler-contact">
                        <p>Pour plus d'informations, n'hésitez pas à nous contacter :</p>
                        <a href="<?= SITE_URL ?>/public/contact.php" class="btn btn-secondaire">Nous contacter</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
// Inclusion du footer
require_once __DIR__ . '/../includes/footer.php';
?>
