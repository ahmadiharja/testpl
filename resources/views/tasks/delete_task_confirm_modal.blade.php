<div id="task-delete-confirm-overlay" class="fixed inset-0 z-[120] hidden bg-slate-950/45 backdrop-blur-[2px]"></div>
<div id="task-delete-confirm-modal" class="fixed inset-0 z-[121] hidden items-center justify-center px-4 py-8">
    <div class="w-full max-w-md rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
        <div class="flex items-start gap-4 px-6 py-5">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 shadow-sm">
                <i data-lucide="trash-2" class="h-5 w-5"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">Delete Task</p>
                <h3 id="task-delete-confirm-title" class="mt-1 text-xl font-bold tracking-tight text-slate-900">Remove this scheduled task?</h3>
                <p id="task-delete-confirm-description" class="mt-2 text-sm leading-6 text-slate-500">This action removes the task from the maintenance pipeline. Continue only if you are sure.</p>
            </div>
        </div>
        <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4">
            <button type="button" id="task-delete-confirm-cancel" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                Cancel
            </button>
            <button type="button" id="task-delete-confirm-submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-700">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
                Delete Task
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        if (window.openTaskDeleteConfirm) {
            return;
        }

        const overlay = document.getElementById('task-delete-confirm-overlay');
        const modal = document.getElementById('task-delete-confirm-modal');
        const cancel = document.getElementById('task-delete-confirm-cancel');
        const submit = document.getElementById('task-delete-confirm-submit');
        const title = document.getElementById('task-delete-confirm-title');
        const description = document.getElementById('task-delete-confirm-description');

        let onConfirm = null;

        function closeTaskDeleteConfirm() {
            onConfirm = null;
            overlay?.classList.add('hidden');
            modal?.classList.add('hidden');
            modal?.classList.remove('flex');
        }

        window.openTaskDeleteConfirm = function (options = {}) {
            if (title) {
                title.textContent = options.title || 'Remove this scheduled task?';
            }
            if (description) {
                description.textContent = options.description || 'This action removes the task from the maintenance pipeline. Continue only if you are sure.';
            }

            onConfirm = typeof options.onConfirm === 'function' ? options.onConfirm : null;

            overlay?.classList.remove('hidden');
            modal?.classList.remove('hidden');
            modal?.classList.add('flex');

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        };

        window.closeTaskDeleteConfirm = closeTaskDeleteConfirm;

        overlay?.addEventListener('click', closeTaskDeleteConfirm);
        cancel?.addEventListener('click', closeTaskDeleteConfirm);
        submit?.addEventListener('click', async () => {
            if (!onConfirm) {
                closeTaskDeleteConfirm();
                return;
            }

            const callback = onConfirm;
            closeTaskDeleteConfirm();
            await callback();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
                closeTaskDeleteConfirm();
            }
        });
    })();
</script>
