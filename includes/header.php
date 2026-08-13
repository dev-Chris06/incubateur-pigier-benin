<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/fonctions.php';

// Récupération des variables de page ou valeurs par défaut
$titre_page = isset($titre_page) ? $titre_page : SITE_NOM;
$description_page = isset($description_page) ? $description_page : 'Le premier incubateur étudiant du Bénin, dédié aux étudiants de PIGIER Bénin.';

// Détection de la page active pour le menu
$page_actuelle = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= echapper($titre_page) ?></title>
    <meta name="description" content="<?= echapper($description_page) ?>">

    <!-- Favicon -->
    <link rel="icon" href="<?= SITE_URL ?>/assets/images/favicon.png">

    <!-- Open Graph pour réseaux sociaux -->
    <meta property="og:title" content="<?= echapper($titre_page) ?>">
    <meta property="og:description" content="<?= echapper($description_page) ?>">
    <meta property="og:image" content="<?= SITE_URL ?>/assets/images/logo.png">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= SITE_URL . $_SERVER['REQUEST_URI'] ?>">

    <!-- Police Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
    <header class="header-principal">
        <div class="container">
            <div class="header-contenu">
                <a href="<?= SITE_URL ?>/index" class="logo-lien">
                    <img src="<?= SITE_URL ?>/assets/images/logo.png" alt="Logo <?= SITE_NOM ?>" class="logo">
                </a>

                <!-- Navigation desktop -->
                <nav class="nav-principal" aria-label="Navigation principale">
                    <ul class="nav-liste">
                        <li><a href="<?= SITE_URL ?>/index" class="nav-lien <?= $page_actuelle === 'index' ? 'actif' : '' ?>">Accueil</a></li>
                        <li><a href="<?= SITE_URL ?>/ecosysteme" class="nav-lien <?= $page_actuelle === 'ecosysteme' ? 'actif' : '' ?>">Écosystème</a></li>
                        <li><a href="<?= SITE_URL ?>/pech" class="nav-lien <?= $page_actuelle === 'pech' ? 'actif' : '' ?>">PECH</a></li>
                        <li><a href="<?= SITE_URL ?>/actualites" class="nav-lien <?= $page_actuelle === 'actualites' ? 'actif' : '' ?>">Actualités</a></li>
                        <li><a href="<?= SITE_URL ?>/contact" class="nav-lien <?= $page_actuelle === 'contact' ? 'actif' : '' ?>">Contact</a></li>
                        <li><a href="<?= SITE_URL ?>/postuler" class="btn-postuler">Postuler</a></li>
                    </ul>
                </nav>

                <!-- Menu hamburger mobile -->
                <button class="menu-hamburger" aria-label="Menu" aria-expanded="false">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

            <!-- Navigation mobile -->
            <nav class="nav-mobile" aria-label="Navigation mobile">
                <ul class="nav-liste">
                    <li><a href="<?= SITE_URL ?>/index" class="nav-lien <?= $page_actuelle === 'index' ? 'actif' : '' ?>">Accueil</a></li>
                    <li><a href="<?= SITE_URL ?>/ecosysteme" class="nav-lien <?= $page_actuelle === 'ecosysteme' ? 'actif' : '' ?>">Écosystème</a></li>
                    <li><a href="<?= SITE_URL ?>/pech" class="nav-lien <?= $page_actuelle === 'pech' ? 'actif' : '' ?>">PECH</a></li>
                    <li><a href="<?= SITE_URL ?>/actualites" class="nav-lien <?= $page_actuelle === 'actualites' ? 'actif' : '' ?>">Actualités</a></li>
                    <li><a href="<?= SITE_URL ?>/contact" class="nav-lien <?= $page_actuelle === 'contact' ? 'actif' : '' ?>">Contact</a></li>
                </ul>
                <a href="<?= SITE_URL ?>/postuler" class="btn-postuler">Postuler</a>
            </nav>
        </div>
    </header>

    <script src="<?= SITE_URL ?>/assets/js/main.js"></script>
