@include('common.navigations.header')

<div class="flex flex-col gap-6 pb-8" x-data="{ releaseType: 'build' }">
    <x-page-header
        title="Build New Version"
        description="Create the next application release version for the remote calibration client and server ecosystem."
        icon="package-plus" />

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.18)]">
        <div class="mb-6">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Release Builder</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Prepare the next application version</h2>
            <p class="mt-2 text-sm text-slate-500">Choose how the version should increment, review the next semantic version, and attach a short release note.</p>
        </div>

        <form method="post" action="{{ url('create-build') }}" class="space-y-6">
            {{ csrf_field() }}

            <div class="grid gap-6 lg:grid-cols-[320px_minmax(0,1fr)]">
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Version Preview</p>
                    <div class="mt-5 space-y-4">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Current Version</p>
                            <p id="current_version" class="mt-3 text-2xl font-black tracking-tight text-slate-900">{{ CommonHelper::appVersion('') }}</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-emerald-600">Next Version</p>
                            <p id="next_version" class="mt-3 text-2xl font-black tracking-tight text-emerald-600"></p>
                            <input type="hidden" name="next_version" id="hidden_next_version">
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-semibold text-slate-900">Release Type</p>
                        <p class="mt-1 text-sm text-slate-500">Select how the version should change relative to the current release.</p>

                        <div class="mt-5 grid gap-4 md:grid-cols-3">
                            <label class="flex cursor-pointer items-start gap-3 rounded-[1.25rem] border border-slate-200 bg-white p-4 transition hover:border-sky-200 hover:bg-sky-50/40">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-sky-500 focus:ring-sky-500" type="radio" value="build" name="type" checked>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Build Number</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">Increment the patch/build version only.</p>
                                </div>
                            </label>
                            <label class="flex cursor-pointer items-start gap-3 rounded-[1.25rem] border border-slate-200 bg-white p-4 transition hover:border-sky-200 hover:bg-sky-50/40">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-sky-500 focus:ring-sky-500" type="radio" value="minor" name="type">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Minor</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">Increment the middle version and reset build.</p>
                                </div>
                            </label>
                            <label class="flex cursor-pointer items-start gap-3 rounded-[1.25rem] border border-slate-200 bg-white p-4 transition hover:border-sky-200 hover:bg-sky-50/40">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-sky-500 focus:ring-sky-500" type="radio" value="major" name="type">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Major</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">Increment the major version and reset lower segments.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Build Comment</span>
                            <textarea
                                class="min-h-[140px] w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"
                                name="comment"
                                id="comment"
                                placeholder="Summarize what changed in this release..."></textarea>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button
                    class="inline-flex h-11 items-center gap-2 rounded-2xl bg-sky-500 px-5 text-sm font-semibold text-white transition hover:bg-sky-400"
                    type="submit"
                    id="create-build-submit">
                    <i data-lucide="save" class="h-4 w-4"></i>
                    Create Build
                </button>
            </div>
        </form>
    </section>
</div>

@include('common.navigations.footer')

<script>
    (function () {
        const currentVersionEl = document.getElementById('current_version');
        const nextVersionEl = document.getElementById('next_version');
        const hiddenVersionEl = document.getElementById('hidden_next_version');
        const typeInputs = document.querySelectorAll('input[type="radio"][name="type"]');

        function computeNextVersion(type) {
            const currentVersion = (currentVersionEl?.textContent || '0.0.0').trim();
            const parts = currentVersion.split('.').map((item) => parseInt(item, 10) || 0);
            const [major, minor, build] = [parts[0] || 0, parts[1] || 0, parts[2] || 0];

            let nextMajor = major;
            let nextMinor = minor;
            let nextBuild = build;

            if (type === 'build') {
                nextBuild += 1;
            } else if (type === 'minor') {
                nextMinor += 1;
                nextBuild = 0;
            } else if (type === 'major') {
                nextMajor += 1;
                nextMinor = 0;
                nextBuild = 0;
            }

            const nextVersion = `${nextMajor}.${nextMinor}.${nextBuild}`;
            if (nextVersionEl) {
                nextVersionEl.textContent = nextVersion;
            }
            if (hiddenVersionEl) {
                hiddenVersionEl.value = nextVersion;
            }
        }

        typeInputs.forEach((input) => {
            input.addEventListener('change', () => computeNextVersion(input.value));
        });

        computeNextVersion(document.querySelector('input[type="radio"][name="type"]:checked')?.value || 'build');
    })();
</script>
