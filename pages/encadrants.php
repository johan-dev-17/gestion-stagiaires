<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$pageTitle = 'Encadrants';
$pageSubtitle = 'Gestion des encadrants et tuteurs';
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
    <div class="card-header">
        <div class="filters-bar">
            <input type="text" id="search" class="search-input" placeholder="Rechercher un encadrant…">
        </div>
        <div style="display:flex;gap:8px;">
            <a href="../api/export.php?entity=encadrants" class="btn btn-secondary btn-sm">📊 Export Excel</a>
            <button class="btn btn-secondary btn-sm" onclick="window.print()">🖨️ Imprimer PDF</button>
            <button class="btn btn-primary" id="btn-add">+ Nouvel encadrant</button>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Nom complet</th><th>Email</th><th>Département</th><th>Poste</th><th style="text-align:right;">Actions</th></tr></thead>
            <tbody id="tbody"><tr><td colspan="5"><div class="spinner"></div></td></tr></tbody>
        </table>
    </div>
</div>

<?php
$pageScript = <<<'HTML'
<script>
let departements = [];

async function loadDeps() {
    const res = await API.get('../api/departements.php');
    departements = res.items;
}

async function loadList() {
    const tbody = document.getElementById('tbody');
    tbody.innerHTML = '<tr><td colspan="5"><div class="spinner"></div></td></tr>';
    const { items } = await API.get('../api/encadrants.php?action=list&search=' + encodeURIComponent(document.getElementById('search').value));
    if (!items.length) { tbody.innerHTML = `<tr><td colspan="5"><div class="empty-state"><div class="icon">👨‍🏫</div><h3>Aucun encadrant</h3></div></td></tr>`; return; }
    tbody.innerHTML = items.map(e => `
        <tr>
            <td><strong>${escapeHtml(e.nom)} ${escapeHtml(e.prenom)}</strong></td>
            <td>${escapeHtml(e.email||'—')}</td>
            <td>${escapeHtml(e.departement||'—')}</td>
            <td>${escapeHtml(e.poste||'—')}</td>
            <td class="actions">
                <button class="btn btn-secondary btn-sm" onclick="editEnc('${e.id}')">✏️ Modifier</button>
                <button class="btn btn-danger btn-sm" onclick="delEnc('${e.id}','${escapeHtml(e.nom)} ${escapeHtml(e.prenom)}')">🗑️ Supprimer</button>
            </td>
        </tr>`).join('');
}

function formBody(e = {}) {
    const depOpts = departements.map(d => `<option value="${escapeHtml(d.nom)}" ${e.departement===d.nom?'selected':''}>${escapeHtml(d.nom)}</option>`).join('');
    return `<div class="form-grid">
        <div class="form-group"><label>Nom *</label><input name="nom" required value="${escapeHtml(e.nom||'')}"></div>
        <div class="form-group"><label>Prénom *</label><input name="prenom" required value="${escapeHtml(e.prenom||'')}"></div>
        <div class="form-group"><label>Email</label><input name="email" type="email" value="${escapeHtml(e.email||'')}"></div>
        <div class="form-group"><label>Téléphone</label><input name="telephone" value="${escapeHtml(e.telephone||'')}"></div>
        <div class="form-group"><label>Poste</label><input name="poste" value="${escapeHtml(e.poste||'')}"></div>
        <div class="form-group"><label>Département</label><select name="departement"><option value="">—</option>${depOpts}</select></div>
    </div>`;
}

document.getElementById('btn-add').onclick = () => openModal({
    title: 'Nouvel encadrant', body: formBody(),
    onSubmit: async (data) => { await API.post('../api/encadrants.php?action=create', data); toast('Encadrant ajouté'); loadList(); }
});

window.editEnc = async (id) => {
    const { item } = await API.get('../api/encadrants.php?action=get&id=' + id);
    openModal({ title: 'Modifier l\'encadrant', body: formBody(item),
        onSubmit: async (data) => { data.id = id; await API.post('../api/encadrants.php?action=update', data); toast('Mis à jour'); loadList(); }});
};

window.delEnc = (id, nom) => confirmAction(`Supprimer <strong>${nom}</strong> ?`, async () => {
    await API.post('../api/encadrants.php?action=delete', { id }); toast('Supprimé', 'warning'); loadList();
});

let t;
document.getElementById('search').addEventListener('input', () => { clearTimeout(t); t = setTimeout(loadList, 250); });

(async () => { await loadDeps(); loadList(); })();
</script>
HTML;
include __DIR__ . '/../includes/footer.php';
