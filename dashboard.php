<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$pageTitle = 'Tableau de bord';
$pageSubtitle = 'Vue d\'ensemble de l\'activité de stage';
include __DIR__ . '/includes/header.php';
?>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon indigo">👥</div>
        <div class="stat-content">
            <div class="stat-value" id="stat-stagiaires">0</div>
            <div class="stat-label">Stagiaires total</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✓</div>
        <div class="stat-content">
            <div class="stat-value" id="stat-actifs">0</div>
            <div class="stat-label">Stages en cours</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">👨‍🏫</div>
        <div class="stat-content">
            <div class="stat-value" id="stat-encadrants">0</div>
            <div class="stat-label">Encadrants</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">🏛</div>
        <div class="stat-content">
            <div class="stat-value" id="stat-departements">0</div>
            <div class="stat-label">Départements</div>
        </div>
    </div>
</div>

<div class="charts-grid">
    <div class="card">
        <div class="card-header"><div class="card-title">Stagiaires par département</div></div>
        <div class="card-body"><canvas id="chart-departements" height="120"></canvas></div>
    </div>
    <div class="card">
        <div class="card-header"><div class="card-title">Répartition par statut</div></div>
        <div class="card-body"><canvas id="chart-statut" height="120"></canvas></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">Derniers stagiaires</div>
        <a href="pages/stagiaires.php" class="btn btn-secondary btn-sm">Voir tout →</a>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Nom</th><th>École</th><th>Département</th><th>Statut</th></tr></thead>
            <tbody id="recent-stagiaires"><tr><td colspan="4"><div class="spinner"></div></td></tr></tbody>
        </table>
    </div>
</div>

<?php
$pageScript = <<<'HTML'
<script>
(async () => {
    try {
        const stats = await API.get('api/stats.php');
        document.getElementById('stat-stagiaires').textContent = stats.totaux.stagiaires;
        document.getElementById('stat-actifs').textContent = stats.totaux.actifs;
        document.getElementById('stat-encadrants').textContent = stats.totaux.encadrants;
        document.getElementById('stat-departements').textContent = stats.totaux.departements;

        const depKeys = Object.keys(stats.parDepartement);
        const depVals = Object.values(stats.parDepartement);
        new Chart(document.getElementById('chart-departements'), {
            type: 'bar',
            data: { labels: depKeys.length ? depKeys : ['Aucun'], datasets: [{ label: 'Stagiaires', data: depVals.length ? depVals : [0], backgroundColor: '#1e40af', borderRadius: 8 }] },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
        new Chart(document.getElementById('chart-statut'), {
            type: 'doughnut',
            data: { labels: ['En cours', 'Terminés', 'Annulés'], datasets: [{ data: [stats.parStatut.en_cours, stats.parStatut.termine, stats.parStatut.annule], backgroundColor: ['#2563eb', '#059669', '#dc2626'] }] },
            options: { plugins: { legend: { position: 'bottom' } } }
        });

        const list = await API.get('api/stagiaires.php?action=list');
        const tbody = document.getElementById('recent-stagiaires');
        const items = list.items.slice(-5).reverse();
        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="4"><div class="empty-state"><div class="icon">📭</div><h3>Aucun stagiaire</h3><p>Commencez par en ajouter un.</p></div></td></tr>';
        } else {
            tbody.innerHTML = items.map(s => {
                const badge = { en_cours: 'badge-info', termine: 'badge-success', annule: 'badge-danger' }[s.statut] || 'badge-muted';
                const label = { en_cours: 'En cours', termine: 'Terminé', annule: 'Annulé' }[s.statut] || (s.statut || '—');
                return `<tr><td><strong>${escapeHtml(s.nom)} ${escapeHtml(s.prenom)}</strong></td><td>${escapeHtml(s.ecole||'—')}</td><td>${escapeHtml(s.departement||'—')}</td><td><span class="badge ${badge}">${label}</span></td></tr>`;
            }).join('');
        }
    } catch (e) { toast(e.message, 'error'); }
})();
</script>
HTML;
include __DIR__ . '/includes/footer.php';
