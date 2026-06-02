<?php
require_once __DIR__ . '/auth.php';
requireLogin();
$user = currentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$inPages = basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'pages';
$basePath = $inPages ? '../' : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?> — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="app-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="main-content">
        <header class="topbar">
            <div>
                <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? 'Tableau de bord') ?></h1>
                <p class="page-subtitle"><?= htmlspecialchars($pageSubtitle ?? '') ?></p>
            </div>
            <div class="topbar-user">
                <div class="user-info">
                    <div class="user-name"><?= htmlspecialchars($user['nom']) ?></div>
                    <div class="user-role"><?= htmlspecialchars(strtoupper($user['role'])) ?></div>
                </div>
                <div class="user-avatar"><?= strtoupper(substr($user['nom'], 0, 1)) ?></div>
            </div>
        </header>
        <div class="content">
