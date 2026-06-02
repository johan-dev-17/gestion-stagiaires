# StageManager : Gestion des Stagiaires (PHP / XML / AJAX)

> **Description courte (pour la section "About" de GitHub) :**  
> *Une application web légère et élégante de gestion des stagiaires. Développée en PHP sans base de données (stockage 100% XML), elle offre une interface premium (Glassmorphism) et des interactions dynamiques via AJAX.*

## 🌟 Présentation
**StageManager** est une solution complète permettant aux entreprises de centraliser et de gérer facilement leurs stagiaires, encadrants, départements et évaluations. 
Conçue sans base de données SQL, l'application utilise l'API `SimpleXML` de PHP, couplée à une interface moderne en **AJAX (Fetch API)** et un design customisé "Glassmorphism" ultra-premium.

## 🚀 Installation

### Prérequis
- PHP 7.4 ou supérieur (avec extension `simplexml` et `dom` activées — incluses par défaut)
- Un serveur web : Apache, Nginx, ou tout simplement le serveur intégré PHP

### Démarrage rapide

#### Option 1 : Serveur PHP intégré (le plus simple)
```bash
cd gestion-stagiaires
php -S localhost:8000
```
Puis ouvrir : http://localhost:8000

#### Option 2 : XAMPP / WAMP / MAMP
1. Copier le dossier `gestion-stagiaires` dans `htdocs/` (XAMPP) ou `www/` (WAMP)
2. Démarrer Apache
3. Ouvrir : http://localhost/gestion-stagiaires

### Permissions
Le dossier `data/` doit être accessible en écriture par PHP :
```bash
chmod -R 755 data/
```

## 👥 Rôles du système

L'application gère trois niveaux d'accès distincts :
- **Admin** : Accès total, y compris à la gestion des comptes utilisateurs.
- **RH** : Accès aux stagiaires, départements et évaluations.
- **Encadrant** : Accès limité aux stagiaires qu'il encadre et à leurs évaluations.

## 📁 Structure

```
gestion-stagiaires/
├── index.php              # Page de connexion
├── dashboard.php          # Tableau de bord
├── logout.php
├── config/
│   └── config.php         # Configuration globale
├── includes/
│   ├── xml_handler.php    # Lecture/écriture XML
│   ├── auth.php           # Authentification
│   ├── header.php         # En-tête commun
│   └── sidebar.php        # Menu latéral
├── api/                   # Endpoints AJAX
│   ├── stagiaires.php
│   ├── encadrants.php
│   ├── departements.php
│   ├── stages.php
│   ├── stats.php
│   └── export.php
├── pages/                 # Pages principales
│   ├── stagiaires.php
│   ├── encadrants.php
│   ├── departements.php
│   ├── stages.php
│   └── evaluations.php
├── data/                  # Fichiers XML (données)
│   ├── stagiaires.xml
│   ├── encadrants.xml
│   ├── departements.xml
│   ├── stages.xml
│   ├── evaluations.xml
│   └── users.xml
├── exports/               # Exports XML générés
└── assets/
    ├── css/style.css
    ├── js/app.js
    └── img/
```

## ✨ Fonctionnalités

- 🔐 Authentification multi-rôles (Admin / RH / Encadrant)
- 👥 CRUD complet : Stagiaires, Encadrants, Départements, Stages
- 📊 Tableau de bord avec statistiques en temps réel (Chart.js)
- 🔍 Recherche & filtres en AJAX (sans rechargement)
- 📄 Stockage XML natif (SimpleXML)
- 📤 Export XML des données
- 📝 Évaluations de fin de stage
- 🎨 Interface moderne, responsive, sans dépendance lourde
"# gestion-stagiaires"  git init git add README.md git commit -m "first commit" git branch -M main git remote add origin https://github.com/johan-dev-17/gestion-stagiaires.git git push -u origin main
