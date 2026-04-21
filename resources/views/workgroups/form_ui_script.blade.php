<script>
window.WorkgroupFormUI = window.WorkgroupFormUI || (() => {
    const closePanels = (root) => {
        root.querySelectorAll('[data-phone-panel], [data-facility-panel]').forEach((panel) => {
            panel.classList.add('hidden');
        });
        root.querySelectorAll('[data-phone-trigger], [data-facility-trigger]').forEach((trigger) => trigger.setAttribute('aria-expanded', 'false'));
    };

    const matches = (node, query) => {
        const haystack = `${node.dataset.name || ''} ${node.dataset.code || ''}`.toLowerCase();
        return haystack.includes(query.toLowerCase());
    };

    const initPhone = (scope) => {
        scope.querySelectorAll('[data-workgroup-phone]').forEach((root) => {
            if (root.dataset.bound === '1') return;
            root.dataset.bound = '1';

            const trigger = root.querySelector('[data-phone-trigger]');
            const panel = root.querySelector('[data-phone-panel]');
            const search = root.querySelector('[data-phone-search]');
            const input = root.querySelector('[data-phone-code-input]');
            const flag = root.querySelector('[data-phone-flag]');
            const code = root.querySelector('[data-phone-code]');
            const localInput = root.querySelector('[data-phone-local-input]');
            const normalizeLocalPhone = () => {
                if (!localInput) return;
                const digits = String(localInput.value || '').replace(/\D+/g, '').replace(/^0+/, '');
                localInput.value = digits;
            };

            localInput?.addEventListener('input', normalizeLocalPhone);
            localInput?.addEventListener('blur', normalizeLocalPhone);

            trigger?.addEventListener('click', (event) => {
                event.preventDefault();
                const opening = panel?.classList.contains('hidden');
                closePanels(document);
                panel?.classList.toggle('hidden', !opening);
                trigger.setAttribute('aria-expanded', opening ? 'true' : 'false');
                if (opening) window.setTimeout(() => {
                    search?.focus();
                }, 0);
            });

            search?.addEventListener('input', () => {
                const query = search.value.trim();
                root.querySelectorAll('[data-phone-option]').forEach((option) => {
                    option.classList.toggle('hidden', !matches(option, query));
                });
            });

            root.querySelectorAll('[data-phone-option]').forEach((option) => {
                option.addEventListener('click', () => {
                    input.value = option.dataset.code || '';
                    flag.textContent = option.dataset.flag || '';
                    code.textContent = option.dataset.code || '';
                    normalizeLocalPhone();
                    panel?.classList.add('hidden');
                    trigger?.setAttribute('aria-expanded', 'false');
                });
            });
        });
    };

    const initFacility = (scope) => {
        scope.querySelectorAll('[data-workgroup-facility-select]').forEach((root) => {
            if (root.dataset.bound === '1') return;
            root.dataset.bound = '1';

            const trigger = root.querySelector('[data-facility-trigger]');
            const panel = root.querySelector('[data-facility-panel]');
            const search = root.querySelector('[data-facility-search]');
            const input = root.querySelector('[data-facility-id-input]');
            const label = root.querySelector('[data-facility-label]');

            trigger?.addEventListener('click', (event) => {
                event.preventDefault();
                const opening = panel?.classList.contains('hidden');
                closePanels(document);
                panel?.classList.toggle('hidden', !opening);
                trigger.setAttribute('aria-expanded', opening ? 'true' : 'false');
                if (opening) window.setTimeout(() => {
                    search?.focus();
                }, 0);
            });

            search?.addEventListener('input', () => {
                const query = search.value.trim().toLowerCase();
                root.querySelectorAll('[data-facility-option]').forEach((option) => {
                    option.classList.toggle('hidden', !(option.dataset.name || '').toLowerCase().includes(query));
                });
            });

            root.querySelectorAll('[data-facility-option]').forEach((option) => {
                option.addEventListener('click', () => {
                    input.value = option.dataset.id || '';
                    label.textContent = option.dataset.name || '';
                    root.querySelectorAll('[data-facility-option]').forEach((node) => node.classList.remove('bg-sky-50', 'text-sky-700'));
                    option.classList.add('bg-sky-50', 'text-sky-700');
                    panel?.classList.add('hidden');
                    trigger?.setAttribute('aria-expanded', 'false');
                });
            });
        });
    };

    document.addEventListener('click', (event) => {
        if (!event.target.closest('[data-workgroup-phone], [data-workgroup-facility-select]')) {
            closePanels(document);
        }
    });

    window.addEventListener('resize', () => closePanels(document));

    const initValidation = (scope) => {
        scope.querySelectorAll('[data-workgroup-form]').forEach((form) => {
            if (form.dataset.validationBound === '1') return;
            form.dataset.validationBound = '1';

            const nameInput = form.querySelector('[data-workgroup-name]');
            const nameMessage = form.querySelector('[data-workgroup-name-message]');
            const nameMax = Number(form.dataset.nameMax || 100);
            const setFieldState = (input, invalid) => {
                input?.classList.toggle('workgroup-field-invalid', invalid);
            };
            const validateName = (force = false) => {
                if (!nameInput || !nameMessage) return true;
                const trimmed = String(nameInput.value || '').trim();
                let message = '';

                if (trimmed.length === 0) {
                    message = 'Workgroup Name is required.';
                } else if (trimmed.length > nameMax) {
                    message = `Workgroup Name must not exceed ${nameMax} characters.`;
                } else if (!/^[A-Za-z0-9][A-Za-z0-9\s._,'()/-]*$/.test(trimmed)) {
                    message = 'Workgroup Name may only contain letters, numbers, spaces, and basic punctuation.';
                }

                const invalid = message !== '';
                nameMessage.textContent = message;
                nameMessage.classList.toggle('hidden', !invalid && !force);
                setFieldState(nameInput, invalid);
                return !invalid;
            };

            nameInput?.addEventListener('input', () => validateName());
            nameInput?.addEventListener('blur', () => validateName(true));
            form.addEventListener('submit', (event) => {
                form.querySelectorAll('[data-phone-local-input]').forEach((input) => {
                    input.value = String(input.value || '').replace(/\D+/g, '').replace(/^0+/, '');
                });

                if (!validateName(true)) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                }
            });
        });
    };

    return {
        init(scope = document) {
            initPhone(scope);
            initFacility(scope);
            initValidation(scope);
            window.lucide?.createIcons();
        },
    };
})();
</script>
