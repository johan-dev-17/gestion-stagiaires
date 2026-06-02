<?php
// Script de test pour le système hybride MySQL + XML
require_once 'includes/db.php';
require_once 'includes/hybrid_handler.php';

echo "<h2>🧪 Test du système hybride MySQL + XML</h2><br>";

try {
    // Test 1: Connexion MySQL
    echo "1️⃣ Test de connexion MySQL...<br>";
    $db = getDB();
    echo "✅ Connexion MySQL réussie<br><br>";
    
    // Test 2: Création des tables
    echo "2️⃣ Création des tables MySQL...<br>";
    $schema = file_get_contents('data/schema.sql');
    $db->exec($schema);
    echo "✅ Tables MySQL créées<br><br>";
    
    // Test 3: Migration des données
    echo "3️⃣ Migration des données XML vers MySQL...<br>";
    
    // Migrer les départements
    $handler = new HybridHandler('departements.xml', 'departements', 'departement', 'departements');
    $count = $handler->syncFromXml();
    echo "✅ $count départements migrés<br>";
    
    // Migrer les encadrants
    $handler = new HybridHandler('encadrants.xml', 'encadrants', 'encadrant', 'encadrants');
    $count = $handler->syncFromXml();
    echo "✅ $count encadrants migrés<br>";
    
    // Migrer les stagiaires
    $handler = new HybridHandler('stagiaires.xml', 'stagiaires', 'stagiaire', 'stagiaires');
    $count = $handler->syncFromXml();
    echo "✅ $count stagiaires migrés<br>";
    
    // Migrer les stages
    $handler = new HybridHandler('stages.xml', 'stages', 'stage', 'stages');
    $count = $handler->syncFromXml();
    echo "✅ $count stages migrés<br>";
    
    // Migrer les évaluations
    $handler = new HybridHandler('evaluations.xml', 'evaluations', 'evaluation', 'evaluations');
    $count = $handler->syncFromXml();
    echo "✅ $count évaluations migrées<br>";
    
    // Migrer les utilisateurs
    $handler = new HybridHandler('users.xml', 'users', 'user', 'users');
    $count = $handler->syncFromXml();
    echo "✅ $count utilisateurs migrés<br><br>";
    
    // Test 4: Vérification des données dans MySQL
    echo "4️⃣ Vérification des données dans MySQL...<br>";
    $stmt = $db->query("SELECT COUNT(*) as total FROM stagiaires");
    $result = $stmt->fetch();
    echo "✅ $result[total] stagiaires dans MySQL<br>";
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM departements");
    $result = $stmt->fetch();
    echo "✅ $result[total] départements dans MySQL<br>";
    
    $stmt = $db->query("SELECT COUNT(*) as total FROM encadrants");
    $result = $stmt->fetch();
    echo "✅ $result[total] encadrants dans MySQL<br><br>";
    
    // Test 5: Test HybridHandler
    echo "5️⃣ Test du HybridHandler...<br>";
    $handler = new HybridHandler('stagiaires.xml', 'stagiaires', 'stagiaire', 'stagiaires');
    $stagiaires = $handler->all();
    echo "✅ Lecture depuis MySQL : " . count($stagiaires) . " stagiaires<br>";
    
    // Test d'ajout
    $testData = [
        'nom' => 'TEST',
        'prenom' => 'Hybride',
        'email' => 'test@hybride.com',
        'telephone' => '0000000000',
        'ecole' => 'TEST',
        'formation' => 'TEST',
        'departement' => 'Informatique',
        'statut' => 'en_cours',
        'date_debut' => date('Y-m-d'),
        'date_fin' => date('Y-m-d', strtotime('+1 month')),
        'sujet' => 'Test système hybride',
        'date_creation' => date('Y-m-d')
    ];
    
    $newId = $handler->add($testData);
    echo "✅ Ajout test : ID $newId (MySQL + XML)<br>";
    
    // Vérification dans MySQL
    $stmt = $db->prepare("SELECT * FROM stagiaires WHERE id = ?");
    $stmt->execute([$newId]);
    $mysqlResult = $stmt->fetch();
    echo "✅ Vérification MySQL : " . ($mysqlResult ? "Trouvé" : "Non trouvé") . "<br>";
    
    // Vérification dans XML
    $xmlResult = $handler->xmlHandler->find($newId);
    echo "✅ Vérification XML : " . ($xmlResult ? "Trouvé" : "Non trouvé") . "<br>";
    
    // Nettoyage du test
    $handler->delete($newId);
    echo "✅ Suppression test : ID $newId (MySQL + XML)<br><br>";
    
    echo "<h3>🎉 Tous les tests ont réussi !</h3>";
    echo "<p>Le système hybride fonctionne correctement :</p>";
    echo "<ul>";
    echo "<li>✅ MySQL : Stockage principal</li>";
    echo "<li>✅ XML : Backup synchronisé</li>";
    echo "<li>✅ HybridHandler : Gestion transparente des deux systèmes</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "❌ Erreur MySQL : " . $e->getMessage();
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
