<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$pageTitle = 'Stages';
$pageSubtitle = 'Sujets de stage et affectations';
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
    <div class="card-header">
        <div class="filters-bar">
            <select id="filter-statut" class="filter-select">
                <option value="">Tous les statuts</option>
                <option value="en_cours">En cours</option>
                <option value="termine">Terminé</option>
                <option value="annule">Annulé</option>
            </select>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="../api/export.php?entity=stages" class="btn btn-secondary btn-sm">📊 Export Excel</a>
            <button class="btn btn-secondary btn-sm" onclick="window.print()">🖨️ Imprimer PDF</button>
            <button class="btn btn-primary" id="btn-add">+ Nouveau stage</button>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Sujet</th><th>Stagiaire</th><th>Encadrant</th><th>Période</th><th>Statut</th><th style="text-align:right;">Actions</th></tr></thead>
            <tbody id="tbody"><tr><td colspan="6"><div class="spinner"></div></td></tr></tbody>
        </table>
    </div>
</div>

<?php
$pageScript = <<<'HTML'
<script>
let stagiaires = [], encadrants = [];

function statutBadge(s) {
    const map = { en_cours: ['badge-info','En cours'], termine: ['badge-success','Terminé'], annule: ['badge-danger','Annulé'] };
    const [cls, label] = map[s] || ['badge-muted', s || '—'];
    return `<span class="badge ${cls}">${label}</span>`;
}

async function loadRefs() {
    [stagiaires, encadrants] = await Promise.all([
        API.get('../api/stagiaires.php?action=list').then(r => r.items),
        API.get('../api/encadrants.php?action=list').then(r => r.items),
    ]);
}

function nomStagiaire(id) { const s = stagiaires.find(x => x.id == id); return s ? `${s.nom} ${s.prenom}` : '—'; }
function nomEncadrant(id) { const e = encadrants.find(x => x.id == id); return e ? `${e.nom} ${e.prenom}` : '—'; }

async function loadList() {
    const tbody = document.getElementById('tbody');
    tbody.innerHTML = '<tr><td colspan="6"><div class="spinner"></div></td></tr>';
    const { items } = await API.get('../api/stages.php?action=list&statut=' + document.getElementById('filter-statut').value);
    if (!items.length) { tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state"><div class="icon">📋</div><h3>Aucun stage</h3></div></td></tr>`; return; }
    tbody.innerHTML = items.map(s => `
        <tr>
            <td><strong>${escapeHtml(s.sujet)}</strong></td>
            <td>${escapeHtml(nomStagiaire(s.stagiaire_id))}</td>
            <td>${escapeHtml(nomEncadrant(s.encadrant_id))}</td>
            <td>${escapeHtml(s.date_debut||'—')} → ${escapeHtml(s.date_fin||'—')}</td>
            <td>${statutBadge(s.statut)}</td>
            <td class="actions">
                <button class="btn btn-secondary btn-sm" onclick="edit('${s.id}')">✏️ Modifier</button>
                <button class="btn btn-danger btn-sm" onclick="del('${s.id}','${escapeHtml(s.sujet)}')">🗑️ Supprimer</button>
            </td>
        </tr>`).join('');
}

function formBody(s = {}) {
    const stagOpts = stagiaires.map(x => `<option value="${x.id}" ${s.stagiaire_id==x.id?'selected':''}>${escapeHtml(x.nom)} ${escapeHtml(x.prenom)}</option>`).join('');
    const encOpts = encadrants.map(x => `<option value="${x.id}" ${s.encadrant_id==x.id?'selected':''}>${escapeHtml(x.nom)} ${escapeHtml(x.prenom)}</option>`).join('');
    return `<div class="form-grid">
        <div class="form-group full"><label>Sujet *</label><input name="sujet" required value="${escapeHtml(s.sujet||'')}"></div>
        <div class="form-group"><label>Stagiaire</label><select name="stagiaire_id"><option value="">—</option>${stagOpts}</select></div>
        <div class="form-group"><label>Encadrant</label><select name="encadrant_id"><option value="">—</option>${encOpts}</select></div>
        <div class="form-group"><label>Date début</label><input name="date_debut" type="date" value="${escapeHtml(s.date_debut||'')}"></div>
        <div class="form-group"><label>Date fin</label><input name="date_fin" type="date" value="${escapeHtml(s.date_fin||'')}"></div>
        <div class="form-group"><label>Statut</label><select name="statut">
            <option value="en_cours" ${s.statut==='en_cours'?'selected':''}>En cours</option>
            <option value="termine" ${s.statut==='termine'?'selected':''}>Terminé</option>
            <option value="annule" ${s.statut==='annule'?'selected':''}>Annulé</option>
        </select></div>
        <div class="form-group full"><label>Description</label><textarea name="description">${escapeHtml(s.description||'')}</textarea></div>
    </div>`;
}

document.getElementById('btn-add').onclick = () => openModal({
    title: 'Nouveau stage', body: formBody(),
    onSubmit: async (data) => { await API.post('../api/stages.php?action=create', data); toast('Ajouté'); loadList(); }
});
window.edit = async (id) => {
    const { item } = await API.get('../api/stages.php?action=get&id=' + id);
    openModal({ title: 'Modifier le stage', body: formBody(item),
        onSubmit: async (data) => { data.id = id; await API.post('../api/stages.php?action=update', data); toast('Mis à jour'); loadList(); }});
};
window.del = (id, sujet) => confirmAction(`Supprimer <strong>${sujet}</strong> ?`, async () => {
    await API.post('../api/stages.php?action=delete', { id }); toast('Supprimé', 'warning'); loadList();
});

document.getElementById('filter-statut').addEventListener('change', loadList);

(async () => { await loadRefs(); loadList(); })();
</script>
HTML;
include __DIR__ . '/../includes/footer.php';
