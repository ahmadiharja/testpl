@include('common.navigations.header')

@php
    $settings = $data ?? [];
    $siteLogo = !empty($settings['Site logo']) ? url($settings['Site logo']) : '';
    $favicon = !empty($settings['favicon']) ? url($settings['favicon']) : '';
@endphp

<div class="flex flex-col gap-6 pb-8" x-data='{ activeTab: @json(request("tab") === "release" ? "release" : (request("tab") === "smtp" ? "smtp" : "branding")), releaseType: "build" }'>
    <x-page-header title="Site Settings" description="Configure branding assets and outbound email defaults for the remote calibration platform." icon="settings" />

    <div class="grid gap-6 xl:grid-cols-[20rem_minmax(0,1fr)]">
        <aside class="space-y-5">
            <section class="rounded-[1.9rem] border border-slate-200 bg-white p-5 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.18)]">
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-sky-500">Site Setup</p>
                <h2 class="mt-2 text-[1.25rem] font-semibold tracking-tight text-slate-900">Manage branding, mail delivery, and release creation</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Keep the platform identity, email transport, and application release flow in one settings workspace.</p>

                <div class="mt-4 space-y-2">
                    <button
                        type="button"
                        @click="activeTab='branding'"
                        :class="activeTab === 'branding' ? 'border-sky-200 bg-sky-50 text-sky-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'"
                        class="flex w-full items-start gap-3 rounded-xl border px-3.5 py-3 text-left transition">
                        <span class="mt-0.5 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-500"><i data-lucide="image" class="h-4.5 w-4.5"></i></span>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold">Brand Identity</span>
                            <span class="mt-0.5 block text-xs leading-5 text-slate-500">Manage site name, logo, favicon, and default sender details.</span>
                        </span>
                    </button>

                    <button
                        type="button"
                        @click="activeTab='smtp'"
                        :class="activeTab === 'smtp' ? 'border-sky-200 bg-sky-50 text-sky-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'"
                        class="flex w-full items-start gap-3 rounded-xl border px-3.5 py-3 text-left transition">
                        <span class="mt-0.5 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-500"><i data-lucide="server-cog" class="h-4.5 w-4.5"></i></span>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold">SMTP Delivery</span>
                            <span class="mt-0.5 block text-xs leading-5 text-slate-500">Configure outbound mail used by alerts, test email, and other notifications.</span>
                        </span>
                    </button>

                    <button
                        type="button"
                        @click="activeTab='release'"
                        :class="activeTab === 'release' ? 'border-sky-200 bg-sky-50 text-sky-700' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'"
                        class="flex w-full items-start gap-3 rounded-xl border px-3.5 py-3 text-left transition">
                        <span class="mt-0.5 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-500"><i data-lucide="package-plus" class="h-4.5 w-4.5"></i></span>
                        <span class="min-w-0">
                            <span class="block text-sm font-semibold">Release Builder</span>
                            <span class="mt-0.5 block text-xs leading-5 text-slate-500">Create the next application version for the remote calibration client and server stack.</span>
                        </span>
                    </button>
                </div>
            </section>

        </aside>

        <div class="space-y-6">
            <section
                x-show="activeTab === 'branding'"
                x-cloak
                class="rounded-[1.9rem] border border-slate-200 bg-white p-6 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.18)]">
                <div class="mb-6">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-sky-500">Brand Identity</p>
                    <h2 class="mt-2 text-[1.7rem] font-semibold tracking-tight text-slate-900">Site appearance and sender defaults</h2>
                    <p class="mt-2 text-sm text-slate-500">Keep the branding assets and sender information aligned with the customer-facing remote calibration workspace.</p>
                </div>

                <form method="post" enctype="multipart/form-data" class="space-y-6">
                    {{ csrf_field() }}

                    <div class="grid gap-5 xl:grid-cols-2">
                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                            <div class="flex items-start gap-4">
                                <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-[1.2rem] border border-slate-200 bg-white">
                                    <img src="{{ $siteLogo }}" alt="Site Logo" id="imagePreview" class="h-full w-full object-contain p-2">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-slate-900">Site Logo</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">PNG or JPG, recommended square format, maximum 5 MB.</p>
                                    <input type="file" id="imageUpload" name="site_logo" accept="image/*" class="hidden">
                                    <button
                                        type="button"
                                        onclick="document.getElementById('imageUpload').click();"
                                        class="mt-4 inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-sky-600 transition hover:border-sky-200 hover:bg-sky-50">
                                        <i data-lucide="upload" class="h-4 w-4"></i>
                                        Choose Logo
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                            <div class="flex items-start gap-4">
                                <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-[1.2rem] border border-slate-200 bg-white">
                                    <img src="{{ $favicon }}" alt="Favicon" id="favicon_image" class="h-full w-full object-contain p-3">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-slate-900">Favicon</p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">PNG or JPG, small square image recommended, maximum 5 MB.</p>
                                    <input type="file" id="imageUpload2" name="favicon" accept="image/*" class="hidden">
                                    <button
                                        type="button"
                                        onclick="document.getElementById('imageUpload2').click();"
                                        class="mt-4 inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-sky-600 transition hover:border-sky-200 hover:bg-sky-50">
                                        <i data-lucide="upload" class="h-4 w-4"></i>
                                        Choose Favicon
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-5 xl:grid-cols-2">
                        <label class="space-y-2 xl:col-span-2">
                            <span class="text-sm font-semibold text-slate-700">Site Name</span>
                            <input
                                type="text"
                                name="site"
                                value="{{ $settings['Site name'] ?? '' }}"
                                required
                                class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Default Sender Email</span>
                            <input
                                type="email"
                                name="email"
                                value="{{ $smtp_details->senderemail }}"
                                class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Default Sender Name</span>
                            <input
                                type="text"
                                name="sender"
                                value="{{ $smtp_details->sendername }}"
                                class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex h-11 items-center gap-2 rounded-2xl bg-sky-500 px-5 text-sm font-semibold text-white transition hover:bg-sky-400">
                            <i data-lucide="save" class="h-4 w-4"></i>
                            Save Site Settings
                        </button>
                    </div>
                </form>
            </section>

            <section
                x-show="activeTab === 'smtp'"
                x-cloak
                class="rounded-[1.9rem] border border-slate-200 bg-white p-6 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.18)]">
                <div class="mb-6">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-sky-500">SMTP Delivery</p>
                    <h2 class="mt-2 text-[1.7rem] font-semibold tracking-tight text-slate-900">Email transport configuration</h2>
                    <p class="mt-2 text-sm text-slate-500">Define the SMTP connection used by alerts, test email, password recovery, and other notification flows.</p>
                </div>

                <form method="post" class="space-y-6">
                    {{ csrf_field() }}

                    <div class="grid gap-5 lg:grid-cols-2 xl:grid-cols-3">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Host</span>
                            <input type="text" name="host" value="{{ $smtp_details->host }}" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Port</span>
                            <input type="text" name="port" value="{{ $smtp_details->port }}" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Username</span>
                            <input type="text" name="username" value="{{ $smtp_details->username }}" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Password</span>
                            <input type="password" name="password" value="{{ $smtp_details->password }}" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Sender Email</span>
                            <input type="text" name="sender_email" value="{{ $smtp_details->senderemail }}" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">Sender Name</span>
                            <input type="text" name="sender_name" value="{{ $smtp_details->sendername }}" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                    </div>

                    <div class="rounded-[1.25rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">
                        These SMTP values are used by alert routing, test email, and any other outbound mail sent by the platform.
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex h-11 items-center gap-2 rounded-2xl bg-sky-500 px-5 text-sm font-semibold text-white transition hover:bg-sky-400">
                            <i data-lucide="save" class="h-4 w-4"></i>
                            Save SMTP Settings
                        </button>
                    </div>
                </form>
            </section>

            <section
                x-show="activeTab === 'release'"
                x-cloak
                class="rounded-[1.9rem] border border-slate-200 bg-white p-6 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.18)]">
                <div class="mb-6">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-sky-500">Release Builder</p>
                    <h2 class="mt-2 text-[1.7rem] font-semibold tracking-tight text-slate-900">Prepare the next application version</h2>
                    <p class="mt-2 text-sm text-slate-500">Choose how the version should increment, review the semantic version preview, and add a short release comment.</p>
                </div>

                <form method="post" action="{{ url('create-build') }}" class="space-y-6">
                    {{ csrf_field() }}

                    <div class="grid gap-6 xl:grid-cols-[20rem_minmax(0,1fr)]">
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
                                        <input class="mt-1 h-4 w-4 border-slate-300 text-sky-500 focus:ring-sky-500" type="radio" value="build" name="type" x-model="releaseType">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Build Number</p>
                                            <p class="mt-1 text-xs leading-5 text-slate-500">Increment the patch/build segment only.</p>
                                        </div>
                                    </label>
                                    <label class="flex cursor-pointer items-start gap-3 rounded-[1.25rem] border border-slate-200 bg-white p-4 transition hover:border-sky-200 hover:bg-sky-50/40">
                                        <input class="mt-1 h-4 w-4 border-slate-300 text-sky-500 focus:ring-sky-500" type="radio" value="minor" name="type" x-model="releaseType">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Minor</p>
                                            <p class="mt-1 text-xs leading-5 text-slate-500">Increment the middle version and reset build.</p>
                                        </div>
                                    </label>
                                    <label class="flex cursor-pointer items-start gap-3 rounded-[1.25rem] border border-slate-200 bg-white p-4 transition hover:border-sky-200 hover:bg-sky-50/40">
                                        <input class="mt-1 h-4 w-4 border-slate-300 text-sky-500 focus:ring-sky-500" type="radio" value="major" name="type" x-model="releaseType">
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
    </div>
</div>

@include('common.navigations.footer')

<script>
    document.getElementById('imageUpload')?.addEventListener('change', function (event) {
        const file = event.target.files?.[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function () {
            document.getElementById('imagePreview').src = reader.result;
        };
        reader.readAsDataURL(file);
    });

    document.getElementById('imageUpload2')?.addEventListener('change', function (event) {
        const file = event.target.files?.[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function () {
            document.getElementById('favicon_image').src = reader.result;
        };
        reader.readAsDataURL(file);
    });

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
            if (nextVersionEl) nextVersionEl.textContent = nextVersion;
            if (hiddenVersionEl) hiddenVersionEl.value = nextVersion;
        }

        typeInputs.forEach((input) => {
            input.addEventListener('change', () => computeNextVersion(input.value));
        });

        computeNextVersion(document.querySelector('input[type="radio"][name="type"]:checked')?.value || 'build');
    })();
</script>
