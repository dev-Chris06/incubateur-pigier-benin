<?php
require_once __DIR__ . '/../config/config.php';

// Vérification obligatoire de l'authentification
verifierAuthentification();

// Configuration des tables gérables
$config_tables = [
    'personnes' => [
        'champs' => [
            'nom' => 'text',
            'bio' => 'textarea',
            'est_cadre_direction' => 'checkbox',
            'ordre_affichage' => 'number',
            'actif' => 'checkbox'
        ],
        'libelle' => 'Personnes (mentors, coachs, cadres)',
        'has_roles' => true
    ],
    'roles' => [
        'champs' => [
            'libelle' => 'text',
            'categorie' => ['direction', 'coordination', 'mentor', 'coach', 'expert']
        ],
        'libelle' => 'Rôles'
    ],
    'pech_editions' => [
        'champs' => [
            'numero_edition' => 'number',
            'annee_academique' => 'text',
            'date_debut' => 'date',
            'date_fin' => 'date',
            'nb_finalistes' => 'number',
            'nb_laureats' => 'number',
            'description' => 'textarea',
            'statut' => ['a_venir', 'en_cours', 'terminee']
        ],
        'libelle' => 'Éditions PECH'
    ],
    'pech_realisations' => [
        'champs' => [
            'edition_id' => 'select_editions',
            'titre_projet' => 'text',
            'secteur' => 'text',
            'description' => 'textarea',
            'visuel_url' => 'file',
            'est_laureat' => 'checkbox',
            'ordre_affichage' => 'number'
        ],
        'libelle' => 'Réalisations PECH'
    ],
    'evenements' => [
        'champs' => [
            'titre' => 'text',
            'description' => 'textarea',
            'date_evenement' => 'date',
            'lieu' => ['calavi', 'cotonou', 'autre']
        ],
        'libelle' => 'Événements'
    ],
    'contenu_statique' => [
        'champs' => [
            'page' => 'text',
            'cle' => 'text',
            'valeur' => 'textarea'
        ],
        'libelle' => 'Contenu du site'
    ],
    'candidature_infos' => [
        'champs' => [
            'edition_ouverte' => 'number',
            'date_ouverture' => 'date',
            'date_cloture' => 'date',
            'criteres' => 'textarea',
            'candidatures_ouvertes' => 'checkbox'
        ],
        'libelle' => 'Gestion des candidatures',
        'single_row' => true
    ]
];

$table = $_GET['table'] ?? '';
$action = $_GET['action'] ?? 'liste';
$id = $_GET['id'] ?? null;

// Vérifier que la table est autorisée
if (!isset($config_tables[$table])) {
    die("Table non autorisée");
}

$config = $config_tables[$table];
$message_succes = '';
$message_erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !verifierJetonCsrf()) {
    http_response_code(403);
    exit('Jeton de sécurité invalide.');
}
// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'ajouter' || $action === 'editer')) {
    $donnees = [];
    $anciens_fichiers = [];

    // Récupérer l'ancien enregistrement pour nettoyer les fichiers remplacés
    $ancien_element = null;
    if ($action === 'editer' && $id) {
        try {
            $stmt_ancien = $pdo->prepare("SELECT * FROM $table WHERE id = :id");
            $stmt_ancien->execute(['id' => $id]);
            $ancien_element = $stmt_ancien->fetch();
        } catch (PDOException $e) {
            error_log('Erreur récupération ancien élément: ' . $e->getMessage());
        }
    }

    foreach ($config['champs'] as $champ => $type) {
        if ($type === 'checkbox') {
            $donnees[$champ] = isset($_POST[$champ]) ? 1 : 0;
        } elseif ($type === 'file') {
            if (!isset($_FILES[$champ]) || $_FILES[$champ]['error'] === UPLOAD_ERR_NO_FILE) {
                if ($action === 'editer') {
                    continue;
                }
                continue;
            }
            $dossier = $table === 'personnes' ? 'personnes' : 'pech';
            $upload = uploaderImage($_FILES[$champ], $dossier);
            if (!$upload['success']) {
                $message_erreur = $upload['message'];
                break;
            }
            $donnees[$champ] = $upload['chemin'];
            // Marquer l'ancien fichier pour suppression après mise à jour réussie
            if ($action === 'editer' && $ancien_element && !empty($ancien_element[$champ])) {
                $anciens_fichiers[] = UPLOAD_DIR . $ancien_element[$champ];
            }
        } elseif ($type === 'number') {
            $donnees[$champ] = !empty($_POST[$champ]) ? (int) $_POST[$champ] : null;
        } else {
            $donnees[$champ] = $_POST[$champ] ?? '';
        }
    }

    if (!$message_erreur) {
        try {
            if ($action === 'ajouter') {
                $champs = implode(', ', array_keys($donnees));
                $placeholders = ':' . implode(', :', array_keys($donnees));
                $stmt = $pdo->prepare("INSERT INTO $table ($champs) VALUES ($placeholders)");
                $stmt->execute($donnees);
                $message_succes = 'Élément ajouté avec succès.';
                if ($table === 'personnes' && isset($_POST['roles'])) {
                    $personneId = $pdo->lastInsertId();
                    $stmtRole = $pdo->prepare('INSERT INTO personnes_roles (personne_id, role_id) VALUES (:personne_id, :role_id)');
                    foreach ($_POST['roles'] as $roleId) {
                        $stmtRole->execute(['personne_id' => $personneId, 'role_id' => $roleId]);
                    }
                }
            } else {
                $set = [];
                foreach (array_keys($donnees) as $champ) {
                    $set[] = "$champ = :$champ";
                }
                $donnees['id'] = $id;
                $stmt = $pdo->prepare("UPDATE $table SET " . implode(', ', $set) . ' WHERE id = :id');
                $stmt->execute($donnees);
                $message_succes = 'Élément modifié avec succès.';
                if ($table === 'personnes') {
                    $pdo->prepare('DELETE FROM personnes_roles WHERE personne_id = :personne_id')->execute(['personne_id' => $id]);
                    if (isset($_POST['roles'])) {
                        $stmtRole = $pdo->prepare('INSERT INTO personnes_roles (personne_id, role_id) VALUES (:personne_id, :role_id)');
                        foreach ($_POST['roles'] as $roleId) {
                            $stmtRole->execute(['personne_id' => $id, 'role_id' => $roleId]);
                        }
                    }
                }
            }
            // Pour les tables single_row, rester en mode édition
            if (empty($config['single_row'])) {
                $action = 'liste';
            }
            // Supprimer les anciens fichiers remplacés par de nouveaux uploads
            foreach ($anciens_fichiers as $ancien_chemin) {
                if (file_exists($ancien_chemin)) {
                    unlink($ancien_chemin);
                }
            }
        } catch (PDOException $e) {
            error_log('Erreur CRUD: ' . $e->getMessage());
            $message_erreur = 'Une erreur est survenue.';
        }
    }
}

// Suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'supprimer' && $id) {
    try {
        // Nettoyer les fichiers uploadés avant suppression en base
        $champs_fichiers = array_keys(array_filter($config['champs'], fn($type) => $type === 'file'));
        if (!empty($champs_fichiers)) {
            $stmt_fichier = $pdo->prepare("SELECT * FROM $table WHERE id = :id");
            $stmt_fichier->execute(['id' => $id]);
            $elem_a_supprimer = $stmt_fichier->fetch();
            if ($elem_a_supprimer) {
                foreach ($champs_fichiers as $champ_f) {
                    if (!empty($elem_a_supprimer[$champ_f])) {
                        $chemin_fichier = UPLOAD_DIR . $elem_a_supprimer[$champ_f];
                        if (file_exists($chemin_fichier)) {
                            unlink($chemin_fichier);
                            error_log("Fichier supprimé: $chemin_fichier");
                        }
                    }
                }
            }
        }

        $stmt = $pdo->prepare("DELETE FROM $table WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $message_succes = "Élément supprimé avec succès.";
        $action = 'liste';
    } catch (PDOException $e) {
        error_log("Erreur suppression: " . $e->getMessage());
        $message_erreur = "Impossible de supprimer cet élément.";
    }
}

// Gestion spéciale pour les tables à ligne unique (candidature_infos)
if (!empty($config['single_row'])) {
    // Récupérer ou créer la ligne unique
    try {
        $stmt = $pdo->query("SELECT * FROM $table LIMIT 1");
        $element = $stmt->fetch();
        if (!$element) {
            // Créer une ligne vide si elle n'existe pas
            $pdo->exec("INSERT INTO $table (candidatures_ouvertes) VALUES (0)");
            $stmt = $pdo->query("SELECT * FROM $table LIMIT 1");
            $element = $stmt->fetch();
        }
        $id = $element['id'];
        $action = 'editer'; // Forcer le mode édition
    } catch (PDOException $e) {
        error_log("Erreur récupération ligne unique: " . $e->getMessage());
    }
} else {
    // Récupération des données pour édition
    $element = null;
    $roles_personne = [];
    if ($action === 'editer' && $id) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $element = $stmt->fetch();

            // Récupérer les rôles si c'est une personne
            if ($table === 'personnes') {
                $stmt_roles = $pdo->prepare("SELECT role_id FROM personnes_roles WHERE personne_id = :personne_id");
                $stmt_roles->execute(['personne_id' => $id]);
                $roles_personne = $stmt_roles->fetchAll(PDO::FETCH_COLUMN);
            }
        } catch (PDOException $e) {
            error_log("Erreur récupération: " . $e->getMessage());
        }
    }

    // Liste des éléments avec pagination
    $elements = [];
    $pagination = [];
    if ($action === 'liste') {
        $page_courante = max(1, (int) ($_GET['page'] ?? 1));
        $par_page = 20;
        $pagination = paginer($pdo, $table, $page_courante, $par_page, 'id DESC');
        $elements = $pagination['donnees'];
    }
}

// Récupérer les éditions pour le select
$editions = [];
if (in_array('edition_id', array_keys($config['champs']))) {
    try {
        $stmt = $pdo->query("SELECT id, numero_edition, annee_academique FROM pech_editions ORDER BY numero_edition DESC");
        $editions = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur récupération éditions: " . $e->getMessage());
    }
}

// Récupérer tous les rôles pour le formulaire personnes
$tous_roles = [];
if ($table === 'personnes') {
    try {
        $stmt = $pdo->query("SELECT * FROM roles ORDER BY categorie, libelle");
        $tous_roles = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur récupération rôles: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer <?= echapper($config['libelle']) ?> - Administration</title>
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
            <h1 class="admin-titre">Gérer : <?= echapper($config['libelle']) ?></h1>

            <?php if ($message_succes): ?>
                <div class="alert alert-succes"><?= echapper($message_succes) ?></div>
            <?php endif; ?>

            <?php if ($message_erreur): ?>
                <div class="alert alert-erreur"><?= echapper($message_erreur) ?></div>
            <?php endif; ?>

            <?php if ($action === 'liste'): ?>
                <!-- Liste des éléments -->
                <div class="admin-actions">
                    <a href="?table=<?= $table ?>&action=ajouter" class="btn btn-primaire">+ Ajouter</a>
                </div>

                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <?php foreach (array_keys($config['champs']) as $champ): ?>
                                    <th><?= echapper(ucfirst(str_replace('_', ' ', $champ))) ?></th>
                                <?php endforeach; ?>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($elements as $elem): ?>
                                <tr>
                                    <td><?= echapper($elem['id']) ?></td>
                                    <?php foreach (array_keys($config['champs']) as $champ): ?>
                                        <td>
                                            <?php
                                            if ($config['champs'][$champ] === 'checkbox') {
                                                echo $elem[$champ] ? '✓' : '✗';
                                            } elseif ($config['champs'][$champ] === 'textarea') {
                                                echo echapper(substr($elem[$champ] ?? '', 0, 50)) . '...';
                                            } else {
                                                echo echapper($elem[$champ] ?? '');
                                            }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                    <td class="table-actions">
                                        <a href="?table=<?= $table ?>&action=editer&id=<?= $elem['id'] ?>" class="btn-action btn-edit">Modifier</a>
                                        <form method="POST" action="?table=<?= $table ?>&action=supprimer&id=<?= $elem['id'] ?>" style="display:inline">
                                            <?= champCsrf() ?>
                                            <button type="submit" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.')">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($pagination) && $pagination['pages'] > 1): ?>
                    <p class="pagination-info">
                        Affichage de <?= count($elements) ?> élément(s) sur <?= $pagination['total'] ?>
                    </p>
                    <?= afficherPagination($pagination, '?table=' . $table . '&') ?>
                <?php endif; ?>

            <?php elseif ($action === 'ajouter' || $action === 'editer'): ?>
                <!-- Formulaire d'ajout/édition -->
                <?php if (empty($config['single_row'])): ?>
                    <div class="admin-actions">
                        <a href="?table=<?= $table ?>" class="btn btn-secondaire">← Retour à la liste</a>
                    </div>
                <?php endif; ?>

                <div class="form-container">
                    <form method="POST" enctype="multipart/form-data" class="admin-form">
                        <?= champCsrf() ?>
                        <?php foreach ($config['champs'] as $champ => $type): ?>
                            <div class="form-groupe">
                                <label for="<?= $champ ?>"><?= echapper(ucfirst(str_replace('_', ' ', $champ))) ?></label>

                                <?php if ($type === 'text'): ?>
                                    <input type="text" id="<?= $champ ?>" name="<?= $champ ?>" class="form-input" value="<?= $element ? echapper($element[$champ]) : '' ?>">

                                <?php elseif ($type === 'number'): ?>
                                    <input type="number" id="<?= $champ ?>" name="<?= $champ ?>" class="form-input" value="<?= $element ? echapper($element[$champ]) : '' ?>">

                                <?php elseif ($type === 'date'): ?>
                                    <input type="date" id="<?= $champ ?>" name="<?= $champ ?>" class="form-input" value="<?= $element ? echapper($element[$champ]) : '' ?>">

                                <?php elseif ($type === 'textarea'): ?>
                                    <textarea id="<?= $champ ?>" name="<?= $champ ?>" class="form-textarea" rows="5"><?= $element ? echapper($element[$champ]) : '' ?></textarea>

                                <?php elseif ($type === 'checkbox'): ?>
                                    <input type="checkbox" id="<?= $champ ?>" name="<?= $champ ?>" value="1" <?= ($element && $element[$champ]) ? 'checked' : '' ?>>

                                <?php elseif ($type === 'file'): ?>
                                    <input type="file" id="<?= $champ ?>" name="<?= $champ ?>" class="form-input">
                                    <?php if ($element && !empty($element[$champ])): ?>
                                        <p class="info-fichier">Fichier actuel : <?= echapper($element[$champ]) ?></p>
                                    <?php endif; ?>

                                <?php elseif ($type === 'select_editions'): ?>
                                    <select id="<?= $champ ?>" name="<?= $champ ?>" class="form-input">
                                        <option value="">Sélectionner une édition</option>
                                        <?php foreach ($editions as $ed): ?>
                                            <option value="<?= $ed['id'] ?>" <?= ($element && $element[$champ] == $ed['id']) ? 'selected' : '' ?>>
                                                Édition <?= echapper($ed['numero_edition']) ?> - <?= echapper($ed['annee_academique']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                <?php elseif (is_array($type)): ?>
                                    <select id="<?= $champ ?>" name="<?= $champ ?>" class="form-input">
                                        <?php foreach ($type as $option): ?>
                                            <option value="<?= $option ?>" <?= ($element && $element[$champ] === $option) ? 'selected' : '' ?>>
                                                <?= echapper($option) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <?php if ($table === 'personnes' && !empty($tous_roles)): ?>
                            <div class="form-groupe">
                                <label>Rôles</label>
                                <div class="roles-checkboxes">
                                    <?php foreach ($tous_roles as $role): ?>
                                        <label class="checkbox-label">
                                            <input type="checkbox" name="roles[]" value="<?= $role['id'] ?>" <?= in_array($role['id'], $roles_personne) ? 'checked' : '' ?>>
                                            <?= echapper($role['libelle']) ?> (<?= echapper($role['categorie']) ?>)
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primaire"><?= $action === 'ajouter' ? 'Ajouter' : 'Modifier' ?></button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </main>
    <script src="<?= SITE_URL ?>/assets/js/admin.js"></script>
</body>
</html>
