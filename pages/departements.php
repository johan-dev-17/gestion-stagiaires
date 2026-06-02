<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$pageTitle = 'Départements';
$pageSubtitle = 'Organisation par département';
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
    <div class="card-header">
        <div class="card-title">Liste des départements</div>
        <div style="display:flex;gap:8px;">
            <a href="../api/export.php?entity=departements" class="btn btn-secondary btn-sm">📊 Export Excel</a>
            <button class="btn btn-secondary btn-sm" onclick="window.print()">🖨️ Imprimer PDF</button>
            <button class="btn btn-primary" id="btn-add">+ Nouveau département</button>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Nom</th><th>Responsable</th><th>Description</th><th style="text-align:right;">Actions</th></tr></thead>
            <tbody id="tbody"><tr><td colspan="4"><div class="spinner"></div></td></tr></tbody>
        </table>
    </div>
</div>

<?php
$pageScript = <<<'HTML'
<script>
async function loadList() {
    const tbody = document.getElementById('tbody');
    tbody.innerHTML = '<tr><td colspan="4"><div class="spinner"></div></td></tr>';
    const { items } = await API.get('../api/departements.php');
    if (!items.length) { tbody.innerHTML = `<tr><td colspan="4"><div class="empty-state"><div class="icon">🏛</div><h3>Aucun département</h3></div></td></tr>`; return; }
    tbody.innerHTML = items.map(d => `
        <tr>
            <td><strong>${escapeHtml(d.nom)}</strong></td>
            <td>${escapeHtml(d.responsable||'—')}</td>
            <td>${escapeHtml(d.description||'—')}</td>
            <td class="actions">
                <button class="btn btn-secondary btn-sm" onclick="edit('${d.id}')">✏️ Modifier</button>
                <button class="btn btn-danger btn-sm" onclick="del('${d.id}','${escapeHtml(d.nom)}')">🗑️ Supprimer</button>
            </td>
        </tr>`).join('');
}

function formBody(d = {}) {
    return `<div class="form-grid">
        <div class="form-group full"><label>Nom *</label><input name="nom" required value="${escapeHtml(d.nom||'')}"></div>
        <div class="form-group full"><label>Responsable</label><input name="responsable" value="${escapeHtml(d.responsable||'')}"></div>
        <div class="form-group full"><label>Description</label><textarea name="description">${escapeHtml(d.description||'')}</textarea></div>
    </div>`;
}

document.getElementById('btn-add').onclick = () => openModal({
    title: 'Nouveau département', body: formBody(),
    onSubmit: async (data) => { await API.post('../api/departements.php?action=create', data); toast('Ajouté'); loadList(); }
});
window.edit = async (id) => {
    const { item } = await API.get('../api/departements.php?action=get&id=' + id);
    openModal({ title: 'Modifier', body: formBody(item),
        onSubmit: async (data) => { data.id = id; await API.post('../api/departements.php?action=update', data); toast('Mis à jour'); loadList(); }});
};
window.del = (id, nom) => confirmAction(`Supprimer <strong>${nom}</strong> ?`, async () => {
    await API.post('../api/departements.php?action=delete', { id }); toast('Supprimé', 'warning'); loadList();
});

loadList();
</script>
HTML;
include __DIR__ . '/../includes/footer.php';
