@include('common.navigations.header')

@php
    $canManageDisplayCalibration = in_array(($role ?? session('role')), ['super', 'admin'], true);
@endphp

{{-- ===================== DISPLAY CALIBRATION PAGE ===================== --}}
<div class="space-y-6 pb-8 font-inter theme-lum">

    <section class="rounded-[2rem] border border-slate-200 bg-white px-7 py-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl border border-slate-200 bg-sky-50 text-sky-600 shadow-sm">
                    <i data-lucide="monitor-play" class="h-6 w-6"></i>
                </div>
                <div class="space-y-2">
                    <p class="text-[11px] font-black uppercase tracking-[0.28em] text-slate-400">{{ __('Admin Workspace') }}</p>
                    <h1 class="text-4xl font-extrabold tracking-tight text-slate-900">{{ __('Calibrate Display') }}</h1>
                    <p class="max-w-3xl text-sm text-slate-500">
                        {{ __('Run immediate calibration tasks for selected displays and review the most recently created calibration jobs.') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="relative" x-data="{ openDownload: false }">
                    <button @click="openDownload = !openDownload" @click.away="openDownload = false"
                            class="inline-flex h-11 items-center justify-center gap-2 rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50">
                        <i data-lucide="download" class="h-4 w-4"></i>
                        {{ __('Export') }}
                        <i data-lucide="chevron-down" class="h-4 w-4"></i>
                    </button>
                    <div x-show="openDownload" x-cloak
                         x-transition.opacity.duration.200ms
                         class="absolute right-0 mt-2 w-52 overflow-hidden rounded-2xl border border-slate-200 bg-white py-1 shadow-[0_18px_45px_rgba(15,23,42,0.14)] z-50">
                        <a href="{{ url('reports/display-calibration?export_type=excel') }}" target="_blank"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                            <i data-lucide="file-spreadsheet" class="h-4 w-4 text-emerald-500"></i>
                            {{ __('Download Excel') }}
                        </a>
                        <a href="{{ url('reports/display-calibration?export_type=pdf') }}" target="_blank"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                            <i data-lucide="file-text" class="h-4 w-4 text-rose-500"></i>
                            {{ __('Download PDF') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">

    @if($canManageDisplayCalibration)
    {{-- ── NEW CALIBRATION TASK FORM ── --}}
    <div class="mb-6 rounded-[1.75rem] border border-slate-200 bg-slate-50/70 p-5 shadow-[0_12px_34px_rgba(15,23,42,0.05)]">
    <div class="mb-4">
        <p class="text-[11px] font-black uppercase tracking-[0.28em] text-slate-400">{{ __('Create Calibration') }}</p>
        <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ __('Run calibration by hierarchy') }}</h2>
        <p class="mt-1 text-sm text-slate-500">{{ __('Pick a facility, workgroup, workstation, and one or more displays to create a new calibration task.') }}</p>
    </div>
    <form method="post" action="" class="w-full">
        {{csrf_field()}}
        <input type="hidden" name="calibrate" value="1">

        {{-- Top Filter Row --}}
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">
            
            {{-- 1. Facility --}}
            <div class="flex flex-col gap-1.5">
                <label class="mb-2 block text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Facility') }}</label>
                <div class="relative">
                    <select name="facility" id="calibrate-facility-native" required class="hidden" onchange="fetch_workgroups(this);">
                        <option value="">{{ __('Please select') }}</option>
                        @if (!$user->hasRole('super'))
                            @foreach($facilities as $fc)
                            <option value="{{$fc['id']}}">{{$fc['name']}}</option>
                            @endforeach
                        @else
                            @foreach($facilities as $fc)
                            <option value="{{$fc->id}}">{{$fc->name}}</option>
                            @endforeach
                        @endif
                    </select>
                    <button type="button" id="calibrate-facility-trigger"
                            class="flex w-full h-[42px] items-center justify-between rounded-lg border border-gray-200 bg-white px-4 text-[13px] text-gray-700 shadow-sm transition-all hover:border-gray-300 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 cursor-pointer">
                        <span id="calibrate-facility-label" class="truncate">{{ __('Please select') }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </button>
                    <div id="calibrate-facility-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-2xl border border-gray-200 bg-white p-3 shadow-xl">
                        <input id="calibrate-facility-search" type="text" placeholder="{{ __('Search facilities...') }}" class="mb-2 h-10 w-full rounded-xl border border-gray-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="calibrate-facility-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="calibrate-facility-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            {{-- 2. Workgroup --}}
            <div class="flex flex-col gap-1.5">
                <label class="mb-2 block text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Workgroup') }}</label>
                <div class="relative">
                    <select name="workgroup" id="workgroups_field" onchange="fetch_workstations(this)" class="hidden">
                        <option value="">{{ __('Select Facility first') }}</option>
                    </select>
                    <button type="button" id="calibrate-workgroup-trigger"
                            class="flex w-full h-[42px] items-center justify-between rounded-lg border border-gray-200 bg-white px-4 text-[13px] text-gray-700 shadow-sm transition-all hover:border-gray-300 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 cursor-pointer disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400">
                        <span id="calibrate-workgroup-label" class="truncate">{{ __('Select Facility first') }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </button>
                    <div id="calibrate-workgroup-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-2xl border border-gray-200 bg-white p-3 shadow-xl">
                        <input id="calibrate-workgroup-search" type="text" placeholder="{{ __('Search workgroups...') }}" class="mb-2 h-10 w-full rounded-xl border border-gray-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="calibrate-workgroup-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="calibrate-workgroup-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            {{-- 3. Workstation --}}
            <div class="flex flex-col gap-1.5">
                <label class="mb-2 block text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Workstation') }}</label>
                <div class="relative">
                    <select name="workstation" id="workstations_field" onchange="fetch_displays_checklist(this)" class="hidden">
                        <option value="">{{ __('Select Workgroup first') }}</option>
                    </select>
                    <button type="button" id="calibrate-workstation-trigger"
                            class="flex w-full h-[42px] items-center justify-between rounded-lg border border-gray-200 bg-white px-4 text-[13px] text-gray-700 shadow-sm transition-all hover:border-gray-300 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 cursor-pointer disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-400">
                        <span id="calibrate-workstation-label" class="truncate">{{ __('Select Workgroup first') }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </button>
                    <div id="calibrate-workstation-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-2xl border border-gray-200 bg-white p-3 shadow-xl">
                        <input id="calibrate-workstation-search" type="text" placeholder="{{ __('Search workstations...') }}" class="mb-2 h-10 w-full rounded-xl border border-gray-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="calibrate-workstation-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="calibrate-workstation-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            {{-- 4. Display (Checklist Dropdown) --}}
            <div class="flex flex-col gap-1.5 relative">
                <label class="mb-2 block text-[11px] font-black uppercase tracking-[0.22em] text-slate-400">{{ __('Displays') }}</label>
                <button type="button" id="displays-dropdown"
                        class="w-full h-[42px] px-4 flex items-center justify-between rounded-lg text-[13px] outline-none border border-gray-200 bg-white text-gray-700 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 shadow-sm transition-all cursor-pointer">
                    <span id="calibrate-displays-label">{{ __('Select Workstation first') }}</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                </button>
                <div class="absolute z-50 top-[66px] left-0 w-full bg-white border border-gray-200 rounded-2xl shadow-xl hidden"
                     id="displays_field">
                    <div class="p-3 border-b border-gray-100">
                        <input id="calibrate-displays-search" type="text" placeholder="{{ __('Search displays...') }}" class="h-10 w-full rounded-xl border border-gray-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                    </div>
                    <div id="calibrate-displays-options" class="max-h-60 overflow-y-auto">
                        <div class="px-4 py-3 text-[13px] text-gray-500">{{ __('Select Workstation first') }}</div>
                    </div>
                </div>
            </div>

            <div class="flex items-end">
                <button type="submit" id="submit_btn"
                        class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-full bg-sky-500 px-6 text-sm font-semibold text-white shadow-[0_16px_30px_rgba(14,165,233,0.24)] transition hover:bg-sky-400">
                    <i data-lucide="play" class="h-4 w-4"></i>
                    {{ __('Calibrate') }}
                </button>
            </div>
        </div>
    </form>
    </div>
    @endif

    <div class="mb-4 flex items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-black uppercase tracking-[0.28em] text-slate-400">{{ __('Calibration Tasks') }}</p>
            <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ __('Recent calibration jobs') }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ __('Newest created calibration tasks appear first so recent actions are easier to verify.') }}</p>
        </div>

        <div class="relative w-full max-w-[320px]">
            <i data-lucide="search" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="gridjs-custom-search" placeholder="{{ __('Search') }}" 
                   class="w-full h-[42px] pl-10 pr-4 rounded-full text-[13px] outline-none border border-gray-200 bg-white text-gray-700 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 shadow-sm transition-all placeholder-gray-400">
        </div>
    </div>

    {{-- Tasks Table Wrapper --}}
    <div class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-[0_16px_44px_rgba(15,23,42,0.06)]">
        <div id="tasks-grid"></div>
    </div>

</section>
</div>

{{-- ── MODAL: DELETE CONFIRM (Alpine Version) ── --}}
<div x-data="{ open: false, taskId: null }" 
     @delete-task.window="open = true; taskId = $event.detail.id"
     class="relative z-[9999]">
    <div x-show="open" x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>
    
    <div x-show="open" x-cloak
         class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.away="open = false"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4 text-center">
                    <div class="mx-auto flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-xl bg-red-50 mb-4">
                        <i data-lucide="trash-2" class="h-6 w-6 text-red-500"></i>
                    </div>
                    <h3 class="text-base font-semibold leading-6 text-gray-900">{{ __('Delete Task') }}</h3>
                    <div class="mt-2 text-sm text-gray-500">
                        {{ __('This action cannot be undone. Are you sure?') }}
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 flex items-center justify-center gap-3 sm:px-6">
                    <button type="button" @click="open = false" class="inline-flex w-full justify-center rounded-full bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:w-auto min-w-[100px]">{{ __('Cancel') }}</button>
                    <button type="button" @click="confirmDelete(taskId); open = false;" class="inline-flex w-full justify-center rounded-full bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:w-auto min-w-[100px]">{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $calibrationText = [
        'display' => __('Display'),
        'workstation' => __('Workstation'),
        'workgroup' => __('Workgroup'),
        'facility' => __('Facility'),
        'taskType' => __('Task Type'),
        'scheduleType' => __('Schedule Type'),
        'dueDate' => __('Due Date'),
        'actions' => __('Actions'),
        'searchCalibrationTasks' => __('Search calibration tasks...'),
        'previous' => __('Previous'),
        'next' => __('Next'),
        'showing' => __('Showing'),
        'results' => __('results'),
        'loading' => __('Loading...'),
        'noMatchingRecordsFound' => __('No matching records found'),
        'unableToLoadData' => __('Unable to load data'),
        'selectWorkstation' => __('Select Workstation'),
        'selectWorkgroupFirst' => __('Select Workgroup first'),
        'selectWorkstationFirst' => __('Select Workstation first'),
        'pleaseSelect' => __('Please select'),
        'noOptionsFound' => __('No options found'),
        'noWorkstationsFound' => __('No workstations found'),
        'deletionFailed' => __('Deletion failed'),
        'taskDeletedSuccessfully' => __('Task deleted successfully'),
    ];
@endphp
<script>
    const calibrationText = @json($calibrationText);
    const calibrateHierarchyState = {
        facilitySearch: '',
        workgroupSearch: '',
        workstationSearch: '',
        displaysSearch: '',
        activeDropdown: null,
    };
    const canManageCalibrationTasks = @json($canManageDisplayCalibration);

    function parseNativeSelectOptions(selectId) {
        const select = document.getElementById(selectId);
        if (!select) return [];
        return Array.from(select.options || []).map((option) => ({
            value: option.value,
            label: option.textContent.trim(),
        }));
    }

    function toggleCalibrateDropdown(type) {
        const panels = {
            facility: document.getElementById('calibrate-facility-panel'),
            workgroup: document.getElementById('calibrate-workgroup-panel'),
            workstation: document.getElementById('calibrate-workstation-panel'),
            displays: document.getElementById('displays_field'),
        };
        calibrateHierarchyState.activeDropdown = calibrateHierarchyState.activeDropdown === type ? null : type;
        Object.entries(panels).forEach(([key, panel]) => {
            if (!panel) return;
            panel.classList.toggle('hidden', calibrateHierarchyState.activeDropdown !== key);
        });
    }

    function closeCalibrateDropdowns() {
        calibrateHierarchyState.activeDropdown = null;
        ['calibrate-facility-panel', 'calibrate-workgroup-panel', 'calibrate-workstation-panel', 'displays_field']
            .forEach((id) => document.getElementById(id)?.classList.add('hidden'));
    }

    function renderCalibrationTaskActions(taskId) {
        if (!canManageCalibrationTasks) {
            return '<span class="text-xs text-slate-400">No actions</span>';
        }

        return `
            <div class="relative flex justify-end">
                <button type="button"
                        data-calibration-task-toggle="${taskId}"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700">
                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                </button>
                <div data-calibration-task-menu="${taskId}" class="absolute right-0 top-full z-20 mt-2 hidden w-44 overflow-hidden rounded-2xl border border-slate-200 bg-white py-1 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                    <button type="button" data-calibration-task-edit="${taskId}" class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        <i data-lucide="calendar-clock" class="h-4 w-4 text-sky-500"></i>
                        Schedule Task
                    </button>
                    <button type="button" data-calibration-task-delete="${taskId}" class="flex w-full items-center gap-3 px-4 py-2.5 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                        Delete Task
                    </button>
                </div>
            </div>
        `;
    }

    function closeCalibrationTaskMenus() {
        document.querySelectorAll('[data-calibration-task-menu]').forEach((menu) => menu.classList.add('hidden'));
    }

    function renderCalibrateNativeOptions(selectId, optionsId, hintId, query, onPick, emptyText = calibrationText.noOptionsFound) {
        const items = parseNativeSelectOptions(selectId).filter((item, index) => index > 0);
        const filtered = items.filter((item) => item.label.toLowerCase().includes((query || '').trim().toLowerCase()));
        const hint = document.getElementById(hintId);
        const box = document.getElementById(optionsId);
        if (hint) hint.textContent = filtered.length ? `${filtered.length} option${filtered.length === 1 ? '' : 's'}` : emptyText;
        if (!box) return;
        box.innerHTML = filtered.length
            ? filtered.map((item) => `<button type="button" data-value="${Perfectlum.escapeHtml(item.value)}" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 hover:bg-sky-50 hover:text-sky-700">${Perfectlum.escapeHtml(item.label)}</button>`).join('')
            : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">${emptyText}</div>`;

        box.querySelectorAll('button[data-value]').forEach((button) => {
            button.addEventListener('click', () => onPick(button.dataset.value));
        });
    }

    function updateCalibrateDisplaysLabel() {
        const list = Array.from(document.querySelectorAll('#calibrate-displays-options input[name="displays[]"]:not([data-select-all="1"])'));
        const checked = list.filter((item) => item.checked);
        const label = document.getElementById('calibrate-displays-label');
        if (!label) return;
        if (!list.length) {
            label.textContent = 'No displays found';
            return;
        }
        if (!checked.length || checked.length === list.length) {
            label.textContent = 'All displays in scope';
            return;
        }
        label.textContent = checked.length === 1
            ? (checked[0].dataset.label || '1 display selected')
            : `${checked.length} displays selected`;
    }

    function fetch_workgroups(th) {
        var formData = new FormData();
        formData.append('_token', '{{csrf_token()}}');
        formData.append('id', th.value || '');

        fetch("{{ url('fetch-groups') }}", {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: formData
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    document.getElementById('workgroups_field').innerHTML = data.content;
                    document.getElementById('workstations_field').innerHTML = '<option value="">Select Workgroup first</option>';
                    document.getElementById('calibrate-workgroup-label').textContent = 'Select Workgroup';
                    document.getElementById('calibrate-workstation-label').textContent = 'Select Workgroup first';
                    document.getElementById('calibrate-displays-options').innerHTML = '<div class="px-4 py-3 text-[13px] text-gray-500">Select Workstation first</div>';
                    document.getElementById('calibrate-displays-label').textContent = 'Select Workstation first';
                    renderCalibrateNativeOptions('workgroups_field', 'calibrate-workgroup-options', 'calibrate-workgroup-hint', calibrateHierarchyState.workgroupSearch, (value) => {
                        const select = document.getElementById('workgroups_field');
                        select.value = value;
                        document.getElementById('calibrate-workgroup-label').textContent = select.options[select.selectedIndex]?.textContent || 'Select Workgroup';
                        fetch_workstations(select);
                        closeCalibrateDropdowns();
                    }, 'No workgroups found');
                }
            });
    }

    function fetch_workstations(th) {
        const formData = new FormData();
        formData.append('_token', '{{csrf_token()}}');
        formData.append('id', th.value || '');

        fetch("{{ url('fetch-workstations') }}", {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: formData
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    document.getElementById('workstations_field').innerHTML = data.content;
                    document.getElementById('calibrate-workstation-label').textContent = 'Select Workstation';
                    document.getElementById('calibrate-displays-options').innerHTML = '<div class="px-4 py-3 text-[13px] text-gray-500">Select Workstation first</div>';
                    document.getElementById('calibrate-displays-label').textContent = 'Select Workstation first';
                    renderCalibrateNativeOptions('workstations_field', 'calibrate-workstation-options', 'calibrate-workstation-hint', calibrateHierarchyState.workstationSearch, (value) => {
                        const select = document.getElementById('workstations_field');
                        select.value = value;
                        document.getElementById('calibrate-workstation-label').textContent = select.options[select.selectedIndex]?.textContent || 'Select Workstation';
                        fetch_displays_checklist(select);
                        closeCalibrateDropdowns();
                    }, 'No workstations found');
                }
            });
    }

    function fetch_displays_checklist(th) {
        var formData = new FormData();
        formData.append('_token', '{{csrf_token()}}');
        formData.append('id', th.value || '');

        fetch("{{ url('fetch-displays-checklist') }}", {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: formData
        })
            .then((res) => res.json())
            .then((data) => {
                if (data.success) {
                    let content = data.content;
                    if(content === '') content = '<div class="px-4 py-3 text-[13px] text-gray-500">No displays found.</div>';

                    content = content.replace(/form-check-input/g, "w-4 h-4 text-sky-500 border-gray-300 rounded focus:ring-sky-500 cursor-pointer");
                    content = content.replace(/form-check-label/g, "ml-3 text-[13px] text-gray-700 cursor-pointer select-none flex-1");
                    content = content.replace(/form-check mb-0 py-1 px-4/g, "flex items-center px-4 py-2.5 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0");

                    const box = document.getElementById('calibrate-displays-options');
                    box.innerHTML = content;

                    box.querySelectorAll('input[name="displays[]"], input[name="displays2[]"]').forEach((input) => {
                        if (!input.value) {
                            input.setAttribute('data-select-all', '1');
                        } else {
                            input.setAttribute('name', 'displays[]');
                            const label = input.parentElement?.querySelector('label')?.textContent?.trim() || 'Display';
                            input.dataset.label = label;
                        }
                        input.addEventListener('change', function () {
                            if (this.dataset.selectAll === '1') {
                                const checked = this.checked;
                                box.querySelectorAll('input[name="displays[]"]:not([data-select-all="1"])').forEach((item) => {
                                    item.checked = checked;
                                });
                            }
                            updateCalibrateDisplaysLabel();
                        });
                    });

                    updateCalibrateDisplaysLabel();
                }
            });
    }

    function filterCalibrateDisplays() {
        const query = (document.getElementById('calibrate-displays-search')?.value || '').trim().toLowerCase();
        const rows = document.querySelectorAll('#calibrate-displays-options > div');
        rows.forEach((row) => {
            const label = row.textContent.toLowerCase();
            row.style.display = !query || label.includes(query) ? '' : 'none';
        });
    }

    function initCalibrateSearchableDropdowns() {
        const facilityTrigger = document.getElementById('calibrate-facility-trigger');
        const workgroupTrigger = document.getElementById('calibrate-workgroup-trigger');
        const workstationTrigger = document.getElementById('calibrate-workstation-trigger');
        const displaysTrigger = document.getElementById('displays-dropdown');

        facilityTrigger?.addEventListener('click', () => {
            renderCalibrateNativeOptions('calibrate-facility-native', 'calibrate-facility-options', 'calibrate-facility-hint', calibrateHierarchyState.facilitySearch, (value) => {
                const select = document.getElementById('calibrate-facility-native');
                select.value = value;
                document.getElementById('calibrate-facility-label').textContent = select.options[select.selectedIndex]?.textContent || 'Please select';
                fetch_workgroups(select);
                closeCalibrateDropdowns();
            }, 'No facilities found');
            toggleCalibrateDropdown('facility');
        });
        workgroupTrigger?.addEventListener('click', () => {
            renderCalibrateNativeOptions('workgroups_field', 'calibrate-workgroup-options', 'calibrate-workgroup-hint', calibrateHierarchyState.workgroupSearch, (value) => {
                const select = document.getElementById('workgroups_field');
                select.value = value;
                document.getElementById('calibrate-workgroup-label').textContent = select.options[select.selectedIndex]?.textContent || 'Select Workgroup';
                fetch_workstations(select);
                closeCalibrateDropdowns();
            }, 'No workgroups found');
            toggleCalibrateDropdown('workgroup');
        });
        workstationTrigger?.addEventListener('click', () => {
            renderCalibrateNativeOptions('workstations_field', 'calibrate-workstation-options', 'calibrate-workstation-hint', calibrateHierarchyState.workstationSearch, (value) => {
                const select = document.getElementById('workstations_field');
                select.value = value;
                document.getElementById('calibrate-workstation-label').textContent = select.options[select.selectedIndex]?.textContent || 'Select Workstation';
                fetch_displays_checklist(select);
                closeCalibrateDropdowns();
            }, 'No workstations found');
            toggleCalibrateDropdown('workstation');
        });
        displaysTrigger?.addEventListener('click', () => toggleCalibrateDropdown('displays'));

        document.getElementById('calibrate-facility-search')?.addEventListener('input', (e) => {
            calibrateHierarchyState.facilitySearch = e.target.value || '';
            renderCalibrateNativeOptions('calibrate-facility-native', 'calibrate-facility-options', 'calibrate-facility-hint', calibrateHierarchyState.facilitySearch, (value) => {
                const select = document.getElementById('calibrate-facility-native');
                select.value = value;
                document.getElementById('calibrate-facility-label').textContent = select.options[select.selectedIndex]?.textContent || 'Please select';
                fetch_workgroups(select);
                closeCalibrateDropdowns();
            }, 'No facilities found');
        });
        document.getElementById('calibrate-workgroup-search')?.addEventListener('input', (e) => {
            calibrateHierarchyState.workgroupSearch = e.target.value || '';
            renderCalibrateNativeOptions('workgroups_field', 'calibrate-workgroup-options', 'calibrate-workgroup-hint', calibrateHierarchyState.workgroupSearch, (value) => {
                const select = document.getElementById('workgroups_field');
                select.value = value;
                document.getElementById('calibrate-workgroup-label').textContent = select.options[select.selectedIndex]?.textContent || 'Select Workgroup';
                fetch_workstations(select);
                closeCalibrateDropdowns();
            }, 'No workgroups found');
        });
        document.getElementById('calibrate-workstation-search')?.addEventListener('input', (e) => {
            calibrateHierarchyState.workstationSearch = e.target.value || '';
            renderCalibrateNativeOptions('workstations_field', 'calibrate-workstation-options', 'calibrate-workstation-hint', calibrateHierarchyState.workstationSearch, (value) => {
                const select = document.getElementById('workstations_field');
                select.value = value;
                document.getElementById('calibrate-workstation-label').textContent = select.options[select.selectedIndex]?.textContent || calibrationText.selectWorkstation;
                fetch_displays_checklist(select);
                closeCalibrateDropdowns();
            }, calibrationText.noWorkstationsFound);
        });
        document.getElementById('calibrate-displays-search')?.addEventListener('input', filterCalibrateDisplays);

        document.addEventListener('click', (event) => {
            if (
                !document.getElementById('calibrate-facility-panel')?.contains(event.target) &&
                !facilityTrigger?.contains(event.target) &&
                !document.getElementById('calibrate-workgroup-panel')?.contains(event.target) &&
                !workgroupTrigger?.contains(event.target) &&
                !document.getElementById('calibrate-workstation-panel')?.contains(event.target) &&
                !workstationTrigger?.contains(event.target) &&
                !document.getElementById('displays_field')?.contains(event.target) &&
                !displaysTrigger?.contains(event.target)
            ) {
                closeCalibrateDropdowns();
            }
        });
    }

    // Grid.js implementation
    document.addEventListener("DOMContentLoaded", function () {
        initCalibrateSearchableDropdowns();
        const customSearchParams = new URLSearchParams(window.location.search);
        const searchInput = document.getElementById('gridjs-custom-search');
        if (customSearchParams.has('keywords')) {
            searchInput.value = customSearchParams.get('keywords');
        }

        window.grid = Perfectlum.createGrid('tasks-grid', {
            columns: [
                {
                    name: calibrationText.display,
                    formatter: (cell) => gridjs.html(`<a href="#" class="text-[#0ea5e9] hover:underline">${Perfectlum.escapeHtml(cell)}</a>`)
                },
                {
                    name: calibrationText.workstation,
                    formatter: (cell) => gridjs.html(`<a href="#" class="text-[#0ea5e9] hover:underline">${Perfectlum.escapeHtml(cell)}</a>`)
                },
                {
                    name: calibrationText.workgroup,
                    formatter: (cell) => gridjs.html(`<a href="#" class="text-[#0ea5e9] hover:underline">${Perfectlum.escapeHtml(cell)}</a>`)
                },
                {
                    name: calibrationText.facility,
                    formatter: (cell) => gridjs.html(`<a href="#" class="text-[#0ea5e9] hover:underline">${Perfectlum.escapeHtml(cell)}</a>`)
                },
                {
                    name: calibrationText.taskType,
                    formatter: (cell) => gridjs.html(`<span class="text-gray-600">${Perfectlum.escapeHtml(cell)}</span>`)
                },
                {
                    name: calibrationText.scheduleType,
                    formatter: (cell) => gridjs.html(`<span class="text-gray-600">${Perfectlum.escapeHtml(cell)}</span>`)
                },
                {
                    name: calibrationText.dueDate,
                    formatter: (cell) => gridjs.html(`<span class="text-gray-600">${Perfectlum.escapeHtml(cell)}</span>`)
                },
                {
                    name: calibrationText.actions,
                    width: '112px',
                    sort: false,
                    formatter: (_, row) => gridjs.html(renderCalibrationTaskActions(row.cells[8].data))
                },
                {
                    name: 'id', // Hidden column for ID
                    hidden: true
                }
            ],
            server: {
                url: '{{ url("api/calibration-tasks") }}' + (searchInput.value ? `?search=${encodeURIComponent(searchInput.value)}` : ''),
                then: data => {
                    setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 50);
                    
                    if (data && data.data) {
                        return data.data.map(item => [
                            item.displayName,
                            item.wsName,
                            item.wgName,
                            item.facName,
                            item.taskName,
                            item.scheduleName,
                            item.dueAt,
                            '', // Actions
                            item.id // Hidden ID
                        ]);
                    }
                    return [];
                },
                total: data => data.total || 0,
                handle: (res) => {
                    if (res.status === 404) return {data: []};
                    if (res.ok) return res.json();
                    throw Error(calibrationText.unableToLoadData);
                }
            },
            pagination: {
                enabled: true,
                limit: 10,
                server: {
                    url: (prev, page, limit) => {
                        let base = prev.split('?')[0];
                        let params = new URLSearchParams(prev.split('?')[1] || '');
                        params.set('page', page + 1);
                        params.set('limit', limit);
                        return `${base}?${params.toString()}`;
                    }
                }
            },
            sort: false,
            search: false,
            className: {
                table: 'w-full text-sm text-left',
                thead: 'bg-gray-50/50',
                th: 'px-7 py-4 text-xs font-black uppercase tracking-wider text-gray-400 border-b border-gray-100 bg-transparent',
                td: 'px-7 py-4 border-b border-gray-50 bg-transparent',
                container: 'group',
                pagination: 'flex justify-between items-center text-xs font-medium text-gray-500'
            },
            language: {
                search: { placeholder: calibrationText.searchCalibrationTasks },
                pagination: {
                    previous: calibrationText.previous,
                    next: calibrationText.next,
                    showing: calibrationText.showing,
                    results: () => calibrationText.results,
                },
                loading: calibrationText.loading,
                noRecordsFound: calibrationText.noMatchingRecordsFound,
                error: calibrationText.unableToLoadData,
            }
        });

        // Hide default gridjs search completely since we are using custom one 
        const defaultSearch = document.querySelector('.gridjs-search');
        if(defaultSearch) defaultSearch.style.display = 'none';

        // Connect custom search input to Grid.js
        let searchTimeout;
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const keyword = e.target.value;
                const url = new URL(window.location);
                if (keyword) {
                    url.searchParams.set('keywords', keyword);
                } else {
                    url.searchParams.delete('keywords');
                }
                window.history.replaceState({}, '', url);

                // Re-fetch data
                window.grid.updateConfig({
                    server: {
                        url: '{{ url("api/calibration-tasks") }}' + (keyword ? `?search=${encodeURIComponent(keyword)}` : ''),
                        then: window.grid.config.server.then,
                        handle: window.grid.config.server.handle
                    }
                }).forceRender();
            }, 500);
        });
    });

    // Delete task function logic
    function confirmDelete(id) {
        var formData = new FormData();
        formData.append('_token', '{{csrf_token()}}');
        formData.append('id', id);

        fetch("{{ url('delete-task') }}", {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: formData
        })
            .then((res) => res.json())
            .then((data) => {
                if (!data.success) {
                    notify('failed', data.msg || calibrationText.deletionFailed);
                } else {
                    window.grid.forceRender();
                    notify('success', data.msg || calibrationText.taskDeletedSuccessfully);
                }
            });
    }

    document.addEventListener('click', (event) => {
        const toggle = event.target.closest('[data-calibration-task-toggle]');
        if (toggle) {
            event.preventDefault();
            event.stopPropagation();
            const id = toggle.dataset.calibrationTaskToggle;
            const menu = document.querySelector(`[data-calibration-task-menu="${id}"]`);
            const willOpen = menu?.classList.contains('hidden');
            closeCalibrationTaskMenus();
            if (menu && willOpen) {
                menu.classList.remove('hidden');
            }
            return;
        }

        const editButton = event.target.closest('[data-calibration-task-edit]');
        if (editButton) {
            event.preventDefault();
            event.stopPropagation();
            closeCalibrationTaskMenus();
            window.edit_task(null, editButton.dataset.calibrationTaskEdit);
            return;
        }

        const deleteButton = event.target.closest('[data-calibration-task-delete]');
        if (deleteButton) {
            event.preventDefault();
            event.stopPropagation();
            closeCalibrationTaskMenus();
            window.dispatchEvent(new CustomEvent('delete-task', { detail: { id: deleteButton.dataset.calibrationTaskDelete } }));
            return;
        }

        if (!event.target.closest('[data-calibration-task-menu]')) {
            closeCalibrationTaskMenus();
        }
    });
</script>

@include('tasks.schedule_task_modal')
@include('common.navigations.footer')
