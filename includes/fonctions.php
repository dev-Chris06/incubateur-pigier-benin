<?php

/**
 * Récupère une valeur de contenu statique depuis la base de données
 *
 * @param PDO $pdo Instance PDO de connexion
 * @param string $page Nom de la page (ex: 'global', 'accueil')
 * @param string $cle Clé du contenu à récupérer
 * @return string|null Valeur du contenu ou null si non trouvé
 */
function getContenu($pdo, $page, $cle) {
    try {
        $stmt = $pdo->prepare("SELECT valeur FROM contenu_statique WHERE page = :page AND cle = :cle LIMIT 1");
        $stmt->execute(['page' => $page, 'cle' => $cle]);
        $resultat = $stmt->fetch();
        return $resultat ? $resultat['valeur'] : null;
    } catch (PDOException $e) {
        error_log("Erreur getContenu: " . $e->getMessage());
        return null;
    }
}

/**
 * Récupère toutes les valeurs de contenu statique d'une page donnée
 *
 * @param PDO $pdo Instance PDO de connexion
 * @param string $page Nom de la page
 * @return array Tableau associatif [cle => valeur]
 */
function obtenirTousContenusPage($pdo, $page) {
    try {
        $stmt = $pdo->prepare("SELECT cle, valeur FROM contenu_statique WHERE page = :page");
        $stmt->execute(['page' => $page]);
        $resultats = $stmt->fetchAll();

        $contenus = [];
        foreach ($resultats as $row) {
            $contenus[$row['cle']] = $row['valeur'];
        }
        return $contenus;
    } catch (PDOException $e) {
        error_log("Erreur obtenirTousContenusPage: " . $e->getMessage());
        return [];
    }
}

/**
 * Sécurise une chaîne pour l'affichage HTML
 *
 * @param string $texte Texte à sécuriser
 * @return string Texte échappé
 */
function echapper($texte) {
    return htmlspecialchars($texte ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Redirige vers une URL
 *
 * @param string $url URL de destination
 */
function rediriger($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Vérifie si un utilisateur admin est connecté
 *
 * @return bool True si connecté, False sinon
 */
function estConnecte() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Vérifie l'authentification admin et redirige vers login si non connecté
 */
function verifierAuthentification() {
    if (!estConnecte()) {
        rediriger(SITE_URL . '/admin/login.php');
    }
}

/**
 * Récupère les personnes avec leurs rôles
 *
 * @param PDO $pdo Instance PDO de connexion
 * @param bool $cadreSeulement Si true, ne récupère que les cadres de direction
 * @return array Liste des personnes avec leurs rôles
 */
function obtenirPersonnesAvecRoles($pdo, $cadreSeulement = false) {
    try {
        $sql = "SELECT p.id, p.nom, p.bio, p.photo_url, p.est_cadre_direction, p.ordre_affichage,
                       GROUP_CONCAT(r.libelle ORDER BY r.categorie SEPARATOR ', ') as roles
                FROM personnes p
                LEFT JOIN personnes_roles pr ON p.id = pr.personne_id
                LEFT JOIN roles r ON pr.role_id = r.id
                WHERE p.actif = 1";

        if ($cadreSeulement) {
            $sql .= " AND p.est_cadre_direction = 1";
        }

        $sql .= " GROUP BY p.id ORDER BY p.ordre_affichage ASC, p.nom ASC";

        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur obtenirPersonnesAvecRoles: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les éditions du PECH avec leurs statistiques
 *
 * @param PDO $pdo Instance PDO de connexion
 * @return array Liste des éditions
 */
function obtenirEditionsPECH($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM pech_editions ORDER BY numero_edition DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur obtenirEditionsPECH: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les réalisations d'une édition donnée
 *
 * @param PDO $pdo Instance PDO de connexion
 * @param int $editionId ID de l'édition
 * @return array Liste des réalisations
 */
function obtenirRealisationsPECH($pdo, $editionId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM pech_realisations WHERE edition_id = :edition_id ORDER BY ordre_affichage ASC, titre_projet ASC");
        $stmt->execute(['edition_id' => $editionId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur obtenirRealisationsPECH: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les événements à venir
 *
 * @param PDO $pdo Instance PDO de connexion
 * @param int $limite Nombre maximum d'événements à récupérer
 * @return array Liste des événements
 */
function obtenirEvenementsAVenir($pdo, $limite = 5) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM evenements WHERE date_evenement >= CURDATE() ORDER BY date_evenement ASC LIMIT :limite");
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur obtenirEvenementsAVenir: " . $e->getMessage());
        return [];
    }
}

// =============================================================================
// FONCTIONS DE SÉCURITÉ (CSRF, RATE LIMITING, UPLOAD)
// =============================================================================

/**
 * Génère un jeton CSRF et le stocke en session
 *
 * @return string Jeton CSRF
 */
function genererJetonCsrf() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie que le jeton CSRF soumis correspond à celui en session
 *
 * @return bool True si valide, False sinon
 */
function verifierJetonCsrf() {
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

/**
 * Génère un champ hidden pour le formulaire avec le jeton CSRF
 *
 * @return string HTML du champ CSRF
 */
function champCsrf() {
    return '<input type="hidden" name="csrf_token" value="' . genererJetonCsrf() . '">';
}

/**
 * Limite le nombre de soumissions par fichier/IP dans un délai donné
 *
 * @param string $identifiant Identifiant unique (ex: nom du fichier)
 * @param int $maxTentatives Nombre maximum de tentatives autorisées
 * @param int $delaiSecondes Délai en secondes avant réinitialisation
 * @return bool True si autorisé, False si limite atteinte
 */
function limiterSoumission($identifiant, $maxTentatives = 5, $delaiSecondes = 3600) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $cle = 'rate_' . md5($identifiant . '_' . $ip);

    if (!isset($_SESSION[$cle])) {
        $_SESSION[$cle] = ['count' => 0, 'first' => time()];
    }

    // Réinitialiser si le délai est dépassé
    if (time() - $_SESSION[$cle]['first'] > $delaiSecondes) {
        $_SESSION[$cle] = ['count' => 0, 'first' => time()];
    }

    $_SESSION[$cle]['count']++;

    return $_SESSION[$cle]['count'] <= $maxTentatives;
}

/**
 * Upload une image avec validation sécurisée
 *
 * @param array $fichier Données $_FILES['champ']
 * @param string $dossier Dossier de destination (personnes ou pech)
 * @return array ['success' => bool, 'chemin' => string|null, 'message' => string]
 */
function uploaderImage($fichier, $dossier = 'pech') {
    // Vérifications de base
    if (!isset($fichier) || $fichier['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'chemin' => null, 'message' => 'Aucun fichier reçu ou erreur d\'upload.'];
    }

    // Vérifier la taille (5 Mo max)
    if ($fichier['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'chemin' => null, 'message' => 'Le fichier dépasse la taille maximale autorisée (5 Mo).'];
    }

    // Types MIME autorisés
    $typesMimeAutorises = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];

    // Vérifier le type MIME réel (pas seulement l'extension)
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $typeMime = $finfo->file($fichier['tmp_name']);

    if (!array_key_exists($typeMime, $typesMimeAutorises)) {
        return ['success' => false, 'chemin' => null, 'message' => 'Type de fichier non autorisé. Formats acceptés : JPG, PNG, GIF, WebP.'];
    }

    // Vérifier l'extension
    $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
    $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($extension, $extensionsAutorisees)) {
        return ['success' => false, 'chemin' => null, 'message' => 'Extension non autorisée.'];
    }

    // Vérifier que le type MIME correspond à l'extension
    if ($typesMimeAutorises[$typeMime] !== $extension && !($typeMime === 'image/jpeg' && in_array($extension, ['jpg', 'jpeg']))) {
        return ['success' => false, 'chemin' => null, 'message' => 'Le type du fichier ne correspond pas à son extension.'];
    }

    // Générer un nom unique
    $nomUnique = uniqid($dossier . '_', true) . '.' . $extension;

    // Créer le dossier si nécessaire
    $dossierCible = UPLOAD_DIR . $dossier . '/';
    if (!is_dir($dossierCible)) {
        mkdir($dossierCible, 0755, true);
    }

    // Déplacer le fichier
    $cheminComplet = $dossierCible . $nomUnique;
    if (!move_uploaded_file($fichier['tmp_name'], $cheminComplet)) {
        return ['success' => false, 'chemin' => null, 'message' => 'Erreur lors du déplacement du fichier.'];
    }

    // Retourner le chemin relatif pour stockage en base
    return [
        'success' => true,
        'chemin' => $dossier . '/' . $nomUnique,
        'message' => 'Fichier uploadé avec succès.'
    ];
}

/**
 * Formate une date au format français (JJ/MM/AAAA)
 *
 * @param string $date Date au format SQL (YYYY-MM-DD)
 * @return string Date formatée ou 'N/A' si invalide
 */
function formaterDateFr($date) {
    if (empty($date) || $date === '0000-00-00') {
        return 'N/A';
    }
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return 'N/A';
    }
    return date('d/m/Y', $timestamp);
}

// =============================================================================
// FONCTIONS DE PAGINATION
// =============================================================================

/**
 * Pagine les résultats d'une requête SQL
 *
 * @param PDO $pdo Instance PDO
 * @param string $table Nom de la table
 * @param int $page Numéro de page courante (1-indexed)
 * @param int $parPage Nombre d'éléments par page
 * @param string $orderBy Clause ORDER BY
 * @param string $where Clause WHERE optionnelle (sans le mot WHERE)
 * @param array $params Paramètres pour la clause WHERE
 * @return array ['donnees', 'total', 'pages', 'page_courante', 'par_page']
 */
function paginer($pdo, $table, $page = 1, $parPage = 20, $orderBy = 'id DESC', $where = '', $params = []) {
    try {
        // Compter le total
        $sqlCount = "SELECT COUNT(*) FROM $table";
        if ($where) {
            $sqlCount .= " WHERE $where";
        }
        $stmtCount = $pdo->prepare($sqlCount);
        $stmtCount->execute($params);
        $total = (int) $stmtCount->fetchColumn();

        // Calculer le nombre de pages
        $pages = max(1, (int) ceil($total / $parPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $parPage;

        // Récupérer les données paginées
        $sql = "SELECT * FROM $table";
        if ($where) {
            $sql .= " WHERE $where";
        }
        $sql .= " ORDER BY $orderBy LIMIT :offset, :parPage";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $cle => $valeur) {
            $stmt->bindValue($cle, $valeur);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':parPage', $parPage, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'donnees' => $stmt->fetchAll(),
            'total' => $total,
            'pages' => $pages,
            'page_courante' => $page,
            'par_page' => $parPage
        ];
    } catch (PDOException $e) {
        error_log("Erreur pagination: " . $e->getMessage());
        return [
            'donnees' => [],
            'total' => 0,
            'pages' => 1,
            'page_courante' => 1,
            'par_page' => $parPage
        ];
    }
}

/**
 * Génère le HTML de la navigation de pagination
 *
 * @param array $pagination Résultat de paginer()
 * @param string $url_base URL de base avec paramètres existants (ex: '?table=personnes&')
 * @return string HTML de la pagination
 */
function afficherPagination($pagination, $url_base = '?') {
    if ($pagination['pages'] <= 1) {
        return '';
    }

    $page = $pagination['page_courante'];
    $total = $pagination['pages'];
    $html = '<div class="pagination">';

    // Bouton Précédent
    if ($page > 1) {
        $html .= '<a href="' . $url_base . 'page=' . ($page - 1) . '" class="pagination-lien">« Préc.</a>';
    } else {
        $html .= '<span class="pagination-lien pagination-desactive">« Préc.</span>';
    }

    // Calcul des pages à afficher
    $pages_visibles = [];
    if ($total <= 7) {
        // Afficher toutes les pages
        for ($i = 1; $i <= $total; $i++) {
            $pages_visibles[] = $i;
        }
    } else {
        // Première page
        $pages_visibles[] = 1;
        if ($page > 3) {
            $pages_visibles[] = '...';
        }
        // Pages autour de la page courante
        for ($i = max(2, $page - 1); $i <= min($total - 1, $page + 1); $i++) {
            $pages_visibles[] = $i;
        }
        if ($page < $total - 2) {
            $pages_visibles[] = '...';
        }
        // Dernière page
        $pages_visibles[] = $total;
    }

    // Afficher les numéros de pages
    foreach ($pages_visibles as $p) {
        if ($p === '...') {
            $html .= '<span class="pagination-ellipsis">…</span>';
        } elseif ($p == $page) {
            $html .= '<span class="pagination-lien pagination-active">' . $p . '</span>';
        } else {
            $html .= '<a href="' . $url_base . 'page=' . $p . '" class="pagination-lien">' . $p . '</a>';
        }
    }

    // Bouton Suivant
    if ($page < $total) {
        $html .= '<a href="' . $url_base . 'page=' . ($page + 1) . '" class="pagination-lien">Suiv. »</a>';
    } else {
        $html .= '<span class="pagination-lien pagination-desactive">Suiv. »</span>';
    }

    $html .= '</div>';
    return $html;
}

