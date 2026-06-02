<?php
// Script de diagnostic pour l'accès aux données XML
require_once 'config/config.php';
require_once 'includes/xml_handler.php';

echo "<h2>🔍 Diagnostic d'accès aux données XML</h2><br>";

// Test 1: Vérifier le dossier data
echo "1️⃣ Vérification du dossier data...<br>";
if (is_dir(DATA_PATH)) {
    echo "✅ Dossier data existe : " . DATA_PATH . "<br>";
    echo "✅ Permissions : " . substr(sprintf('%o', fileperms(DATA_PATH)), -4) . "<br>";
} else {
    echo "❌ Dossier data n'existe pas<br>";
}
echo "<br>";

// Test 2: Vérifier les fichiers XML
echo "2️⃣ Vérification des fichiers XML...<br>";
$xmlFiles = ['stagiaires.xml', 'departements.xml', 'encadrants.xml', 'stages.xml', 'evaluations.xml', 'users.xml'];
foreach ($xmlFiles as $file) {
    $filePath = DATA_PATH . $file;
    if (file_exists($filePath)) {
        echo "✅ $file existe (" . filesize($filePath) . " octets)<br>";
    } else {
        echo "❌ $file n'existe pas<br>";
    }
}
echo "<br>";

// Test 3: Test XMLHandler avec stagiaires.xml
echo "3️⃣ Test de lecture avec XMLHandler...<br>";
try {
    $handler = new XMLHandler('stagiaires.xml', 'stagiaires', 'stagiaire');
    $stagiaires = $handler->all();
    echo "✅ XMLHandler fonctionne correctement<br>";
    echo "✅ Nombre de stagiaires lus : " . count($stagiaires) . "<br>";
    
    if (count($stagiaires) > 0) {
        echo "<br>📋 Premier stagiaire trouvé :<br>";
        echo "<pre>" . print_r($stagiaires[0], true) . "</pre>";
    }
} catch (Exception $e) {
    echo "❌ Erreur XMLHandler : " . $e->getMessage() . "<br>";
}
echo "<br>";

// Test 4: Test de lecture directe SimpleXML
echo "4️⃣ Test de lecture directe avec SimpleXML...<br>";
try {
    $xml = simplexml_load_file(DATA_PATH . 'stagiaires.xml');
    if ($xml === false) {
        echo "❌ Impossible de charger le fichier XML<br>";
    } else {
        echo "✅ SimpleXML a chargé le fichier<br>";
        echo "✅ Nombre d'éléments : " . count($xml->stagiaire) . "<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur SimpleXML : " . $e->getMessage() . "<br>";
}
echo "<br>";

// Test 5: Vérifier la connexion MySQL
echo "5️⃣ Vérification de la connexion MySQL...<br>";
try {
    require_once 'includes/db.php';
    $db = getDB();
    echo "✅ Connexion MySQL réussie<br>";
    
    // Vérifier si la table stagiaires existe
    $stmt = $db->query("SHOW TABLES LIKE 'stagiaires'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table stagiaires existe dans MySQL<br>";
        
        // Compter les enregistrements
        $stmt = $db->query("SELECT COUNT(*) as total FROM stagiaires");
        $result = $stmt->fetch();
        echo "✅ Nombre d'enregistrements MySQL : " . $result['total'] . "<br>";
    } else {
        echo "⚠️ Table stagiaires n'existe pas dans MySQL<br>";
    }
} catch (PDOException $e) {
    echo "❌ Erreur MySQL : " . $e->getMessage() . "<br>";
}
echo "<br>";

// Test 6: Test HybridHandler
echo "6️⃣ Test HybridHandler...<br>";
try {
    require_once 'includes/hybrid_handler.php';
    $handler = new HybridHandler('stagiaires.xml', 'stagiaires', 'stagiaire', 'stagiaires');
    $stagiaires = $handler->all();
    echo "✅ HybridHandler fonctionne<br>";
    echo "✅ Nombre de stagiaires lus : " . count($stagiaires) . "<br>";
} catch (Exception $e) {
    echo "❌ Erreur HybridHandler : " . $e->getMessage() . "<br>";
}
echo "<br>";

// Test 7: Test API sans authentification
echo "7️⃣ Test API (sans authentification)...<br>";
echo "⚠️ Les API nécessitent une authentification. Pour tester :<br>";
echo "- Connectez-vous à l'application<br>";
echo "- Utilisez le dashboard pour accéder aux données<br>";
echo "- Ou appelez directement l'API après connexion<br>";
echo "<br>";

echo "<h3>📊 Résumé</h3>";
echo "<p>Si tous les tests ci-dessus sont ✅, alors l'accès aux données XML fonctionne correctement.</p>";
echo "<p>Le problème pourrait venir de :</p>";
echo "<ul>";
echo "<li>Authentification requise pour les API</li>";
echo "<li>Problème de configuration MySQL si les tables existent</li>";
echo "<li>Problème dans l'application frontend</li>";
echo "</ul>";
