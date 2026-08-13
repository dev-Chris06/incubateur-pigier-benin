<?php
/**
 * Script de maintenance : Nettoyage des fichiers uploadés orphelins
 * 
 * Ce script compare les fichiers présents dans uploads/ avec les URLs
 * référencées en base de données et supprime les fichiers orphelins.
 * 
 * Usage CLI uniquement :
 *   php scripts/nettoyer_uploads.php          # Mode simulation (dry-run)
 *   php scripts/nettoyer_uploads.php --exec   # Suppression effective
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Ce script doit être exécuté en ligne de commande.');
}

require_once __DIR__ . '/../config/config.php';

// Mode d'exécution
$mode_exec = in_array('--exec', $argv);
$fichiers_orphelins = [];
$taille_totale = 0;

echo "=== Nettoyage des fichiers uploadés orphelins ===" . PHP_EOL;
echo "Mode : " . ($mode_exec ? "SUPPRESSION EFFECTIVE" : "SIMULATION (dry-run)") . PHP_EOL;
echo str_repeat('-', 50) . PHP_EOL;

// Récupérer tous les fichiers référencés en base
$fichiers_en_base = [];

try {
    // Photos des personnes
    $stmt = $pdo->query("SELECT photo_url FROM personnes WHERE photo_url IS NOT NULL AND photo_url != ''");
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $url) {
        $fichiers_en_base[] = $url;
    }

    // Visuels des réalisations PECH
    $stmt = $pdo->query("SELECT visuel_url FROM pech_realisations WHERE visuel_url IS NOT NULL AND visuel_url != ''");
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $url) {
        $fichiers_en_base[] = $url;
    }
} catch (PDOException $e) {
    echo "ERREUR: Impossible de lire la base de données: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo "Fichiers référencés en base : " . count($fichiers_en_base) . PHP_EOL;

// Scanner les dossiers d'uploads
$dossiers = ['personnes', 'pech'];

foreach ($dossiers as $dossier) {
    $chemin_dossier = UPLOAD_DIR . $dossier;
    
    if (!is_dir($chemin_dossier)) {
        echo "Dossier '$dossier/' : inexistant, ignoré." . PHP_EOL;
        continue;
    }

    $fichiers = glob($chemin_dossier . '/*');
    $nb_orphelins_dossier = 0;

    foreach ($fichiers as $fichier) {
        if (is_dir($fichier)) {
            continue;
        }

        // Chemin relatif tel que stocké en base (ex: "personnes/abc123.jpg")
        $chemin_relatif = $dossier . '/' . basename($fichier);

        if (!in_array($chemin_relatif, $fichiers_en_base)) {
            $taille = filesize($fichier);
            $taille_totale += $taille;
            $nb_orphelins_dossier++;
            $fichiers_orphelins[] = $fichier;

            $taille_lisible = round($taille / 1024, 1);
            echo "  ORPHELIN: $chemin_relatif ({$taille_lisible} Ko)" . PHP_EOL;

            if ($mode_exec) {
                if (unlink($fichier)) {
                    echo "    → Supprimé ✓" . PHP_EOL;
                } else {
                    echo "    → ERREUR de suppression ✗" . PHP_EOL;
                }
            }
        }
    }

    $total_dossier = count($fichiers);
    echo "Dossier '$dossier/' : $total_dossier fichier(s), $nb_orphelins_dossier orphelin(s)" . PHP_EOL;
}

// Résumé
echo str_repeat('-', 50) . PHP_EOL;
$taille_lisible = round($taille_totale / 1024, 1);
echo "Total fichiers orphelins : " . count($fichiers_orphelins) . PHP_EOL;
echo "Espace récupérable : {$taille_lisible} Ko" . PHP_EOL;

if (!$mode_exec && count($fichiers_orphelins) > 0) {
    echo PHP_EOL . "Pour supprimer ces fichiers, relancez avec --exec :" . PHP_EOL;
    echo "  php scripts/nettoyer_uploads.php --exec" . PHP_EOL;
}

echo PHP_EOL . "Terminé." . PHP_EOL;
