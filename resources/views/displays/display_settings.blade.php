@include('common.navigations.header')

@php
    $role = session('role') ?? 'user';
    $canManageDisplaySettings = in_array($role, ['super', 'admin'], true);
@endphp

<div x-data="{ activeTab: 'settings' }" class="flex flex-col gap-6 pb-8">
    <x-page-header
        title="Display Settings"
        description="Kelola detail teknis dan finansial display tanpa Bootstrap tab lama."
        icon="monitor-cog"
    />

    <div class="grid gap-6 xl:grid-cols-[320px_minmax(0,1fr)]">
        <x-bento-card title="Select Display" dot-color="sky">
            <div class="space-y-4 p-6">
                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Facility</span>
                    <select id="facilities_field" onchange="fetch_workgroups(this)" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <option value="">Please select</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}" @if($facility_id == $facility->id) selected @endif>{{ $facility->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Workgroup</span>
                    <select id="workgroups_field" onchange="fetch_workstations(this)" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <option value="">Select facility first</option>
                        @foreach($workgroups as $workgroup)
                            <option value="{{ $workgroup->id }}" @if($workgroup_id == $workgroup->id) selected @endif>{{ $workgroup->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Workstation</span>
                    <select id="workstations_field" onchange="fetch_displays(this)" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <option value="">Select workgroup first</option>
                        @foreach($workstations as $workstation)
                            <option value="{{ $workstation->id }}" @if($workstation_id == $workstation->id) selected @endif>{{ $workstation->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Display</span>
                    <select id="displays_field" onchange="fetch_data(this)" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <option value="">Select display</option>
                    </select>
                </label>

                <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Current Display</p>
                    <p id="display_name" class="mt-2 text-sm font-semibold text-slate-900">Loading...</p>
                    <p id="display_resolution_summary" class="mt-1 text-sm text-slate-500">Resolution will appear here.</p>
                </div>
            </div>
        </x-bento-card>

        <div class="space-y-6">
            <x-bento-card>
                <div class="border-b border-slate-200 px-6 py-4">
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            @click="activeTab = 'settings'"
                            :class="activeTab === 'settings' ? 'bg-sky-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="rounded-full px-4 py-2 text-sm font-semibold transition"
                        >
                            Settings
                        </button>
                        <button
                            type="button"
                            @click="activeTab = 'financial'"
                            :class="activeTab === 'financial' ? 'bg-sky-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="rounded-full px-4 py-2 text-sm font-semibold transition"
                        >
                            Financial Status
                        </button>
                    </div>
                </div>

                <div class="space-y-6 p-6">
                    <div x-show="activeTab === 'settings'" x-cloak>
                        <form id="displaysetting" class="grid gap-4 md:grid-cols-2">
                            {{ csrf_field() }}

                            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 md:col-span-2">
                                <input class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/30" type="checkbox" name="exclude" id="exclude" value="1">
                                <span>Exclude Display from Testing / Calibration</span>
                            </label>

                            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 md:col-span-2">
                                <input class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/30" type="checkbox" id="CommunicationType" name="CommunicationType" value="1">
                                <span>Use graphicboard LUTs only</span>
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Save Calibration To</span>
                                <select class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" id="lut_names" name="CurrentLUTIndex"></select>
                            </label>

                            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                <input class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/30" type="checkbox" id="InternalSensor" name="InternalSensor" value="1">
                                <span>Use internal sensor if possible</span>
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Display Model</span>
                                <input class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" type="text" id="Model" name="Model" required>
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Display Serial Number</span>
                                <input class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" type="text" id="SerialNumber" name="SerialNumber">
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Display Manufacturer</span>
                                <input class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" type="text" id="Manufacturer" name="Manufacturer">
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Inventory Number</span>
                                <input class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" type="text" id="InventoryNumber" name="InventoryNumber">
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Type of Display</span>
                                <select class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" id="TypeOfDisplay" name="TypeOfDisplay"></select>
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Display Technology</span>
                                <select class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" id="DisplayTechnology" name="DisplayTechnology"></select>
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Screen Size</span>
                                <input class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" type="text" id="ScreenSize" name="ScreenSize">
                            </label>

                            <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)] md:col-span-2">
                                <label class="block">
                                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Resolution Horizontal</span>
                                    <div class="relative">
                                        <input class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 pr-12 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" type="text" autocomplete="off" id="ResolutionHorizontal" name="ResolutionHorizontal">
                                        <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-xs text-slate-400">px</span>
                                    </div>
                                </label>
                                <label class="block">
                                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Resolution Vertical</span>
                                    <div class="relative">
                                        <input class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 pr-12 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" type="text" autocomplete="off" id="ResolutionVertical" name="ResolutionVertical">
                                        <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-xs text-slate-400">px</span>
                                    </div>
                                </label>
                            </div>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Backlight Stabilization</span>
                                <select class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" id="BacklightStabilization" name="BacklightStabilization"></select>
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Installation Date</span>
                                {{ Form::date('InstalationDate', null, ['class' => 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20']) }}
                            </label>

                            @if($canManageDisplaySettings)
                                <div class="md:col-span-2 flex justify-end">
                                    <button class="rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-400" type="button" id="display_save">
                                        Save Changes
                                    </button>
                                </div>
                            @endif
                        </form>
                    </div>

                    <div x-show="activeTab === 'financial'" x-cloak class="space-y-6">
                        <form id="financial_f" class="grid gap-4 md:grid-cols-2">
                            {{ csrf_field() }}

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Date Of Purchase / Lease</span>
                                {{ Form::date('purchase_date', null, ['class' => 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20']) }}
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Initial Value</span>
                                {{ Form::text('initial_value', null, ['class' => 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20']) }}
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Expected Value At Warranty End</span>
                                {{ Form::text('expected_value', null, ['class' => 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20']) }}
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Annual Straight Line Depreciation</span>
                                {{ Form::text('annual_straight_line', null, ['class' => 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20']) }}
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Monthly Straight Line Depreciation</span>
                                {{ Form::text('monthly_straight_line', null, ['class' => 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20']) }}
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Current Value</span>
                                {{ Form::text('current_value', null, ['class' => 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20']) }}
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Expected Replacement Date</span>
                                {{ Form::date('expected_replacement_date', null, ['class' => 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20']) }}
                            </label>

                            @if($canManageDisplaySettings)
                                <div class="md:col-span-2 flex justify-end">
                                    <button class="rounded-xl bg-sky-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-400" type="button" id="save-financial">
                                        Save Financial Status
                                    </button>
                                </div>
                            @endif
                        </form>

                        <div class="grid gap-4 md:grid-cols-3">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Current Value</p>
                                <p id="financial_current_value" class="mt-2 text-lg font-semibold text-slate-900">-</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Monthly Depreciation</p>
                                <p id="financial_monthly_value" class="mt-2 text-lg font-semibold text-slate-900">-</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Replacement Date</p>
                                <p id="financial_replacement_date" class="mt-2 text-lg font-semibold text-slate-900">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </x-bento-card>
        </div>
    </div>
</div>

@include('common.navigations.footer')

<script>
    (function () {
        const displayId = @json((string) $display_id);
        const canManage = @json($canManageDisplaySettings);

        function parseOptionSource(value) {
            if (!value) return [];
            try {
                const parsed = JSON.parse(value);
                if (Array.isArray(parsed)) {
                    return parsed.map((item, index) => ({ value: index, label: item }));
                }
                return Object.entries(parsed).map(([key, label]) => ({ value: key, label }));
            } catch (error) {
                return [];
            }
        }

        function populateSelect(selectId, options, selectedValue) {
            const select = document.getElementById(selectId);
            if (!select) return;
            select.innerHTML = '';

            options.forEach((option) => {
                const el = document.createElement('option');
                el.value = option.value;
                el.textContent = option.label;
                if (String(option.value) === String(selectedValue)) {
                    el.selected = true;
                }
                select.appendChild(el);
            });
        }

        function setFieldValue(selector, value) {
            const field = document.querySelector(selector);
            if (!field) return;
            field.value = value ?? '';
        }

        function setCheckboxValue(selector, value) {
            const field = document.querySelector(selector);
            if (!field) return;
            field.checked = value === 1 || value === '1' || value === true || value === 'true';
        }

        function updateSummary(data) {
            const displayName = data.treeText || `${data.Manufacturer || ''} ${data.Model || ''} (${data.SerialNumber || ''})`;
            document.getElementById('display_name').textContent = displayName || '-';
            document.getElementById('display_resolution_summary').textContent = `${data.ResolutionHorizontal || '-'} x ${data.ResolutionVertical || '-'} px`;
            document.getElementById('financial_current_value').textContent = data.current_value || '-';
            document.getElementById('financial_monthly_value').textContent = data.monthly_straight_line || '-';
            document.getElementById('financial_replacement_date').textContent = data.expected_replacement_date || '-';
        }

        function fillForm(payload) {
            const data = payload.data || {};
            const options = payload.options || {};

            setCheckboxValue('#exclude', data.exclude);
            setCheckboxValue('#CommunicationType', data.CommunicationType === '1' || data.CommunicationType === 1);
            setCheckboxValue('#InternalSensor', data.InternalSensor);

            [
                'Model',
                'SerialNumber',
                'Manufacturer',
                'InventoryNumber',
                'ScreenSize',
                'ResolutionHorizontal',
                'ResolutionVertical',
                'purchase_date',
                'initial_value',
                'expected_value',
                'annual_straight_line',
                'monthly_straight_line',
                'current_value',
                'expected_replacement_date'
            ].forEach((key) => setFieldValue(`[name="${key}"]`, data[key]));

            setFieldValue('[name="InstalationDate"]', data.InstalationDate);

            populateSelect('lut_names', parseOptionSource(options.lut_names), data.CurrentLUTIndex);
            populateSelect('TypeOfDisplay', parseOptionSource(options.TypeOfDisplay), data.TypeOfDisplay);
            populateSelect('DisplayTechnology', parseOptionSource(options.DisplayTechnology), data.DisplayTechnology);
            populateSelect('BacklightStabilization', parseOptionSource(options.BacklightStabilization), data.BacklightStabilization);

            updateSummary(data);
        }

        async function loadDisplay(id) {
            const formData = new FormData();
            formData.append('_token', @json(csrf_token()));
            formData.append('id', id);

            try {
                const response = await fetch(@json(url('displaysettings')) + `/${id}`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const payload = await response.json();
                fillForm(payload);
            } catch (error) {
                notify('failed', 'Failed to load display settings.');
            }
        }

        async function saveDisplaySettings() {
            if (!canManage) return;
            const response = await fetch(@json(url('displaysettings/save')) + `/${displayId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(document.getElementById('displaysetting'))
            });

            if (!response.ok) {
                notify('failed', 'Failed to save display settings.');
                return;
            }

            notify('success', 'Display settings saved successfully.');
            loadDisplay(displayId);
        }

        async function saveFinancialSettings() {
            if (!canManage) return;

            const purchaseDate = document.querySelector('#financial_f input[name="purchase_date"]').value;
            const initialValue = document.querySelector('#financial_f input[name="initial_value"]').value;

            if (!purchaseDate) {
                notify('failed', 'Please select a purchase date.');
                return;
            }

            if (!initialValue) {
                notify('failed', 'Please enter initial value.');
                return;
            }

            const response = await fetch(@json(url('displaysettings/save/finance')) + `/${displayId}`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(document.getElementById('financial_f'))
            });

            if (!response.ok) {
                notify('failed', 'Failed to save financial status.');
                return;
            }

            notify('success', 'Financial status saved successfully.');
            loadDisplay(displayId);
        }

        async function postOptions(url, value) {
            const formData = new FormData();
            formData.append('_token', @json(csrf_token()));
            formData.append('id', value || '');
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            return response.json();
        }

        window.fetch_workgroups = async function (el) {
            const data = await postOptions(@json(url('fetch-groups')), el.value);
            if (!data.success) return;
            document.getElementById('workgroups_field').innerHTML = data.content;
            document.getElementById('workstations_field').innerHTML = '<option value="">Select workgroup first</option>';
            document.getElementById('displays_field').innerHTML = '<option value="">Select display</option>';
        };

        window.fetch_workstations = async function (el) {
            const data = await postOptions(@json(url('fetch-workstations')), el.value);
            if (!data.success) return;
            document.getElementById('workstations_field').innerHTML = data.content;
            document.getElementById('displays_field').innerHTML = '<option value="">Select display</option>';
        };

        window.fetch_displays = async function (el) {
            const data = await postOptions(@json(url('fetch-displays')), el.value);
            if (!data.success) return;
            document.getElementById('displays_field').innerHTML = data.content;
            document.getElementById('displays_field').value = displayId;
        };

        window.fetch_data = function (el) {
            const nextId = el.value;
            if (!nextId) return;
            window.location = @json(url('display-settings')) + `/${nextId}`;
        };

        document.getElementById('display_save')?.addEventListener('click', saveDisplaySettings);
        document.getElementById('save-financial')?.addEventListener('click', saveFinancialSettings);

        document.addEventListener('DOMContentLoaded', async function () {
            await fetch_displays(document.getElementById('workstations_field'));
            document.getElementById('displays_field').value = displayId;
            loadDisplay(displayId);
        });
    })();
</script>
