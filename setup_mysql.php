<?php
// Script d'installation et migration vers MySQL
require_once 'includes/db.php';

try {
    $db = getDB();
    echo "✅ Connexion MySQL réussie !<br><br>";
    
    // Lire le fichier schema.sql
    $schema = file_get_contents('data/schema.sql');
    
    // Exécuter le schema
    $db->exec($schema);
    echo "✅ Tables MySQL créées avec succès !<br><br>";
    
    // Migration des données XML vers MySQL
    echo "📦 Migration des données XML vers MySQL...<br><br>";
    
    // Migrer les départements
    migrateDepartements($db);
    
    // Migrer les encadrants
    migrateEncadrants($db);
    
    // Migrer les stagiaires
    migrateStagiaires($db);
    
    // Migrer les stages
    migrateStages($db);
    
    // Migrer les évaluations
    migrateEvaluations($db);
    
    // Migrer les utilisateurs
    migrateUsers($db);
    
    echo "<br>✅ Migration terminée avec succès !<br>";
    echo "📊 Données maintenant disponibles dans MySQL et XML (backup)<br>";
    
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}

function migrateDepartements($db) {
    $handler = new XMLHandler('departements.xml', 'departements', 'departement');
    $items = $handler->all();
    
    foreach ($items as $item) {
        $stmt = $db->prepare("INSERT INTO departements (nom, description) VALUES (?, ?)");
        $stmt->execute([$item['nom'] ?? '', $item['description'] ?? '']);
    }
    echo "✅ " . count($items) . " départements migrés<br>";
}

function migrateEncadrants($db) {
    $handler = new XMLHandler('encadrants.xml', 'encadrants', 'encadrant');
    $items = $handler->all();
    
    foreach ($items as $item) {
        $stmt = $db->prepare("INSERT INTO encadrants (nom, prenom, email, telephone) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $item['nom'] ?? '',
            $item['prenom'] ?? '',
            $item['email'] ?? '',
            $item['telephone'] ?? ''
        ]);
    }
    echo "✅ " . count($items) . " encadrants migrés<br>";
}

function migrateStagiaires($db) {
    $handler = new XMLHandler('stagiaires.xml', 'stagiaires', 'stagiaire');
    $items = $handler->all();
    
    foreach ($items as $item) {
        $stmt = $db->prepare("INSERT INTO stagiaires (nom, prenom, email, telephone, ecole, formation, departement, statut, date_debut, date_fin, sujet, date_creation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $item['nom'] ?? '',
            $item['prenom'] ?? '',
            $item['email'] ?? '',
            $item['telephone'] ?? '',
            $item['ecole'] ?? '',
            $item['formation'] ?? '',
            $item['departement'] ?? '',
            $item['statut'] ?? 'en_cours',
            $item['date_debut'] ?? null,
            $item['date_fin'] ?? null,
            $item['sujet'] ?? '',
            $item['date_creation'] ?? null
        ]);
    }
    echo "✅ " . count($items) . " stagiaires migrés<br>";
}

function migrateStages($db) {
    $handler = new XMLHandler('stages.xml', 'stages', 'stage');
    $items = $handler->all();
    
    foreach ($items as $item) {
        $stmt = $db->prepare("INSERT INTO stages (stagiaire_id, sujet, date_debut, date_fin, statut) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $item['stagiaire_id'] ?? null,
            $item['sujet'] ?? '',
            $item['date_debut'] ?? null,
            $item['date_fin'] ?? null,
            $item['statut'] ?? 'en_cours'
        ]);
    }
    echo "✅ " . count($items) . " stages migrés<br>";
}

function migrateEvaluations($db) {
    $handler = new XMLHandler('evaluations.xml', 'evaluations', 'evaluation');
    $items = $handler->all();
    
    foreach ($items as $item) {
        $stmt = $db->prepare("INSERT INTO evaluations (stagiaire_id, note_technique, note_comportement, note_communication, commentaire, date_evaluation) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $item['stagiaire_id'] ?? null,
            $item['note_technique'] ?? 0,
            $item['note_comportement'] ?? 0,
            $item['note_communication'] ?? 0,
            $item['commentaire'] ?? '',
            $item['date_evaluation'] ?? null
        ]);
    }
    echo "✅ " . count($items) . " évaluations migrées<br>";
}

function migrateUsers($db) {
    $handler = new XMLHandler('users.xml', 'users', 'user');
    $items = $handler->all();
    
    foreach ($items as $item) {
        $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([
            $item['username'] ?? '',
            $item['password'] ?? '',
            $item['role'] ?? 'user'
        ]);
    }
    echo "✅ " . count($items) . " utilisateurs migrés<br>";
}
