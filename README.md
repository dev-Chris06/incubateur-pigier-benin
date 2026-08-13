# START PROJECT PIGIER-BÉNIN

Le premier incubateur étudiant du Bénin, hébergé par PIGIER Bénin.

## Description

Application web PHP/MySQL permettant la gestion d'un incubateur étudiant et du concours PECH (Pigier Entrepreneurship Challenge).

## Prérequis

- PHP 8.0+
- MySQL 5.7+ ou MariaDB
- Extension PDO pour MySQL
- Serveur web (Apache, Nginx)

## Installation

### 1. Configuration de la base de données

```bash
# Créer la base de données
mysql -u root -p < sql/schema.sql
```

### 2. Configuration

Copiez le fichier de configuration d'exemple :

```bash
cp config/config.local.php.example config/config.local.php
```

Modifiez `config/config.local.php` avec vos identifiants :

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'incubateur_pigier');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
define('SITE_URL', 'https://votre-domaine.com');
```

### 3. Création du premier compte administrateur

```bash
php scripts/creer_admin.php admin@votre-domaine.com 'MotDePasse123!'
```

Le mot de passe doit contenir au moins 12 caractères.

### 4. Configuration du serveur web

Si vous déployez dans un sous-dossier (ex: `/incubateur-pigier`), modifiez `SITE_URL` en conséquence.

Pour Apache,确保 le module `mod_rewrite` est activé.

## Utilisation

### Espace public

Accédez au site : `http://votre-domaine/public/index.php`

Pages disponibles :
- **Accueil** : Présentation de l'incubateur
- **Écosystème** : L'équipe de direction et les mentors
- **PECH** : Présentation des éditions du concours
- **Actualités** : Événements à venir
- **Contact** : Formulaire de contact
- **Postuler** : Candidature à l'incubateur

### Administration

L'espace d'administration est accessible à :
`http://votre-domaine/admin/login.php`

#### Fonctionnalités du back-office :

1. **Dashboard** : Vue d'ensemble des statistiques
2. **Personnes** : Gestion des membres de l'équipe (nom, photo, rôle, bio)
3. **Rôles** : Définition des rôles (Directeur, Coach, Mentor...)
4. **Éditions PECH** : Création et gestion des éditions du concours
5. **Réalisations PECH** : Projets présentés avec visuels
6. **Événements** : Gestion des événements et journées portes ouvertes
7. **Contenu du site** : Modification des textes dynamiques (coordonnées, titre d'accueil...)
8. **Ouverture candidatures** : Ouvrir/fermer les candidatures, définir les critères
9. **Candidatures** : Consultation et traitement des candidatures
10. **Messages** : Consultation des messages de contact

### Ouverture des candidatures

1. Connectez-vous à l'administration
2. Allez dans "Ouverture candidatures"
3. Cochez "Candidatures ouvertes"
4. Définissez les dates d'ouverture/fermeture et les critères
5. Cliquez sur "Modifier"

## Structure du projet

```
.
├── admin/              # Pages d'administration
├── public/             # Pages publiques du site
├── includes/           # Fichiers inclusions (header, footer, fonctions)
├── config/             # Configuration de la base de données
├── sql/                # Schéma de la base de données
├── scripts/            # Scripts utilitaires (création admin)
├── assets/             # CSS, JavaScript, images
└── uploads/            # Fichiers uploadés (visuels projets)
```

## Sécurité

- Mot de passe admin hashé avec `password_hash()`
- Protection CSRF sur tous les formulaires
- Requêtes préparées (PDO) contre les injections SQL
- Échappement des sortie HTML contre XSS
- Rate limiting sur les formulaires publics
- Vérification du type MIME pour les uploads

## Maintenance

### Sauvegarde de la base de données

```bash
mysqldump -u utilisateur -p incubateur_pigier > backup_$(date +%Y%m%d).sql
```

### Sauvegarde des fichiers uploadés

```bash
tar -czf uploads_backup_$(date +%Y%m%d).tar.gz uploads/
```

## Licence

Propriété de PIGIER Bénin - 2026