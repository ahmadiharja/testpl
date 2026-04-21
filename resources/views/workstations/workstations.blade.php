@include('common.navigations.header')

@php
    $role = session('role');
    $canManageWorkstations = in_array($role, ['super', 'admin'], true);
    $initialWorkstationStatus = in_array(request('type'), ['ok', 'failed'], true) ? request('type') : '';
    $workstationText = [
        'allFacilities' => __('All facilities'),
        'allWorkgroups' => __('All workgroups'),
        'selectFacility' => __('Select facility'),
        'selectWorkgroup' => __('Select workgroup'),
        'option' => __('option'),
        'options' => __('options'),
        'noOptionsFound' => __('No options found'),
        'display' => __('display'),
        'displays' => __('displays'),
        'needAttention' => __('need attention'),
        'name' => __('Name'),
        'workgroup' => __('Workgroup'),
        'facility' => __('Facility'),
        'sleepTime' => __('Sleep Time'),
        'displaysLabel' => __('Displays'),
        'lastConnected' => __('Last Connected'),
        'actions' => __('Actions'),
        'searchWorkstations' => __('Search workstations...'),
        'searchFacilities' => __('Search facilities...'),
        'searchWorkgroups' => __('Search workgroups...'),
        'previous' => __('Previous'),
        'next' => __('Next'),
        'showing' => __('Showing'),
        'of' => __('of'),
        'page' => __('Page'),
        'results' => __('results'),
        'loading' => __('Loading...'),
        'noMatchingRecordsFound' => __('No matching records found'),
        'unableToLoadData' => __('Unable to load data'),
        'displaysNeedAttentionTitle' => __('Displays needing attention'),
        'loadingAttentionDisplays' => __('Loading displays needing attention...'),
        'noDisplaysNeedAttention' => __('No displays currently need attention in this workstation.'),
        'unableToLoadAttentionDisplays' => __('Unable to load displays needing attention.'),
        'openDisplay' => __('Open Display'),
        'loadingMore' => __('Loading more...'),
        'saveChanges' => __('Save Changes'),
        'saving' => __('Saving...'),
        'updateWorkstationDetails' => __('Update workstation details'),
        'adjustWorkstationTable' => __('Adjust workstation information and settings without leaving the table.'),
        'unableToLoadWorkstationSettings' => __('Unable to load workstation settings.'),
        'unableToSaveWorkstation' => __('Unable to save workstation.'),
        'deleteWorkstation' => __('Delete Workstation'),
        'deleting' => __('Deleting...'),
        'unableToDeleteWorkstation' => __('Unable to delete workstation.'),
        'application' => __('Application'),
        'displayCalibration' => __('Display Calibration'),
        'qualityAssurance' => __('Quality Assurance'),
        'location' => __('Location'),
        'workstationName' => __('Workstation Name'),
        'language' => __('Language'),
        'databaseSynchronizationInterval' => __('Database Synchronization Interval'),
        'reminderInterval' => __('Reminder Interval'),
        'backupPeriod' => __('Backup Period'),
        'selectLanguage' => __('Select language'),
        'selectSyncInterval' => __('Select sync interval'),
        'selectReminderInterval' => __('Select reminder interval'),
        'selectBackupPeriod' => __('Select backup period'),
        'enableScheduler' => __('Enable Scheduler'),
        'enableSchedulerDescription' => __('Allow the client application to run and remind scheduled tasks.'),
        'schedulerAppliedOnNextSync' => __('Applied on the workstation after the next client sync.'),
        'reminderRequiresScheduler' => __('Enable Scheduler first to adjust reminder timing.'),
        'updateSoftwareAutomatically' => __('Update Software Automatically'),
        'updateSoftwareAutomaticallyDescription' => __('Let the workstation pull approved software updates automatically.'),
        'unitsOfLength' => __('Units of Length'),
        'unitsOfLuminance' => __('Units of Luminance'),
        'veilingLuminance' => __('Veiling Luminance'),
        'ambientConditionsStable' => __('Ambient Conditions Stable'),
        'energySaveStart' => __('Energy Save Start'),
        'energySaveEnd' => __('Energy Save End'),
        'selectLengthUnit' => __('Select length unit'),
        'selectLuminanceUnit' => __('Select luminance unit'),
        'selectValue' => __('Select value'),
        'onlySuperUsersCanChangeWorkgroup' => __('Only super users can change workgroup.'),
        'enableDisplayEnergySaveMode' => __('Enable Display Energy Save Mode'),
        'preset' => __('Preset'),
        'selectPreset' => __('Select preset'),
        'luminanceResponse' => __('Luminance Response'),
        'selectResponse' => __('Select response'),
        'colorTemperature' => __('Color Temperature'),
        'selectTemperature' => __('Select temperature'),
        'maxLuminance' => __('Max Luminance'),
        'selectMaxLuminance' => __('Select max luminance'),
        'blackLevel' => __('Black Level'),
        'applyWhiteLevel' => __('Apply white level target'),
        'applyWhiteLevelDescription' => __('Tell the client to apply the selected max luminance target during calibration.'),
        'applyBlackLevel' => __('Apply black level target'),
        'applyBlackLevelDescription' => __('Tell the client to apply the selected black level target during calibration.'),
        'gamut' => __('Gamut'),
        'selectGamut' => __('Select gamut'),
        'createDisplayIccProfile' => __('Create Display ICC Profile'),
        'regulation' => __('Regulation'),
        'selectRegulation' => __('Select regulation'),
        'displayCategory' => __('Display Category'),
        'selectCategory' => __('Select category'),
        'bodyRegion' => __('Body Region'),
        'startDailyTestsAutomatically' => __('Start daily tests automatically'),
        'qaStepsCatalog' => __('QA Steps Catalog'),
        'qaStepsCatalogDescription' => __('Read-only QA step details received from the client application.'),
        'qaStepsCatalogEmpty' => __('No QA step catalog has been received from this workstation yet.'),
        'received' => __('Received'),
        'facilityLabel' => __('Facility Label'),
        'department' => __('Department'),
        'room' => __('Room'),
        'responsiblePerson' => __('Responsible Person'),
        'address' => __('Address'),
        'city' => __('City'),
        'email' => __('Email'),
        'phoneNumber' => __('Phone Number'),
    ];
@endphp

<style>
    .workstation-directory-shell {
        border-radius: 2rem;
        border: 1px solid #d5e0ec;
        background: linear-gradient(180deg, #f7fbff 0%, #ffffff 100%);
        box-shadow: 0 26px 64px -46px rgba(15, 23, 42, 0.34);
        overflow: hidden;
    }
    .workstation-table-controlbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 14px 18px;
        border-bottom: 1px solid #e3ecf5;
        background: linear-gradient(180deg, #f4f9ff 0%, #ffffff 100%);
    }
    .workstation-controls-left {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .workstation-filter-picker {
        position: relative;
        min-width: 240px;
    }
    .workstation-filter-trigger {
        width: 100%;
        height: 36px;
        border-radius: 999px;
        border: 1px solid #d3dfec;
        background: #ffffff;
        color: #4b6078;
        font-size: 12px;
        font-weight: 700;
        padding: 0 12px;
    }
    .workstation-filter-panel {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        z-index: 40;
        border-radius: 16px;
        border: 1px solid #d3dfec;
        background: #ffffff;
        box-shadow: 0 24px 50px -26px rgba(15, 23, 42, 0.32);
        padding: 10px;
    }
    .workstation-filter-search {
        width: 100%;
        height: 34px;
        border-radius: 10px;
        border: 1px solid #d7e3ef;
        padding: 0 10px;
        font-size: 12px;
        color: #334155;
    }
    .workstation-filter-search:focus {
        outline: none;
        border-color: #1d9bf0;
        box-shadow: 0 0 0 3px rgba(29, 155, 240, 0.14);
    }
    .workstation-status-group {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px;
        border-radius: 999px;
        border: 1px solid #d3dfec;
        background: #f3f8fe;
    }
    .workstation-status-pill {
        height: 36px;
        border-radius: 999px;
        padding: 0 14px;
        font-size: 12px;
        font-weight: 700;
        color: #4c6077;
        transition: all .18s ease;
    }
    .workstation-status-pill:hover {
        color: #243b53;
        background: #eff6fd;
    }
    .workstation-reset-btn {
        height: 36px;
        border-radius: 999px;
        border: 1px solid #d3dfec;
        background: #fff;
        color: #4b6078;
        font-size: 12px;
        font-weight: 700;
        padding: 0 14px;
        transition: all .18s ease;
    }
    .workstation-reset-btn:hover {
        border-color: #9ec6ea;
        color: #1f4f80;
        background: #f3f9ff;
    }
    .workstation-table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 18px;
        border-bottom: 1px solid #e3ecf5;
        background: #f8fbff;
    }
    .workstation-table-search-wrap {
        position: relative;
        width: min(440px, 100%);
    }
    .workstation-table-search {
        width: 100%;
        height: 42px;
        border-radius: 999px;
        border: 1px solid #c9d8e8;
        padding: 0 46px 0 16px;
        font-size: 14px;
        font-weight: 600;
        color: #12263a;
        background: #fff;
    }
    .workstation-table-search:focus {
        outline: none;
        border-color: #1d9bf0;
        box-shadow: 0 0 0 3px rgba(29, 155, 240, 0.16);
    }
    .workstation-table-search-clear {
        position: absolute;
        top: 50%;
        right: 8px;
        display: inline-flex;
        width: 28px;
        height: 28px;
        align-items: center;
        justify-content: center;
        transform: translateY(-50%);
        border: 0;
        border-radius: 999px;
        color: #64748b;
        background: transparent;
        transition: background .18s ease, color .18s ease;
    }
    .workstation-table-search-clear:hover,
    .workstation-table-search-clear:focus {
        color: #0f172a;
        background: #e8f2fb;
        outline: none;
    }
    .workstation-table-search-clear[hidden] {
        display: none;
    }
    .workstation-table-wrap {
        overflow-x: auto;
        background: #fff;
    }
    .workstation-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 1080px;
        table-layout: fixed;
    }
    .workstation-table th {
        padding: 13px 16px;
        text-align: left;
        border-bottom: 1px solid #d8e4f0;
        background: #e9f1fa;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .18em;
        text-transform: uppercase;
        color: #4d647d;
        white-space: nowrap;
    }
    .workstation-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #edf2f8;
        font-size: 14px;
        color: #334155;
        vertical-align: middle;
        background: #fff;
    }
    .workstation-table td:nth-child(2),
    .workstation-table td:nth-child(3) {
        overflow: visible;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .workstation-tooltip-cell {
        position: relative;
        display: block;
        flex: 1 1 auto;
        min-width: 0;
        max-width: 100%;
        vertical-align: middle;
    }
    .workstation-tooltip-bubble {
        position: absolute;
        left: 0;
        bottom: calc(100% + 10px);
        z-index: 80;
        min-width: 220px;
        max-width: min(420px, 70vw);
        padding: 10px 12px;
        border-radius: 10px;
        background: #0f172a;
        color: #fff;
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.22);
        font-size: 12px;
        font-weight: 600;
        line-height: 1.45;
        letter-spacing: 0;
        white-space: pre-line;
        overflow-wrap: anywhere;
        opacity: 0;
        pointer-events: none;
        transform: translateY(4px);
        transition: opacity .16s ease, transform .16s ease;
    }
    .workstation-tooltip-bubble::after {
        content: '';
        position: absolute;
        left: 18px;
        top: 100%;
        border: 6px solid transparent;
        border-top-color: #0f172a;
    }
    .workstation-tooltip-cell:hover .workstation-tooltip-bubble,
    .workstation-tooltip-cell:focus .workstation-tooltip-bubble {
        opacity: 1;
        transform: translateY(0);
    }
    .workstation-table th:nth-child(4),
    .workstation-table th:nth-child(5),
    .workstation-table th:nth-child(6),
    .workstation-table th:nth-child(7),
    .workstation-table td:nth-child(4),
    .workstation-table td:nth-child(5),
    .workstation-table td:nth-child(6),
    .workstation-table td:nth-child(7) {
        text-align: center;
    }
    .workstation-table th:nth-child(4),
    .workstation-table td:nth-child(4) {
        padding-right: 20px;
    }
    .workstation-table th:nth-child(5),
    .workstation-table td:nth-child(5) {
        padding-left: 20px;
    }
    .workstation-row-clickable {
        cursor: pointer;
    }
    .workstation-scope-cell {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
        max-width: 100%;
    }
    .workstation-scope-badge {
        width: 18px;
        height: 18px;
        border-radius: 999px;
        background: #1d9bf0;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: 0;
        line-height: 1;
        flex-shrink: 0;
    }
    .workstation-scope-link {
        display: block;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .workstation-table tbody tr:hover td {
        background: #f7fbff;
    }
    .workstation-sort-btn {
        border: 0;
        background: transparent;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #5a7087;
        font: inherit;
        letter-spacing: inherit;
        text-transform: inherit;
        cursor: pointer;
        border-radius: 999px;
        padding: 2px 8px;
        margin-left: -8px;
        transition: all .18s ease;
    }
    .workstation-sort-btn:hover {
        background: #f2f7fd;
        color: #2f4d6a;
    }
    .workstation-sort-btn.is-active {
        background: #e2edf9;
        color: #24486b;
    }
    .workstation-sort-indicator {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        border-radius: 999px;
        background: #d4e3f4;
        color: #2f5477;
        font-size: 10px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: 0;
        transition: all .18s ease;
    }
    .workstation-sort-btn.is-active .workstation-sort-indicator {
        background: #2f6fae;
        color: #ffffff;
    }
    .workstation-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 16px 14px;
        border-top: 1px solid #dbe7f3;
        background: #f7fbff;
    }
    .workstation-pager {
        display: inline-flex;
        gap: 8px;
        align-items: center;
    }
    .workstation-page-btn {
        height: 32px;
        min-width: 32px;
        border-radius: 999px;
        border: 1px solid #c7d6e7;
        background: #ffffff;
        color: #2c4158;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        padding: 0 12px;
        transition: all .18s ease;
    }
    .workstation-page-btn:hover:not(:disabled) {
        border-color: #1d9bf0;
        color: #0f5f9f;
        background: #f0f8ff;
    }
    .workstation-page-btn:disabled {
        opacity: .45;
        cursor: not-allowed;
    }
    .workstation-row-action {
        width: 36px;
        height: 36px;
        border-radius: 9999px;
        border: 1px solid #d6dee8;
        background: #fff;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .workstation-empty {
        padding: 24px 16px;
        text-align: center;
        color: #5f7388;
        font-size: 14px;
        border-bottom: 1px solid #edf2f8;
    }
    @media (max-width: 1240px) {
        .workstation-table-controlbar {
            flex-wrap: wrap;
            align-items: stretch;
        }
        .workstation-controls-left {
            flex-wrap: wrap;
            width: 100%;
        }
        .workstation-filter-picker {
            min-width: 100%;
        }
        .workstation-status-group {
            width: 100%;
            justify-content: space-between;
        }
        .workstation-status-pill {
            flex: 1 1 0;
            padding: 0 10px;
        }
        .workstation-reset-btn {
            width: 100%;
            justify-content: center;
        }
    }
    @media (max-width: 768px) {
        .workstation-table-toolbar {
            flex-direction: column;
            align-items: stretch;
        }
        .workstation-table-search-wrap {
            width: 100%;
        }
        .workstation-table-footer {
            flex-direction: column;
            align-items: stretch;
        }
        .workstation-pager {
            justify-content: flex-end;
        }
    }
</style>

<div class="flex flex-col gap-6 pb-8">
    <x-page-header title="{{ __('All Workstations') }}" description="{{ __('Manage physical hardware clusters connecting your remote displays.') }}" icon="monitor-speaker">
        <x-slot name="actions">
            <x-export-dropdown
                excel-url="{{ url('reports/workstations?export_type=excel') }}"
                pdf-url="{{ url('reports/workstations?export_type=pdf') }}" />
        </x-slot>
    </x-page-header>

    <section class="workstation-directory-shell mb-10">
        <div class="workstation-table-controlbar">
            <div class="workstation-controls-left">
                <div class="workstation-filter-picker">
                    <button id="facility-filter-trigger" type="button" class="workstation-filter-trigger inline-flex items-center justify-between gap-2">
                        <span id="facility-filter-label" class="truncate">{{ __('All facilities') }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>
                    <div id="facility-filter-panel" class="workstation-filter-panel hidden">
                        <input id="facility-filter-search" type="text" placeholder="{{ __('Search facilities...') }}" class="workstation-filter-search">
                        <p id="facility-filter-hint" class="mb-2 mt-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="facility-filter-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>

                <div class="workstation-filter-picker">
                    <button id="workgroup-filter-trigger" type="button" class="workstation-filter-trigger inline-flex items-center justify-between gap-2">
                        <span id="workgroup-filter-label" class="truncate">{{ __('All workgroups') }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>
                    <div id="workgroup-filter-panel" class="workstation-filter-panel hidden">
                        <input id="workgroup-filter-search" type="text" placeholder="{{ __('Search workgroups...') }}" class="workstation-filter-search">
                        <p id="workgroup-filter-hint" class="mb-2 mt-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="workgroup-filter-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>

                <div class="workstation-status-group">
                    <button id="workstation-status-all" type="button" data-status="" class="workstation-status-pill">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="layers-3" class="h-4 w-4"></i>
                            <span>{{ __('All') }}</span>
                        </span>
                    </button>
                    <button id="workstation-status-ok" type="button" data-status="ok" class="workstation-status-pill">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="badge-check" class="h-4 w-4"></i>
                            <span>{{ __('OK') }}</span>
                        </span>
                    </button>
                    <button id="workstation-status-failed" type="button" data-status="failed" class="workstation-status-pill">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="triangle-alert" class="h-4 w-4"></i>
                            <span>{{ __('Not OK') }}</span>
                        </span>
                    </button>
                </div>
            </div>

            <button id="reset-workstation-filters" type="button" class="workstation-reset-btn inline-flex items-center gap-2">
                <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                {{ __('Reset Filters') }}
            </button>
        </div>

        <div class="workstation-table-toolbar">
            <div class="workstation-table-search-wrap">
                <input id="workstation-table-search" type="text" class="workstation-table-search" placeholder="{{ __('Search workstations...') }}">
                <button id="workstation-table-search-clear" type="button" class="workstation-table-search-clear" aria-label="{{ __('Clear search') }}" hidden>
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>
            <div class="text-[12px] font-semibold text-slate-500" id="workstation-table-meta"></div>
        </div>

        <div class="workstation-table-wrap">
            <table class="workstation-table">
                <colgroup>
                    <col style="width: 24%">
                    <col style="width: 17%">
                    <col style="width: 17%">
                    <col style="width: 11%">
                    <col style="width: 10%">
                    <col style="width: 13%">
                    <col style="width: 8%">
                </colgroup>
                <thead>
                    <tr>
                        <th><button type="button" data-workstation-sort="name" class="workstation-sort-btn"><span>{{ __('Name') }}</span><span class="workstation-sort-indicator" data-workstation-sort-indicator="name">↕</span></button></th>
                        <th><button type="button" data-workstation-sort="wgName" class="workstation-sort-btn"><span>{{ __('Workgroup') }}</span><span class="workstation-sort-indicator" data-workstation-sort-indicator="wgName">↕</span></button></th>
                        <th><button type="button" data-workstation-sort="facName" class="workstation-sort-btn"><span>{{ __('Facility') }}</span><span class="workstation-sort-indicator" data-workstation-sort-indicator="facName">↕</span></button></th>
                        <th><button type="button" data-workstation-sort="sleepTime" class="workstation-sort-btn"><span>{{ __('Sleep Time') }}</span><span class="workstation-sort-indicator" data-workstation-sort-indicator="sleepTime">↕</span></button></th>
                        <th><button type="button" data-workstation-sort="displaysCount" class="workstation-sort-btn"><span>{{ __('Displays') }}</span><span class="workstation-sort-indicator" data-workstation-sort-indicator="displaysCount">↕</span></button></th>
                        <th><button type="button" data-workstation-sort="lastConnected" class="workstation-sort-btn"><span>{{ __('Last Connected') }}</span><span class="workstation-sort-indicator" data-workstation-sort-indicator="lastConnected">↕</span></button></th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="workstations-table-body"></tbody>
            </table>
        </div>

        <div class="workstation-table-footer">
            <div class="text-[12px] font-semibold text-slate-500" id="workstation-table-summary"></div>
            <div class="workstation-pager">
                <button id="workstation-page-prev" type="button" class="workstation-page-btn">{{ __('Previous') }}</button>
                <span id="workstation-page-label" class="text-[12px] font-semibold text-slate-500"></span>
                <button id="workstation-page-next" type="button" class="workstation-page-btn">{{ __('Next') }}</button>
            </div>
        </div>
    </section>
</div>

<div id="workstation-action-overlay" class="pointer-events-none fixed inset-0 z-[1200] hidden">
    <div
        id="workstation-action-menu"
        class="pointer-events-auto fixed hidden w-56 rounded-2xl border border-slate-200 bg-white p-2 shadow-[0_20px_60px_-28px_rgba(15,23,42,0.35)]">
        @if($canManageWorkstations)
            <button
                id="workstation-action-edit"
                type="button"
                class="grid w-full grid-cols-[1rem_1fr] items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
                <i data-lucide="settings" class="h-4 w-4"></i>
                <span>{{ __('Workstation Setting') }}</span>
            </button>
            <button
                id="workstation-action-delete"
                type="button"
                class="grid w-full grid-cols-[1rem_1fr] items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
                <span>{{ __('Delete Workstation') }}</span>
            </button>
        @endif
    </div>
</div>

<div id="workstation-edit-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="flex max-h-[88vh] w-full max-w-5xl flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Workstation Settings') }}</p>
                <h3 id="workstation-edit-title" class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Update workstation details') }}</h3>
                <p id="workstation-edit-subtitle" class="mt-2 text-sm text-slate-500">{{ __('Adjust workstation information and settings without leaving the table.') }}</p>
            </div>
            <button id="workstation-edit-close" type="button" class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <div class="shrink-0 border-b border-slate-200 px-6 py-4">
            <div id="workstation-edit-tabs" class="flex flex-wrap gap-2"></div>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6">
            <div id="workstation-edit-loading" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                {{ __('Loading workstation settings...') }}
            </div>
            <div id="workstation-edit-error" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700"></div>
            <div id="workstation-edit-form" class="hidden"></div>
        </div>

        <div class="flex shrink-0 justify-end gap-3 border-t border-slate-200 px-6 py-5">
            <button
                id="workstation-edit-cancel"
                type="button"
                class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                {{ __('Cancel') }}
            </button>
            <button
                id="workstation-edit-save"
                type="button"
                class="inline-flex h-11 items-center gap-2 rounded-2xl bg-sky-500 px-4 text-sm font-semibold text-white transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-60">
                <i data-lucide="save" class="h-4 w-4"></i>
                <span id="workstation-edit-save-label">{{ __('Save Changes') }}</span>
            </button>
        </div>
    </div>
</div>

<div id="workstation-delete-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="w-full max-w-lg rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="border-b border-slate-200 px-6 py-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rose-400">{{ __('Delete Workstation') }}</p>
            <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Delete this workstation?') }}</h3>
            <p class="mt-3 text-sm text-slate-500">
                {{ __('This action will permanently remove') }} <span id="workstation-delete-name" class="font-semibold text-slate-700"></span>.
                {{ __('Workstations that still have displays attached cannot be deleted.') }}
            </p>
        </div>

        <div class="flex justify-end gap-3 px-6 py-5">
            <button
                id="workstation-delete-cancel"
                type="button"
                class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                {{ __('Cancel') }}
            </button>
            <button
                id="workstation-delete-confirm"
                type="button"
                class="inline-flex h-11 items-center rounded-2xl bg-rose-500 px-4 text-sm font-semibold text-white transition hover:bg-rose-400 disabled:cursor-not-allowed disabled:opacity-60">
                {{ __('Delete Workstation') }}
            </button>
        </div>
    </div>
</div>

<div id="workstation-attention-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="flex max-h-[88vh] w-full max-w-6xl flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rose-400">{{ __('Needs Attention') }}</p>
                <h3 id="workstation-attention-title" class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Displays needing attention') }}</h3>
                <p id="workstation-attention-subtitle" class="mt-2 text-sm text-slate-500"></p>
            </div>
            <button id="workstation-attention-close" type="button" class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <div class="min-h-0 flex-1 px-6 py-6">
            <div id="workstation-attention-loading" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                {{ __('Loading displays needing attention...') }}
            </div>
            <div id="workstation-attention-error" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700"></div>
            <div id="workstation-attention-empty" class="hidden rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-700">{{ __('No displays currently need attention in this workstation.') }}</div>

            <div id="workstation-attention-list-wrap" class="hidden h-[56vh] overflow-y-auto rounded-2xl border border-slate-200 overscroll-contain">
                <table class="min-w-full table-fixed">
                    <thead class="sticky top-0 z-10 bg-slate-100 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-left">
                                <button type="button" data-attention-sort="displayName" class="inline-flex items-center gap-1 transition hover:text-slate-700">
                                    <span>{{ __('Display') }}</span><span data-attention-sort-indicator="displayName">↕</span>
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left">
                                <button type="button" data-attention-sort="updatedAt" class="inline-flex items-center gap-1 transition hover:text-slate-700">
                                    <span>{{ __('Last Sync') }}</span><span data-attention-sort-indicator="updatedAt">↓</span>
                                </button>
                            </th>
                            <th class="px-4 py-3 text-left">
                                <button type="button" data-attention-sort="attentionText" class="inline-flex items-center gap-1 transition hover:text-slate-700">
                                    <span>{{ __('Detail') }}</span><span data-attention-sort-indicator="attentionText">↕</span>
                                </button>
                            </th>
                            <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="workstation-attention-list" class="divide-y divide-slate-100 bg-white"></tbody>
                </table>
                <div id="workstation-attention-more" class="hidden border-t border-slate-200 bg-slate-50 px-4 py-2 text-center text-[12px] font-medium text-slate-500">{{ __('Loading more...') }}</div>
            </div>
        </div>
    </div>
</div>

<script id="workstation-filters-data" type="application/json">@json($filters)</script>
<script>
(function () {
    const text = @json($workstationText);
    const canManageWorkstations = @json($canManageWorkstations);
    let initialized = false;
    const state = {
        config: { canChooseFacility: false, facilities: [], workgroupsByFacility: {}, selectedFacilityId: '', selectedWorkgroupId: '' },
        selectedFacilityId: '',
        selectedWorkgroupId: '',
        defaultStatus: @json($initialWorkstationStatus),
        selectedStatus: @json($initialWorkstationStatus),
        facilitySearch: '',
        workgroupSearch: '',
        activeDropdown: null,
        actionTarget: null,
        deleteTarget: null,
        workstationRows: [],
        workstationPage: 1,
        workstationLimit: 10,
        workstationTotal: 0,
        workstationLoading: false,
        workstationSearch: '',
        workstationSearchTimer: null,
        workstationSortKey: 'name',
        workstationSortDir: 'asc',
        attentionTarget: null,
        attentionPage: 1,
        attentionHasMore: false,
        attentionLoadingMore: false,
        attentionLimit: 9,
        attentionRows: [],
        attentionSortKey: 'updatedAt',
        attentionSortDir: 'desc',
        edit: {
            id: null,
            meta: null,
            settings: null,
            options: {},
            tab: 'application',
            saving: false,
        },
    };

    const els = {};

    function init() {
        if (initialized) return;
        if (!window.Perfectlum) {
            window.setTimeout(init, 50);
            return;
        }

        initialized = true;

        try {
            state.config = JSON.parse(document.getElementById('workstation-filters-data')?.textContent || '{}');
        } catch (error) {
            state.config = { canChooseFacility: false, facilities: [], workgroupsByFacility: {}, selectedFacilityId: '', selectedWorkgroupId: '' };
        }

        state.selectedFacilityId = state.config.selectedFacilityId || '';
        state.selectedWorkgroupId = state.config.selectedWorkgroupId || '';

        bindElements();
        portalWorkstationLayersToBody();
        bindEvents();
        renderFilters();
        updateWorkstationSortIndicators();
        updateAttentionSortIndicators();
        updateSearchClearButton();
        loadWorkstations();
        window.workstationsPage = {
            toggleActionMenu,
            openEditModal,
            openDeleteModal,
            openQuickEdit: openQuickEditAction,
            openQuickDelete: openQuickDeleteAction,
        };
        window.lucide?.createIcons();
    }

    function bindElements() {
        els.facilityTrigger = document.getElementById('facility-filter-trigger');
        els.facilityLabel = document.getElementById('facility-filter-label');
        els.facilityPanel = document.getElementById('facility-filter-panel');
        els.facilitySearch = document.getElementById('facility-filter-search');
        els.facilityHint = document.getElementById('facility-filter-hint');
        els.facilityOptions = document.getElementById('facility-filter-options');

        els.workgroupTrigger = document.getElementById('workgroup-filter-trigger');
        els.workgroupLabel = document.getElementById('workgroup-filter-label');
        els.workgroupPanel = document.getElementById('workgroup-filter-panel');
        els.workgroupSearch = document.getElementById('workgroup-filter-search');
        els.workgroupHint = document.getElementById('workgroup-filter-hint');
        els.workgroupOptions = document.getElementById('workgroup-filter-options');
        els.statusButtons = [
            document.getElementById('workstation-status-all'),
            document.getElementById('workstation-status-ok'),
            document.getElementById('workstation-status-failed'),
        ].filter(Boolean);

        els.resetFilters = document.getElementById('reset-workstation-filters');
        els.tableSearch = document.getElementById('workstation-table-search');
        els.tableSearchClear = document.getElementById('workstation-table-search-clear');
        els.tableMeta = document.getElementById('workstation-table-meta');
        els.tableBody = document.getElementById('workstations-table-body');
        els.tableSummary = document.getElementById('workstation-table-summary');
        els.pagePrev = document.getElementById('workstation-page-prev');
        els.pageNext = document.getElementById('workstation-page-next');
        els.pageLabel = document.getElementById('workstation-page-label');
        els.sortButtons = Array.from(document.querySelectorAll('[data-workstation-sort]'));

        els.actionOverlay = document.getElementById('workstation-action-overlay');
        els.actionMenu = document.getElementById('workstation-action-menu');
        els.actionEdit = document.getElementById('workstation-action-edit');
        els.actionDelete = document.getElementById('workstation-action-delete');

        els.editModal = document.getElementById('workstation-edit-modal');
        els.editClose = document.getElementById('workstation-edit-close');
        els.editCancel = document.getElementById('workstation-edit-cancel');
        els.editSave = document.getElementById('workstation-edit-save');
        els.editSaveLabel = document.getElementById('workstation-edit-save-label');
        els.editTitle = document.getElementById('workstation-edit-title');
        els.editSubtitle = document.getElementById('workstation-edit-subtitle');
        els.editTabs = document.getElementById('workstation-edit-tabs');
        els.editLoading = document.getElementById('workstation-edit-loading');
        els.editError = document.getElementById('workstation-edit-error');
        els.editForm = document.getElementById('workstation-edit-form');

        els.deleteModal = document.getElementById('workstation-delete-modal');
        els.deleteName = document.getElementById('workstation-delete-name');
        els.deleteCancel = document.getElementById('workstation-delete-cancel');
        els.deleteConfirm = document.getElementById('workstation-delete-confirm');

        els.attentionModal = document.getElementById('workstation-attention-modal');
        els.attentionClose = document.getElementById('workstation-attention-close');
        els.attentionTitle = document.getElementById('workstation-attention-title');
        els.attentionSubtitle = document.getElementById('workstation-attention-subtitle');
        els.attentionLoading = document.getElementById('workstation-attention-loading');
        els.attentionError = document.getElementById('workstation-attention-error');
        els.attentionEmpty = document.getElementById('workstation-attention-empty');
        els.attentionListWrap = document.getElementById('workstation-attention-list-wrap');
        els.attentionList = document.getElementById('workstation-attention-list');
        els.attentionMore = document.getElementById('workstation-attention-more');
        els.attentionSortButtons = Array.from(document.querySelectorAll('[data-attention-sort]'));
    }

    function portalWorkstationLayersToBody() {
        const nodes = [
            els.actionOverlay,
            els.editModal,
            els.deleteModal,
            els.attentionModal,
        ].filter(Boolean);

        nodes.forEach((node) => {
            if (node.parentElement !== document.body) {
                document.body.appendChild(node);
            }
        });
    }

    function bindEvents() {
        els.facilityTrigger?.addEventListener('click', () => toggleDropdown('facility'));
        els.workgroupTrigger?.addEventListener('click', () => toggleDropdown('workgroup'));
        els.facilitySearch?.addEventListener('input', (event) => {
            state.facilitySearch = event.target.value || '';
            renderFacilityOptions();
        });
        els.workgroupSearch?.addEventListener('input', (event) => {
            state.workgroupSearch = event.target.value || '';
            renderWorkgroupOptions();
        });
        els.statusButtons.forEach((button) => {
            button.addEventListener('click', () => {
                state.selectedStatus = button.dataset.status || '';
                state.workstationPage = 1;
                renderStatusFilter();
                loadWorkstations();
            });
        });
        els.resetFilters?.addEventListener('click', resetFilters);

        els.tableSearch?.addEventListener('input', (event) => {
            const value = String(event.target.value || '').trim();
            updateSearchClearButton();
            if (state.workstationSearchTimer) {
                window.clearTimeout(state.workstationSearchTimer);
            }
            state.workstationSearchTimer = window.setTimeout(() => {
                state.workstationSearch = value;
                state.workstationPage = 1;
                loadWorkstations();
            }, 260);
        });

        els.tableSearchClear?.addEventListener('click', () => {
            if (!els.tableSearch) return;

            if (state.workstationSearchTimer) {
                window.clearTimeout(state.workstationSearchTimer);
                state.workstationSearchTimer = null;
            }

            const hadSearch = els.tableSearch.value.length > 0 || state.workstationSearch.length > 0;
            els.tableSearch.value = '';
            state.workstationSearch = '';
            state.workstationPage = 1;
            updateSearchClearButton();
            els.tableSearch.focus();

            if (hadSearch) {
                loadWorkstations();
            }
        });

        els.sortButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const key = button.dataset.workstationSort;
                if (!key) return;
                if (state.workstationSortKey === key) {
                    state.workstationSortDir = state.workstationSortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    state.workstationSortKey = key;
                    state.workstationSortDir = key === 'lastConnected' ? 'desc' : 'asc';
                }
                updateWorkstationSortIndicators();
                state.workstationPage = 1;
                loadWorkstations();
            });
        });

        els.pagePrev?.addEventListener('click', () => {
            if (state.workstationPage <= 1 || state.workstationLoading) return;
            state.workstationPage -= 1;
            loadWorkstations();
        });

        els.pageNext?.addEventListener('click', () => {
            const totalPages = Math.max(1, Math.ceil(state.workstationTotal / state.workstationLimit));
            if (state.workstationPage >= totalPages || state.workstationLoading) return;
            state.workstationPage += 1;
            loadWorkstations();
        });

        els.tableBody?.addEventListener('click', (event) => {
            const attentionButton = event.target.closest('[data-action="attention"]');
            if (attentionButton) {
                const id = Number(attentionButton.dataset.workstationId || 0);
                const name = decodeURIComponent(attentionButton.dataset.workstationName || '');
                const failedCount = Number(attentionButton.dataset.failedCount || 0);
                openAttentionModal(id, name, failedCount);
                return;
            }

            const openButton = event.target.closest('[data-action="open-hierarchy"]');
            if (openButton) {
                const type = String(openButton.dataset.hierarchyType || '');
                const id = Number(openButton.dataset.hierarchyId || 0);
                if (type && id) {
                    window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type, id } }));
                }
                return;
            }

            const actionButton = event.target.closest('[data-action="menu"]');
            if (actionButton) {
                toggleActionMenu(event, Number(actionButton.dataset.workstationId || 0), decodeURIComponent(actionButton.dataset.workstationName || ''), actionButton);
                return;
            }

            const row = event.target.closest('tr[data-action="open-workstation-row"]');
            if (!row) {
                return;
            }

            if (event.target.closest('button, a, input, select, textarea, [role="button"]')) {
                return;
            }

            const id = Number(row.dataset.workstationId || 0);
            if (id > 0) {
                window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'workstation', id } }));
            }
        });

        document.addEventListener('click', (event) => {
            if (state.activeDropdown === 'facility' && !els.facilityPanel.contains(event.target) && !els.facilityTrigger.contains(event.target)) {
                closeDropdown();
            }
            if (state.activeDropdown === 'workgroup' && !els.workgroupPanel.contains(event.target) && !els.workgroupTrigger.contains(event.target)) {
                closeDropdown();
            }
            const clickedToggle = event.target.closest('[data-action="menu"]');
            if (!clickedToggle && els.actionMenu && !els.actionMenu.contains(event.target)) {
                closeActionMenu();
            }
        });

        els.actionOverlay?.addEventListener('click', closeActionMenu);
        els.actionEdit?.addEventListener('click', openQuickEditAction);
        els.actionDelete?.addEventListener('click', openQuickDeleteAction);

        els.editClose?.addEventListener('click', closeEditModal);
        els.editCancel?.addEventListener('click', closeEditModal);
        els.editSave?.addEventListener('click', saveEditModal);
        els.editModal?.addEventListener('click', (event) => {
            if (event.target === els.editModal) {
                closeEditModal();
            }
        });

        els.deleteCancel?.addEventListener('click', closeDeleteModal);
        els.deleteConfirm?.addEventListener('click', confirmDelete);
        els.deleteModal?.addEventListener('click', (event) => {
            if (event.target === els.deleteModal) {
                closeDeleteModal();
            }
        });

        els.attentionClose?.addEventListener('click', closeAttentionModal);
        els.attentionModal?.addEventListener('click', (event) => {
            if (event.target === els.attentionModal) closeAttentionModal();
        });
        els.attentionListWrap?.addEventListener('scroll', maybeLoadMoreAttentionRows);
        els.attentionSortButtons?.forEach((button) => {
            button.addEventListener('click', () => {
                const key = button.dataset.attentionSort;
                if (!key) return;
                if (state.attentionSortKey === key) {
                    state.attentionSortDir = state.attentionSortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    state.attentionSortKey = key;
                    state.attentionSortDir = key === 'updatedAt' ? 'desc' : 'asc';
                }
                updateAttentionSortIndicators();
                renderAttentionRows(state.attentionRows);
            });
        });
    }

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    function updateSearchClearButton() {
        if (!els.tableSearchClear || !els.tableSearch) return;

        if (String(els.tableSearch.value || '').length > 0) {
            els.tableSearchClear.removeAttribute('hidden');
        } else {
            els.tableSearchClear.setAttribute('hidden', 'hidden');
        }
    }

    function getFacilityOptions() {
        return Array.isArray(state.config.facilities) ? state.config.facilities : [];
    }

    function getWorkgroupOptions() {
        if (!state.selectedFacilityId) {
            return [];
        }

        const options = state.config.workgroupsByFacility?.[String(state.selectedFacilityId)] || [];
        return Array.isArray(options) ? options : [];
    }

    function findOptionLabel(options, value, fallback) {
        const match = options.find((item) => String(item.id) === String(value));
        return match?.name || fallback;
    }

    function renderFilters() {
        const facilities = getFacilityOptions();
        const workgroups = getWorkgroupOptions();

        els.facilityTrigger.disabled = !state.config.canChooseFacility && facilities.length <= 1;
        els.workgroupTrigger.disabled = !workgroups.length;

        els.facilityLabel.textContent = state.selectedFacilityId
            ? findOptionLabel(facilities, state.selectedFacilityId, text.selectFacility)
            : text.allFacilities;
        els.workgroupLabel.textContent = state.selectedWorkgroupId
            ? findOptionLabel(workgroups, state.selectedWorkgroupId, text.selectWorkgroup)
            : text.allWorkgroups;

        renderFacilityOptions();
        renderWorkgroupOptions();
        renderStatusFilter();
    }

    function renderStatusFilter() {
        els.statusButtons.forEach((button) => {
            const status = button.dataset.status || '';
            const active = status === (state.selectedStatus || '');
            button.className = 'workstation-status-pill';
            if (active) {
                if (status === 'ok') {
                    button.classList.add('bg-emerald-50', 'text-emerald-700', 'shadow-[inset_0_0_0_1px_rgba(16,185,129,0.22)]');
                } else if (status === 'failed') {
                    button.classList.add('bg-rose-50', 'text-rose-700', 'shadow-[inset_0_0_0_1px_rgba(244,63,94,0.22)]');
                } else {
                    button.classList.add('bg-sky-50', 'text-sky-700', 'shadow-[inset_0_0_0_1px_rgba(14,165,233,0.18)]');
                }
                return;
            }
            button.classList.add('text-slate-600', 'hover:bg-slate-50', 'hover:text-slate-900');
        });

        window.lucide?.createIcons();
    }

    function renderFacilityOptions() {
        const facilities = getFacilityOptions();
        const query = state.facilitySearch.trim().toLowerCase();
        let options = facilities.filter((item) => item.name.toLowerCase().includes(query));
        if (state.config.canChooseFacility) {
            options = [{ id: '', name: text.allFacilities }, ...options];
        }

        els.facilityHint.textContent = options.length
            ? `${options.length} ${options.length === 1 ? text.option : text.options}`
            : text.noOptionsFound;

        els.facilityOptions.innerHTML = options.length
            ? options.map((item) => `
                <button
                    type="button"
                    data-id="${String(item.id)}"
                    class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm ${String(item.id) === String(state.selectedFacilityId) ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-sky-50 hover:text-sky-700'}">
                    ${Perfectlum.escapeHtml(item.name)}
                </button>
            `).join('')
            : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">${Perfectlum.escapeHtml(text.noOptionsFound)}</div>`;

        els.facilityOptions.querySelectorAll('button[data-id]').forEach((button) => {
            button.addEventListener('click', () => {
                state.selectedFacilityId = button.dataset.id || '';
                state.selectedWorkgroupId = '';
                state.facilitySearch = '';
                if (els.facilitySearch) {
                    els.facilitySearch.value = '';
                }
                closeDropdown();
                renderFilters();
                state.workstationPage = 1;
                loadWorkstations();
            });
        });
    }

    function renderWorkgroupOptions() {
        const workgroups = getWorkgroupOptions();
        const query = state.workgroupSearch.trim().toLowerCase();
        const options = [{ id: '', name: text.allWorkgroups }, ...workgroups.filter((item) => item.name.toLowerCase().includes(query))];

        els.workgroupHint.textContent = options.length
            ? `${options.length} ${options.length === 1 ? text.option : text.options}`
            : text.noOptionsFound;

        els.workgroupOptions.innerHTML = options.length
            ? options.map((item) => `
                <button
                    type="button"
                    data-id="${String(item.id)}"
                    class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm ${String(item.id) === String(state.selectedWorkgroupId) ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-sky-50 hover:text-sky-700'}">
                    ${Perfectlum.escapeHtml(item.name)}
                </button>
            `).join('')
            : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">${Perfectlum.escapeHtml(text.noOptionsFound)}</div>`;

        els.workgroupOptions.querySelectorAll('button[data-id]').forEach((button) => {
            button.addEventListener('click', () => {
                state.selectedWorkgroupId = button.dataset.id || '';
                state.workgroupSearch = '';
                if (els.workgroupSearch) {
                    els.workgroupSearch.value = '';
                }
                closeDropdown();
                renderFilters();
                state.workstationPage = 1;
                loadWorkstations();
            });
        });
    }

    function toggleDropdown(type) {
        if (type === 'facility' && els.facilityTrigger.disabled) return;
        if (type === 'workgroup' && els.workgroupTrigger.disabled) return;

        state.activeDropdown = state.activeDropdown === type ? null : type;
        els.facilityPanel.classList.toggle('hidden', state.activeDropdown !== 'facility');
        els.workgroupPanel.classList.toggle('hidden', state.activeDropdown !== 'workgroup');

        if (state.activeDropdown === 'facility') {
            els.facilitySearch?.focus();
        }

        if (state.activeDropdown === 'workgroup') {
            els.workgroupSearch?.focus();
        }
    }

    function closeDropdown() {
        state.activeDropdown = null;
        els.facilityPanel.classList.add('hidden');
        els.workgroupPanel.classList.add('hidden');
    }

    function resetFilters() {
        state.selectedFacilityId = state.config.canChooseFacility ? '' : (getFacilityOptions()[0] ? String(getFacilityOptions()[0].id) : '');
        state.selectedWorkgroupId = '';
        state.selectedStatus = state.defaultStatus || '';
        state.facilitySearch = '';
        state.workgroupSearch = '';
        state.workstationSearch = '';
        state.workstationPage = 1;
        if (els.facilitySearch) els.facilitySearch.value = '';
        if (els.workgroupSearch) els.workgroupSearch.value = '';
        if (els.tableSearch) els.tableSearch.value = '';
        if (state.workstationSearchTimer) {
            window.clearTimeout(state.workstationSearchTimer);
            state.workstationSearchTimer = null;
        }
        updateSearchClearButton();
        closeDropdown();
        renderFilters();
        loadWorkstations();
    }

    function buildWorkstationsUrl(extra = {}) {
        return Perfectlum.buildServerUrl('/api/workstations', {
            facility_id: state.selectedFacilityId || '',
            workgroup_id: state.selectedWorkgroupId || '',
            type: state.selectedStatus || '',
            search: state.workstationSearch || '',
            page: state.workstationPage,
            limit: state.workstationLimit,
            sort: state.workstationSortKey || 'name',
            order: state.workstationSortDir || 'asc',
            ...extra,
        });
    }

    function workstationMatchesSelectedStatus(item) {
        if (!state.selectedStatus) {
            return true;
        }

        const displaysCount = Number(item.displaysCount || 0);
        const okDisplaysCount = Number(item.okDisplaysCount || 0);
        const failedDisplaysCount = Number(item.failedDisplaysCount || 0);

        if (state.selectedStatus === 'failed') {
            return failedDisplaysCount > 0;
        }

        if (state.selectedStatus === 'ok') {
            return displaysCount > 0 && failedDisplaysCount === 0 && okDisplaysCount === displaysCount;
        }

        return true;
    }

    function updateWorkstationSortIndicators() {
        document.querySelectorAll('[data-workstation-sort]').forEach((button) => {
            const key = button.getAttribute('data-workstation-sort');
            button.classList.toggle('is-active', key === state.workstationSortKey);
        });
        document.querySelectorAll('[data-workstation-sort-indicator]').forEach((node) => {
            const key = node.getAttribute('data-workstation-sort-indicator');
            if (key === state.workstationSortKey) {
                node.textContent = state.workstationSortDir === 'asc' ? '↑' : '↓';
            } else {
                node.textContent = '↕';
            }
        });
    }

    function renderWorkstationPager() {
        const totalPages = Math.max(1, Math.ceil(state.workstationTotal / state.workstationLimit));
        const current = Math.min(state.workstationPage, totalPages);
        if (els.pageLabel) {
            els.pageLabel.textContent = `${text.page || 'Page'} ${current} / ${totalPages}`;
        }
        if (els.pagePrev) els.pagePrev.disabled = state.workstationLoading || current <= 1;
        if (els.pageNext) els.pageNext.disabled = state.workstationLoading || current >= totalPages;

        const from = state.workstationTotal === 0 ? 0 : ((current - 1) * state.workstationLimit) + 1;
        const to = Math.min(state.workstationTotal, current * state.workstationLimit);
        if (els.tableSummary) {
            els.tableSummary.textContent = `${text.showing || 'Showing'} ${from}-${to} ${text.of || 'of'} ${state.workstationTotal} ${text.results || 'results'}`;
        }
        if (els.tableMeta) {
            const statusLabel = state.selectedStatus === 'ok' ? 'OK' : (state.selectedStatus === 'failed' ? 'Not OK' : 'All');
            els.tableMeta.textContent = `${statusLabel} • ${state.workstationTotal} ${text.results || 'results'}`;
        }
    }

    function rowStatusDotClass(item) {
        if (Number(item.failedDisplaysCount || 0) > 0) return 'bg-rose-500 shadow-[0_0_0_4px_rgba(244,63,94,0.14)]';
        if (Number(item.okDisplaysCount || 0) > 0) return 'bg-emerald-500 shadow-[0_0_0_4px_rgba(16,185,129,0.14)]';
        return 'bg-slate-300 shadow-[0_0_0_4px_rgba(148,163,184,0.14)]';
    }

    function renderWorkstationRows() {
        if (!els.tableBody) return;
        if (state.workstationLoading) {
            els.tableBody.innerHTML = `<tr><td colspan="7" class="workstation-empty">${Perfectlum.escapeHtml(text.loading || 'Loading...')}</td></tr>`;
            return;
        }

        const rows = state.workstationRows;
        if (!rows.length) {
            els.tableBody.innerHTML = `<tr><td colspan="7" class="workstation-empty">${Perfectlum.escapeHtml(text.noMatchingRecordsFound || 'No matching records found')}</td></tr>`;
            return;
        }

        els.tableBody.innerHTML = rows.map((item) => {
            const failedCount = Number(item.attentionCount || 0);
            const name = String(item.name || '-');
            const encodedName = encodeURIComponent(name);
            const wgName = String(item.wgName || '-');
            const facName = String(item.facName || '-');
            const wgTooltip = wgName.trim() && wgName !== '-'
                ? `<span class="workstation-tooltip-bubble" role="tooltip">${Perfectlum.escapeHtml(wgName)}</span>`
                : '';
            const facTooltip = facName.trim() && facName !== '-'
                ? `<span class="workstation-tooltip-bubble" role="tooltip">${Perfectlum.escapeHtml(facName)}</span>`
                : '';
            const lastConnected = formatBrowserDateTime(item.lastConnectedAt, item.lastConnected || '-');
            return `
                <tr class="workstation-row-clickable" data-action="open-workstation-row" data-workstation-id="${Number(item.id || 0)}">
                    <td>
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex h-2.5 w-2.5 shrink-0 rounded-full ${rowStatusDotClass(item)}"></span>
                            <div class="min-w-0">
                                <button type="button" data-action="open-hierarchy" data-hierarchy-type="workstation" data-hierarchy-id="${Number(item.id || 0)}" class="cursor-pointer font-semibold text-sky-600 transition hover:text-sky-700 hover:underline">${Perfectlum.escapeHtml(name)}</button>
                                ${failedCount > 0 ? `<button type="button" data-action="attention" data-workstation-id="${Number(item.id || 0)}" data-workstation-name="${encodedName}" data-failed-count="${failedCount}" class="mt-1 block text-[11px] font-semibold text-rose-600 underline decoration-rose-300 decoration-dashed underline-offset-2 transition hover:text-rose-700">${Perfectlum.escapeHtml(String(failedCount))} ${Perfectlum.escapeHtml(failedCount === 1 ? text.display : text.displays)} ${Perfectlum.escapeHtml(text.needAttention)}</button>` : ''}
                            </div>
                        </div>
                    </td>
                    <td>${!item.wgId || wgName === '-' ? '-' : `<span class="workstation-scope-cell"><span class="workstation-scope-badge">WG</span><span class="workstation-tooltip-cell min-w-0" tabindex="0" aria-label="${Perfectlum.escapeHtml(wgName)}"><button type="button" data-action="open-hierarchy" data-hierarchy-type="workgroup" data-hierarchy-id="${Number(item.wgId || 0)}" class="workstation-scope-link cursor-pointer text-slate-600 transition hover:text-sky-600 hover:underline">${Perfectlum.escapeHtml(wgName)}</button>${wgTooltip}</span></span>`}</td>
                    <td>${!item.facId || facName === '-' ? '-' : `<span class="workstation-scope-cell"><span class="workstation-scope-badge">F</span><span class="workstation-tooltip-cell min-w-0" tabindex="0" aria-label="${Perfectlum.escapeHtml(facName)}"><button type="button" data-action="open-hierarchy" data-hierarchy-type="facility" data-hierarchy-id="${Number(item.facId || 0)}" class="workstation-scope-link cursor-pointer text-slate-600 transition hover:text-sky-600 hover:underline">${Perfectlum.escapeHtml(facName)}</button>${facTooltip}</span></span>`}</td>
                    <td>${Perfectlum.escapeHtml(String(item.sleepTime || '-'))}</td>
                    <td><span class="font-semibold text-slate-700">${Perfectlum.escapeHtml(String(item.displaysCount ?? 0))}</span></td>
                    <td><div class="leading-tight"><div>${Perfectlum.escapeHtml(String(lastConnected.date || '-'))}</div>${lastConnected.time ? `<div class="mt-1">${Perfectlum.escapeHtml(String(lastConnected.time))}</div>` : ''}</div></td>
                    <td class="text-center">${!canManageWorkstations ? '' : `<button type="button" data-action="menu" data-workstation-id="${Number(item.id || 0)}" data-workstation-name="${encodedName}" class="workstation-row-action mx-auto transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="2"></circle><circle cx="12" cy="5" r="2"></circle><circle cx="12" cy="19" r="2"></circle></svg></button>`}</td>
                </tr>
            `;
        }).join('');
    }

    async function loadWorkstations() {
        closeActionMenu();
        state.workstationLoading = true;
        renderWorkstationPager();
        renderWorkstationRows();
        let hasError = false;
        try {
            const payload = await Perfectlum.request(buildWorkstationsUrl());
            const incoming = Array.isArray(payload?.data) ? payload.data : [];
            state.workstationRows = incoming.filter(workstationMatchesSelectedStatus);
            state.workstationTotal = Number(payload?.total || 0);
            if (!Number.isFinite(state.workstationTotal) || state.workstationTotal < state.workstationRows.length) {
                state.workstationTotal = state.workstationRows.length;
            }

            const totalPages = Math.max(1, Math.ceil(state.workstationTotal / state.workstationLimit));
            if (state.workstationPage > totalPages) {
                state.workstationPage = totalPages;
                return loadWorkstations();
            }
        } catch (error) {
            hasError = true;
            state.workstationRows = [];
            state.workstationTotal = 0;
            if (els.tableBody) {
                els.tableBody.innerHTML = `<tr><td colspan="7" class="workstation-empty text-rose-600">${Perfectlum.escapeHtml(error.message || text.unableToLoadData)}</td></tr>`;
            }
        } finally {
            state.workstationLoading = false;
            if (!hasError) {
                renderWorkstationRows();
            }
            renderWorkstationPager();
            window.lucide?.createIcons();
        }
    }

    function toggleActionMenu(event, id, name, triggerEl) {
        if (!canManageWorkstations) return;
        event.preventDefault();
        event.stopPropagation();

        const anchor = triggerEl instanceof Element ? triggerEl : (event.currentTarget instanceof Element ? event.currentTarget : event.target.closest('[data-action="menu"]'));
        if (!anchor) return;
        const rect = anchor.getBoundingClientRect();
        const nextOpen = !(state.actionTarget && state.actionTarget.id === id && !els.actionMenu.classList.contains('hidden'));
        state.actionTarget = nextOpen ? { id, name } : null;

        if (!nextOpen) {
            closeActionMenu();
            return;
        }

        els.actionOverlay.classList.remove('hidden');
        els.actionMenu.classList.remove('hidden');
        els.actionMenu.style.left = `${Math.max(16, rect.right - 224)}px`;
        els.actionMenu.style.top = `${rect.bottom + 10}px`;
        window.lucide?.createIcons();
    }

    function closeActionMenu() {
        els.actionOverlay.classList.add('hidden');
        els.actionMenu.classList.add('hidden');
    }

    function openQuickEditAction(event) {
        if (!canManageWorkstations) return;
        event?.preventDefault?.();
        event?.stopPropagation?.();
        if (!state.actionTarget?.id) return;
        openEditModal(state.actionTarget.id);
    }

    function openQuickDeleteAction(event) {
        if (!canManageWorkstations) return;
        event?.preventDefault?.();
        event?.stopPropagation?.();
        if (!state.actionTarget?.id) return;
        openDeleteModal(state.actionTarget.id, state.actionTarget.name);
    }

    function attentionLastSyncToEpoch(value) {
        if (!value || value === '-') return 0;
        const dt = Date.parse(String(value).replace(/(\d{2}) (\w{3}) (\d{4}) (\d{2}):(\d{2})/, '$1 $2 $3 $4:$5 UTC'));
        return Number.isNaN(dt) ? 0 : dt;
    }

    function compareAttentionRows(a, b) {
        const key = state.attentionSortKey;
        const dir = state.attentionSortDir === 'desc' ? -1 : 1;
        if (key === 'updatedAt') {
            return (attentionLastSyncToEpoch(a.updatedAt) - attentionLastSyncToEpoch(b.updatedAt)) * dir;
        }
        const av = String(a[key] ?? '').toLowerCase();
        const bv = String(b[key] ?? '').toLowerCase();
        if (av === bv) return 0;
        return av > bv ? dir : -dir;
    }

    function updateAttentionSortIndicators() {
        document.querySelectorAll('[data-attention-sort-indicator]').forEach((node) => {
            const key = node.getAttribute('data-attention-sort-indicator');
            if (key === state.attentionSortKey) {
                node.textContent = state.attentionSortDir === 'asc' ? '↑' : '↓';
            } else {
                node.textContent = '↕';
            }
        });
    }

    function renderAttentionRows(rows) {
        if (!els.attentionList) return;
        const sortedRows = [...rows].sort(compareAttentionRows);
        els.attentionList.innerHTML = sortedRows.map((item) => `
            <tr>
                <td class="px-4 py-3 text-[13px] font-semibold text-slate-700">${Perfectlum.escapeHtml(item.displayName || '-')}</td>
                <td class="px-4 py-3 text-[12px] text-slate-500">${Perfectlum.escapeHtml(item.updatedAt || '-')}</td>
                <td class="px-4 py-3 text-[12px] text-rose-600">${Perfectlum.escapeHtml(item.attentionText || 'No alert detail')}</td>
                <td class="px-4 py-3 text-right">
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy',{detail:{type:'display',id:${Number(item.id || 0)}}}))" class="inline-flex h-9 items-center rounded-xl border border-slate-200 px-3 text-xs font-semibold text-slate-600 transition hover:border-sky-400 hover:text-sky-700">${Perfectlum.escapeHtml(text.openDisplay || 'Open Display')}</button>
                </td>
            </tr>
        `).join('');
    }

    async function fetchAttentionRowsPage(page) {
        const payload = await Perfectlum.request(Perfectlum.buildServerUrl('/api/displays', {
            workstation_id: Number(state.attentionTarget.id),
            type: 'failed',
            sort: 'updated_at',
            order: 'desc',
            page,
            limit: state.attentionLimit,
        }));
        return Array.isArray(payload?.data) ? payload.data : [];
    }

    function updateAttentionListWrapHeight() {
        if (!els.attentionListWrap) return;
        const maxFromViewport = Math.max(260, Math.min(window.innerHeight * 0.56, 560));
        els.attentionListWrap.style.height = `${maxFromViewport}px`;
    }

    async function maybeLoadMoreAttentionRows() {
        if (!state.attentionHasMore || state.attentionLoadingMore || !state.attentionTarget) return;
        const node = els.attentionListWrap;
        if (!node) return;
        const nearBottom = node.scrollTop + node.clientHeight >= node.scrollHeight - 24;
        if (!nearBottom) return;

        state.attentionLoadingMore = true;
        els.attentionMore.textContent = text.loadingMore;
        els.attentionMore.classList.remove('hidden');
        try {
            const nextPage = state.attentionPage + 1;
            const rows = await fetchAttentionRowsPage(nextPage);
            if (rows.length) {
                state.attentionRows = state.attentionRows.concat(rows);
                renderAttentionRows(state.attentionRows);
                state.attentionPage = nextPage;
            }
            state.attentionHasMore = rows.length === state.attentionLimit;
        } catch (error) {
            state.attentionHasMore = false;
        } finally {
            state.attentionLoadingMore = false;
            if (!state.attentionHasMore) {
                els.attentionMore.classList.add('hidden');
            }
        }
    }

    async function openAttentionModal(workstationId, workstationName, failedCount) {
        if (!workstationId) return;
        state.attentionTarget = { id: Number(workstationId), name: workstationName || '' };
        state.attentionPage = 1;
        state.attentionHasMore = false;
        state.attentionLoadingMore = false;
        state.attentionRows = [];
        els.attentionTitle.textContent = text.displaysNeedAttentionTitle;
        const suffix = failedCount ? ` • ${failedCount} ${failedCount === 1 ? text.display : text.displays}` : '';
        els.attentionSubtitle.textContent = `${workstationName || '-'}${suffix}`;

        els.attentionLoading.classList.remove('hidden');
        els.attentionError.classList.add('hidden');
        els.attentionEmpty.classList.add('hidden');
        els.attentionListWrap.classList.add('hidden');
        els.attentionMore.classList.add('hidden');
        els.attentionList.innerHTML = '';
        els.attentionModal.classList.remove('hidden');
        els.attentionModal.classList.add('flex');
        updateAttentionListWrapHeight();
        if (els.attentionListWrap) els.attentionListWrap.scrollTop = 0;

        try {
            const rows = await fetchAttentionRowsPage(1);
            if (!rows.length) {
                els.attentionEmpty.classList.remove('hidden');
                return;
            }

            state.attentionRows = rows;
            renderAttentionRows(state.attentionRows);
            state.attentionHasMore = rows.length === state.attentionLimit;
            if (state.attentionHasMore) {
                els.attentionMore.textContent = text.loadingMore;
                els.attentionMore.classList.remove('hidden');
            }
            els.attentionListWrap.classList.remove('hidden');
        } catch (error) {
            els.attentionError.textContent = error.message || text.unableToLoadAttentionDisplays;
            els.attentionError.classList.remove('hidden');
        } finally {
            els.attentionLoading.classList.add('hidden');
        }
    }

    function closeAttentionModal() {
        state.attentionTarget = null;
        state.attentionPage = 1;
        state.attentionHasMore = false;
        state.attentionLoadingMore = false;
        state.attentionRows = [];
        els.attentionModal.classList.add('hidden');
        els.attentionModal.classList.remove('flex');
        els.attentionLoading.classList.add('hidden');
        els.attentionError.classList.add('hidden');
        els.attentionEmpty.classList.add('hidden');
        els.attentionListWrap.classList.add('hidden');
        els.attentionMore.classList.add('hidden');
        els.attentionList.innerHTML = '';
        if (els.attentionListWrap) els.attentionListWrap.scrollTop = 0;
    }

    function boolValue(value) {
        return value === true || value === 'true' || value === 1 || value === '1';
    }

    function parseOptionList(raw, preferredOrder = []) {
        let parsed = raw;
        if (typeof parsed === 'string') {
            try {
                parsed = JSON.parse(parsed);
            } catch (error) {
                parsed = [];
            }
        }

        let options = [];
        if (Array.isArray(parsed)) {
            options = parsed.map((item) => ({
                value: String(item.value ?? item.id ?? item.key ?? item),
                label: String(item.label ?? item.name ?? item.value ?? item),
            }));
        } else if (parsed && typeof parsed === 'object') {
            options = Object.entries(parsed).map(([value, label]) => ({
                value: String(value),
                label: String(label),
            }));
        }

        options.sort((a, b) => a.label.localeCompare(b.label, undefined, { sensitivity: 'base' }));

        if (preferredOrder.length) {
            options.sort((a, b) => {
                const aIndex = preferredOrder.indexOf(a.value);
                const bIndex = preferredOrder.indexOf(b.value);
                if (aIndex !== -1 || bIndex !== -1) {
                    return (aIndex === -1 ? 999 : aIndex) - (bIndex === -1 ? 999 : bIndex);
                }
                return a.label.localeCompare(b.label, undefined, { sensitivity: 'base' });
            });
        }

        return options;
    }

    function inputValue(name, fallback = '') {
        return Perfectlum.escapeHtml(String(state.edit.settings?.[name] ?? fallback ?? ''));
    }

    function checkedAttr(name) {
        return boolValue(state.edit.settings?.[name]) ? 'checked' : '';
    }

    function formatBrowserDateTime(value, fallback = '-') {
        if (!value) return { date: fallback, time: '' };
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return { date: fallback, time: '' };

        const parts = new Intl.DateTimeFormat(undefined, {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        }).formatToParts(date);

        const datePart = [parts.find((part) => part.type === 'day')?.value, parts.find((part) => part.type === 'month')?.value, parts.find((part) => part.type === 'year')?.value]
            .filter(Boolean)
            .join(' ');
        const timePart = [parts.find((part) => part.type === 'hour')?.value, parts.find((part) => part.type === 'minute')?.value]
            .filter(Boolean)
            .join(':');

        return {
            date: datePart || fallback,
            time: timePart || '',
        };
    }

    function selectOptionsHtml(name, placeholder = 'Select an option', preferredOrder = []) {
        const current = String(state.edit.settings?.[name] ?? '');
        const options = parseOptionList(state.edit.options?.[name], preferredOrder);
        const hasCurrentOption = options.some((option) => option.value === current);
        const placeholderOption = `<option value="">${Perfectlum.escapeHtml(placeholder)}</option>`;
        return placeholderOption + options.map((option) => `
            <option value="${Perfectlum.escapeHtml(option.value)}" ${option.value === current ? 'selected' : ''}>
                ${Perfectlum.escapeHtml(option.label)}
            </option>
        `).join('') + (current && !hasCurrentOption ? `
            <option value="${Perfectlum.escapeHtml(current)}" selected>${Perfectlum.escapeHtml(current)}</option>
        ` : '');
    }

    function colorTemperatureSelectionState() {
        const raw = state.edit.options?.ColorTemperatureAdjustment_extcombo
            || state.edit.options?.ColorTemperatureAdjustment
            || {};
        const options = parseOptionList(raw);
        const presetValues = new Set(['native', ...options.map((option) => String(option.value))]);
        const current = String(state.edit.settings?.ColorTemperatureAdjustment ?? '');
        const customValue = String(state.edit.settings?.ColorTemperatureAdjustment_ext ?? '');
        const isCustom = current === '20' || (current !== '' && !presetValues.has(current));

        return {
            isCustom,
            selectValue: isCustom ? '20' : current,
            inputValue: isCustom ? (current === '20' ? customValue : current) : customValue,
        };
    }

    function colorTemperatureOptionsHtml() {
        const current = colorTemperatureSelectionState().selectValue;
        const raw = state.edit.options?.ColorTemperatureAdjustment_extcombo
            || state.edit.options?.ColorTemperatureAdjustment
            || {};
        const options = parseOptionList(raw);
        const normalized = [];
        const seen = new Set();

        [{ value: 'native', label: 'native' }, ...options].forEach((option) => {
            const key = String(option.value);
            if (seen.has(key)) return;
            seen.add(key);
            normalized.push({
                value: key,
                label: String(option.label),
            });
        });

        const placeholderOption = `<option value="">${Perfectlum.escapeHtml(text.selectTemperature)}</option>`;

        return placeholderOption + normalized.map((option) => `
            <option value="${Perfectlum.escapeHtml(option.value)}" ${option.value === current ? 'selected' : ''}>
                ${Perfectlum.escapeHtml(option.label)}
            </option>
        `).join('');
    }

    function renderEditTabs() {
        const tabs = [
            { key: 'application', label: text.application },
            { key: 'display-calibration', label: text.displayCalibration },
            { key: 'quality-assurance', label: text.qualityAssurance },
            { key: 'location', label: text.location },
        ];

        els.editTabs.innerHTML = tabs.map((tab) => `
            <button
                type="button"
                data-tab="${tab.key}"
                class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition ${
                    state.edit.tab === tab.key
                        ? 'border-sky-500 bg-sky-500 text-white'
                        : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:text-slate-900'
                }">
                ${tab.label}
            </button>
        `).join('');

        els.editTabs.querySelectorAll('button[data-tab]').forEach((button) => {
            button.addEventListener('click', () => {
                captureEditFormState();
                state.edit.tab = button.dataset.tab;
                renderEditModal();
            });
        });
    }

    function renderApplicationTab() {
        const canChangeWorkgroup = !!state.edit.meta?.permissions?.changeWorkgroup;
        return `
            <div class="grid gap-5 lg:grid-cols-2">
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                    <div class="mb-5 grid gap-5 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.workstationName)}</span>
                            <input name="name" value="${inputValue('name', state.edit.meta?.name || '')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.workgroup)}</span>
                            <select name="workgroup_id" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" ${canChangeWorkgroup ? '' : 'disabled'}>
                                ${selectOptionsHtml('workgroup_id', text.selectWorkgroup)}
                            </select>
                            ${canChangeWorkgroup ? '' : `<p class="text-xs text-slate-400">${Perfectlum.escapeHtml(text.onlySuperUsersCanChangeWorkgroup)}</p>`}
                        </label>
                    </div>
                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.language)}</span>
                            <select name="Language" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('Language', text.selectLanguage)}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.databaseSynchronizationInterval)}</span>
                            <select name="DataBaseSynchronizationInterval" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('DataBaseSynchronizationInterval', text.selectSyncInterval)}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.reminderInterval)}</span>
                            <select name="RemindMinutes" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400" ${state.edit.settings.UseScheduler ? '' : 'disabled'}>
                                ${selectOptionsHtml('RemindMinutes', text.selectReminderInterval)}
                            </select>
                            <p class="text-xs text-slate-500">${Perfectlum.escapeHtml(state.edit.settings.UseScheduler ? text.schedulerAppliedOnNextSync : text.reminderRequiresScheduler)}</p>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.backupPeriod)}</span>
                            <select name="backupPeriod" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('backupPeriod', text.selectBackupPeriod)}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.unitsOfLength)}</span>
                            <select name="units" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('units', text.selectLengthUnit)}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.unitsOfLuminance)}</span>
                            <select name="LumUnits" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('LumUnits', text.selectLuminanceUnit)}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.veilingLuminance)}</span>
                            <input name="AmbientLight" value="${inputValue('AmbientLight')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.ambientConditionsStable)}</span>
                            <select name="AmbientStable" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('AmbientStable', text.selectValue, ['no', 'yes', '0', '1'])}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.energySaveStart)}</span>
                            <input name="StartEnergySaveMode" value="${inputValue('StartEnergySaveMode')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.energySaveEnd)}</span>
                            <input name="EndEnergySaveMode" value="${inputValue('EndEnergySaveMode')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                    </div>
                    <div class="mt-5 grid gap-3 md:grid-cols-2">
                        <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-700">
                            <input name="UseScheduler" type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500" ${checkedAttr('UseScheduler')}>
                            <span class="space-y-1">
                                <span class="block font-semibold text-slate-800">${Perfectlum.escapeHtml(text.enableScheduler)}</span>
                                <span class="block text-xs leading-5 text-slate-500">${Perfectlum.escapeHtml(text.enableSchedulerDescription)}</span>
                            </span>
                        </label>
                        <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-700">
                            <input name="UpdateSoftwareAutomaticaly" type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500" ${checkedAttr('UpdateSoftwareAutomaticaly')}>
                            <span class="space-y-1">
                                <span class="block font-semibold text-slate-800">${Perfectlum.escapeHtml(text.updateSoftwareAutomatically)}</span>
                                <span class="block text-xs leading-5 text-slate-500">${Perfectlum.escapeHtml(text.updateSoftwareAutomaticallyDescription)}</span>
                            </span>
                        </label>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">${Perfectlum.escapeHtml(text.schedulerAppliedOnNextSync)}</p>
                    <label class="mt-5 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-700">
                        <input name="PutDisplaysToEnergySaveMode" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500" ${checkedAttr('PutDisplaysToEnergySaveMode')}>
                        ${Perfectlum.escapeHtml(text.enableDisplayEnergySaveMode)}
                    </label>
                </div>
            </div>
        `;
    }

    function renderDisplayCalibrationTab() {
        const colorTemperature = colorTemperatureSelectionState();
        return `
            <div class="grid gap-5 lg:grid-cols-2">
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.preset)}</span>
                            <select name="CalibrationPresents" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('CalibrationPresents', text.selectPreset)}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.luminanceResponse)}</span>
                            <select name="CalibrationType" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('CalibrationType', text.selectResponse)}
                            </select>
                        </label>
                        <div class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.colorTemperature)}</span>
                            <div class="grid gap-3 ${colorTemperature.isCustom ? 'grid-cols-[minmax(0,1fr)_220px]' : 'grid-cols-1'}">
                                <select name="ColorTemperatureAdjustment" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                    ${colorTemperatureOptionsHtml()}
                                </select>
                                ${colorTemperature.isCustom ? `
                                    <input
                                        name="ColorTemperatureAdjustment_ext"
                                        value="${Perfectlum.escapeHtml(colorTemperature.inputValue)}"
                                        type="text"
                                        inputmode="numeric"
                                        placeholder="Custom value"
                                        class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"
                                    >
                                ` : ''}
                            </div>
                        </div>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.maxLuminance)}</span>
                            <select name="WhiteLevel" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('WhiteLevel_u_extcombo', text.selectMaxLuminance)}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.blackLevel)}</span>
                            <input name="BlackLevel" value="${inputValue('BlackLevel')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.gamut)}</span>
                            <select name="gamut_name" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('gamut_name', text.selectGamut)}
                            </select>
                        </label>
                        <div class="grid gap-3 md:col-span-2 md:grid-cols-2">
                            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-700">
                                <input name="SetWhiteLevel" type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500" ${checkedAttr('SetWhiteLevel')}>
                                <span class="space-y-1">
                                    <span class="block font-semibold text-slate-800">${Perfectlum.escapeHtml(text.applyWhiteLevel)}</span>
                                    <span class="block text-xs leading-5 text-slate-500">${Perfectlum.escapeHtml(text.applyWhiteLevelDescription)}</span>
                                </span>
                            </label>
                            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-700">
                                <input name="SetBlackLevel" type="checkbox" class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500" ${checkedAttr('SetBlackLevel')}>
                                <span class="space-y-1">
                                    <span class="block font-semibold text-slate-800">${Perfectlum.escapeHtml(text.applyBlackLevel)}</span>
                                    <span class="block text-xs leading-5 text-slate-500">${Perfectlum.escapeHtml(text.applyBlackLevelDescription)}</span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <label class="mt-5 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-700">
                        <input name="CreateICCICMProfile" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500" ${checkedAttr('CreateICCICMProfile')}>
                        ${Perfectlum.escapeHtml(text.createDisplayIccProfile)}
                    </label>
                </div>
            </div>
        `;
    }

    function qaStepsCatalogState() {
        const raw = state.edit.settings?.QA_steps_catalog;
        if (!raw) return { receivedAt: '', steps: [] };

        try {
            const decoded = typeof raw === 'string' ? JSON.parse(raw) : raw;
            const stepsPayload = decoded?.steps ?? decoded;
            let steps = [];

            if (Array.isArray(stepsPayload)) {
                steps = stepsPayload;
            } else if (stepsPayload && typeof stepsPayload === 'object') {
                steps = Object.entries(stepsPayload).map(([key, value]) => {
                    if (value && typeof value === 'object') {
                        return { key, ...value };
                    }
                    return { key, name: value };
                });
            }

            return {
                receivedAt: decoded?.received_at || '',
                steps: steps.filter((step) => step && typeof step === 'object'),
            };
        } catch (error) {
            return { receivedAt: '', steps: [] };
        }
    }

    function renderQaStepsCatalog() {
        const catalog = qaStepsCatalogState();
        if (!catalog.steps.length) {
            return `
                <div class="mt-5 rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-500">
                    ${Perfectlum.escapeHtml(text.qaStepsCatalogEmpty)}
                </div>
            `;
        }

        const received = catalog.receivedAt ? formatBrowserDateTime(catalog.receivedAt) : null;
        const rows = catalog.steps.slice(0, 80).map((step, index) => {
            const label = step.name || step.title || step.caption || step.stepName || step.key || step.id || `Step ${index + 1}`;
            const detail = step.description || step.text || step.type || step.group || step.category || step.limit || '';
            const identifier = step.id || step.stepId || step.step_id || step.key || '';

            return `
                <tr class="border-t border-slate-100">
                    <td class="px-3 py-2 text-xs font-semibold text-slate-500">${index + 1}</td>
                    <td class="px-3 py-2 text-sm font-semibold text-slate-800">${Perfectlum.escapeHtml(String(label))}</td>
                    <td class="px-3 py-2 text-xs text-slate-500">${Perfectlum.escapeHtml(String(identifier || '-'))}</td>
                    <td class="px-3 py-2 text-xs text-slate-500">${Perfectlum.escapeHtml(String(detail || '-'))}</td>
                </tr>
            `;
        }).join('');

        return `
            <div class="mt-5 rounded-[1.25rem] border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 px-4 py-4">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">${Perfectlum.escapeHtml(text.qaStepsCatalog)}</h4>
                        <p class="mt-1 text-xs leading-5 text-slate-500">${Perfectlum.escapeHtml(text.qaStepsCatalogDescription)}</p>
                    </div>
                    ${received ? `
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-500">
                            ${Perfectlum.escapeHtml(text.received)}: ${Perfectlum.escapeHtml(received.date)} ${Perfectlum.escapeHtml(received.time)}
                        </span>
                    ` : ''}
                </div>
                <div class="max-h-64 overflow-y-auto">
                    <table class="min-w-full table-fixed">
                        <tbody>${rows}</tbody>
                    </table>
                </div>
            </div>
        `;
    }

    function renderQualityAssuranceTab() {
        return `
            <div class="grid gap-5 lg:grid-cols-2">
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.regulation)}</span>
                            <select id="workstation-edit-regulation" name="UsedRegulation" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('UsedRegulation', text.selectRegulation)}
                            </select>
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.displayCategory)}</span>
                            <select id="workstation-edit-classification" name="UsedClassification" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                ${selectOptionsHtml('UsedClassification', text.selectCategory)}
                            </select>
                        </label>
                        <label class="space-y-2 md:col-span-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.bodyRegion)}</span>
                            <input name="bodyRegion" value="${inputValue('bodyRegion')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                    </div>
                    <label class="mt-5 flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-700">
                        <input name="AutoDailyTests" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500" ${checkedAttr('AutoDailyTests')}>
                        ${Perfectlum.escapeHtml(text.startDailyTestsAutomatically)}
                    </label>
                    ${renderQaStepsCatalog()}
                </div>
            </div>
        `;
    }

    function renderLocationTab() {
        return `
            <div class="grid gap-5 lg:grid-cols-2">
                <div class="rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                    <div class="grid gap-5 md:grid-cols-2">
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.facilityLabel)}</span>
                            <input name="Facility" value="${inputValue('Facility')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.department)}</span>
                            <input name="Department" value="${inputValue('Department')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.room)}</span>
                            <input name="Room" value="${inputValue('Room')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.responsiblePerson)}</span>
                            <input name="ResponsiblePersonName" value="${inputValue('ResponsiblePersonName')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.address)}</span>
                            <input name="ResponsiblePersonAddress" value="${inputValue('ResponsiblePersonAddress')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.city)}</span>
                            <input name="ResponsiblePersonCity" value="${inputValue('ResponsiblePersonCity')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.email)}</span>
                            <input name="ResponsiblePersonEmail" value="${inputValue('ResponsiblePersonEmail')}" type="email" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                        <label class="space-y-2">
                            <span class="text-sm font-semibold text-slate-700">${Perfectlum.escapeHtml(text.phoneNumber)}</span>
                            <input name="ResponsiblePersonPhoneNumber" value="${inputValue('ResponsiblePersonPhoneNumber')}" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        </label>
                    </div>
                </div>
            </div>
        `;
    }

    function renderEditFormBody() {
        switch (state.edit.tab) {
            case 'display-calibration':
                return renderDisplayCalibrationTab();
            case 'quality-assurance':
                return renderQualityAssuranceTab();
            case 'location':
                return renderLocationTab();
            default:
                return renderApplicationTab();
        }
    }

    function bindEditFormInputs() {
        const form = els.editForm.querySelector('form');
        if (!form) return;

        form.querySelectorAll('input, select, textarea').forEach((input) => {
            const eventName = input.type === 'checkbox' || input.tagName === 'SELECT' ? 'change' : 'input';
            input.addEventListener(eventName, async (event) => {
                const target = event.currentTarget;
                state.edit.settings[target.name] = target.type === 'checkbox' ? target.checked : target.value;
                if (target.name === 'UseScheduler') {
                    renderEditModal();
                    return;
                }
                if (target.name === 'ColorTemperatureAdjustment') {
                    if (target.value !== '20') {
                        state.edit.settings.ColorTemperatureAdjustment_ext = '';
                    }
                    renderEditModal();
                    return;
                }
                if (target.id === 'workstation-edit-regulation') {
                    await refreshClassificationOptions(target.value);
                }
            });
        });
    }

    function renderEditModal() {
        renderEditTabs();
        els.editForm.classList.remove('hidden');
        els.editForm.innerHTML = `
            <form id="workstation-quick-edit-form" class="space-y-5">
                ${renderEditFormBody()}
            </form>
        `;
        els.editSaveLabel.textContent = state.edit.saving ? text.saving : text.saveChanges;
        bindEditFormInputs();
        window.lucide?.createIcons();
    }

    async function refreshClassificationOptions(regulation, target = 'UsedClassification') {
        if (!state.edit.id) return;
        try {
            const items = await Perfectlum.request(`/app-settings/get/categories?id=ws-${state.edit.id}&regulation=${encodeURIComponent(regulation || '')}`);
            state.edit.options[target] = items.reduce((acc, item) => {
                acc[item.key] = item.value;
                return acc;
            }, {});
            renderEditModal();
        } catch (error) {
            // keep current options
        }
    }

    function captureEditFormState() {
        const form = els.editForm.querySelector('form');
        if (!form) return;
        form.querySelectorAll('input[name], select[name], textarea[name]').forEach((input) => {
            state.edit.settings[input.name] = input.type === 'checkbox' ? input.checked : input.value;
        });
    }

    async function openEditModal(id) {
        closeActionMenu();
        state.edit = {
            id,
            meta: null,
            settings: null,
            options: {},
            tab: 'application',
            saving: false,
        };
        els.editModal.classList.remove('hidden');
        els.editModal.classList.add('flex');
        els.editLoading.classList.remove('hidden');
        els.editError.classList.add('hidden');
        els.editError.textContent = '';
        els.editForm.classList.add('hidden');
        els.editForm.innerHTML = '';
        els.editTabs.innerHTML = '';
        els.editTitle.textContent = text.updateWorkstationDetails;
        els.editSubtitle.textContent = text.adjustWorkstationTable;

        try {
            const [meta, payload] = await Promise.all([
                Perfectlum.request(`/api/workstation-modal/${id}`),
                Perfectlum.request(`/app-settings/ws-${id}`),
            ]);
            state.edit.meta = meta;
            state.edit.settings = {
                ...payload.data,
                name: payload.data?.name ?? meta.name ?? '',
                workgroup_id: String(payload.data?.workgroup_id ?? meta.workgroup?.id ?? ''),
                UseScheduler: boolValue(payload.data?.UseScheduler),
                UpdateSoftwareAutomaticaly: boolValue(payload.data?.UpdateSoftwareAutomaticaly),
                PutDisplaysToEnergySaveMode: boolValue(payload.data?.PutDisplaysToEnergySaveMode),
                CreateICCICMProfile: boolValue(payload.data?.CreateICCICMProfile),
                AutoDailyTests: boolValue(payload.data?.AutoDailyTests),
            };
            state.edit.options = payload.options || {};
            const knownColorTemps = new Set([
                'native',
                ...parseOptionList(state.edit.options?.ColorTemperatureAdjustment_extcombo || state.edit.options?.ColorTemperatureAdjustment || {}).map((option) => String(option.value)),
            ]);
            const currentColorTemp = String(state.edit.settings.ColorTemperatureAdjustment ?? '');
            if (currentColorTemp && !knownColorTemps.has(currentColorTemp)) {
                state.edit.settings.ColorTemperatureAdjustment_ext = currentColorTemp;
            }
            els.editTitle.textContent = meta.name || text.updateWorkstationDetails;
            els.editSubtitle.textContent = `${meta.facility?.name || '-'} / ${meta.workgroup?.name || '-'} / ${meta.name || '-'}`;
            els.editLoading.classList.add('hidden');
            renderEditModal();
        } catch (error) {
            els.editLoading.classList.add('hidden');
            els.editError.textContent = error.message || text.unableToLoadWorkstationSettings;
            els.editError.classList.remove('hidden');
        }
    }

    async function saveEditModal() {
        if (!state.edit.id || state.edit.saving) return;
        captureEditFormState();
        state.edit.saving = true;
        els.editSave.disabled = true;
        els.editSaveLabel.textContent = text.saving;
        els.editError.classList.add('hidden');

        const formData = new FormData();
        formData.append('_token', csrfToken());
        let endpoint = `/app-settings/save/app/ws-${state.edit.id}`;

        if (state.edit.tab === 'application') {
            ['name', 'workgroup_id', 'Language', 'DataBaseSynchronizationInterval', 'RemindMinutes', 'backupPeriod', 'units', 'LumUnits', 'AmbientLight', 'AmbientStable', 'StartEnergySaveMode', 'EndEnergySaveMode']
                .forEach((key) => formData.append(key, state.edit.settings[key] ?? ''));
            formData.append('UseScheduler', state.edit.settings.UseScheduler ? '1' : '0');
            formData.append('UpdateSoftwareAutomaticaly', state.edit.settings.UpdateSoftwareAutomaticaly ? '1' : '0');
            formData.append('PutDisplaysToEnergySaveMode', state.edit.settings.PutDisplaysToEnergySaveMode ? '1' : '0');
        } else if (state.edit.tab === 'display-calibration') {
            endpoint = `/app-settings/save/dc/ws-${state.edit.id}`;
            ['CalibrationPresents', 'CalibrationType', 'ColorTemperatureAdjustment', 'ColorTemperatureAdjustment_ext', 'WhiteLevel', 'BlackLevel', 'gamut_name']
                .forEach((key) => formData.append(key, state.edit.settings[key] ?? ''));
            formData.append('SetWhiteLevel', state.edit.settings.SetWhiteLevel ? 'true' : 'false');
            formData.append('SetBlackLevel', state.edit.settings.SetBlackLevel ? 'true' : 'false');
            formData.append('CreateICCICMProfile', state.edit.settings.CreateICCICMProfile ? '1' : '0');
        } else if (state.edit.tab === 'quality-assurance') {
            endpoint = `/app-settings/save/qa/ws-${state.edit.id}`;
            ['UsedRegulation', 'UsedClassification', 'bodyRegion'].forEach((key) => formData.append(key, state.edit.settings[key] ?? ''));
            formData.append('AutoDailyTests', state.edit.settings.AutoDailyTests ? '1' : '0');
        } else {
            endpoint = `/app-settings/save/location/ws-${state.edit.id}`;
            ['Facility', 'Department', 'Room', 'ResponsiblePersonName', 'ResponsiblePersonCity', 'ResponsiblePersonAddress', 'ResponsiblePersonEmail', 'ResponsiblePersonPhoneNumber']
                .forEach((key) => formData.append(key, state.edit.settings[key] ?? ''));
        }

        try {
            await Perfectlum.postForm(endpoint, formData);
            closeEditModal();
            loadWorkstations();
        } catch (error) {
            els.editError.textContent = error.message || text.unableToSaveWorkstation;
            els.editError.classList.remove('hidden');
        } finally {
            state.edit.saving = false;
            els.editSave.disabled = false;
            els.editSaveLabel.textContent = text.saveChanges;
        }
    }

    function closeEditModal() {
        els.editModal.classList.add('hidden');
        els.editModal.classList.remove('flex');
        els.editLoading.classList.add('hidden');
        els.editError.classList.add('hidden');
        els.editError.textContent = '';
        els.editForm.innerHTML = '';
        els.editForm.classList.add('hidden');
        els.editTabs.innerHTML = '';
        els.editSave.disabled = false;
        els.editSaveLabel.textContent = text.saveChanges;
        state.edit = {
            id: null,
            meta: null,
            settings: null,
            options: {},
            tab: 'application',
            saving: false,
        };
    }

    function openDeleteModal(id, name) {
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
        els.deleteConfirm.textContent = text.deleteWorkstation;
    }

    async function confirmDelete() {
        if (!state.deleteTarget?.id || els.deleteConfirm.disabled) return;
        els.deleteConfirm.disabled = true;
        els.deleteConfirm.textContent = text.deleting;

        try {
            const formData = new FormData();
            formData.append('_token', csrfToken());
            formData.append('id', state.deleteTarget.id);
            const payload = await Perfectlum.postForm('/delete-workstation', formData);
            if (!payload.success) {
                throw new Error(payload.msg || text.unableToDeleteWorkstation);
            }
            closeDeleteModal();
            window.notify?.('success', payload.msg || 'Workstation deleted successfully.');
            loadWorkstations();
        } catch (error) {
            window.alert(error.message || text.unableToDeleteWorkstation);
            els.deleteConfirm.disabled = false;
            els.deleteConfirm.textContent = text.deleteWorkstation;
        }
    }

    window.closeWorkstationPanel = closeEditModal;
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
</script>

@include('common.navigations.footer')
