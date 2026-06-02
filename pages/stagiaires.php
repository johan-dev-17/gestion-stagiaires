<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
$pageTitle = 'Stagiaires';
$pageSubtitle = 'Gérer les stagiaires de la société';
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
    <div class="card-header">
        <div class="filters-bar">
            <input type="text" id="search" class="search-input" placeholder="Rechercher un stagiaire…">
            <select id="filter-dep" class="filter-select"><option value="">Tous les départements</option></select>
            <select id="filter-statut" class="filter-select">
                <option value="">Tous les statuts</option>
                <option value="en_cours">En cours</option>
                <option value="termine">Terminé</option>
                <option value="annule">Annulé</option>
            </select>
        </div>
        <div style="display:flex;gap:8px;">
            <a href="../api/export.php?entity=stagiaires" class="btn btn-secondary btn-sm">📊 Export Excel</a>
            <button class="btn btn-secondary btn-sm" onclick="window.print()">🖨️ Imprimer PDF</button>
            <button class="btn btn-primary" id="btn-add">+ Nouveau stagiaire</button>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead><tr><th>Nom complet</th><th>Email</th><th>École</th><th>Département</th><th>Période</th><th>Statut</th><th style="text-align:right;">Actions</th></tr></thead>
            <tbody id="tbody"><tr><td colspan="7"><div class="spinner"></div></td></tr></tbody>
        </table>
    </div>
</div>

<?php
$pageScript = <<<'HTML'
<script>
let departements = [];

function statutBadge(s) {
    const map = { en_cours: ['badge-info','En cours'], termine: ['badge-success','Terminé'], annule: ['badge-danger','Annulé'] };
    const [cls, label] = map[s] || ['badge-muted', s || '—'];
    return `<span class="badge ${cls}">${label}</span>`;
}

async function loadDepartements() {
    const res = await API.get('../api/departements.php');
    departements = res.items;
    const sel = document.getElementById('filter-dep');
    departements.forEach(d => sel.insertAdjacentHTML('beforeend', `<option value="${escapeHtml(d.nom)}">${escapeHtml(d.nom)}</option>`));
}

async function loadList() {
    const params = new URLSearchParams({
        search: document.getElementById('search').value,
        departement: document.getElementById('filter-dep').value,
        statut: document.getElementById('filter-statut').value,
    });
    const tbody = document.getElementById('tbody');
    tbody.innerHTML = '<tr><td colspan="7"><div class="spinner"></div></td></tr>';
    try {
        const { items } = await API.get('../api/stagiaires.php?action=list&' + params);
        if (!items.length) {
            tbody.innerHTML = `<tr><td colspan="7"><div class="empty-state"><div class="icon">🔍</div><h3>Aucun résultat</h3><p>Ajoutez un nouveau stagiaire ou ajustez les filtres.</p></div></td></tr>`;
            return;
        }
        tbody.innerHTML = items.map(s => `
            <tr>
                <td><strong>${escapeHtml(s.nom)} ${escapeHtml(s.prenom)}</strong></td>
                <td>${escapeHtml(s.email||'—')}</td>
                <td>${escapeHtml(s.ecole||'—')}</td>
                <td>${escapeHtml(s.departement||'—')}</td>
                <td>${escapeHtml(s.date_debut||'—')} → ${escapeHtml(s.date_fin||'—')}</td>
                <td>${statutBadge(s.statut)}</td>
                <td class="actions">
                    <button class="btn btn-secondary btn-sm" onclick="editStagiaire('${s.id}')">✏️ Modifier</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteStagiaire('${s.id}','${escapeHtml(s.nom)} ${escapeHtml(s.prenom)}')">🗑️ Supprimer</button>
                </td>
            </tr>
        `).join('');
    } catch (e) { toast(e.message, 'error'); tbody.innerHTML = ''; }
}

function formBody(s = {}) {
    const depOptions = departements.map(d => `<option value="${escapeHtml(d.nom)}" ${s.departement===d.nom?'selected':''}>${escapeHtml(d.nom)}</option>`).join('');
    return `
        <div class="form-grid">
            <div class="form-group"><label>Nom *</label><input name="nom" required value="${escapeHtml(s.nom||'')}"></div>
            <div class="form-group"><label>Prénom *</label><input name="prenom" required value="${escapeHtml(s.prenom||'')}"></div>
            <div class="form-group"><label>Email</label><input name="email" type="email" value="${escapeHtml(s.email||'')}"></div>
            <div class="form-group"><label>Téléphone</label><input name="telephone" value="${escapeHtml(s.telephone||'')}"></div>
            <div class="form-group"><label>École</label><input name="ecole" value="${escapeHtml(s.ecole||'')}"></div>
            <div class="form-group"><label>Formation</label><input name="formation" value="${escapeHtml(s.formation||'')}"></div>
            <div class="form-group"><label>Département</label><select name="departement"><option value="">—</option>${depOptions}</select></div>
            <div class="form-group"><label>Statut</label><select name="statut">
                <option value="en_cours" ${s.statut==='en_cours'?'selected':''}>En cours</option>
                <option value="termine" ${s.statut==='termine'?'selected':''}>Terminé</option>
                <option value="annule" ${s.statut==='annule'?'selected':''}>Annulé</option>
            </select></div>
            <div class="form-group"><label>Date début</label><input name="date_debut" type="date" value="${escapeHtml(s.date_debut||'')}"></div>
            <div class="form-group"><label>Date fin</label><input name="date_fin" type="date" value="${escapeHtml(s.date_fin||'')}"></div>
            <div class="form-group full"><label>Sujet de stage</label><textarea name="sujet">${escapeHtml(s.sujet||'')}</textarea></div>
        </div>`;
}

document.getElementById('btn-add').onclick = () => {
    openModal({
        title: 'Nouveau stagiaire',
        body: formBody(),
        onSubmit: async (data) => {
            await API.post('../api/stagiaires.php?action=create', data);
            toast('Stagiaire ajouté');
            loadList();
        }
    });
};

window.editStagiaire = async (id) => {
    const { item } = await API.get('../api/stagiaires.php?action=get&id=' + id);
    openModal({
        title: 'Modifier le stagiaire',
        body: formBody(item),
        onSubmit: async (data) => {
            data.id = id;
            await API.post('../api/stagiaires.php?action=update', data);
            toast('Stagiaire mis à jour');
            loadList();
        }
    });
};

window.deleteStagiaire = (id, nom) => {
    confirmAction(`Supprimer <strong>${nom}</strong> ? Cette action est irréversible.`, async () => {
        await API.post('../api/stagiaires.php?action=delete', { id });
        toast('Supprimé', 'warning');
        loadList();
    });
};

let searchTimer;
document.getElementById('search').addEventListener('input', () => {
    clearTimeout(searchTimer); searchTimer = setTimeout(loadList, 250);
});
document.getElementById('filter-dep').addEventListener('change', loadList);
document.getElementById('filter-statut').addEventListener('change', loadList);

(async () => { await loadDepartements(); loadList(); })();
</script>
HTML;
include __DIR__ . '/../includes/footer.php';
