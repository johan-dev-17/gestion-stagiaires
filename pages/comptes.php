<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

// Seul l'admin peut accéder à cette page
if (!hasRole('admin')) {
    header('Location: ../dashboard.php');
    exit;
}

$pageTitle = 'Comptes Utilisateurs';
$pageSubtitle = 'Gérer les accès et rôles de l\'application';
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
    <div class="card-header">
        <div class="card-title">Liste des comptes</div>
        <button class="btn btn-primary" id="btn-add">+ Nouveau compte</button>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Identifiant</th>
                    <th>Nom complet</th>
                    <th>Rôle</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody id="tbody">
                <tr><td colspan="4"><div class="spinner"></div></td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$pageScript = <<<'HTML'
<script>
function roleBadge(r) {
    const map = { admin: ['badge-danger','Admin'], rh: ['badge-warning','RH'], encadrant: ['badge-info','Encadrant'] };
    const [cls, label] = map[r] || ['badge-muted', r || '—'];
    return `<span class="badge ${cls}">${label}</span>`;
}

async function loadList() {
    const tbody = document.getElementById('tbody');
    tbody.innerHTML = '<tr><td colspan="4"><div class="spinner"></div></td></tr>';
    try {
        const { items } = await API.get('../api/comptes.php?action=list');
        if (!items.length) {
            tbody.innerHTML = `<tr><td colspan="4"><div class="empty-state"><div class="icon">👥</div><h3>Aucun compte</h3></div></td></tr>`;
            return;
        }
        tbody.innerHTML = items.map(s => `
            <tr>
                <td><strong>${escapeHtml(s.username)}</strong></td>
                <td>${escapeHtml(s.nom)}</td>
                <td>${roleBadge(s.role)}</td>
                <td class="actions">
                    <button class="btn btn-secondary btn-sm" onclick="editCompte('${s.id}')">✏️ Modifier</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteCompte('${s.id}','${escapeHtml(s.username)}')">🗑️ Supprimer</button>
                </td>
            </tr>
        `).join('');
    } catch (e) { toast(e.message, 'error'); tbody.innerHTML = ''; }
}

function formBody(s = {}) {
    return `
        <div class="form-grid">
            <div class="form-group">
                <label>Identifiant (Login) *</label>
                <input name="username" required value="${escapeHtml(s.username||'')}">
            </div>
            <div class="form-group">
                <label>Nom complet *</label>
                <input name="nom" required value="${escapeHtml(s.nom||'')}">
            </div>
            <div class="form-group">
                <label>Rôle *</label>
                <select name="role" required>
                    <option value="admin" ${s.role==='admin'?'selected':''}>Admin</option>
                    <option value="rh" ${s.role==='rh'?'selected':''}>RH</option>
                    <option value="encadrant" ${s.role==='encadrant'?'selected':''}>Encadrant</option>
                </select>
            </div>
            <div class="form-group">
                <label>Mot de passe ${s.id ? '(Laisser vide pour ne pas modifier)' : '*'}</label>
                <input name="password" type="password" placeholder="••••••••" ${s.id ? '' : 'required'}>
            </div>
        </div>`;
}

document.getElementById('btn-add').onclick = () => {
    openModal({
        title: 'Nouveau compte',
        body: formBody(),
        onSubmit: async (data) => {
            await API.post('../api/comptes.php?action=create', data);
            toast('Compte ajouté avec succès', 'success');
            loadList();
        }
    });
};

window.editCompte = async (id) => {
    const { item } = await API.get('../api/comptes.php?action=get&id=' + id);
    openModal({
        title: 'Modifier le compte',
        body: formBody(item),
        onSubmit: async (data) => {
            data.id = id;
            await API.post('../api/comptes.php?action=update', data);
            toast('Compte mis à jour', 'success');
            loadList();
        }
    });
};

window.deleteCompte = (id, username) => {
    confirmAction(`Supprimer le compte <strong>${username}</strong> ? Cette action est irréversible.`, async () => {
        try {
            await API.post('../api/comptes.php?action=delete', { id });
            toast('Compte supprimé', 'warning');
            loadList();
        } catch (e) {
            toast(e.message, 'error');
        }
    });
};

loadList();
</script>
HTML;
include __DIR__ . '/../includes/footer.php';
