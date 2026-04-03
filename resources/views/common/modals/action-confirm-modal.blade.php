@once
    <div id="action-confirm-overlay" class="fixed inset-0 z-[9390] hidden bg-slate-950/45 backdrop-blur-sm"></div>
    <div id="action-confirm-panel" class="fixed inset-0 z-[9400] hidden items-center justify-center p-4 sm:p-6">
        <div class="w-full max-w-md rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-[0_32px_80px_rgba(15,23,42,0.24)]">
            <div class="flex items-start gap-4">
                <div id="action-confirm-icon" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 9v4"/>
                        <path d="M12 17h.01"/>
                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p id="action-confirm-eyebrow" class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ __('Confirmation') }}</p>
                    <h3 id="action-confirm-title" class="mt-2 text-xl font-semibold tracking-tight text-slate-900">{{ __('Confirm action') }}</h3>
                    <p id="action-confirm-message" class="mt-3 text-sm leading-6 text-slate-500">{{ __('Please confirm the action you are about to run.') }}</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3 border-t border-slate-200 pt-5">
                <button id="action-confirm-cancel" type="button" class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-200">{{ __('Cancel') }}</button>
                <button id="action-confirm-approve" type="button" class="rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-600">{{ __('Continue') }}</button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const overlay = document.getElementById('action-confirm-overlay');
            const panel = document.getElementById('action-confirm-panel');
            const title = document.getElementById('action-confirm-title');
            const message = document.getElementById('action-confirm-message');
            const eyebrow = document.getElementById('action-confirm-eyebrow');
            const approveButton = document.getElementById('action-confirm-approve');
            const cancelButton = document.getElementById('action-confirm-cancel');
            const icon = document.getElementById('action-confirm-icon');

            const tones = {
                sky: {
                    icon: 'bg-sky-50 text-sky-600',
                    button: 'bg-sky-500 hover:bg-sky-600 text-white',
                },
                rose: {
                    icon: 'bg-rose-50 text-rose-600',
                    button: 'bg-rose-500 hover:bg-rose-600 text-white',
                },
                emerald: {
                    icon: 'bg-emerald-50 text-emerald-600',
                    button: 'bg-emerald-500 hover:bg-emerald-600 text-white',
                },
            };

            let resolver = null;

            function cleanup(result) {
                panel.classList.add('hidden');
                panel.classList.remove('flex');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');

                if (resolver) {
                    resolver(result);
                    resolver = null;
                }
            }

            function applyTone(tone) {
                const toneConfig = tones[tone] || tones.sky;
                icon.className = `flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl ${toneConfig.icon}`;
                approveButton.className = `rounded-xl px-4 py-2.5 text-sm font-semibold transition ${toneConfig.button}`;
            }

            window.PerfectlumConfirm = {
                open(options = {}) {
                    title.textContent = options.title || @json(__('Confirm action'));
                    message.textContent = options.message || @json(__('Please confirm the action you are about to run.'));
                    eyebrow.textContent = options.eyebrow || @json(__('Confirmation'));
                    approveButton.textContent = options.confirmLabel || @json(__('Continue'));
                    cancelButton.textContent = options.cancelLabel || @json(__('Cancel'));
                    applyTone(options.tone || 'sky');

                    overlay.classList.remove('hidden');
                    panel.classList.remove('hidden');
                    panel.classList.add('flex');
                    document.body.classList.add('overflow-hidden');

                    return new Promise((resolve) => {
                        resolver = resolve;
                    });
                },
                close(result = false) {
                    cleanup(result);
                },
            };

            approveButton.addEventListener('click', () => cleanup(true));
            cancelButton.addEventListener('click', () => cleanup(false));
            overlay.addEventListener('click', () => cleanup(false));
            window.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && resolver) {
                    cleanup(false);
                }
            });
        })();
    </script>
@endonce
