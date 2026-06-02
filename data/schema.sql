-- Schema de la base de données pour gestion_stagiaire
-- Créer la base de données si elle n'existe pas
CREATE DATABASE IF NOT EXISTS gestion_stagiaire CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_stagiaire;

-- Table des utilisateurs (authentification)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des départements
CREATE TABLE IF NOT EXISTS departements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des encadrants
CREATE TABLE IF NOT EXISTS encadrants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
    telephone VARCHAR(20),
    departement_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (departement_id) REFERENCES departements(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des stagiaires
CREATE TABLE IF NOT EXISTS stagiaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
    telephone VARCHAR(20),
    ecole VARCHAR(150),
    formation VARCHAR(200),
    departement VARCHAR(100),
    statut ENUM('en_cours', 'termine', 'annule') DEFAULT 'en_cours',
    date_debut DATE,
    date_fin DATE,
    sujet TEXT,
    date_creation DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des stages
CREATE TABLE IF NOT EXISTS stages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id INT NOT NULL,
    encadrant_id INT,
    departement_id INT,
    sujet TEXT,
    date_debut DATE,
    date_fin DATE,
    statut ENUM('en_cours', 'termine', 'annule') DEFAULT 'en_cours',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id) ON DELETE CASCADE,
    FOREIGN KEY (encadrant_id) REFERENCES encadrants(id) ON DELETE SET NULL,
    FOREIGN KEY (departement_id) REFERENCES departements(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des évaluations
CREATE TABLE IF NOT EXISTS evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stagiaire_id INT NOT NULL,
    encadrant_id INT,
    note_technique DECIMAL(3,2) DEFAULT 0.00,
    note_comportement DECIMAL(3,2) DEFAULT 0.00,
    note_communication DECIMAL(3,2) DEFAULT 0.00,
    commentaire TEXT,
    date_evaluation DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (stagiaire_id) REFERENCES stagiaires(id) ON DELETE CASCADE,
    FOREIGN KEY (encadrant_id) REFERENCES encadrants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index pour optimiser les recherches
CREATE INDEX idx_stagiaires_nom ON stagiaires(nom, prenom);
CREATE INDEX idx_stagiaires_departement ON stagiaires(departement);
CREATE INDEX idx_stagiaires_statut ON stagiaires(statut);
CREATE INDEX idx_stagiaires_dates ON stagiaires(date_debut, date_fin);
CREATE INDEX idx_stages_stagiaire ON stages(stagiaire_id);
CREATE INDEX idx_evaluations_stagiaire ON evaluations(stagiaire_id);
