<?php
// Fichier de test pour la connexion MySQL
require_once 'includes/db.php';

try {
    $db = getDB();
    echo "✅ Connexion MySQL réussie !<br>";
    echo "Informations de connexion :<br>";
    echo "- Hôte : " . DB_HOST . "<br>";
    echo "- Base de données : " . DB_NAME . "<br>";
    echo "- Utilisateur : " . DB_USER . "<br>";
    echo "- Charset : " . DB_CHARSET . "<br>";
    
    // Test simple de requête
    $result = $db->query("SELECT VERSION() as version");
    $version = $result->fetch();
    echo "- Version MySQL : " . $version['version'] . "<br>";
    
} catch (PDOException $e) {
    echo "❌ Erreur de connexion : " . $e->getMessage();
}
