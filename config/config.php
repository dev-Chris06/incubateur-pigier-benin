<?php

$configurationLocale = __DIR__ . '/config.local.php';
if (is_readable($configurationLocale)) {
    require $configurationLocale;
}

$constantesRequises = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'SITE_URL'];
foreach ($constantesRequises as $constante) {
    if (!defined($constante)) {
        $valeur = getenv($constante);
        if ($valeur === false || $valeur === '') {
            http_response_code(500);
            exit('Configuration serveur incomplète.');
        }
        define($constante, $valeur);
    }
}

define('SITE_NOM', 'START PROJECT PIGIER-BÉNIN');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5242880);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    ]);
    session_start();
}

require_once __DIR__ . '/../includes/fonctions.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log('Erreur de connexion à la base de données : ' . $e->getMessage());
    http_response_code(500);
    exit('Erreur de connexion à la base de données. Veuillez réessayer plus tard.');
}
