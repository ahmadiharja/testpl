@include('common.navigations.header')

@php
    $type = request('type', '');
    $role = session('role');
    $canManageDisplays = in_array($role, ['super', 'admin'], true);
    $initialDisplayStatus = in_array($type, ['ok', 'failed'], true) ? $type : '';
@endphp

<div class="flex flex-col gap-6 pb-8">
    <x-page-header title="All Displays" description="Manage and monitor all diagnostic displays across facilities." icon="monitor">
        <x-slot name="actions">
            <x-export-dropdown
                excel-url="{{ url('reports/displays?export_type=excel&type=' . $type) }}"
                pdf-url="{{ url('reports/displays?export_type=pdf&type=' . $type) }}"
                label="Export Report" />
        </x-slot>
    </x-page-header>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.18)]">
        <div class="flex flex-wrap items-end gap-4 xl:flex-nowrap">
            <div class="min-w-0 flex-1 space-y-2 xl:w-[220px] xl:flex-none">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Facility</label>
                <div class="relative">
                    <button
                        id="display-facility-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400">
                        <span id="display-facility-label" class="truncate">All facilities</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>
                    <div id="display-facility-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input id="display-facility-search" type="text" placeholder="Search facilities..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="display-facility-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="display-facility-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="min-w-0 flex-1 space-y-2 xl:w-[220px] xl:flex-none">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Workgroup</label>
                <div class="relative">
                    <button
                        id="display-workgroup-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400">
                        <span id="display-workgroup-label" class="truncate">All workgroups</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>
                    <div id="display-workgroup-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input id="display-workgroup-search" type="text" placeholder="Search workgroups..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="display-workgroup-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="display-workgroup-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="min-w-0 flex-1 space-y-2 xl:w-[220px] xl:flex-none">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Workstation</label>
                <div class="relative">
                    <button
                        id="display-workstation-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400">
                        <span id="display-workstation-label" class="truncate">All workstations</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>
                    <div id="display-workstation-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input id="display-workstation-search" type="text" placeholder="Search workstations..." class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="display-workstation-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="display-workstation-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="min-w-0 flex-1 space-y-2 xl:w-[280px] xl:flex-none">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Status</label>
                <div class="grid h-12 grid-cols-3 rounded-2xl border border-slate-200 bg-white p-1">
                    <button
                        id="display-status-all"
                        type="button"
                        data-status=""
                        class="rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="layers-3" class="h-4 w-4"></i>
                            <span>All</span>
                        </span>
                    </button>
                    <button
                        id="display-status-ok"
                        type="button"
                        data-status="ok"
                        class="rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="badge-check" class="h-4 w-4"></i>
                            <span>OK</span>
                        </span>
                    </button>
                    <button
                        id="display-status-failed"
                        type="button"
                        data-status="failed"
                        class="rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="triangle-alert" class="h-4 w-4"></i>
                            <span>Not OK</span>
                        </span>
                    </button>
                </div>
            </div>

            <div class="flex items-end justify-end xl:ml-auto xl:flex-none">
                <button
                    id="reset-display-filters"
                    type="button"
                    class="inline-flex h-12 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    Reset Filters
                </button>
            </div>
        </div>
    </section>

    <x-data-table id="displays-grid" class="mb-10 workstation-table-shell" />
</div>

<div id="display-action-overlay" class="pointer-events-none fixed inset-0 z-[1200] hidden">
    <div id="display-action-menu" class="pointer-events-auto fixed hidden w-56 rounded-2xl border border-slate-200 bg-white p-2 shadow-[0_20px_60px_-28px_rgba(15,23,42,0.35)]">
        @if($canManageDisplays)
            <button id="display-action-edit" type="button" class="flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
                <i data-lucide="pencil-line" class="h-4 w-4"></i>
                Edit Display
            </button>
            <button id="display-action-delete" type="button" class="flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
                Delete Display
            </button>
        @endif
    </div>
</div>

<div id="display-edit-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="flex max-h-[88vh] w-full max-w-4xl flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="flex shrink-0 items-start justify-between border-b border-slate-200 px-6 py-5">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Edit Display</p>
                <h3 class="mt-2 text-2xl font-semibold text-slate-900">Quick display update</h3>
                <p id="display-edit-subtitle" class="mt-2 text-sm text-slate-500"></p>
            </div>
            <button id="display-edit-close" type="button" class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <form id="display-edit-form" class="flex min-h-0 flex-1 flex-col px-6 py-6">
            <div id="display-edit-loading" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                Loading display form...
            </div>
            <div id="display-edit-error" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700"></div>

            <div id="display-edit-body" class="hidden min-h-0 flex-1 space-y-5 overflow-y-auto pr-1">
                <section class="rounded-[1.5rem] border border-slate-200 bg-slate-50/70 p-4">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Calibration</p>
                            <h4 class="mt-1 text-base font-semibold text-slate-900">General settings</h4>
                        </div>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-2">
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Manufacturer</span>
                            <input name="Manufacturer" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Model</span>
                            <input name="Model" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Serial Number</span>
                            <input name="SerialNumber" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Inventory Number</span>
                            <input name="InventoryNumber" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Type of Display</span>
                            <select name="TypeOfDisplay" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"></select>
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Display Technology</span>
                            <select name="DisplayTechnology" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"></select>
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Screen Size</span>
                            <input name="ScreenSize" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Current LUT Index</span>
                            <input name="CurrentLUTIndex" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Resolution Horizontal</span>
                            <input name="ResolutionHorizontal" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Resolution Vertical</span>
                            <input name="ResolutionVertical" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Installation Date</span>
                            <input name="InstalationDate" type="date" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 lg:col-span-2">
                            <p class="text-sm font-medium text-slate-700">Calibration Options</p>
                            <div class="mt-4 space-y-3">
                                <label class="flex items-center gap-3 text-sm text-slate-600">
                                    <input name="exclude" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                    Exclude display from testing / calibration
                                </label>
                                <label class="flex items-center gap-3 text-sm text-slate-600">
                                    <input name="graphicboardOnly" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                    Use graphicboard LUTs only
                                </label>
                                <label class="flex items-center gap-3 text-sm text-slate-600">
                                    <input name="InternalSensor" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                    Use internal sensor if possible
                                </label>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-[1.5rem] border border-slate-200 bg-slate-50/70 p-4">
                    <div class="mb-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Financial</p>
                        <h4 class="mt-1 text-base font-semibold text-slate-900">Lifecycle values</h4>
                    </div>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Purchase Date</span>
                            <input name="purchase_date" type="date" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Expected Replacement Date</span>
                            <input name="expected_replacement_date" type="date" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Initial Value</span>
                            <input name="initial_value" type="number" step="0.01" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Expected Value</span>
                            <input name="expected_value" type="number" step="0.01" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Annual Straight Line</span>
                            <input name="annual_straight_line" type="number" step="0.01" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">Monthly Straight Line</span>
                            <input name="monthly_straight_line" type="number" step="0.01" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2 lg:col-span-2">
                            <span class="block text-sm font-medium text-slate-700">Current Value</span>
                            <input name="current_value" type="number" step="0.01" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                    </div>
                </section>
            </div>

            <div class="mt-5 flex shrink-0 justify-end gap-3 border-t border-slate-200 pt-5">
                <button id="display-edit-cancel" type="button" class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Cancel
                </button>
                <button id="display-edit-save" type="submit" class="inline-flex h-11 items-center rounded-2xl bg-sky-500 px-4 text-sm font-semibold text-white transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-60">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<div id="display-delete-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="w-full max-w-lg rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="border-b border-slate-200 px-6 py-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rose-400">Delete Display</p>
            <h3 class="mt-2 text-2xl font-semibold text-slate-900">Delete this display?</h3>
            <p class="mt-3 text-sm text-slate-500">
                This action will permanently remove <span id="display-delete-name" class="font-semibold text-slate-700"></span>.
            </p>
        </div>

        <div class="flex justify-end gap-3 px-6 py-5">
            <button id="display-delete-cancel" type="button" class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                Cancel
            </button>
            <button id="display-delete-confirm" type="button" class="inline-flex h-11 items-center rounded-2xl bg-rose-500 px-4 text-sm font-semibold text-white transition hover:bg-rose-400 disabled:cursor-not-allowed disabled:opacity-60">
                Delete Display
            </button>
        </div>
    </div>
</div>

<script id="display-filters-data" type="application/json">@json($filters)</script>
<script>
(function () {
    const canManageDisplays = @json($canManageDisplays);
    let initialized = false;
    const state = {
        config: { canChooseFacility: false, facilities: [], workgroupsByFacility: {}, workstationsByWorkgroup: {}, selectedFacilityId: '', selectedWorkgroupId: '', selectedWorkstationId: '' },
        selectedFacilityId: '',
        selectedWorkgroupId: '',
        selectedWorkstationId: '',
        defaultStatus: @json($initialDisplayStatus),
        selectedStatus: @json($initialDisplayStatus),
        facilitySearch: '',
        workgroupSearch: '',
        workstationSearch: '',
        activeDropdown: null,
        actionTarget: null,
        deleteTarget: null,
        editTarget: null,
        grid: null,
    };

    const els = {};

    function init() {
        if (initialized) return;
        if (!window.Perfectlum || !window.gridjs) {
            window.setTimeout(init, 50);
            return;
        }

        initialized = true;
        try {
            state.config = JSON.parse(document.getElementById('display-filters-data')?.textContent || '{}');
        } catch (error) {
            state.config = { canChooseFacility: false, facilities: [], workgroupsByFacility: {}, workstationsByWorkgroup: {}, selectedFacilityId: '', selectedWorkgroupId: '', selectedWorkstationId: '' };
        }

        state.selectedFacilityId = state.config.selectedFacilityId || '';
        state.selectedWorkgroupId = state.config.selectedWorkgroupId || '';
        state.selectedWorkstationId = state.config.selectedWorkstationId || '';

        bindElements();
        bindEvents();
        renderFilters();
        initGrid();
        window.displaysPage = { toggleActionMenu };
        window.openDisplayModal = function (displayId, options = {}) {
            window.dispatchEvent(new CustomEvent('open-hierarchy', {
                detail: { type: 'display', id: displayId, ...options }
            }));
        };
        window.lucide?.createIcons();
    }

    function bindElements() {
        els.facilityTrigger = document.getElementById('display-facility-trigger');
        els.facilityLabel = document.getElementById('display-facility-label');
        els.facilityPanel = document.getElementById('display-facility-panel');
        els.facilitySearch = document.getElementById('display-facility-search');
        els.facilityHint = document.getElementById('display-facility-hint');
        els.facilityOptions = document.getElementById('display-facility-options');

        els.workgroupTrigger = document.getElementById('display-workgroup-trigger');
        els.workgroupLabel = document.getElementById('display-workgroup-label');
        els.workgroupPanel = document.getElementById('display-workgroup-panel');
        els.workgroupSearch = document.getElementById('display-workgroup-search');
        els.workgroupHint = document.getElementById('display-workgroup-hint');
        els.workgroupOptions = document.getElementById('display-workgroup-options');

        els.workstationTrigger = document.getElementById('display-workstation-trigger');
        els.workstationLabel = document.getElementById('display-workstation-label');
        els.workstationPanel = document.getElementById('display-workstation-panel');
        els.workstationSearch = document.getElementById('display-workstation-search');
        els.workstationHint = document.getElementById('display-workstation-hint');
        els.workstationOptions = document.getElementById('display-workstation-options');

        els.statusButtons = [
            document.getElementById('display-status-all'),
            document.getElementById('display-status-ok'),
            document.getElementById('display-status-failed'),
        ].filter(Boolean);

        els.resetFilters = document.getElementById('reset-display-filters');
        els.grid = document.getElementById('displays-grid');

        els.actionOverlay = document.getElementById('display-action-overlay');
        els.actionMenu = document.getElementById('display-action-menu');
        els.actionEdit = document.getElementById('display-action-edit');
        els.actionDelete = document.getElementById('display-action-delete');

        els.editModal = document.getElementById('display-edit-modal');
        els.editSubtitle = document.getElementById('display-edit-subtitle');
        els.editClose = document.getElementById('display-edit-close');
        els.editCancel = document.getElementById('display-edit-cancel');
        els.editForm = document.getElementById('display-edit-form');
        els.editBody = document.getElementById('display-edit-body');
        els.editLoading = document.getElementById('display-edit-loading');
        els.editError = document.getElementById('display-edit-error');
        els.editSave = document.getElementById('display-edit-save');

        els.deleteModal = document.getElementById('display-delete-modal');
        els.deleteName = document.getElementById('display-delete-name');
        els.deleteCancel = document.getElementById('display-delete-cancel');
        els.deleteConfirm = document.getElementById('display-delete-confirm');
    }

    function bindEvents() {
        els.facilityTrigger?.addEventListener('click', () => toggleDropdown('facility'));
        els.workgroupTrigger?.addEventListener('click', () => toggleDropdown('workgroup'));
        els.workstationTrigger?.addEventListener('click', () => toggleDropdown('workstation'));

        els.facilitySearch?.addEventListener('input', (event) => { state.facilitySearch = event.target.value || ''; renderFacilityOptions(); });
        els.workgroupSearch?.addEventListener('input', (event) => { state.workgroupSearch = event.target.value || ''; renderWorkgroupOptions(); });
        els.workstationSearch?.addEventListener('input', (event) => { state.workstationSearch = event.target.value || ''; renderWorkstationOptions(); });
        els.statusButtons.forEach((button) => {
            button.addEventListener('click', () => {
                state.selectedStatus = button.dataset.status || '';
                renderStatusFilter();
                reloadGrid();
            });
        });
        els.resetFilters?.addEventListener('click', resetFilters);

        document.addEventListener('click', (event) => {
            if (state.activeDropdown === 'facility' && !els.facilityPanel.contains(event.target) && !els.facilityTrigger.contains(event.target)) closeDropdown();
            if (state.activeDropdown === 'workgroup' && !els.workgroupPanel.contains(event.target) && !els.workgroupTrigger.contains(event.target)) closeDropdown();
            if (state.activeDropdown === 'workstation' && !els.workstationPanel.contains(event.target) && !els.workstationTrigger.contains(event.target)) closeDropdown();
            if (els.actionOverlay && !els.actionMenu.contains(event.target)) closeActionMenu();
        });

        els.actionOverlay?.addEventListener('click', closeActionMenu);
        els.actionEdit?.addEventListener('click', () => {
            if (!state.actionTarget) return;
            openEditModal(state.actionTarget.id, state.actionTarget.name);
        });
        els.actionDelete?.addEventListener('click', () => state.actionTarget && openDeleteModal(state.actionTarget.id, state.actionTarget.name));

        els.editClose?.addEventListener('click', closeEditModal);
        els.editCancel?.addEventListener('click', closeEditModal);
        els.editModal?.addEventListener('click', (event) => {
            if (event.target === els.editModal) closeEditModal();
        });
        els.editForm?.addEventListener('submit', submitEditForm);

        els.deleteCancel?.addEventListener('click', closeDeleteModal);
        els.deleteConfirm?.addEventListener('click', confirmDelete);
        els.deleteModal?.addEventListener('click', (event) => {
            if (event.target === els.deleteModal) closeDeleteModal();
        });
    }

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function getFacilityOptions() {
        return Array.isArray(state.config.facilities) ? state.config.facilities : [];
    }

    function getWorkgroupOptions() {
        if (!state.selectedFacilityId) return [];
        return Array.isArray(state.config.workgroupsByFacility?.[String(state.selectedFacilityId)]) ? state.config.workgroupsByFacility[String(state.selectedFacilityId)] : [];
    }

    function getWorkstationOptions() {
        if (!state.selectedWorkgroupId) return [];
        return Array.isArray(state.config.workstationsByWorkgroup?.[String(state.selectedWorkgroupId)]) ? state.config.workstationsByWorkgroup[String(state.selectedWorkgroupId)] : [];
    }

    function findOptionLabel(options, value, fallback) {
        const match = options.find((item) => String(item.id) === String(value));
        return match?.name || fallback;
    }

    function renderFilters() {
        const facilities = getFacilityOptions();
        const workgroups = getWorkgroupOptions();
        const workstations = getWorkstationOptions();

        els.facilityTrigger.disabled = !state.config.canChooseFacility && facilities.length <= 1;
        els.workgroupTrigger.disabled = !workgroups.length;
        els.workstationTrigger.disabled = !workstations.length;

        els.facilityLabel.textContent = state.selectedFacilityId ? findOptionLabel(facilities, state.selectedFacilityId, 'Select facility') : 'All facilities';
        els.workgroupLabel.textContent = state.selectedWorkgroupId ? findOptionLabel(workgroups, state.selectedWorkgroupId, 'Select workgroup') : 'All workgroups';
        els.workstationLabel.textContent = state.selectedWorkstationId ? findOptionLabel(workstations, state.selectedWorkstationId, 'Select workstation') : 'All workstations';

        renderFacilityOptions();
        renderWorkgroupOptions();
        renderWorkstationOptions();
        renderStatusFilter();
    }

    function renderStatusFilter() {
        els.statusButtons.forEach((button) => {
            const status = button.dataset.status || '';
            const active = status === (state.selectedStatus || '');

            const activeClass = status === 'ok'
                ? 'rounded-[0.9rem] bg-white px-3 text-sm font-semibold text-emerald-700 shadow-[0_10px_24px_-16px_rgba(16,185,129,0.5)] ring-1 ring-emerald-200 transition'
                : (status === 'failed'
                    ? 'rounded-[0.9rem] bg-white px-3 text-sm font-semibold text-rose-700 shadow-[0_10px_24px_-16px_rgba(244,63,94,0.45)] ring-1 ring-rose-200 transition'
                    : 'rounded-[0.9rem] bg-white px-3 text-sm font-semibold text-sky-700 shadow-[0_10px_24px_-16px_rgba(14,165,233,0.45)] ring-1 ring-sky-200 transition');

            const inactiveClass = status === 'ok'
                ? 'rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition hover:bg-white/95 hover:text-emerald-700'
                : (status === 'failed'
                    ? 'rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition hover:bg-white/95 hover:text-rose-700'
                    : 'rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition hover:bg-white/95 hover:text-sky-700');

            button.className = active ? activeClass : inactiveClass;
        });

        window.lucide?.createIcons();
    }

    function renderFacilityOptions() {
        const facilities = getFacilityOptions();
        const query = state.facilitySearch.trim().toLowerCase();
        let options = facilities.filter((item) => item.name.toLowerCase().includes(query));
        if (state.config.canChooseFacility) options = [{ id: '', name: 'All facilities' }, ...options];
        els.facilityHint.textContent = options.length ? `${options.length} option${options.length === 1 ? '' : 's'}` : 'No options found';
        els.facilityOptions.innerHTML = options.length ? options.map((item) => `
            <button type="button" data-id="${String(item.id)}" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm ${String(item.id) === String(state.selectedFacilityId) ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-sky-50 hover:text-sky-700'}">
                ${Perfectlum.escapeHtml(item.name)}
            </button>`).join('') : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">No options found</div>`;

        els.facilityOptions.querySelectorAll('button[data-id]').forEach((button) => {
            button.addEventListener('click', () => {
                state.selectedFacilityId = button.dataset.id || '';
                state.selectedWorkgroupId = '';
                state.selectedWorkstationId = '';
                state.facilitySearch = '';
                if (els.facilitySearch) els.facilitySearch.value = '';
                if (els.workgroupSearch) els.workgroupSearch.value = '';
                if (els.workstationSearch) els.workstationSearch.value = '';
                closeDropdown();
                renderFilters();
                reloadGrid();
            });
        });
    }

    function renderWorkgroupOptions() {
        const workgroups = getWorkgroupOptions();
        const query = state.workgroupSearch.trim().toLowerCase();
        const options = [{ id: '', name: 'All workgroups' }, ...workgroups.filter((item) => item.name.toLowerCase().includes(query))];
        els.workgroupHint.textContent = options.length ? `${options.length} option${options.length === 1 ? '' : 's'}` : 'No options found';
        els.workgroupOptions.innerHTML = options.length ? options.map((item) => `
            <button type="button" data-id="${String(item.id)}" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm ${String(item.id) === String(state.selectedWorkgroupId) ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-sky-50 hover:text-sky-700'}">
                ${Perfectlum.escapeHtml(item.name)}
            </button>`).join('') : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">No options found</div>`;

        els.workgroupOptions.querySelectorAll('button[data-id]').forEach((button) => {
            button.addEventListener('click', () => {
                state.selectedWorkgroupId = button.dataset.id || '';
                state.selectedWorkstationId = '';
                state.workgroupSearch = '';
                if (els.workgroupSearch) els.workgroupSearch.value = '';
                if (els.workstationSearch) els.workstationSearch.value = '';
                closeDropdown();
                renderFilters();
                reloadGrid();
            });
        });
    }

    function renderWorkstationOptions() {
        const workstations = getWorkstationOptions();
        const query = state.workstationSearch.trim().toLowerCase();
        const options = [{ id: '', name: 'All workstations' }, ...workstations.filter((item) => item.name.toLowerCase().includes(query))];
        els.workstationHint.textContent = options.length ? `${options.length} option${options.length === 1 ? '' : 's'}` : 'No options found';
        els.workstationOptions.innerHTML = options.length ? options.map((item) => `
            <button type="button" data-id="${String(item.id)}" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm ${String(item.id) === String(state.selectedWorkstationId) ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-sky-50 hover:text-sky-700'}">
                ${Perfectlum.escapeHtml(item.name)}
            </button>`).join('') : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">No options found</div>`;

        els.workstationOptions.querySelectorAll('button[data-id]').forEach((button) => {
            button.addEventListener('click', () => {
                state.selectedWorkstationId = button.dataset.id || '';
                state.workstationSearch = '';
                if (els.workstationSearch) els.workstationSearch.value = '';
                closeDropdown();
                renderFilters();
                reloadGrid();
            });
        });
    }

    function toggleDropdown(type) {
        if (type === 'facility' && els.facilityTrigger.disabled) return;
        if (type === 'workgroup' && els.workgroupTrigger.disabled) return;
        if (type === 'workstation' && els.workstationTrigger.disabled) return;
        state.activeDropdown = state.activeDropdown === type ? null : type;
        els.facilityPanel.classList.toggle('hidden', state.activeDropdown !== 'facility');
        els.workgroupPanel.classList.toggle('hidden', state.activeDropdown !== 'workgroup');
        els.workstationPanel.classList.toggle('hidden', state.activeDropdown !== 'workstation');
        if (state.activeDropdown === 'facility') els.facilitySearch?.focus();
        if (state.activeDropdown === 'workgroup') els.workgroupSearch?.focus();
        if (state.activeDropdown === 'workstation') els.workstationSearch?.focus();
    }

    function closeDropdown() {
        state.activeDropdown = null;
        els.facilityPanel.classList.add('hidden');
        els.workgroupPanel.classList.add('hidden');
        els.workstationPanel.classList.add('hidden');
    }

    function resetFilters() {
        state.selectedFacilityId = state.config.canChooseFacility ? '' : (getFacilityOptions()[0] ? String(getFacilityOptions()[0].id) : '');
        state.selectedWorkgroupId = '';
        state.selectedWorkstationId = '';
        state.selectedStatus = state.defaultStatus || '';
        state.facilitySearch = '';
        state.workgroupSearch = '';
        state.workstationSearch = '';
        if (els.facilitySearch) els.facilitySearch.value = '';
        if (els.workgroupSearch) els.workgroupSearch.value = '';
        if (els.workstationSearch) els.workstationSearch.value = '';
        closeDropdown();
        renderFilters();
        reloadGrid();
    }

    function buildGridUrl(extra = {}) {
        return Perfectlum.buildServerUrl('/api/displays', {
            type: state.selectedStatus || '',
            facility_id: state.selectedFacilityId || '',
            workgroup_id: state.selectedWorkgroupId || '',
            workstation_id: state.selectedWorkstationId || '',
            ...extra,
        });
    }

    function mapRows(d) {
        return d.data.map(r => [
            { id: r.id, displayName: r.displayName },
            { wsName: r.wsName, wsId: r.wsId },
            { wgName: r.wgName, wgId: r.wgId },
            { facName: r.facName, facId: r.facId },
            r.status,
            { id: r.id, name: r.displayName },
        ]);
    }

    function initGrid() {
        if (!els.grid || state.grid) return;

        state.grid = Perfectlum.createGrid(els.grid, {
            columns: [
                {
                    name: 'Display Name',
                    formatter: (cell) => gridjs.html(`
                        <button type="button" onclick="window.openDisplayModal(${cell.id})" class="cursor-pointer font-medium text-sky-600 transition hover:text-sky-700 hover:underline">
                            ${Perfectlum.escapeHtml(cell.displayName)}
                        </button>
                    `),
                },
                {
                    name: 'Workstation',
                    formatter: (cell) => !cell.wsName || cell.wsName === '-'
                        ? '-'
                        : gridjs.html(`<button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy',{detail:{type:'workstation',id:${cell.wsId}}}))" class="cursor-pointer text-gray-600 transition hover:text-sky-500 group-[.theme-chroma]:text-gray-300">${Perfectlum.escapeHtml(cell.wsName)}</button>`),
                },
                {
                    name: 'Workgroup',
                    formatter: (cell) => !cell.wgName || cell.wgName === '-'
                        ? '-'
                        : gridjs.html(`<button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy',{detail:{type:'workgroup',id:${cell.wgId}}}))" class="cursor-pointer text-gray-600 transition hover:text-sky-500 group-[.theme-chroma]:text-gray-300">${Perfectlum.escapeHtml(cell.wgName)}</button>`),
                },
                {
                    name: 'Facility',
                    formatter: (cell) => !cell.facName || cell.facName === '-'
                        ? '-'
                        : gridjs.html(`<button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy',{detail:{type:'facility',id:${cell.facId}}}))" class="cursor-pointer text-gray-600 transition hover:text-sky-500 group-[.theme-chroma]:text-gray-300">${Perfectlum.escapeHtml(cell.facName)}</button>`),
                },
                {
                    name: 'Status',
                    formatter: (cell) => gridjs.html(Perfectlum.badge(Number(cell) === 1 ? 'OK' : 'Failed', Number(cell) === 1 ? 'success' : 'danger')),
                },
                {
                    name: 'Actions',
                    sort: false,
                    width: '112px',
                    formatter: (c) => !canManageDisplays ? '' : gridjs.html(`
                        <div class="flex justify-center">
                            <button
                                type="button"
                                onclick='window.displaysPage && window.displaysPage.toggleActionMenu(event, ${c.id}, ${JSON.stringify(c.name)})'
                                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="2"/><circle cx="12" cy="5" r="2"/><circle cx="12" cy="19" r="2"/></svg>
                            </button>
                        </div>`),
                },
            ],
            server: {
                url: buildGridUrl(),
                then: mapRows,
                total: d => d.total,
            },
            pagination: {
                enabled: true,
                limit: 10,
                server: {
                    url: (_, pg, lim) => buildGridUrl({ page: pg + 1, limit: lim }),
                },
            },
            search: {
                enabled: true,
                server: {
                    url: (_, kw) => buildGridUrl({ search: kw }),
                },
            },
            sort: { multiColumn: false },
            language: { search: { placeholder: 'Search displays...' } },
        });
    }

    function reloadGrid() {
        closeActionMenu();
        state.grid = null;
        if (!els.grid) {
            initGrid();
            return;
        }

        Perfectlum.remountGrid('displays-grid', (freshGrid) => {
            els.grid = freshGrid || document.getElementById('displays-grid');
            state.grid = null;
            initGrid();
        });
    }

    function toggleActionMenu(event, id, name) {
        if (!canManageDisplays) return;
        event.preventDefault();
        event.stopPropagation();
        const rect = event.currentTarget.getBoundingClientRect();
        const nextOpen = !(state.actionTarget && state.actionTarget.id === id && !els.actionMenu.classList.contains('hidden'));
        state.actionTarget = nextOpen ? { id, name } : null;
        if (!nextOpen) {
            closeActionMenu();
            return;
        }
        els.actionOverlay.classList.remove('hidden');
        els.actionMenu.classList.remove('hidden');
        els.actionMenu.style.left = `${Math.max(16, rect.right - 208)}px`;
        els.actionMenu.style.top = `${rect.bottom + 10}px`;
        window.lucide?.createIcons();
    }

    function closeActionMenu() {
        els.actionOverlay.classList.add('hidden');
        els.actionMenu.classList.add('hidden');
    }

    function fillSelect(select, options, selectedValue) {
        if (!select) return;
        const items = Array.isArray(options) ? [...options] : [];
        items.sort((a, b) => String(a.label || a.value).localeCompare(String(b.label || b.value), undefined, { sensitivity: 'base', numeric: true }));
        select.innerHTML = items.map((item) => {
            const value = String(item.value ?? '');
            const label = Perfectlum.escapeHtml(item.label ?? item.value ?? '');
            const selected = String(selectedValue ?? '') === value ? ' selected' : '';
            return `<option value="${Perfectlum.escapeHtml(value)}"${selected}>${label}</option>`;
        }).join('');
    }

    function setFieldValue(name, value) {
        const field = els.editForm?.querySelector(`[name="${name}"]`);
        if (!field) return;
        if (field.type === 'checkbox') {
            field.checked = !!value;
            return;
        }
        field.value = value ?? '';
    }

    async function openEditModal(id, name) {
        if (!canManageDisplays) return;
        closeActionMenu();
        state.editTarget = { id, name };
        els.editSubtitle.textContent = name || '';
        els.editModal.classList.remove('hidden');
        els.editModal.classList.add('flex');
        els.editBody.classList.add('hidden');
        els.editLoading.classList.remove('hidden');
        els.editError.classList.add('hidden');
        els.editError.textContent = '';
        els.editSave.disabled = false;
        els.editSave.textContent = 'Save Changes';

        try {
            const payload = await Perfectlum.request(`/api/display-modal/${id}/edit`);
            const fields = payload.fields || {};
            fillSelect(els.editForm.querySelector('[name="TypeOfDisplay"]'), payload.options?.TypeOfDisplay || [], fields.TypeOfDisplay);
            fillSelect(els.editForm.querySelector('[name="DisplayTechnology"]'), payload.options?.DisplayTechnology || [], fields.DisplayTechnology);

            [
                'Manufacturer',
                'Model',
                'SerialNumber',
                'InventoryNumber',
                'ScreenSize',
                'CurrentLUTIndex',
                'ResolutionHorizontal',
                'ResolutionVertical',
                'InstalationDate',
                'purchase_date',
                'initial_value',
                'expected_value',
                'annual_straight_line',
                'monthly_straight_line',
                'current_value',
                'expected_replacement_date',
            ].forEach((field) => setFieldValue(field, fields[field]));

            setFieldValue('exclude', fields.exclude);
            setFieldValue('graphicboardOnly', String(fields.CommunicationType ?? '3') === '1');
            setFieldValue('InternalSensor', fields.InternalSensor);

            els.editLoading.classList.add('hidden');
            els.editBody.classList.remove('hidden');
        } catch (error) {
            els.editLoading.classList.add('hidden');
            els.editError.textContent = error.message || 'Unable to load display form.';
            els.editError.classList.remove('hidden');
        }
    }

    function closeEditModal() {
        state.editTarget = null;
        els.editModal.classList.add('hidden');
        els.editModal.classList.remove('flex');
        els.editBody.classList.add('hidden');
        els.editLoading.classList.add('hidden');
        els.editError.classList.add('hidden');
        els.editError.textContent = '';
        els.editSubtitle.textContent = '';
        els.editForm.reset();
        fillSelect(els.editForm.querySelector('[name="TypeOfDisplay"]'), [], '');
        fillSelect(els.editForm.querySelector('[name="DisplayTechnology"]'), [], '');
        els.editSave.disabled = false;
        els.editSave.textContent = 'Save Changes';
    }

    async function submitEditForm(event) {
        event.preventDefault();
        if (!state.editTarget?.id || els.editSave.disabled) return;

        els.editSave.disabled = true;
        els.editSave.textContent = 'Saving...';
        els.editError.classList.add('hidden');
        els.editError.textContent = '';

        try {
            const formData = new FormData(els.editForm);
            formData.append('_token', csrfToken());
            formData.set('CommunicationType', els.editForm.querySelector('[name="graphicboardOnly"]')?.checked ? '1' : '3');
            if (!els.editForm.querySelector('[name="exclude"]')?.checked) formData.delete('exclude');
            if (!els.editForm.querySelector('[name="InternalSensor"]')?.checked) formData.delete('InternalSensor');
            formData.delete('graphicboardOnly');

            const payload = await Perfectlum.postForm(`/api/display-modal/${state.editTarget.id}/save`, formData);
            if (!payload.success) throw new Error(payload.message || 'Unable to update display.');
            closeEditModal();
            reloadGrid();
        } catch (error) {
            els.editError.textContent = error.message || 'Unable to update display.';
            els.editError.classList.remove('hidden');
            els.editSave.disabled = false;
            els.editSave.textContent = 'Save Changes';
        }
    }

    function openDeleteModal(id, name) {
        if (!canManageDisplays) return;
        closeActionMenu();
        state.deleteTarget = { id, name };
        els.deleteName.textContent = name || '';
        els.deleteModal.classList.remove('hidden');
        els.deleteModal.classList.add('flex');
    }

    function closeDeleteModal() {
        state.deleteTarget = null;
        els.deleteModal.classList.add('hidden');
        els.deleteModal.classList.remove('flex');
        els.deleteConfirm.disabled = false;
        els.deleteConfirm.textContent = 'Delete Display';
    }

    async function confirmDelete() {
        if (!state.deleteTarget?.id || els.deleteConfirm.disabled) return;
        els.deleteConfirm.disabled = true;
        els.deleteConfirm.textContent = 'Deleting...';
        try {
            const formData = new FormData();
            formData.append('_token', csrfToken());
            formData.append('id', state.deleteTarget.id);
            const payload = await Perfectlum.postForm('/delete-display', formData);
            if (!payload.success) throw new Error(payload.msg || 'Unable to delete display.');
            closeDeleteModal();
            reloadGrid();
        } catch (error) {
            window.alert(error.message || 'Unable to delete display.');
            els.deleteConfirm.disabled = false;
            els.deleteConfirm.textContent = 'Delete Display';
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
</script>

@include('common.navigations.footer')
