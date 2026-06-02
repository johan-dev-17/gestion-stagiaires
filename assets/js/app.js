/* ============ Utilitaires AJAX globaux ============ */
const API = {
    async request(url, options = {}) {
        const opts = {
            headers: { 'X-Requested-With': 'XMLHttpRequest', ...(options.headers || {}) },
            ...options,
        };
        if (opts.body && typeof opts.body === 'object' && !(opts.body instanceof FormData)) {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(opts.body);
        }
        const res = await fetch(url, opts);
        const text = await res.text();
        let data;
        try { data = JSON.parse(text); } catch { data = { raw: text }; }
        if (!res.ok) throw new Error(data.error || `HTTP ${res.status}`);
        return data;
    },
    get(url) { return this.request(url); },
    post(url, body) { return this.request(url, { method: 'POST', body }); },
    put(url, body) { return this.request(url, { method: 'PUT', body }); },
    delete(url) { return this.request(url, { method: 'DELETE' }); },
};

/* ============ Toast ============ */
function toast(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.textContent = msg;
    document.getElementById('toast-container').appendChild(el);
    setTimeout(() => {
        el.style.transition = 'opacity 200ms, transform 200ms';
        el.style.opacity = '0';
        el.style.transform = 'translateX(120%)';
        setTimeout(() => el.remove(), 220);
    }, 2800);
}

/* ============ Modal ============ */
function openModal({ title, body, onSubmit, submitLabel = 'Enregistrer' }) {
    const root = document.getElementById('modal-root');
    root.innerHTML = `
        <div class="modal-overlay" id="modal-overlay">
            <div class="modal">
                <div class="modal-header">
                    <div class="modal-title">${title}</div>
                    <button class="modal-close" id="modal-close-btn" type="button">×</button>
                </div>
                <form id="modal-form">
                    <div class="modal-body">${body}</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="modal-cancel-btn">Annuler</button>
                        <button type="submit" class="btn btn-primary">${submitLabel}</button>
                    </div>
                </form>
            </div>
        </div>`;
    const close = () => { root.innerHTML = ''; };
    document.getElementById('modal-close-btn').onclick = close;
    document.getElementById('modal-cancel-btn').onclick = close;
    document.getElementById('modal-overlay').onclick = (e) => { if (e.target.id === 'modal-overlay') close(); };
    document.getElementById('modal-form').onsubmit = async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const data = Object.fromEntries(fd.entries());
        try {
            await onSubmit(data);
            close();
        } catch (err) {
            toast(err.message || 'Erreur', 'error');
        }
    };
}

function confirmAction(message, onYes) {
    openModal({
        title: 'Confirmation',
        body: `<p style="font-size:14px;color:var(--text);">${message}</p>`,
        submitLabel: 'Confirmer',
        onSubmit: async () => { await onYes(); },
    });
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[&<>"']/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c]));
}
