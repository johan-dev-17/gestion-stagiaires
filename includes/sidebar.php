<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="logo-circle">GS</div>
        <div>
            <div class="brand-name">StageManager</div>
            <div class="brand-version">v<?= APP_VERSION ?></div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="<?= $basePath ?>dashboard.php" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <span class="icon">▦</span> Tableau de bord
        </a>
        <a href="<?= $basePath ?>pages/stagiaires.php" class="<?= $currentPage === 'stagiaires' ? 'active' : '' ?>">
            <span class="icon">👤</span> Stagiaires
        </a>
        <a href="<?= $basePath ?>pages/encadrants.php" class="<?= $currentPage === 'encadrants' ? 'active' : '' ?>">
            <span class="icon">👨‍🏫</span> Encadrants
        </a>
        <a href="<?= $basePath ?>pages/departements.php" class="<?= $currentPage === 'departements' ? 'active' : '' ?>">
            <span class="icon">🏛</span> Départements
        </a>
        <a href="<?= $basePath ?>pages/stages.php" class="<?= $currentPage === 'stages' ? 'active' : '' ?>">
            <span class="icon">📋</span> Stages
        </a>
        <a href="<?= $basePath ?>pages/evaluations.php" class="<?= $currentPage === 'evaluations' ? 'active' : '' ?>">
            <span class="icon">⭐</span> Évaluations
        </a>
        <?php if (hasRole('admin')): ?>
        <a href="<?= $basePath ?>pages/comptes.php" class="<?= $currentPage === 'comptes' ? 'active' : '' ?>">
            <span class="icon">🔐</span> Comptes
        </a>
        <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
        <a href="<?= $basePath ?>logout.php" class="logout-link">
            <span class="icon">⎋</span> Déconnexion
        </a>
    </div>
</aside>
