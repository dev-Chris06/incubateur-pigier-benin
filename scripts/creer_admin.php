<?php

if (PHP_SAPI !== 'cli') {
    exit("Ce script doit être exécuté en ligne de commande.\n");
}

if ($argc !== 3) {
    exit("Usage : php scripts/creer_admin.php email@domaine.tld 'mot-de-passe-solide'\n");
}

[$script, $email, $motDePasse] = $argv;
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit("Adresse e-mail invalide.\n");
}
if (strlen($motDePasse) < 12) {
    exit("Le mot de passe doit contenir au moins 12 caractères.\n");
}

require_once __DIR__ . '/../config/config.php';
$stmt = $pdo->prepare('INSERT INTO admins (email, mot_de_passe_hash) VALUES (:email, :mot_de_passe_hash)');
$stmt->execute([
    'email' => $email,
    'mot_de_passe_hash' => password_hash($motDePasse, PASSWORD_DEFAULT)
]);

echo "Administrateur créé.\n";
