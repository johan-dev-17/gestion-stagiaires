# Système Hybride MySQL + XML

## 🎯 Architecture

Votre application utilise maintenant un système hybride :

- **MySQL** : Stockage principal des données (lecture/écriture rapide)
- **XML** : Backup et synchronisation automatique
- **HybridHandler** : Classe PHP qui gère les deux systèmes de manière transparente

## 📋 Avantages

✅ **Performance** : MySQL pour les opérations rapides
✅ **Backup** : XML garde une copie de toutes les données
✅ **Synchronisation** : Les données sont automatiquement synchronisées entre les deux systèmes
✅ **Fallback** : Si MySQL échoue, le système utilise automatiquement XML

## 🚀 Installation

### 1. Lancer le script de test

Ouvrez votre navigateur et accédez à :
```
http://localhost/gestion-stagiaires/test_hybrid.php
```

Ce script va :
- Créer les tables MySQL automatiquement
- Migrer toutes vos données XML vers MySQL
- Tester le système hybride
- Afficher un rapport de succès

### 2. Vérifier dans phpMyAdmin

Après l'installation, vous pouvez voir vos données dans phpMyAdmin :
- Base de données : `gestion_stagiaire`
- Tables : `stagiaires`, `departements`, `encadrants`, `stages`, `evaluations`, `users`

## 📊 Fonctionnement

### Lecture des données
```php
$handler = new HybridHandler('stagiaires.xml', 'stagiaires', 'stagiaire', 'stagiaires');
$stagiaires = $handler->all(); // Lit depuis MySQL
```

### Ajout de données
```php
$data = ['nom' => 'Dupont', 'prenom' => 'Marie', ...];
$id = $handler->add($data); // Ajoute dans MySQL ET XML
```

### Mise à jour
```php
$handler->update($id, $data); // Met à jour dans MySQL ET XML
```

### Suppression
```php
$handler->delete($id); // Supprime dans MySQL ET XML
```

## 🔧 Fichiers modifiés

- `includes/hybrid_handler.php` : Nouvelle classe hybride
- `api/stagiaires.php` : Utilise HybridHandler
- `api/departements.php` : Utilise HybridHandler
- `api/encadrants.php` : Utilise HybridHandler
- `api/stages.php` : Utilise HybridHandler
- `api/evaluations.php` : Utilise HybridHandler
- `api/comptes.php` : Utilise HybridHandler
- `data/schema.sql` : Structure des tables MySQL
- `test_hybrid.php` : Script de test et installation

## 🧪 Tests

Le script `test_hybrid.php` effectue les tests suivants :

1. ✅ Connexion MySQL
2. ✅ Création des tables
3. ✅ Migration des données XML vers MySQL
4. ✅ Vérification des données dans MySQL
5. ✅ Test HybridHandler (lecture, ajout, suppression)

## 📝 Notes importantes

- Vos données XML existantes sont conservées comme backup
- Toutes les opérations CRUD sont synchronisées automatiquement
- En cas de problème MySQL, le système utilise XML automatiquement
- Les fichiers XML dans `data/` sont toujours mis à jour

## 🔄 Synchronisation manuelle

Si vous avez besoin de synchroniser manuellement :

```php
// Synchroniser XML → MySQL
$handler->syncFromXml();

// Synchroniser MySQL → XML
$handler->syncToXml();
```

## 🎉 Résultat

Vous avez maintenant le meilleur des deux mondes :
- La puissance et la performance de MySQL
- La simplicité et la portabilité des fichiers XML
- Une synchronisation automatique entre les deux
