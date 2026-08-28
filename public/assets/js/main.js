(() => {
    'use strict';

    // --- Navigation mobile ---
    const navToggle = document.querySelector('[data-nav-toggle]');
    const mainNav = document.querySelector('[data-main-nav]');
    if (navToggle && mainNav) {
        navToggle.addEventListener('click', () => {
            const isOpen = mainNav.classList.toggle('is-open');
            navToggle.setAttribute('aria-expanded', String(isOpen));
        });
    }

    // --- Confirmation avant soumission (respecte la CSP, pas d'attribut onsubmit) ---
    document.querySelectorAll('[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });

    // --- Fermeture des alertes flash ---
    document.querySelectorAll('[data-dismiss]').forEach((el) => {
        el.addEventListener('click', () => el.closest('.alert')?.remove());
    });

    // --- Copier un texte dans le presse-papier ---
    document.querySelectorAll('[data-copy]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const target = document.querySelector(btn.getAttribute('data-copy'));
            if (!target) return;
            try {
                await navigator.clipboard.writeText(target.value ?? target.innerText);
                const original = btn.textContent;
                btn.textContent = 'Copié ✓';
                setTimeout(() => { btn.textContent = original; }, 1600);
            } catch (e) {
                console.error('Copie impossible', e);
            }
        });
    });

    // --- Partage natif ---
    document.querySelectorAll('[data-share]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const url = btn.getAttribute('data-share-url') || window.location.href;
            const title = btn.getAttribute('data-share-title') || document.title;
            if (navigator.share) {
                try { await navigator.share({ title, url }); } catch (e) { /* annulé par l'utilisateur */ }
            } else {
                await navigator.clipboard.writeText(url);
                btn.textContent = 'Lien copié ✓';
            }
        });
    });

    // --- Recherche de documents (filtrage client si liste déjà chargée) ---
    const searchInput = document.querySelector('[data-doc-search]');
    if (searchInput) {
        const cards = Array.from(document.querySelectorAll('[data-doc-card]'));
        const sections = Array.from(document.querySelectorAll('[data-doc-section]'));
        const emptyState = document.querySelector('[data-search-empty]');
        searchInput.addEventListener('input', () => {
            const term = searchInput.value.trim().toLowerCase();
            let visible = 0;
            cards.forEach((card) => {
                const haystack = card.getAttribute('data-doc-keywords') || '';
                const match = haystack.includes(term);
                card.style.display = match ? '' : 'none';
                if (match) visible += 1;
            });
            sections.forEach((section) => {
                const hasVisible = section.querySelectorAll('[data-doc-card]:not([style*="display: none"])').length > 0;
                section.style.display = hasVisible ? '' : 'none';
            });
            if (emptyState) emptyState.style.display = visible === 0 ? '' : 'none';
        });
    }

    // --- Générateur de document : aperçu live + brouillon localStorage ---
    const form = document.querySelector('[data-generator-form]');
    if (form) {
        const slug = form.getAttribute('data-doc-slug');
        const draftKey = `madadocs_draft_${slug}`;
        const preview = document.querySelector('[data-preview]');
        const progressWrap = document.querySelector('[data-progress]');

        const renderPreview = () => {
            if (!preview) return;
            const template = preview.getAttribute('data-template');
            let html = template;
            new FormData(form).forEach((value, key) => {
                const safe = String(value).replace(/</g, '&lt;');
                html = html.replaceAll(`{{${key}}}`, safe || `<span class="ph">…</span>`);
            });
            preview.innerHTML = html;
        };

        const updateProgress = () => {
            if (!progressWrap) return;
            const required = Array.from(form.querySelectorAll('[required]'));
            if (required.length === 0) return;
            const filled = required.filter((el) => String(el.value || '').trim() !== '').length;
            const ratio = filled / required.length;
            const segments = progressWrap.querySelectorAll('span');
            segments.forEach((seg, i) => {
                seg.classList.toggle('is-filled', i / segments.length < ratio);
            });
        };

        const saveDraft = () => {
            const data = Object.fromEntries(new FormData(form).entries());
            try { localStorage.setItem(draftKey, JSON.stringify(data)); } catch (e) { /* stockage indisponible */ }
        };

        const loadDraft = () => {
            try {
                const raw = localStorage.getItem(draftKey);
                if (!raw) return;
                const data = JSON.parse(raw);
                Object.entries(data).forEach(([key, value]) => {
                    const field = form.elements.namedItem(key);
                    if (field && 'value' in field) field.value = value;
                });
            } catch (e) { /* brouillon corrompu, ignoré */ }
        };

        // --- Facture / devis : lignes de produits dynamiques ---
        const itemsBody = document.querySelector('[data-invoice-items]');
        const renderInvoiceTable = () => {
            if (!itemsBody || !preview) return;
            const target = preview.querySelector('[data-invoice-table]');
            if (!target) return;

            const rows = Array.from(itemsBody.querySelectorAll('[data-item-row]'));
            const tva = parseFloat(form.elements.namedItem('tva')?.value || '0') || 0;
            let subtotal = 0;
            let rowsHtml = '';

            rows.forEach((row) => {
                const desc = row.querySelector('[name="produit_description[]"]')?.value.trim();
                const qty = parseFloat(row.querySelector('[name="produit_quantite[]"]')?.value || '0') || 0;
                const price = parseFloat(row.querySelector('[name="produit_prix[]"]')?.value || '0') || 0;
                if (!desc || qty <= 0) return;
                const lineTotal = qty * price;
                subtotal += lineTotal;
                rowsHtml += `<tr><td>${desc.replace(/</g, '&lt;')}</td><td class="num">${qty}</td><td class="num">${price.toLocaleString('fr-FR')} Ar</td><td class="num">${lineTotal.toLocaleString('fr-FR')} Ar</td></tr>`;
            });

            const taxAmount = subtotal * (tva / 100);
            const total = subtotal + taxAmount;

            target.innerHTML = `
                <table class="invoice-items"><thead><tr><th>Description</th><th class="num">Qté</th><th class="num">Prix unitaire</th><th class="num">Total</th></tr></thead>
                <tbody>${rowsHtml || '<tr><td colspan="4" class="text-muted">Ajoutez au moins une ligne.</td></tr>'}</tbody></table>
                <div class="invoice-totals">
                    <div><span>Sous-total</span><span>${subtotal.toLocaleString('fr-FR')} Ar</span></div>
                    <div><span>TVA (${tva}%)</span><span>${taxAmount.toLocaleString('fr-FR')} Ar</span></div>
                    <div class="grand-total"><span>Total</span><span>${total.toLocaleString('fr-FR')} Ar</span></div>
                </div>`;
        };

        if (itemsBody) {
            const addRowBtn = document.querySelector('[data-add-item]');
            const rowTemplate = () => `
                <div class="field-group-inline" data-item-row style="grid-template-columns:2fr 1fr 1fr auto;align-items:end">
                    <div class="field"><label>Description</label><input class="input" name="produit_description[]" type="text" placeholder="Prestation ou produit"></div>
                    <div class="field"><label>Quantité</label><input class="input" name="produit_quantite[]" type="number" min="0" step="0.01" value="1"></div>
                    <div class="field"><label>Prix unitaire (Ar)</label><input class="input" name="produit_prix[]" type="number" min="0" step="0.01"></div>
                    <button type="button" class="btn btn-ghost btn-sm" data-remove-item aria-label="Supprimer la ligne">✕</button>
                </div>`;

            addRowBtn?.addEventListener('click', () => {
                itemsBody.insertAdjacentHTML('beforeend', rowTemplate());
            });

            itemsBody.addEventListener('click', (event) => {
                if (event.target.closest('[data-remove-item]')) {
                    event.target.closest('[data-item-row]')?.remove();
                    renderInvoiceTable();
                }
            });

            if (itemsBody.children.length === 0) {
                itemsBody.insertAdjacentHTML('beforeend', rowTemplate());
            }
        }

        loadDraft();
        renderPreview();
        renderInvoiceTable();
        updateProgress();

        let saveTimer;
        form.addEventListener('input', () => {
            renderPreview();
            renderInvoiceTable();
            updateProgress();
            clearTimeout(saveTimer);
            saveTimer = setTimeout(saveDraft, 400);
        });

        const clearDraftBtn = document.querySelector('[data-clear-draft]');
        if (clearDraftBtn) {
            clearDraftBtn.addEventListener('click', () => {
                localStorage.removeItem(draftKey);
                form.reset();
                renderPreview();
                renderInvoiceTable();
                updateProgress();
            });
        }
    }

    // --- Assistant IA (aide à la rédaction) ---
    document.querySelectorAll('[data-ai-form]').forEach((aiForm) => {
        const resultBox = document.querySelector(aiForm.getAttribute('data-ai-result-target') || '[data-ai-result]');
        const submitBtn = aiForm.querySelector('button[type="submit"]');

        aiForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const formData = new FormData(aiForm);
            submitBtn.disabled = true;
            const originalLabel = submitBtn.textContent;
            submitBtn.textContent = 'Rédaction en cours…';
            if (resultBox) resultBox.innerHTML = '';

            try {
                const response = await fetch(aiForm.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData,
                });
                const data = await response.json();

                if (data.ok) {
                    if (resultBox) {
                        const safeText = String(data.text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                        resultBox.innerHTML = `<textarea class="input" rows="10" readonly>${safeText}</textarea>
                            <button type="button" class="btn btn-secondary btn-sm mt-6">Copier le texte</button>`;
                        const copyBtn = resultBox.querySelector('button');
                        const textarea = resultBox.querySelector('textarea');
                        copyBtn.addEventListener('click', async () => {
                            try {
                                await navigator.clipboard.writeText(textarea.value);
                                copyBtn.textContent = 'Copié ✓';
                                setTimeout(() => { copyBtn.textContent = 'Copier le texte'; }, 1600);
                            } catch (e) { /* presse-papier indisponible */ }
                        });
                    }
                } else {
                    const messages = {
                        unavailable: "L'assistant IA n'est pas configuré pour le moment. Vous pouvez continuer à remplir votre document manuellement.",
                        rate_limited: 'Vous avez atteint la limite de demandes pour cette heure. Réessayez plus tard.',
                        quota: "Le quota gratuit de l'assistant est atteint pour le moment. Réessayez plus tard.",
                        network: 'Connexion impossible avec l’assistant. Vérifiez votre connexion et réessayez.',
                        invalid_response: "L'assistant n'a pas pu générer de réponse cette fois-ci.",
                    };
                    if (resultBox) {
                        resultBox.innerHTML = `<p class="text-muted">${messages[data.error] || 'Une erreur est survenue.'}</p>`;
                    }
                }
            } catch (e) {
                if (resultBox) resultBox.innerHTML = '<p class="text-muted">Connexion impossible. Réessayez.</p>';
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalLabel;
            }
        });
    });

    // --- Favoris (toggle sans rechargement) ---
    document.querySelectorAll('[data-favorite-toggle]').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const id = btn.getAttribute('data-document-id');
            try {
                const response = await fetch('/favoris/basculer', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `document_id=${encodeURIComponent(id)}&_csrf=${encodeURIComponent(btn.getAttribute('data-csrf'))}`,
                });
                const data = await response.json();
                if (data.ok) {
                    btn.classList.toggle('is-active', data.favorite);
                    btn.textContent = data.favorite ? '★ Favori' : '☆ Ajouter aux favoris';
                }
            } catch (e) { /* ignoré, l'utilisateur peut réessayer */ }
        });
    });
})();
