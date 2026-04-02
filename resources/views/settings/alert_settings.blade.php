@include('common.navigations.header')

@php
    $canManageAlertSettings = in_array(($role ?? session('role')), ['super', 'admin'], true);
@endphp

<main class="main-vertical-layout">
    <div class="container-fluid">
        <section class="py-3">
            @php
                $alertText = [
                    'searchAlertEmails' => __('Search alert emails...'),
                    'deleteThisAlert' => __('Delete this alert?'),
                    'failedToUpdateSmtpSettings' => __('Failed to update SMTP settings.'),
                ];
            @endphp
            <div x-data="alertSettingsPage()" x-init="init()" class="space-y-4">
                <x-page-header
                    :title="__('Alert Settings')"
                    :description="__('Configure alert recipients, error thresholds, and SMTP delivery for this remote calibration platform.')"
                    icon="bell-ring"
                />

                <div class="rounded-xl border border-sky-100 bg-sky-50/70 px-4 py-2.5 text-xs text-slate-600 shadow-sm">
                    <p class="font-semibold text-slate-700">{{ __('Manage notifications in three layers: recipients, threshold values, and outbound mail delivery.') }}</p>
                    <p class="mt-0.5 text-slate-500">{{ __('Changes saved here apply to future alert emails, daily reports, and test messages.') }}</p>
                </div>

                <div class="grid gap-4 xl:grid-cols-[20rem_minmax(0,1fr)]">
                    <aside class="space-y-4">
                        <div class="rounded-[1.25rem] border border-slate-200 bg-white p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-500">{{ __('Configuration Areas') }}</p>
                            <h2 class="mt-1 text-[1.15rem] font-bold tracking-tight text-slate-900">{{ __('Choose a section') }}</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-500">{{ __('The workspace on the right updates based on the selected configuration area.') }}</p>

                            <div class="mt-4 space-y-2">
                                <button type="button" @click="activeTab='alerts'" :class="tabClass('alerts')" class="flex w-full items-start gap-3 rounded-xl border px-3.5 py-3 text-left transition">
                                    <span class="mt-0.5 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-500"><i data-lucide="mail" class="h-4.5 w-4.5"></i></span>
                                    <span class="min-w-0">
                                        <span class="block text-sm font-semibold">{{ __('Alert Emails') }}</span>
                                        <span class="mt-0.5 block text-xs leading-5 text-slate-500">{{ __('Control the recipients who receive alert notifications.') }}</span>
                                    </span>
                                </button>

                                @if($role == 'super' || $role == 'admin')
                                    <button type="button" @click="activeTab='limits'" :class="tabClass('limits')" class="flex w-full items-start gap-3 rounded-xl border px-3.5 py-3 text-left transition">
                                        <span class="mt-0.5 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-500"><i data-lucide="triangle-alert" class="h-4.5 w-4.5"></i></span>
                                        <span class="min-w-0">
                                            <span class="block text-sm font-semibold">{{ __('Error Limits') }}</span>
                                            <span class="mt-0.5 block text-xs leading-5 text-slate-500">{{ __('Define threshold values that classify alert conditions.') }}</span>
                                        </span>
                                    </button>
                                @endif

                                @if($role == 'super')
                                    <button type="button" @click="activeTab='smtp'" :class="tabClass('smtp')" class="flex w-full items-start gap-3 rounded-xl border px-3.5 py-3 text-left transition">
                                        <span class="mt-0.5 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-500"><i data-lucide="server-cog" class="h-4.5 w-4.5"></i></span>
                                        <span class="min-w-0">
                                            <span class="block text-sm font-semibold">{{ __('SMTP Delivery') }}</span>
                                            <span class="mt-0.5 block text-xs leading-5 text-slate-500">{{ __('Configure the transport used for outgoing mail.') }}</span>
                                        </span>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="rounded-[1.25rem] border border-slate-200 bg-white p-4 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Current Access') }}</p>
                            <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-3">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400">{{ __('Role') }}</p>
                                <p class="mt-1 font-semibold capitalize text-slate-900">{{ $role }}</p>
                            </div>
                            <ul class="mt-3 space-y-1.5 text-xs leading-5 text-slate-500">
                                <li>{{ __('Users can review recipients.') }}</li>
                                <li>{{ __('Admins can manage recipients and error limits inside their facility scope.') }}</li>
                                <li>{{ __('Super users can also configure SMTP delivery.') }}</li>
                            </ul>
                        </div>
                    </aside>

                    <div class="space-y-4">
                        <div x-show="activeTab === 'alerts'" x-cloak class="space-y-4">
                            <section class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-500">{{ __('Alert Email Routing') }}</p>
                                        <h2 class="mt-1 text-[1.2rem] font-bold tracking-tight text-slate-900">{{ __('Recipient rules') }}</h2>
                                        <p class="mt-1 text-sm leading-6 text-slate-500">{{ __('Review, search, and update the recipient rules used for alerts and daily summaries.') }}</p>
                                    </div>
                                    @if($canManageAlertSettings)
                                        <button type="button" @click="openForm(0)" class="inline-flex shrink-0 items-center justify-center rounded-2xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-400">
                                            {{ __('Add Alert') }}
                                        </button>
                                    @endif
                                </div>
                            </section>

                            <div x-show="alertsInfoVisible" x-cloak class="flex items-start justify-between gap-3 rounded-xl border border-sky-100 bg-sky-50/70 px-4 py-3 text-xs text-slate-600 shadow-sm">
                                <div class="min-w-0 space-y-1">
                                    <p class="font-semibold text-slate-700">{{ __('How it works') }}</p>
                                    <p class="text-slate-500">{{ __('Add one recipient per rule, use Daily Report for summary emails, and disable Active to keep a rule without deleting it.') }}</p>
                                    <p class="text-slate-500">{{ $role === 'super' ? 'You can manage alert recipients across the full platform.' : ($role === 'admin' ? 'You can manage recipients and thresholds inside your assigned facility scope.' : 'You can review alert recipient rules, but editing is disabled for your role.') }}</p>
                                </div>
                                <button type="button" @click="alertsInfoVisible = false" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-400 transition hover:border-slate-300 hover:text-slate-700" aria-label="{{ __('Dismiss alert info') }}">
                                    <span class="text-base leading-none">&times;</span>
                                </button>
                            </div>

                            <x-data-table id="alerts-grid" class="workstation-table-shell mb-0" />
                        </div>

                        @if($role == 'super' || $role == 'admin')
                            <div x-show="activeTab === 'limits'" x-cloak class="rounded-[1.25rem] border border-slate-200 bg-white p-4 shadow-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-500">Thresholds</p>
                                <h2 class="mt-1 text-[1.2rem] font-bold tracking-tight text-slate-900">Error limits</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-500">Set the threshold values used to determine alert conditions.</p>

                                <form id="limit-form" @submit.prevent="submitLimits" class="mt-4 space-y-4">
                                    {{ csrf_field() }}
                                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                        @foreach ($errorlimit as $error)
                                            <label class="block rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">
                                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $error->name }}</span>
                                                <div class="relative">
                                                    {{ Form::text($error->id, $error->value, ['class' => 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 pr-16 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20']) }}
                                                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm text-slate-400">{{ $error->suffix }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" :disabled="savingLimits" class="inline-flex items-center justify-center rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-60">
                                            <span x-text="savingLimits ? @js(__('Saving...')) : @js(__('Save Error Limits'))"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif

                        @if($role == 'super')
                            <div x-show="activeTab === 'smtp'" x-cloak class="rounded-[1.25rem] border border-slate-200 bg-white p-4 shadow-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-500">{{ __('Mail Delivery') }}</p>
                                <h2 class="mt-1 text-[1.2rem] font-bold tracking-tight text-slate-900">{{ __('SMTP transport') }}</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-500">{{ __('Configure the outbound mail server used for alert notifications and test email.') }}</p>

                                <form id="smtp-form" @submit.prevent="submitSmtp" class="mt-4 space-y-4">
                                    {{ csrf_field() }}
                                    {{ Form::hidden('smtp_id', $smtp->id) }}
                                    {{ Form::hidden('ajax', 0) }}

                                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                        <label class="block rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">
                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Sender Email') }}</span>
                                            {{ Form::text('senderemail', $smtp->senderemail, ['class' => 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20']) }}
                                        </label>
                                        <label class="block rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">
                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Sender Name') }}</span>
                                            {{ Form::text('sendername', $smtp->sendername, ['class' => 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20']) }}
                                        </label>
                                        <label class="block rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">
                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('SMTP Server') }}</span>
                                            {{ Form::text('smtpserver', $smtp->host, ['class' => 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20']) }}
                                        </label>
                                        <label class="block rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">
                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('SMTP Port') }}</span>
                                            {{ Form::text('smtpport', $smtp->port, ['class' => 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20']) }}
                                        </label>
                                        <label class="block rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">
                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('SMTP User') }}</span>
                                            {{ Form::text('smtpuser', $smtp->username, ['class' => 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20', 'autocomplete' => 'off']) }}
                                        </label>
                                        <label class="block rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">
                                            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('SMTP Password') }}</span>
                                            <input type="text" id="smtppassword" name="smtppassword" value="{{ $smtp->password }}" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                        </label>
                                    </div>

                                    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                        <input class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/30" type="checkbox" value="1" name="usetls" @if ($smtp->encryption == 'tls') checked @endif>
                                        <span>
                                            <span class="block font-semibold text-slate-900">{{ __('Use TLS encryption') }}</span>
                                            <span class="mt-0.5 block text-xs leading-5 text-slate-500">{{ __('Enable TLS if the SMTP provider requires secure transport.') }}</span>
                                        </span>
                                    </label>

                                    <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Test Delivery') }}</p>
                                        <div class="mt-3 grid gap-3 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
                                            <label class="block">
                                                <span class="mb-2 block text-sm font-medium text-slate-700">{{ __('Send Test Email To') }}</span>
                                                <input x-model="testEmail" type="email" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" placeholder="{{ __('name@example.com') }}">
                                            </label>
                                            <button type="button" @click="sendTestEmail" :disabled="sendingTest" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-60">
                                                <span x-text="sendingTest ? @js(__('Sending...')) : @js(__('Send Test Email'))"></span>
                                            </button>
                                        </div>
                                        <p class="mt-3 text-sm" :class="testResultType === 'error' ? 'text-rose-600' : 'text-emerald-600'" x-text="testResult"></p>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" :disabled="savingSmtp" class="inline-flex items-center justify-center rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-60">
                                            <span x-text="savingSmtp ? @js(__('Saving...')) : @js(__('Save SMTP Settings'))"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <div x-show="formOpen" x-cloak class="fixed inset-0 z-[70]">
                    <div x-show="formOpen"
                         x-transition.opacity.duration.200ms
                         class="absolute inset-0 bg-slate-950/35"
                         @click="closeForm"></div>
                    <div class="absolute inset-y-0 right-0 flex w-full justify-end pointer-events-none">
                        <div x-show="formOpen"
                             x-transition:enter="transform transition ease-out duration-300"
                             x-transition:enter-start="translate-x-8 opacity-0"
                             x-transition:enter-end="translate-x-0 opacity-100"
                             x-transition:leave="transform transition ease-in duration-220"
                             x-transition:leave-start="translate-x-0 opacity-100"
                             x-transition:leave-end="translate-x-8 opacity-0"
                             @click.stop
                             class="pointer-events-auto w-full max-w-xl overflow-y-auto bg-white shadow-2xl">
                        <div class="sticky top-0 flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-500">{{ __('Alert Rule') }}</p>
                                <h3 class="mt-1 text-lg font-semibold text-slate-900">{{ __('Add or edit alert routing') }}</h3>
                                <p class="text-sm text-slate-500">{{ __('Set the recipient, activation status, and facility scope for this alert rule.') }}</p>
                            </div>
                            <button type="button" @click="closeForm" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition hover:bg-slate-200 hover:text-slate-700">
                                <span class="sr-only">{{ __('Close panel') }}</span>
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>

                        <div class="p-6">
                            <div x-show="formLoading" class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center text-sm text-slate-500">{{ __('Loading form...') }}</div>
                            <div id="alert-form-box" x-show="!formLoading" x-html="formHtml"></div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

@include('common.navigations.footer')

<script>
function alertSettingsPage() {
    return {
        activeTab: 'alerts',
        formOpen: false,
        formLoading: false,
        formHtml: '',
        formCloseTimer: null,
        savingLimits: false,
        savingSmtp: false,
        sendingTest: false,
        testEmail: '',
        testResult: '',
        testResultType: 'success',
        alertsInfoVisible: true,
        alertsGrid: null,
        tabClass(tab) {
            return this.activeTab === tab
                ? 'border-sky-200 bg-sky-50 text-sky-700'
                : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50';
        },
        init() {
            this.initAlertsGrid();
            window.closeAlertPanel = () => this.closeForm();
            window.alert_form = (_trigger, _action, id = 0) => this.openForm(id);
            window.delete_record = (_trigger, id) => this.deleteAlert(id);
            window.update_alert = (input, id, column) => this.toggleAlert(input, id, column);
        },
        initAlertsGrid() {
            const el = document.getElementById('alerts-grid');
            if (!el || this.alertsGrid) return;
            this.alertsGrid = Perfectlum.createGrid(el, {
                columns: [
                    { name: 'Recipient', formatter: (_, row) => gridjs.html(`<div><div class=\"text-sm font-semibold text-slate-900\">${this.escapeHtml(row.cells[0].data.email)}</div><div class=\"text-xs text-slate-500\">${this.escapeHtml(row.cells[0].data.facName || 'No facility scope')}</div></div>`) },
                    { name: 'Daily Report', width: '140px', sort: false, formatter: (_, row) => gridjs.html(this.renderToggle(row.cells[0].data.id, row.cells[0].data.dailyReport, 'daily_report')) },
                    { name: 'Active', width: '120px', sort: false, formatter: (_, row) => gridjs.html(this.renderToggle(row.cells[0].data.id, row.cells[0].data.active, 'active')) },
                    { name: 'Actions', width: '120px', sort: false, formatter: (_, row) => { const item=row.cells[0].data; const editButton=`<button onclick=\"window.alert_form(this,'edit',${item.id})\" class=\"inline-flex h-8 w-8 items-center justify-center rounded-lg bg-sky-50 text-sky-600 transition hover:bg-sky-100\"><svg class=\"h-4 w-4\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"/><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"/></svg></button>`; const deleteButton=@json($canManageAlertSettings)?`<button onclick=\"window.delete_record(this,${item.id})\" class=\"inline-flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition hover:bg-rose-100\"><svg class=\"h-4 w-4\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\"><path d=\"M3 6h18\"/><path d=\"M8 6V4h8v2\"/><path d=\"m19 6-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6\"/><path d=\"M10 11v6\"/><path d=\"M14 11v6\"/></svg></button>`:''; return gridjs.html(`<div class=\"flex items-center gap-2\">${editButton}${deleteButton}</div>`); } }
                ],
                server: { url: '/api/alerts', then: (payload) => payload.data.map((item) => [item]), total: (payload) => payload.total },
                search: { enabled: true, server: { url: (prev, keyword) => `${prev}${prev.includes('?') ? '&' : '?'}search=${encodeURIComponent(keyword)}` } },
                pagination: { enabled: true, limit: 10, server: { url: (prev, page, limit) => `${prev}${prev.includes('?') ? '&' : '?'}page=${page + 1}&limit=${limit}` } },
                sort: true,
                className: { td: 'align-middle' },
                language: { search: { placeholder: @json($alertText['searchAlertEmails']) } }
            });
        },
        renderToggle(id, enabled, column) {
            const checked = enabled ? 'checked' : '';
            const disabled = @json(!$canManageAlertSettings) ? 'disabled' : '';
            return `<label class=\"relative inline-flex items-center ${disabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer'}\"><input type=\"checkbox\" class=\"peer sr-only\" ${checked} ${disabled} onchange=\"window.update_alert(this, ${id}, '${column}')\"><span class=\"h-6 w-11 rounded-full bg-slate-200 transition peer-checked:bg-sky-500\"></span><span class=\"absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition peer-checked:translate-x-5\"></span></label>`;
        },
        async openForm(id) {
            if (this.formCloseTimer) {
                clearTimeout(this.formCloseTimer);
                this.formCloseTimer = null;
            }
            this.formOpen = true; this.formLoading = true; this.formHtml = '';
            const formData = new FormData(); formData.append('_token', @json(csrf_token())); formData.append('id', id);
            try { const payload = await Perfectlum.postForm(@json(url('alert-form')), formData); this.formHtml = payload.content || ''; this.$nextTick(() => this.bindLoadedForm()); }
            catch (_) { this.formHtml = `<div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-600">${@json(__('Failed to load the alert form.'))}</div>`; }
            finally { this.formLoading = false; }
        },
        bindLoadedForm() {
            const form = document.querySelector('#alert-form-box form'); if (!form) return;
            this.initLoadedFormUi(form);
            form.addEventListener('submit', async (event) => { event.preventDefault(); await Perfectlum.postForm(@json(url('alert-settings')), new FormData(form)); this.closeForm(); this.reloadAlertsGrid(); notify('success', @json(__('Alert saved successfully.'))); }, { once: true });
        },
        initLoadedFormUi(form) {
            const root = form.querySelector('[data-searchable-select="facility"]');
            if (!root) return;
            const hiddenInput = root.querySelector('input[name="facility_id"]');
            const trigger = root.querySelector('[data-role="trigger"]');
            const label = root.querySelector('[data-role="label"]');
            const panel = root.querySelector('[data-role="panel"]');
            const search = root.querySelector('[data-role="search"]');
            const empty = root.querySelector('[data-role="empty"]');
            const options = Array.from(root.querySelectorAll('[data-role="option"]'));

            const syncLabel = () => {
                const current = options.find((item) => item.dataset.value === String(hiddenInput?.value ?? ''));
                if (label) label.textContent = current?.dataset.label || @json(__('All facilities'));
            };

            const applySearch = () => {
                const keyword = String(search?.value || '').trim().toLowerCase();
                let visible = 0;
                options.forEach((option) => {
                    const matches = !keyword || String(option.dataset.label || '').toLowerCase().includes(keyword);
                    option.classList.toggle('hidden', !matches);
                    if (matches) visible += 1;
                });
                if (empty) empty.classList.toggle('hidden', visible > 0);
            };

            const closePanel = () => {
                panel?.classList.add('hidden');
                if (search) search.value = '';
                applySearch();
            };

            trigger?.addEventListener('click', () => {
                panel?.classList.toggle('hidden');
                if (panel && !panel.classList.contains('hidden')) {
                    search?.focus();
                    applySearch();
                }
            });

            options.forEach((option) => {
                option.addEventListener('click', () => {
                    if (hiddenInput) hiddenInput.value = option.dataset.value || '';
                    syncLabel();
                    closePanel();
                });
            });

            search?.addEventListener('input', applySearch);

            document.addEventListener('click', (event) => {
                if (!root.contains(event.target)) closePanel();
            });

            syncLabel();
            applySearch();
        },
        closeForm() {
            if (!this.formOpen && !this.formLoading && !this.formHtml) return;
            if (this.formCloseTimer) {
                clearTimeout(this.formCloseTimer);
                this.formCloseTimer = null;
            }
            this.formOpen = false;
            this.formLoading = false;
            this.formCloseTimer = setTimeout(() => {
                this.formHtml = '';
                this.formCloseTimer = null;
            }, 240);
        },
        async deleteAlert(id) {
            if (!confirm(@json($alertText['deleteThisAlert']))) return;
            const formData = new FormData(); formData.append('_token', @json(csrf_token())); formData.append('id', id);
            try { const payload = await Perfectlum.postForm(@json(url('delete-alert')), formData); if (!payload.success) { notify('error', payload.msg || 'Failed to delete alert.'); return; } this.reloadAlertsGrid(); notify('success', payload.msg || 'Alert deleted successfully.'); }
            catch (_) { notify('error', 'Failed to delete alert.'); }
        },
        async toggleAlert(input, id, column) {
            if (@json(!$canManageAlertSettings)) { input.checked = !input.checked; return; }
            const formData = new FormData(); formData.append('_token', @json(csrf_token())); formData.append('id', id); formData.append('column', column); formData.append('value', input.checked ? 1 : 0);
            try { const payload = await Perfectlum.postForm(@json(url('update-alert')), formData); if (!payload.success) throw new Error(); }
            catch (_) { input.checked = !input.checked; notify('error', 'Failed to update the alert state.'); }
        },
        async submitLimits() {
            this.savingLimits = true;
            try { const payload = await Perfectlum.postForm(@json(url('errorlimit-update')), new FormData(document.getElementById('limit-form'))); notify(payload.success ? 'success' : 'error', payload.msg || 'Failed to update error limits.'); }
            catch (_) { notify('error', 'Failed to update error limits.'); }
            finally { this.savingLimits = false; }
        },
        async submitSmtp() {
            this.savingSmtp = true;
            try { const payload = await Perfectlum.postForm(@json(url('errorsmtp-update')), new FormData(document.getElementById('smtp-form'))); notify(payload.success ? 'success' : 'error', payload.msg || @json($alertText['failedToUpdateSmtpSettings'])); }
            catch (_) { notify('error', @json($alertText['failedToUpdateSmtpSettings'])); }
            finally { this.savingSmtp = false; }
        },
        async sendTestEmail() {
            if (!this.testEmail) { this.testResultType = 'error'; this.testResult = 'Please enter an email address for the test message.'; return; }
            this.sendingTest = true; this.testResult = 'Sending test email...'; this.testResultType = 'success';
            const formData = new FormData(); formData.append('_token', @json(csrf_token())); formData.append('email', this.testEmail);
            try {
                const response = await fetch(@json(url('sendtestmail')), { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: formData });
                const contentType = response.headers.get('content-type') || '';
                if (!response.ok) { const payload = contentType.includes('application/json') ? await response.json() : { message: await response.text() }; throw new Error(payload.message || 'Failed to send test email.'); }
                this.testResult = contentType.includes('application/json') ? ((await response.json()).message || 'Test email sent.') : await response.text();
                this.testResultType = 'success';
            } catch (error) { this.testResultType = 'error'; this.testResult = error.message || 'Failed to send test email.'; }
            finally { this.sendingTest = false; }
        },
        reloadAlertsGrid() { const wrapper = document.getElementById('alerts-grid'); if (!wrapper) return; wrapper.innerHTML = ''; this.alertsGrid = null; this.initAlertsGrid(); },
        escapeHtml(value) { return String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\"/g, '&quot;').replace(/'/g, '&#039;'); }
    };
}
</script>
