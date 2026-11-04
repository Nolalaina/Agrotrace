// AgroTrace — interactions légères, sans dépendance.

document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;

    /* ---------- Thème clair / sombre ---------- */
    const themeToggles = document.querySelectorAll('[data-theme-toggle]');
    const applyTheme = (theme) => {
        document.documentElement.dataset.theme = theme;
        themeToggles.forEach((toggle) => {
            toggle.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
            toggle.setAttribute('aria-label', theme === 'dark' ? 'Activer le thème clair' : 'Activer le thème sombre');
        });
        try { localStorage.setItem('agrotrace-theme', theme); } catch (e) { /* stockage indisponible */ }
    };
    applyTheme(document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light');
    themeToggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            applyTheme(document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark');
        });
    });

    /* ---------- Menu mobile ---------- */
    const toggle = document.querySelector('[data-menu-toggle]');
    const closeMenu = () => {
        body.classList.remove('nav-open');
        toggle?.setAttribute('aria-expanded', 'false');
    };
    toggle?.addEventListener('click', () => {
        const open = body.classList.toggle('nav-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    document.querySelector('.scrim')?.addEventListener('click', closeMenu);
    document.addEventListener('keydown', (e) => e.key === 'Escape' && closeMenu());

    /* ---------- Messages : fermeture + disparition auto des succès ---------- */
    const dismiss = (alert) => {
        alert.classList.add('is-leaving');
        setTimeout(() => alert.remove(), 300);
    };
    document.querySelectorAll('.alert').forEach((alert) => {
        alert.querySelector('[data-dismiss]')?.addEventListener('click', () => dismiss(alert));
        if (alert.classList.contains('alert-success')) setTimeout(() => dismiss(alert), 6000);
    });

    /* ---------- Confirmation avant action sensible (data-confirm) ---------- */
    let dialog = null;
    const buildDialog = () => {
        dialog = document.createElement('dialog');
        dialog.className = 'confirm';
        dialog.innerHTML = `
            <form method="dialog">
                <div class="confirm-body"><h3>Confirmer l'action</h3><p data-message></p></div>
                <div class="confirm-actions">
                    <button value="cancel" class="btn btn-secondary">Annuler</button>
                    <button value="ok" class="btn btn-danger" data-ok>Confirmer</button>
                </div>
            </form>`;
        document.body.appendChild(dialog);
    };

    const markLoading = (form) => {
        form.querySelectorAll('button[type="submit"], button:not([type])').forEach((btn) => {
            btn.classList.add('is-loading');
            btn.setAttribute('aria-busy', 'true');
        });
    };

    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement) || form.method === 'dialog') return;

        const message = form.dataset.confirm;
        if (message && form.dataset.confirmed !== '1') {
            e.preventDefault();
            if (typeof HTMLDialogElement === 'undefined') {
                // Navigateur très ancien : repli sur la boîte native.
                if (window.confirm(message)) { form.dataset.confirmed = '1'; form.requestSubmit(); }
                return;
            }
            if (!dialog) buildDialog();
            dialog.querySelector('[data-message]').textContent = message;
            dialog.querySelector('[data-ok]').textContent = form.dataset.confirmLabel || 'Confirmer';
            dialog.returnValue = '';
            dialog.onclose = () => {
                if (dialog.returnValue === 'ok') {
                    form.dataset.confirmed = '1';
                    form.requestSubmit();
                }
            };
            dialog.showModal();
            return;
        }

        // Anti double-envoi : le bouton passe en chargement après le départ du formulaire.
        if (!e.defaultPrevented) setTimeout(() => markLoading(form), 0);
    });

    // Retour arrière (cache bfcache) : on rétablit l'état normal des boutons.
    window.addEventListener('pageshow', (e) => {
        if (!e.persisted) return;
        document.querySelectorAll('.is-loading').forEach((b) => b.classList.remove('is-loading'));
        document.querySelectorAll('form[data-confirmed]').forEach((f) => delete f.dataset.confirmed);
    });

    /* ---------- Mouvement de stock : champs selon le type ---------- */
    document.querySelectorAll('[data-movement-form]').forEach((form) => {
        const type = form.querySelector('[name="type"]');
        const campagne = form.querySelector('[name="campagne_id"]');
        const cout = form.querySelector('[name="cout_total"]');
        const sync = () => {
            const t = type.value;
            campagne?.classList.toggle('is-hidden', t !== 'consommation');
            if (campagne) campagne.required = t === 'consommation';
            cout?.classList.toggle('is-hidden', t !== 'entree');
            const qty = form.querySelector('[name="quantite"]');
            if (qty) qty.min = t === 'ajustement' ? '' : '0.01';
        };
        type?.addEventListener('change', sync);
        sync();
    });
});
