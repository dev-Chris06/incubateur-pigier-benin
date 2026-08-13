CREATE DATABASE IF NOT EXISTS incubateur_pigier CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE incubateur_pigier;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE contenu_statique (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page VARCHAR(50) NOT NULL,
    cle VARCHAR(100) NOT NULL UNIQUE,
    valeur TEXT NOT NULL,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE personnes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    bio TEXT,
    photo_url VARCHAR(255),
    est_cadre_direction BOOLEAN DEFAULT FALSE,
    ordre_affichage INT DEFAULT 0,
    actif BOOLEAN DEFAULT TRUE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL UNIQUE,
    categorie ENUM('direction', 'coordination', 'mentor', 'coach', 'expert') NOT NULL
);

CREATE TABLE personnes_roles (
    personne_id INT NOT NULL,
    role_id INT NOT NULL,
    PRIMARY KEY (personne_id, role_id),
    FOREIGN KEY (personne_id) REFERENCES personnes(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

CREATE TABLE pech_editions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_edition INT NOT NULL UNIQUE,
    annee_academique VARCHAR(20),
    date_debut DATE,
    date_fin DATE,
    nb_finalistes INT,
    nb_laureats INT,
    description TEXT,
    statut ENUM('a_venir', 'en_cours', 'terminee') DEFAULT 'a_venir'
);

CREATE TABLE pech_realisations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    edition_id INT NOT NULL,
    titre_projet VARCHAR(150) NOT NULL,
    secteur VARCHAR(100),
    description TEXT,
    visuel_url VARCHAR(255),
    est_laureat BOOLEAN DEFAULT FALSE,
    ordre_affichage INT DEFAULT 0,
    FOREIGN KEY (edition_id) REFERENCES pech_editions(id) ON DELETE CASCADE
);

CREATE TABLE evenements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT,
    date_evenement DATE,
    lieu ENUM('calavi', 'cotonou', 'autre') DEFAULT 'calavi',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE candidature_infos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    edition_ouverte INT,
    date_ouverture DATE,
    date_cloture DATE,
    criteres TEXT,
    candidatures_ouvertes BOOLEAN DEFAULT FALSE
);

CREATE TABLE candidatures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_etudiant VARCHAR(150) NOT NULL,
    filiere VARCHAR(100),
    email VARCHAR(150) NOT NULL,
    telephone VARCHAR(30),
    nom_projet VARCHAR(150),
    description_projet TEXT,
    date_soumission TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('nouvelle', 'vue', 'traitee') DEFAULT 'nouvelle'
);

CREATE TABLE messages_contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    sujet VARCHAR(150),
    message TEXT NOT NULL,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lu BOOLEAN DEFAULT FALSE
);

-- DONNÉES DE DÉPART

INSERT INTO roles (libelle, categorie) VALUES
('Directeur Général', 'direction'),
('Directeur des Études', 'direction'),
('Coordonnatrice de l''Incubateur', 'coordination'),
('Assistant de la Coordonnatrice', 'coordination'),
('Directeur Financier de l''Incubateur', 'direction'),
('Coach', 'coach'),
('Mentor', 'mentor'),
('Entrepreneure', 'expert');

INSERT INTO personnes (nom, est_cadre_direction, ordre_affichage) VALUES
('ASSOGBA Victor V.', TRUE, 1),
('VIGAN Arsène', TRUE, 2),
('FALADJOU Hortence', TRUE, 3),
('BANKOLE Luiz', TRUE, 4),
('PIRUS', FALSE, 5),
('Mme Taïrou', FALSE, 6);

INSERT INTO personnes_roles (personne_id, role_id) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 5),
(5, 4),
(6, 6),
(6, 8);

INSERT INTO pech_editions (numero_edition, annee_academique, date_debut, date_fin, nb_finalistes, nb_laureats, description, statut) VALUES
(1, '2024-2025', NULL, NULL, 5, NULL, 'Première édition du Pigier Entrepreneurship Challenge, marquée par la remise des prix aux premiers lauréats qui servent aujourd''hui de mentors.', 'terminee'),
(2, '2025-2026', '2025-12-01', '2026-07-10', 12, 6, 'Deuxième édition du PECH : 12 projets présentés en finale, 6 lauréats récompensés.', 'terminee');

INSERT INTO evenements (titre, description, date_evenement, lieu) VALUES
('Journées du Bachelier PIGIER', 'Journées portes ouvertes avec sessions de tests d''admission les samedis.', '2026-08-01', 'calavi'),
('Journées d''information Master', 'Sessions d''orientation continue pour les diplômés de Licence.', '2026-08-15', 'cotonou');

INSERT INTO contenu_statique (page, cle, valeur) VALUES
('global', 'nom_site', 'START PROJECT PIGIER-BÉNIN'),
('global', 'telephone_fixe', '+229 21 30 29 06'),
('global', 'telephone_mobile', '+229 97 84 67 28'),
('global', 'email_contact', 'contact@pigier-benin.com'),
('global', 'adresse_cotonou', 'Carré 1270, Rue 320 Agontinkon-Ayidoté, Cotonou, Bénin'),
('global', 'adresse_calavi', 'Campus PIGIER Bénin, Abomey-Calavi'),
('global', 'horaires', 'Lundi - Vendredi : 7h30 - 22h30'),
('accueil', 'hero_titre', 'Le premier incubateur étudiant du Bénin'),
('accueil', 'hero_soustitre', 'Un espace exclusivement dédié aux étudiants de PIGIER Bénin pour transformer leurs idées en entreprises.'),
('accueil', 'chiffre_cle', '2 éditions du PECH, 17 projets finalistes, 6 lauréats en 2026');

INSERT INTO candidature_infos (edition_ouverte, candidatures_ouvertes, criteres) VALUES
(NULL, FALSE, 'Critères à définir par la coordination.');
