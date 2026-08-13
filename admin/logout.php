<?php
require_once __DIR__ . '/../config/config.php';

// Vérification obligatoire de l'authentification
verifierAuthentification();

// Détruire la session
session_destroy();

// Redirection vers la page de connexion
rediriger(SITE_URL . '/admin/login.php');
