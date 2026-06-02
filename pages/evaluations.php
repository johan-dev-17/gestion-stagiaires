<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$pageTitle = 'Évaluations';
$pageSubtitle = 'Notes et commentaires de fin de stage';
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
    <div class="card-header">
        <div class="card-title">Évaluations des stagiaires</div>
        <div style="display:flex;gap:8px;">
            <a href="../api/export.php?entity=evaluations" class="btn btn-secondary btn-sm">📊 Export Excel</a>
            <button class="btn btn-secondary btn-sm" onclick="window.print()">🖨️ Imprimer PDF</button>
            <button class="btn btn-primary" id="btn-add">+ Nouvelle évaluation</button>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Stagiaire</th><th>Note /20</th><th>Date</th><th>Commentaire</th><th style="text-align:right;">Actions</th></tr></thead>
            <tbody id="tbody"><tr><td colspan="5"><div class="spinner"></div></td></tr></tbody>
        </table>
    </div>
</div>

<?php
$pageScript = <<<'HTML'
<script>
let stagiaires = [];

function noteBadge(n) {
    n = parseFloat(n);
    if (isNaN(n)) return '<span class="badge badge-muted">—</span>';
    if (n >= 16) return `<span class="badge badge-success">${n}/20</span>`;
    if (n >= 12) return `<span class="badge badge-info">${n}/20</span>`;
    if (n >= 10) return `<span class="badge badge-warning">${n}/20</span>`;
    return `<span class="badge badge-danger">${n}/20</span>`;
}

function nomStagiaire(id) { const s = stagiaires.find(x => x.id == id); return s ? `${s.nom} ${s.prenom}` : '—'; }

async function loadRefs() { stagiaires = (await API.get('../api/stagiaires.php?action=list')).items; }

async function loadList() {
    const tbody = document.getElementById('tbody');
    tbody.innerHTML = '<tr><td colspan="5"><div class="spinner"></div></td></tr>';
    const { items } = await API.get('../api/evaluations.php?action=list');
    if (!items.length) { tbody.innerHTML = `<tr><td colspan="5"><div class="empty-state"><div class="icon">⭐</div><h3>Aucune évaluation</h3></div></td></tr>`; return; }
    tbody.innerHTML = items.map(e => `
        <tr>
            <td><strong>${escapeHtml(nomStagiaire(e.stagiaire_id))}</strong></td>
            <td>${noteBadge(e.note)}</td>
            <td>${escapeHtml(e.date_evaluation||'—')}</td>
            <td>${escapeHtml((e.commentaire||'').substring(0,80))}${(e.commentaire||'').length>80?'…':''}</td>
            <td class="actions"><button class="btn btn-danger btn-sm" onclick="del('${e.id}')">🗑️ Supprimer</button></td>
        </tr>`).join('');
}

function formBody() {
    const opts = stagiaires.map(x => `<option value="${x.id}">${escapeHtml(x.nom)} ${escapeHtml(x.prenom)}</option>`).join('');
    return `<div class="form-grid">
        <div class="form-group"><label>Stagiaire *</label><select name="stagiaire_id" required><option value="">—</option>${opts}</select></div>
        <div class="form-group"><label>Note /20</label><input name="note" type="number" min="0" max="20" step="0.5"></div>
        <div class="form-group full"><label>Points forts</label><textarea name="points_forts"></textarea></div>
        <div class="form-group full"><label>Points à améliorer</label><textarea name="points_ameliorer"></textarea></div>
        <div class="form-group full"><label>Commentaire général</label><textarea name="commentaire"></textarea></div>
    </div>`;
}

document.getElementById('btn-add').onclick = () => openModal({
    title: 'Nouvelle évaluation', body: formBody(),
    onSubmit: async (data) => { await API.post('../api/evaluations.php?action=create', data); toast('Évaluation enregistrée'); loadList(); }
});

window.del = (id) => confirmAction('Supprimer cette évaluation ?', async () => {
    await API.post('../api/evaluations.php?action=delete', { id }); toast('Supprimé', 'warning'); loadList();
});

(async () => { await loadRefs(); loadList(); })();
</script>
HTML;
include __DIR__ . '/../includes/footer.php';
