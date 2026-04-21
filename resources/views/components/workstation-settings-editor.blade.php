@props([
    'title' => 'Workstation Settings',
    'description' => 'Select target facilities, workgroups, or workstations, then open a bulk configuration workspace.',
    'multiple' => true,
    'treeData' => [],
    'optionCatalog' => [],
    'showHeaderIcon' => true,
    'eyebrow' => null,
    'scopeBannerTitle' => null,
    'scopeBannerDescription' => null,
    'browserTitle' => null,
    'browserDescription' => null,
    'selectionTitle' => null,
    'selectionDescription' => null,
    'rulesTitle' => null,
    'rulesItems' => null,
])

@php
    $settingsText = [
        'application' => __('Application'),
        'displayCalibration' => __('Display Calibration'),
        'qualityAssurance' => __('Quality Assurance'),
        'location' => __('Location'),
        'moveToWorkgroup' => __('Move to Workgroup'),
        'moveWorkstations' => __('Move workstations'),
        'facility' => __('Facility'),
        'workgroup' => __('Workgroup'),
        'workstation' => __('Workstation'),
        'selectableFacilities' => __('Selectable facilities'),
        'selectableWorkgroups' => __('Selectable workgroups'),
        'selectableWorkstations' => __('Selectable workstations'),
        'unitsOfLength' => __('Units of Length'),
        'unitsOfLuminance' => __('Units of Luminance'),
        'ambientConditionsStable' => __('Ambient Conditions Stable'),
        'preset' => __('Preset'),
        'luminanceResponse' => __('Luminance Response'),
        'maxLuminance' => __('Max Luminance'),
        'regulation' => __('Regulation'),
        'displayCategory' => __('Display Category'),
        'enableDisplayEnergySaveMode' => __('Enable Display Energy Save Mode'),
        'start' => __('Start'),
        'end' => __('End'),
        'createDisplayIccProfile' => __('Create Display ICC Profile'),
        'facilityLabel' => __('Facility Label'),
        'destinationWorkgroup' => __('Destination Workgroup'),
        'noOptionsFound' => __('No options found'),
        'noMatchingTargets' => __('No matching targets were found for the current search.'),
        'selectRegulation' => __('Select regulation'),
        'selectDestinationWorkgroup' => __('Select a destination workgroup'),
        'moveUnavailable' => __('Move unavailable'),
        'loadingWorkstationSettings' => __('Loading workstation settings...'),
        'failedToLoadWorkstationSettings' => __('Failed to load workstation settings.'),
        'failedToSaveSettings' => __('Failed to save settings for one or more selected targets.'),
    ];
@endphp

@php
    $editorEyebrow = $eyebrow ?? __('Application Settings');
    $editorScopeBannerTitle = $scopeBannerTitle ?? __('Bulk editing resolves to workstation preferences under the selected targets.');
    $editorScopeBannerDescription = $scopeBannerDescription ?? __('Choose a target type, check one or more items, review the affected workstation count, then open Bulk Configure.');
    $editorBrowserTitle = $browserTitle ?? __('Choose bulk edit targets');
    $editorBrowserDescription = $browserDescription ?? __('Only the selected target level will show checkboxes.');
    $editorSelectionTitle = $selectionTitle ?? __('Prepare the bulk configuration set');
    $editorSelectionDescription = $selectionDescription ?? __('Selected targets turn into workstation sets at save time. The configuration form opens only when you are ready.');
    $editorRulesTitle = $rulesTitle ?? __('Rules');
    $editorRulesItems = $rulesItems ?? [
        __('Facility and Workgroup targets expose Application and Display Calibration only.'),
        __('Workstation targets also expose Quality Assurance, Location, and Move to Workgroup.'),
        __('Move to Workgroup stays disabled when selected workstations belong to different facilities.'),
    ];
@endphp

<section class="py-3">
    <style>
        #settings-browser-panel summary {
            list-style: none;
        }

        #settings-browser-panel summary::-webkit-details-marker {
            display: none;
        }

        #settings-browser-panel .settings-toggle-minus {
            display: none;
        }

        #settings-browser-panel details[open] > summary .settings-toggle-plus {
            display: none;
        }

        #settings-browser-panel details[open] > summary .settings-toggle-minus {
            display: block;
        }
    </style>

    <div class="mb-3 flex items-center gap-3 rounded-[1.15rem] border border-slate-200 bg-white px-4 py-3 shadow-sm">
        @if($showHeaderIcon)
            <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-sky-100 bg-sky-50 text-sky-500 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <rect x="4" y="4" width="6" height="6" rx="1"></rect>
                    <rect x="14" y="4" width="6" height="6" rx="1"></rect>
                    <rect x="4" y="14" width="6" height="6" rx="1"></rect>
                    <rect x="14" y="14" width="6" height="6" rx="1"></rect>
                </svg>
            </span>
        @endif
        <div class="min-w-0">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ $editorEyebrow }}</p>
            <h1 class="mt-0.5 text-[1.85rem] font-bold tracking-tight text-slate-900">{{ $title }}</h1>
            <p class="mt-0.5 text-sm text-slate-500">{{ $description }}</p>
        </div>
    </div>

    <div id="settings-scope-banner" class="mb-3 flex items-start justify-between gap-3 rounded-xl border border-sky-100 bg-sky-50/70 px-4 py-2.5 text-xs text-slate-600 shadow-sm">
        <div class="min-w-0">
            <p class="font-semibold text-slate-700">{{ $editorScopeBannerTitle }}</p>
            <p class="mt-0.5 text-slate-500">{{ $editorScopeBannerDescription }}</p>
        </div>
        <button id="settings-banner-close" type="button" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-400 transition hover:border-slate-300 hover:text-slate-700" aria-label="{{ __('Dismiss help banner') }}">
            <span class="text-base leading-none">&times;</span>
        </button>
    </div>

    <div class="grid gap-4 xl:grid-cols-[28rem_minmax(0,1fr)]">
        <aside class="rounded-[1.25rem] border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-500">{{ __('Target Browser') }}</p>
                    <h2 class="mt-1 text-[1.15rem] font-bold tracking-tight text-slate-900">{{ $editorBrowserTitle }}</h2>
                    <p class="mt-1 text-sm leading-6 text-slate-500">{{ $editorBrowserDescription }}</p>
                </div>
                <span id="settings-browser-count" class="inline-flex min-w-[5.5rem] items-center justify-center text-center rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold leading-tight text-slate-500">0 targets</span>
            </div>

            <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-1">
                <div class="grid grid-cols-3 gap-1" id="settings-target-type-buttons">
                    <button type="button" data-target-type="facility" class="settings-target-type rounded-lg px-3 py-2 text-sm font-semibold transition">{{ __('Facility') }}</button>
                    <button type="button" data-target-type="workgroup" class="settings-target-type rounded-lg px-3 py-2 text-sm font-semibold transition">{{ __('Workgroup') }}</button>
                    <button type="button" data-target-type="workstation" class="settings-target-type rounded-lg px-3 py-2 text-sm font-semibold transition">{{ __('Workstation') }}</button>
                </div>
            </div>

            <div class="mt-3 flex items-center gap-2">
                <div class="relative min-w-0 flex-1">
                    <input id="settings-browser-search" type="text" placeholder="{{ __('Search the visible level...') }}" class="h-10 w-full rounded-xl border border-slate-200 bg-white px-3 pr-9 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                    <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.5-3.5"></path></svg>
                    </span>
                </div>
                <button id="settings-expand-all" type="button" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">{{ __('Expand') }}</button>
                <button id="settings-collapse-all" type="button" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">{{ __('Collapse') }}</button>
            </div>

            <div class="mt-3 flex items-center text-xs font-semibold text-slate-400">
                <span id="settings-browser-caption" class="whitespace-nowrap">Selectable facilities</span>
                <span id="settings-browser-meta" class="hidden"></span>
            </div>

            <div id="settings-browser-panel" class="mt-2 h-[calc(100vh-19rem)] overflow-auto rounded-[1rem] border border-slate-200 bg-slate-50 p-2"></div>
        </aside>

        <div class="space-y-4">
            <div class="rounded-[1.25rem] border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-500">{{ __('Bulk selection') }}</p>
                    <h2 class="mt-1 text-[1.2rem] font-bold tracking-tight text-slate-900">{{ $editorSelectionTitle }}</h2>
                    <p class="mt-1 text-sm leading-6 text-slate-500">{{ $editorSelectionDescription }}</p>
                    </div>
                    <button id="settings-clear-selection" type="button" class="hidden rounded-full border border-sky-200 bg-white px-3.5 py-2 text-xs font-semibold text-sky-700 transition hover:bg-sky-50">{{ __('Clear all') }}</button>
                </div>

                <div class="mt-3 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-3">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Target type</p>
                        <p id="settings-summary-target-type" class="mt-1 text-base font-bold text-slate-900">Facility</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-3">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Items selected</p>
                        <p id="settings-summary-selected-count" class="mt-1 text-base font-bold text-slate-900">0</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-3">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Affected workstations</p>
                        <p id="settings-summary-affected-count" class="mt-1 text-base font-bold text-slate-900">0</p>
                    </div>
                </div>

                <div id="settings-selected-empty" class="mt-3 rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-5 text-sm text-slate-500">
                    {{ __('No target has been checked yet.') }}
                </div>
                <div id="settings-selected-list" class="mt-3 flex flex-wrap gap-2"></div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Available bulk configuration</p>
                        <div id="settings-config-chips" class="mt-2 flex flex-wrap gap-2"></div>
                    </div>
                    <button id="settings-open-config" type="button" class="inline-flex items-center justify-center rounded-full bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sky-500/20 transition hover:bg-sky-600 disabled:cursor-not-allowed disabled:bg-slate-200 disabled:text-slate-500 disabled:shadow-none" disabled>
                        {{ __('Bulk Configure') }}
                    </button>
                </div>
            </div>

            <div class="rounded-[1.25rem] border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-500">{{ $editorRulesTitle }}</p>
                <ul class="mt-2 space-y-1.5 text-sm leading-6 text-slate-500">
                    @foreach($editorRulesItems as $editorRulesItem)
                        <li>{{ $editorRulesItem }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div id="settings-config-modal" class="fixed inset-0 z-[90] hidden">
        <div id="settings-config-overlay" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-200 ease-out"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div id="settings-config-panel" class="relative flex h-[min(90vh,56rem)] w-full max-w-5xl translate-y-4 scale-[0.98] flex-col overflow-hidden rounded-[1.35rem] border border-slate-200 bg-white opacity-0 shadow-2xl transition duration-200 ease-out">
                <div class="border-b border-slate-200 px-5 py-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-500">{{ __('Bulk Configure') }}</p>
                            <h3 id="settings-modal-title" class="mt-1 text-[1.35rem] font-bold tracking-tight text-slate-900">{{ __('Configure workstation preferences') }}</h3>
                            <p id="settings-modal-subtitle" class="mt-1 text-sm text-slate-500">{{ __('Review the allowed tabs for the selected targets, then save the current section.') }}</p>
                        </div>
                        <button id="settings-modal-close" type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-400 transition hover:border-slate-300 hover:text-slate-700" aria-label="{{ __('Close bulk configure modal') }}">
                            <span class="text-xl leading-none">&times;</span>
                        </button>
                    </div>
                    <div id="settings-modal-summary-chips" class="mt-3 flex flex-wrap gap-2"></div>
                    <div id="settings-modal-mixed-hint" class="mt-3 hidden rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-900">
                        {{ __('Some fields show Mixed values because the selected targets do not share the same value.') }}
                    </div>
                </div>

                <div id="settings-modal-tabs" class="border-b border-slate-200 px-5 py-3"></div>

                <div id="settings-modal-body" class="flex-1 overflow-y-auto px-5 py-4">
                    <section data-panel="application" class="settings-modal-panel space-y-4">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-sm font-semibold text-slate-900">Application settings</p>
                            <p class="mt-1 text-sm text-slate-500">Units, ambient assumptions, and energy save defaults.</p>
                        </div>
                        <form id="settings-modal-form-application" class="space-y-4">
                            {{ csrf_field() }}
                            <div class="grid gap-4 lg:grid-cols-2">
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">{{ __('Language') }}</span><select id="Language" name="Language" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></select></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">{{ __('Database Synchronization Interval') }}</span><select id="DataBaseSynchronizationInterval" name="DataBaseSynchronizationInterval" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></select></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">{{ __('Reminder Interval') }}</span><select id="RemindMinutes" name="RemindMinutes" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></select><span class="block text-xs leading-5 text-slate-500">{{ __('Applied on the workstation after the next client sync.') }}</span></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">{{ __('Backup Period') }}</span><select id="backupPeriod" name="backupPeriod" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></select></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">{{ __('Units of Length') }}</span><select id="units" name="units" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></select></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">{{ __('Units of Luminance') }}</span><select id="LumUnits" name="LumUnits" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></select></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Veiling Luminance</span><input id="AmbientLight" name="AmbientLight" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">{{ __('Ambient Conditions Stable') }}</span><select id="AmbientStable" name="AmbientStable" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></select></label>
                            </div>
                            <div class="grid gap-3 lg:grid-cols-2">
                                <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3.5 text-sm text-slate-700"><input id="UseScheduler" name="UseScheduler" type="checkbox" value="1" class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500"><span><span class="block font-medium text-slate-800">{{ __('Enable Scheduler') }}</span><span class="mt-1 block text-xs leading-5 text-slate-500">{{ __('Allow the client application to run and remind scheduled tasks.') }}</span></span></label>
                                <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3.5 text-sm text-slate-700"><input id="UpdateSoftwareAutomaticaly" name="UpdateSoftwareAutomaticaly" type="checkbox" value="1" class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500"><span><span class="block font-medium text-slate-800">{{ __('Update Software Automatically') }}</span><span class="mt-1 block text-xs leading-5 text-slate-500">{{ __('Let the workstation pull approved software updates automatically.') }}</span></span></label>
                            </div>
                            <div class="rounded-xl border border-slate-200 p-3.5">
                                <label class="flex items-center gap-3 text-sm font-medium text-slate-700"><input id="PutDisplaysToEnergySaveMode" name="PutDisplaysToEnergySaveMode" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-sky-500">{{ __('Enable Display Energy Save Mode') }}</label>
                                <div id="settings-energy-fields" class="mt-3 grid gap-4 lg:grid-cols-2">
                                    <label class="space-y-2"><span class="text-sm font-medium text-slate-700">{{ __('Start') }}</span><input id="StartEnergySaveMode" name="StartEnergySaveMode" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></label>
                                    <label class="space-y-2"><span class="text-sm font-medium text-slate-700">{{ __('End') }}</span><input id="EndEnergySaveMode" name="EndEnergySaveMode" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></label>
                                </div>
                            </div>
                        </form>
                    </section>

                    <section data-panel="calibration" class="settings-modal-panel hidden space-y-4">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-sm font-semibold text-slate-900">{{ __('Display Calibration') }}</p>
                            <p class="mt-1 text-sm text-slate-500">Shared calibration presets, response targets, and luminance defaults.</p>
                        </div>
                        <form id="settings-modal-form-calibration" class="space-y-4">
                            {{ csrf_field() }}
                            <input id="Gamma" name="Gamma" type="hidden">
                            <input id="gamut_name" name="gamut_name" type="hidden">
                            <input id="BlackLevel" name="BlackLevel" type="hidden">
                            <input id="SetBlackLevel" name="SetBlackLevel" type="hidden">
                            <div class="grid gap-4 lg:grid-cols-2">
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">{{ __('Preset') }}</span><select id="CalibrationPresents" name="CalibrationPresents" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></select></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">{{ __('Luminance Response') }}</span><select id="CalibrationType" name="CalibrationType" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></select></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Color Temperature</span><select id="ColorTemperatureAdjustment" name="ColorTemperatureAdjustment" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></select></label>
                                <label id="settings-color-temp-custom-wrap" class="space-y-2"><span class="text-sm font-medium text-slate-700">Custom Color Temperature</span><input id="ColorTemperatureAdjustment_ext" name="ColorTemperatureAdjustment_ext" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">{{ __('Max Luminance') }}</span><select id="WhiteLevel_u_extcombo" name="WhiteLevel_u_extcombo" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></select></label>
                                <label id="settings-white-level-custom-wrap" class="space-y-2"><span class="text-sm font-medium text-slate-700">Custom White Level</span><input id="WhiteLevel_u_input" name="WhiteLevel_u_input" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></label>
                            </div>
                            <input id="WhiteLevel" name="WhiteLevel" type="hidden">
                            <input id="SetWhiteLevel" name="SetWhiteLevel" type="hidden">
                            <label class="flex items-center gap-3 text-sm font-medium text-slate-700"><input id="CreateICCICMProfile" name="CreateICCICMProfile" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-sky-500">{{ __('Create Display ICC Profile') }}</label>
                        </form>
                    </section>

                    <section data-panel="qa" class="settings-modal-panel hidden space-y-4">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-sm font-semibold text-slate-900">Quality Assurance</p>
                            <p class="mt-1 text-sm text-slate-500">{{ __('Regulation defaults, display category, and QA automation.') }}</p>
                        </div>
                        <form id="settings-modal-form-qa" class="space-y-4">
                            {{ csrf_field() }}
                            <input id="UsedClassificationForLastScheduling" name="UsedClassificationForLastScheduling" type="hidden">
                            <input id="UsedRegulationForLastScheduling" name="UsedRegulationForLastScheduling" type="hidden">
                            <div class="grid gap-4 lg:grid-cols-2">
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">{{ __('Regulation') }}</span><select id="UsedRegulation" name="UsedRegulation" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></select></label>
                                <label class="space-y-2"><span id="settings-classification-label" class="text-sm font-medium text-slate-700">{{ __('Display Category') }}</span><select id="UsedClassification" name="UsedClassification" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></select></label>
                                <label class="space-y-2 lg:col-span-2" id="settings-body-region-wrap"><span class="text-sm font-medium text-slate-700">Body Region</span><input id="bodyRegion" name="bodyRegion" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></label>
                            </div>
                            <label class="flex items-center gap-3 text-sm font-medium text-slate-700"><input id="AutoDailyTests" name="AutoDailyTests" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-sky-500">Start daily tests automatically</label>
                        </form>
                    </section>

                    <section data-panel="location" class="settings-modal-panel hidden space-y-4">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-sm font-semibold text-slate-900">Location</p>
                            <p class="mt-1 text-sm text-slate-500">Administrative metadata for the selected workstation targets.</p>
                        </div>
                        <form id="settings-modal-form-location" class="space-y-4">
                            {{ csrf_field() }}
                            <div class="grid gap-4 lg:grid-cols-2">
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">{{ __('Facility Label') }}</span><input id="Facility" name="Facility" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Department</span><input id="Department" name="Department" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Room</span><input id="Room" name="Room" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Responsible Person</span><input id="ResponsiblePersonName" name="ResponsiblePersonName" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></label>
                                <label class="space-y-2 lg:col-span-2"><span class="text-sm font-medium text-slate-700">Address</span><input id="ResponsiblePersonAddress" name="ResponsiblePersonAddress" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">City</span><input id="ResponsiblePersonCity" name="ResponsiblePersonCity" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Email</span><input id="ResponsiblePersonEmail" name="ResponsiblePersonEmail" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></label>
                                <label class="space-y-2"><span class="text-sm font-medium text-slate-700">Phone Number</span><input id="ResponsiblePersonPhoneNumber" name="ResponsiblePersonPhoneNumber" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></label>
                            </div>
                        </form>
                    </section>

                    <section data-panel="move" class="settings-modal-panel hidden space-y-4">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-sm font-semibold text-slate-900">{{ __('Move to Workgroup') }}</p>
                            <p class="mt-1 text-sm text-slate-500">Reassign the selected workstations to a different workgroup inside the same facility.</p>
                        </div>
                        <div id="settings-move-warning" class="hidden rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            Move to Workgroup is available only when all selected workstations belong to the same facility.
                        </div>
                        <form id="settings-modal-form-move" class="space-y-4">
                            {{ csrf_field() }}
                            <label class="space-y-2"><span class="text-sm font-medium text-slate-700">{{ __('Destination Workgroup') }}</span><select id="workgroup_id" name="workgroup_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></select></label>
                        </form>
                    </section>
                </div>

                <div class="flex items-center justify-between gap-4 border-t border-slate-200 px-5 py-3">
                    <p id="settings-modal-status" class="text-sm text-slate-500">Choose a tab, review the values, then save the current section.</p>
                    <div class="flex items-center gap-2">
                        <button id="settings-modal-cancel" type="button" class="rounded-full border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">{{ __('Cancel') }}</button>
                        <button id="settings-modal-save" type="button" class="rounded-full bg-sky-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sky-500/20 transition hover:bg-sky-600">{{ __('Save changes') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@once
@push('scripts')
<script>
(() => {
const config = {
    multiple: @json($multiple),
    treeData: @json($treeData),
    optionCatalog: @json($optionCatalog),
    csrf: @json(csrf_token()),
};

const text = @json($settingsText);
const tabDefinitions = {
    application: { label: text.application, action: 'app' },
    calibration: { label: text.displayCalibration, action: 'dc' },
    qa: { label: text.qualityAssurance, action: 'qa' },
    location: { label: text.location, action: 'location' },
    move: { label: text.moveToWorkgroup, action: 'move' },
};

const fieldIds = [
    'Language','DataBaseSynchronizationInterval','RemindMinutes','backupPeriod','UseScheduler','UpdateSoftwareAutomaticaly',
    'units','LumUnits','AmbientLight','AmbientStable','PutDisplaysToEnergySaveMode','StartEnergySaveMode','EndEnergySaveMode',
    'CalibrationPresents','CalibrationType','Gamma','gamut_name','ColorTemperatureAdjustment','ColorTemperatureAdjustment_ext',
    'WhiteLevel_u_extcombo','WhiteLevel_u_input','WhiteLevel','BlackLevel','SetWhiteLevel','SetBlackLevel','CreateICCICMProfile',
    'UsedRegulation','UsedClassification','UsedClassificationForLastScheduling','UsedRegulationForLastScheduling','bodyRegion','AutoDailyTests',
    'Facility','Department','Room','ResponsiblePersonName','ResponsiblePersonAddress','ResponsiblePersonCity','ResponsiblePersonEmail','ResponsiblePersonPhoneNumber','workgroup_id'
];

const state = {
    targetType: 'facility',
    search: '',
    browserExpanded: true,
    selections: new Set(),
    facilities: [],
    entityMaps: {
        facility: new Map(),
        workgroup: new Map(),
        workstation: new Map(),
    },
    modalOpen: false,
    modalTab: 'application',
    currentScope: null,
    currentPayload: { data: {}, options: {}, meta: { mixedFields: [] } },
    saving: false,
    closeTimer: null,
};
const byId = (id) => document.getElementById(id);
const searchableSelectIds = ['Language','DataBaseSynchronizationInterval','RemindMinutes','backupPeriod','units','LumUnits','AmbientStable','CalibrationPresents','CalibrationType','ColorTemperatureAdjustment','WhiteLevel_u_extcombo','UsedRegulation','UsedClassification','workgroup_id'];
const searchableSelects = new Map();
const normalizeSearch = (value) => String(value || '').trim().toLowerCase();
const escapeHtml = (value) => String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
const numericId = (scope) => String(scope || '').replace(/^[a-zA-Z-]+/, '');

const currentMap = () => state.entityMaps[state.targetType];
const currentSelections = () => Array.from(state.selections).map((scope) => currentMap().get(scope)).filter(Boolean);
const isMixed = (field) => (state.currentPayload?.meta?.mixedFields || []).includes(field);

const hasMeaningfulOptions = (raw) => {
    if (raw === null || raw === undefined || raw === '') return false;
    let parsed = raw;
    if (typeof raw === 'string') {
        try { parsed = JSON.parse(raw); } catch (_) { return String(raw).trim() !== ''; }
    }
    if (Array.isArray(parsed)) {
        return parsed.some((item) => {
            if (item && typeof item === 'object') {
                return Object.values(item).some((value) => String(value ?? '').trim() !== '');
            }
            return String(item ?? '').trim() !== '';
        });
    }
    if (parsed && typeof parsed === 'object') {
        return Object.entries(parsed).some(([key, value]) => String(key ?? '').trim() !== '' || String(value ?? '').trim() !== '');
    }
    return String(parsed).trim() !== '';
};

const optionSource = (key, raw) => hasMeaningfulOptions(raw) ? raw : (config.optionCatalog?.[key] ?? raw);
const normalizeOptions = (raw) => {
    if (!raw) return [];
    let parsed = raw;
    if (typeof raw === 'string') {
        try { parsed = JSON.parse(raw); } catch (_) { return []; }
    }
    if (Array.isArray(parsed)) {
        return parsed.map((item) => typeof item === 'object'
            ? { value: item.key ?? item.value ?? '', label: item.value ?? item.label ?? item.key ?? '' }
            : { value: item, label: item })
            .filter((item) => String(item.value ?? '').trim() !== '' || String(item.label ?? '').trim() !== '');
    }
    return Object.entries(parsed)
        .map(([value, label]) => ({ value, label }))
        .filter((item) => String(item.value ?? '').trim() !== '' || String(item.label ?? '').trim() !== '');
};

const populateSelect = (id, rawOptions, selectedValue = '', mixed = false, placeholder = '') => {
    const select = byId(id);
    if (!select) return;
    const options = normalizeOptions(rawOptions);
    const selected = selectedValue == null ? '' : String(selectedValue);
    const optionValues = options.map((item) => String(item.value));
    select.innerHTML = '';
    const firstOption = document.createElement('option');
    firstOption.value = '';
    firstOption.textContent = mixed ? 'Mixed values' : placeholder;
    select.appendChild(firstOption);
    options.forEach((option) => {
        const item = document.createElement('option');
        item.value = option.value;
        item.textContent = option.label;
        if (!mixed && selected !== '' && String(option.value) === selected) {
            item.selected = true;
        }
        select.appendChild(item);
    });
    if (!mixed && selected !== '' && !optionValues.includes(selected)) {
        const custom = document.createElement('option');
        custom.value = selected;
        custom.textContent = selected;
        custom.selected = true;
        select.appendChild(custom);
    }
    syncSearchableSelect(id);
};

const setInputValue = (id, value) => {
    const element = byId(id);
    if (!element) return;
    if (element.type === 'checkbox') {
        element.indeterminate = false;
        element.checked = value === true || value === 1 || value === '1' || value === 'true';
        return;
    }
    element.value = value ?? '';
    if (element.tagName === 'SELECT') syncSearchableSelect(id);
};

const applyMixedState = (id, mixed) => {
    const element = byId(id);
    if (!element) return;
    element.classList.remove('border-amber-300', 'bg-amber-50');
    if (element.type === 'checkbox') {
        element.indeterminate = mixed;
        return;
    }
    if (mixed) {
        element.classList.add('border-amber-300', 'bg-amber-50');
    }
    if (element.tagName === 'INPUT' && element.type !== 'hidden') {
        element.placeholder = mixed ? 'Mixed values' : '';
        if (mixed) element.value = '';
    }
    if (element.tagName === 'SELECT') syncSearchableSelect(id);
};

const closeAllSearchableSelects = (exceptId = null) => {
    searchableSelects.forEach((instance, id) => {
        if (exceptId && id === exceptId) return;
        instance.panel.classList.add('hidden');
    });
};

const ensureSearchableSelect = (id) => {
    const select = byId(id);
    if (!select) return null;
    if (searchableSelects.has(id)) return searchableSelects.get(id);

    const wrapper = document.createElement('div');
    wrapper.className = 'relative';
    select.parentNode.insertBefore(wrapper, select);
    wrapper.appendChild(select);
    select.classList.add('hidden');

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'flex h-[42px] w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-3 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400';
    trigger.innerHTML = '<span class="truncate">Select an option</span><svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>';

    const panel = document.createElement('div');
    panel.className = 'absolute left-0 right-0 top-[calc(100%+0.5rem)] z-20 hidden rounded-[1.15rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]';

    const search = document.createElement('input');
    search.type = 'text';
    search.placeholder = 'Type to search...';
    search.className = 'mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20';

    const options = document.createElement('div');
    options.className = 'max-h-56 space-y-1 overflow-y-auto';

    const empty = document.createElement('div');
    empty.className = 'hidden rounded-xl border border-dashed border-slate-200 px-3 py-3 text-sm text-slate-400';
    empty.textContent = text.noOptionsFound;

    panel.appendChild(search);
    panel.appendChild(options);
    panel.appendChild(empty);
    wrapper.appendChild(trigger);
    wrapper.appendChild(panel);

    const instance = { select, wrapper, trigger, panel, search, options, empty };
    searchableSelects.set(id, instance);

    trigger.addEventListener('click', () => {
        if (select.disabled) return;
        const opening = panel.classList.contains('hidden');
        closeAllSearchableSelects(id);
        panel.classList.toggle('hidden', !opening);
        if (opening) {
            search.focus();
            filterSearchableOptions(id);
        }
    });

    wrapper.addEventListener('click', (event) => event.stopPropagation());
    wrapper.addEventListener('mousedown', (event) => event.stopPropagation());
    panel.addEventListener('click', (event) => event.stopPropagation());
    panel.addEventListener('mousedown', (event) => event.stopPropagation());
    search.addEventListener('input', () => filterSearchableOptions(id));
    select.addEventListener('change', () => syncSearchableSelect(id));

    return instance;
};

const filterSearchableOptions = (id) => {
    const instance = searchableSelects.get(id);
    if (!instance) return;
    const keyword = normalizeSearch(instance.search.value);
    let visible = 0;
    instance.options.querySelectorAll('button[data-value]').forEach((button) => {
        const matches = !keyword || normalizeSearch(button.dataset.label).includes(keyword);
        button.classList.toggle('hidden', !matches);
        if (matches) visible += 1;
    });
    instance.empty.classList.toggle('hidden', visible > 0);
};

const syncSearchableSelect = (id) => {
    const instance = ensureSearchableSelect(id);
    if (!instance) return;
    const { select, trigger, options, search, panel } = instance;
    const labelEl = trigger.querySelector('span');
    const selectedOption = select.options[select.selectedIndex] || null;
    const placeholder = select.options[0]?.textContent || 'Select an option';
    const mixed = placeholder === 'Mixed values' && String(select.value || '') === '';

    labelEl.textContent = selectedOption?.textContent || placeholder;
    trigger.disabled = select.disabled;
    trigger.classList.toggle('border-amber-300', mixed);
    trigger.classList.toggle('bg-amber-50', mixed);

    options.innerHTML = '';
    Array.from(select.options).forEach((option, index) => {
        const isPlaceholderOption = index === 0 && String(option.value || '') === '';
        const isEmptyOption = String(option.value || '').trim() === '' && String(option.textContent || '').trim() === '';
        if (isPlaceholderOption || isEmptyOption) return;
        const button = document.createElement('button');
        button.type = 'button';
        button.dataset.value = option.value;
        button.dataset.label = option.textContent || '';
        button.className = `flex w-full items-center rounded-xl px-3 py-2 text-left text-sm transition ${String(option.value) === String(select.value) ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-slate-50'}`;
        button.textContent = option.textContent || '';
        button.addEventListener('mousedown', (event) => {
            event.preventDefault();
            event.stopPropagation();
        });
        button.addEventListener('click', () => {
            select.value = option.value;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            panel.classList.add('hidden');
            search.value = '';
        });
        options.appendChild(button);
    });
    filterSearchableOptions(id);
};

const initSearchableSelects = () => searchableSelectIds.forEach((id) => ensureSearchableSelect(id) && syncSearchableSelect(id));

const syncConditionalFields = () => {
    const energyWrap = byId('settings-energy-fields');
    if (energyWrap) energyWrap.classList.toggle('hidden', !byId('PutDisplaysToEnergySaveMode')?.checked);

    const reminderSelect = byId('RemindMinutes');
    const schedulerEnabled = !!byId('UseScheduler')?.checked;
    if (reminderSelect) {
        reminderSelect.disabled = !schedulerEnabled;
        reminderSelect.classList.toggle('cursor-not-allowed', !schedulerEnabled);
        reminderSelect.classList.toggle('bg-slate-100', !schedulerEnabled);
        reminderSelect.classList.toggle('text-slate-400', !schedulerEnabled);
    }

    const colorWrap = byId('settings-color-temp-custom-wrap');
    if (colorWrap) colorWrap.classList.toggle('hidden', byId('ColorTemperatureAdjustment')?.value !== '20');

    const whiteWrap = byId('settings-white-level-custom-wrap');
    if (whiteWrap) whiteWrap.classList.toggle('hidden', byId('WhiteLevel_u_extcombo')?.value !== 'custom');

    const regulation = byId('UsedRegulation')?.value;
    const bodyWrap = byId('settings-body-region-wrap');
    if (bodyWrap) bodyWrap.classList.toggle('hidden', regulation !== 'DIN 6868-157');
    const classificationLabel = byId('settings-classification-label');
    if (classificationLabel) classificationLabel.textContent = regulation === 'DIN 6868-157' ? @js(__('Room Class')) : text.displayCategory;
};

const buildHierarchy = () => {
    const flat = Array.isArray(config.treeData) ? config.treeData : [];
    const facilities = flat.filter((node) => node.type === 'facility').sort((a, b) => String(a.text).localeCompare(String(b.text)));
    const workgroups = flat.filter((node) => node.type === 'workgroup').sort((a, b) => String(a.text).localeCompare(String(b.text)));
    const workstations = flat.filter((node) => node.type === 'workstation').sort((a, b) => String(a.text).localeCompare(String(b.text)));

    state.facilities = facilities.map((node) => ({
        scope: node.id,
        id: numericId(node.id),
        name: node.text,
        workgroups: [],
        workstationIds: [],
    }));

    state.entityMaps.facility.clear();
    state.entityMaps.workgroup.clear();
    state.entityMaps.workstation.clear();

    const facilityMap = new Map();
    state.facilities.forEach((facility) => {
        facilityMap.set(facility.scope, facility);
        state.entityMaps.facility.set(facility.scope, facility);
    });

    const workgroupMap = new Map();
    workgroups.forEach((node) => {
        const facility = facilityMap.get(node.parent);
        if (!facility) return;
        const workgroup = {
            scope: node.id,
            id: numericId(node.id),
            name: node.text,
            facilityScope: facility.scope,
            facilityName: facility.name,
            workstations: [],
            workstationIds: [],
        };
        facility.workgroups.push(workgroup);
        workgroupMap.set(workgroup.scope, workgroup);
        state.entityMaps.workgroup.set(workgroup.scope, workgroup);
    });

    workstations.forEach((node) => {
        const workgroup = workgroupMap.get(node.parent);
        if (!workgroup) return;
        const workstation = {
            scope: node.id,
            id: numericId(node.id),
            name: node.text,
            facilityScope: workgroup.facilityScope,
            facilityName: workgroup.facilityName,
            workgroupScope: workgroup.scope,
            workgroupName: workgroup.name,
            workstationIds: [numericId(node.id)],
        };
        workgroup.workstations.push(workstation);
        state.entityMaps.workstation.set(workstation.scope, workstation);
    });

    state.facilities.forEach((facility) => {
        facility.workgroups.sort((a, b) => a.name.localeCompare(b.name));
        facility.workgroups.forEach((workgroup) => {
            workgroup.workstations.sort((a, b) => a.name.localeCompare(b.name));
            workgroup.workstationIds = workgroup.workstations.map((item) => item.id);
        });
        facility.workstationIds = facility.workgroups.flatMap((workgroup) => workgroup.workstationIds);
    });
};

const allVisibleWorkstationCount = () => {
    if (state.targetType === 'facility') {
        return visibleFacilities().reduce((sum, facility) => sum + facility.workstationIds.length, 0);
    }
    if (state.targetType === 'workgroup') {
        return visibleFacilities().reduce((sum, facility) => sum + visibleWorkgroups(facility).reduce((inner, workgroup) => inner + workgroup.workstationIds.length, 0), 0);
    }
    return visibleFacilities().reduce((sum, facility) => sum + visibleWorkgroups(facility).reduce((inner, workgroup) => inner + visibleWorkstations(workgroup).length, 0), 0);
};

const targetTypeMeta = {
    facility: { label: text.facility, caption: text.selectableFacilities },
    workgroup: { label: text.workgroup, caption: text.selectableWorkgroups },
    workstation: { label: text.workstation, caption: text.selectableWorkstations },
};

const itemMatches = (...parts) => parts.some((part) => normalizeSearch(part).includes(normalizeSearch(state.search)));
const visibleWorkstations = (workgroup) => workgroup.workstations.filter((workstation) => !state.search || itemMatches(workstation.name, workgroup.name, workgroup.facilityName));
const visibleWorkgroups = (facility) => facility.workgroups.filter((workgroup) => {
    if (!state.search) return true;
    return itemMatches(workgroup.name, facility.name) || workgroup.workstations.some((workstation) => itemMatches(workstation.name, workgroup.name, facility.name));
});
const visibleFacilities = () => state.facilities.filter((facility) => {
    if (!state.search) return true;
    return itemMatches(facility.name) || facility.workgroups.some((workgroup) => itemMatches(workgroup.name, facility.name) || workgroup.workstations.some((workstation) => itemMatches(workstation.name, workgroup.name, facility.name)));
});
const renderTargetTypeButtons = () => {
    document.querySelectorAll('.settings-target-type').forEach((button) => {
        const active = button.dataset.targetType === state.targetType;
        button.className = `settings-target-type rounded-lg px-3 py-2 text-sm font-semibold transition ${active ? 'bg-white text-sky-600 shadow-sm ring-1 ring-sky-200' : 'text-slate-500 hover:bg-white/70 hover:text-slate-700'}`;
    });
    setText('settings-summary-target-type', targetTypeMeta[state.targetType].label);
    setText('settings-browser-caption', targetTypeMeta[state.targetType].caption);
};

const buildSelectionPillLabel = (item) => {
    if (state.targetType === 'facility') return item.name;
    if (state.targetType === 'workgroup') return `${item.facilityName} / ${item.name}`;
    return item.name;
};

const affectedWorkstationIds = () => {
    const ids = new Set();
    currentSelections().forEach((item) => (item.workstationIds || []).forEach((id) => ids.add(String(id))));
    return Array.from(ids);
};

const saveScopes = () => currentSelections().map((item) => item.scope);
const loadScope = () => {
    const selections = currentSelections();
    if (selections.length === 0) return null;
    if (selections.length === 1) return selections[0].scope;
    const ids = affectedWorkstationIds();
    return ids.length ? `list-${ids.join(',')}` : null;
};

const availableTabs = () => state.targetType === 'workstation'
    ? ['application', 'calibration', 'qa', 'location', 'move']
    : ['application', 'calibration'];

const moveFacilityIds = () => {
    const ids = new Set(currentSelections().map((item) => item.facilityScope).filter(Boolean));
    return Array.from(ids);
};
const moveEnabled = () => state.targetType === 'workstation' && moveFacilityIds().length === 1 && currentSelections().length > 0;

const setStatus = (message, tone = 'neutral') => {
    const el = byId('settings-modal-status');
    if (!el) return;
    el.textContent = message;
    el.className = `text-sm ${tone === 'success' ? 'text-emerald-600' : tone === 'error' ? 'text-rose-600' : 'text-slate-500'}`;
};

const showConfigModal = () => {
    const modal = byId('settings-config-modal');
    const overlay = byId('settings-config-overlay');
    const panel = byId('settings-config-panel');
    if (!modal || !overlay || !panel) return;
    if (state.closeTimer) {
        clearTimeout(state.closeTimer);
        state.closeTimer = null;
    }
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    requestAnimationFrame(() => {
        overlay.classList.remove('opacity-0');
        overlay.classList.add('opacity-100');
        panel.classList.remove('opacity-0', 'translate-y-4', 'scale-[0.98]');
        panel.classList.add('opacity-100', 'translate-y-0', 'scale-100');
    });
};

const hideConfigModal = () => {
    const modal = byId('settings-config-modal');
    const overlay = byId('settings-config-overlay');
    const panel = byId('settings-config-panel');
    if (!modal || !overlay || !panel) return;
    overlay.classList.remove('opacity-100');
    overlay.classList.add('opacity-0');
    panel.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
    panel.classList.add('opacity-0', 'translate-y-4', 'scale-[0.98]');
    document.body.classList.remove('overflow-hidden');
    state.closeTimer = setTimeout(() => {
        modal.classList.add('hidden');
        state.closeTimer = null;
    }, 200);
};

const resetForms = () => {
    fieldIds.forEach((fieldId) => {
        setInputValue(fieldId, '');
        applyMixedState(fieldId, false);
    });
    ['Language','DataBaseSynchronizationInterval','RemindMinutes','backupPeriod','units','LumUnits','AmbientStable','CalibrationPresents','CalibrationType','ColorTemperatureAdjustment','WhiteLevel_u_extcombo','UsedRegulation','UsedClassification','workgroup_id'].forEach((fieldId) => populateSelect(fieldId, fieldId === 'workgroup_id' || fieldId === 'UsedClassification' ? [] : optionSource(fieldId, null), '', false));
    syncConditionalFields();
};

const renderBrowser = () => {
    const container = byId('settings-browser-panel');
    if (!container) return;
    const facilities = visibleFacilities();
    let html = '';
    let selectableCount = 0;

    if (state.targetType === 'facility') {
        selectableCount = facilities.length;
        facilities.forEach((facility) => {
            const checked = state.selections.has(facility.scope);
            const preview = facility.workgroups.slice(0, 5).map((workgroup) => `<span class="inline-flex rounded-full bg-white px-2 py-1 text-[11px] font-medium text-slate-500">${escapeHtml(workgroup.name)}</span>`).join('');
            html += `<details class="rounded-xl border border-slate-200 bg-white" ${state.browserExpanded ? 'open' : ''}><summary class="cursor-pointer px-3 py-2.5"><div class="flex items-start gap-3"><input type="checkbox" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-sky-500" data-scope="${facility.scope}" ${checked ? 'checked' : ''}><div class="min-w-0 flex-1"><div class="flex items-center gap-2"><span class="inline-flex rounded-full bg-sky-100 px-2 py-0.5 text-[11px] font-semibold text-sky-600">FA</span><p class="truncate text-sm font-semibold text-slate-800">${escapeHtml(facility.name)}</p></div><p class="mt-1 text-xs text-slate-500">${facility.workgroups.length} workgroups · ${facility.workstationIds.length} workstations</p></div><span class="inline-flex h-5 w-5 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-400"><svg xmlns="http://www.w3.org/2000/svg" class="settings-toggle-plus h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v8M8 12h8"/></svg><svg xmlns="http://www.w3.org/2000/svg" class="settings-toggle-minus h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 12h8"/></svg></span></div></summary><div class="border-t border-slate-100 px-3 py-2"><div class="flex flex-wrap gap-1.5">${preview || '<span class="text-xs text-slate-400">No workgroups found.</span>'}</div></div></details>`;
        });
    } else if (state.targetType === 'workgroup') {
        facilities.forEach((facility) => {
            const workgroups = visibleWorkgroups(facility);
            if (!workgroups.length) return;
            selectableCount += workgroups.length;
            html += `<details class="rounded-xl border border-slate-200 bg-white" ${state.browserExpanded ? 'open' : ''}><summary class="cursor-pointer px-3 py-2.5"><div class="flex items-center justify-between gap-3"><div class="min-w-0"><div class="flex items-center gap-2"><span class="inline-flex rounded-full bg-sky-100 px-2 py-0.5 text-[11px] font-semibold text-sky-600">FA</span><p class="truncate text-sm font-semibold text-slate-800">${escapeHtml(facility.name)}</p></div><p class="mt-1 text-xs text-slate-500">${workgroups.length} selectable workgroups</p></div><span class="inline-flex h-5 w-5 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-400"><svg xmlns="http://www.w3.org/2000/svg" class="settings-toggle-plus h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v8M8 12h8"/></svg><svg xmlns="http://www.w3.org/2000/svg" class="settings-toggle-minus h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 12h8"/></svg></span></div></summary><div class="space-y-1.5 border-t border-slate-100 px-3 py-2">`;
            workgroups.forEach((workgroup) => {
                const checked = state.selections.has(workgroup.scope);
                html += `<label class="flex items-center gap-3 rounded-lg border border-transparent px-2 py-2 transition hover:border-slate-200 hover:bg-slate-50"><input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500" data-scope="${workgroup.scope}" ${checked ? 'checked' : ''}><div class="min-w-0 flex-1"><div class="flex items-center gap-2"><span class="inline-flex rounded-full bg-violet-100 px-2 py-0.5 text-[11px] font-semibold text-violet-600">WG</span><p class="truncate text-sm font-medium text-slate-800">${escapeHtml(workgroup.name)}</p></div><p class="mt-1 text-xs text-slate-500">${workgroup.workstationIds.length} workstations</p></div></label>`;
            });
            html += '</div></details>';
        });
    } else {
        facilities.forEach((facility) => {
            const workgroups = visibleWorkgroups(facility).map((workgroup) => ({ ...workgroup, visibleWorkstations: visibleWorkstations(workgroup) })).filter((workgroup) => workgroup.visibleWorkstations.length > 0);
            if (!workgroups.length) return;
            selectableCount += workgroups.reduce((sum, workgroup) => sum + workgroup.visibleWorkstations.length, 0);
            html += `<details class="rounded-xl border border-slate-200 bg-white" ${state.browserExpanded ? 'open' : ''}><summary class="cursor-pointer px-3 py-2.5"><div class="flex items-center justify-between gap-3"><div class="min-w-0"><div class="flex items-center gap-2"><span class="inline-flex rounded-full bg-sky-100 px-2 py-0.5 text-[11px] font-semibold text-sky-600">FA</span><p class="truncate text-sm font-semibold text-slate-800">${escapeHtml(facility.name)}</p></div><p class="mt-1 text-xs text-slate-500">${workgroups.reduce((sum, workgroup) => sum + workgroup.visibleWorkstations.length, 0)} visible workstations</p></div><span class="inline-flex h-5 w-5 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-400"><svg xmlns="http://www.w3.org/2000/svg" class="settings-toggle-plus h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v8M8 12h8"/></svg><svg xmlns="http://www.w3.org/2000/svg" class="settings-toggle-minus h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 12h8"/></svg></span></div></summary><div class="space-y-2 border-t border-slate-100 px-3 py-2">`;
            workgroups.forEach((workgroup) => {
                html += `<details class="rounded-lg border border-slate-200 bg-slate-50" ${state.browserExpanded ? 'open' : ''}><summary class="cursor-pointer px-3 py-2"><div class="flex items-center justify-between gap-3"><div class="min-w-0"><div class="flex items-center gap-2"><span class="inline-flex rounded-full bg-violet-100 px-2 py-0.5 text-[11px] font-semibold text-violet-600">WG</span><p class="truncate text-sm font-medium text-slate-800">${escapeHtml(workgroup.name)}</p></div><p class="mt-1 text-xs text-slate-500">${workgroup.visibleWorkstations.length} visible workstations</p></div><span class="inline-flex h-5 w-5 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-400"><svg xmlns="http://www.w3.org/2000/svg" class="settings-toggle-plus h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 8v8M8 12h8"/></svg><svg xmlns="http://www.w3.org/2000/svg" class="settings-toggle-minus h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 12h8"/></svg></span></div></summary><div class="space-y-1.5 border-t border-slate-100 px-3 py-2">`;
                workgroup.visibleWorkstations.forEach((workstation) => {
                    const checked = state.selections.has(workstation.scope);
                    html += `<label class="flex items-center gap-3 rounded-lg border border-transparent px-2 py-2 transition hover:border-slate-200 hover:bg-white"><input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500" data-scope="${workstation.scope}" ${checked ? 'checked' : ''}><div class="min-w-0 flex-1"><div class="flex items-center gap-2"><span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-600">WS</span><p class="truncate text-sm font-medium text-slate-800">${escapeHtml(workstation.name)}</p></div><p class="mt-1 text-xs text-slate-500">${escapeHtml(workgroup.name)}</p></div></label>`;
                });
                html += '</div></details>';
            });
            html += '</div></details>';
        });
    }

    if (!html) {
        html = `<div class="rounded-xl border border-dashed border-slate-200 bg-white px-4 py-5 text-sm text-slate-500">${escapeHtml(text.noMatchingTargets)}</div>`;
    }

    container.innerHTML = html;
    const compactMobileCount = document.body?.dataset.surface === 'mobile';
    setText(
        'settings-browser-count',
        compactMobileCount
            ? `${selectableCount} items`
            : `${selectableCount} ${targetTypeMeta[state.targetType].label.toLowerCase()}${selectableCount === 1 ? '' : 's'}`
    );
    setText('settings-browser-meta', '');
};

const renderSelectionSummary = () => {
    const items = currentSelections();
    const affectedCount = affectedWorkstationIds().length;
    const empty = byId('settings-selected-empty');
    const list = byId('settings-selected-list');
    const clearButton = byId('settings-clear-selection');
    const openButton = byId('settings-open-config');
    const chips = byId('settings-config-chips');

    setText('settings-summary-selected-count', String(items.length));
    setText('settings-summary-affected-count', String(affectedCount));

    if (empty) empty.classList.toggle('hidden', items.length > 0);
    if (clearButton) clearButton.classList.toggle('hidden', items.length === 0);
    if (openButton) {
        openButton.disabled = items.length === 0;
        openButton.textContent = items.length === 0 ? 'Bulk Configure' : `Bulk Configure ${affectedCount} workstation${affectedCount === 1 ? '' : 's'}`;
    }

    if (list) {
        list.innerHTML = '';
        items.forEach((item) => {
            const pill = document.createElement('div');
            pill.className = 'inline-flex max-w-full items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1.5 text-sm font-medium text-sky-700';
            const label = document.createElement('span');
            label.className = 'truncate';
            label.textContent = buildSelectionPillLabel(item);
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'inline-flex h-5 w-5 items-center justify-center rounded-full bg-white text-sky-600 transition hover:bg-sky-100';
            remove.innerHTML = '&times;';
            remove.addEventListener('click', () => {
                state.selections.delete(item.scope);
                renderBrowser();
                renderSelectionSummary();
            });
            pill.appendChild(label);
            pill.appendChild(remove);
            list.appendChild(pill);
        });
    }

    if (chips) {
        chips.innerHTML = '';
        availableTabs().forEach((tabKey) => {
            const chip = document.createElement('span');
            chip.className = 'inline-flex rounded-full border border-slate-200 bg-white px-2.5 py-1 text-xs font-semibold text-slate-600';
            chip.textContent = tabDefinitions[tabKey].label;
            chips.appendChild(chip);
        });
    }
};

const updateSelection = (scope, checked) => {
    if (checked) state.selections.add(scope);
    else state.selections.delete(scope);
    renderBrowser();
    renderSelectionSummary();
};

const renderModalTabs = () => {
    const container = byId('settings-modal-tabs');
    if (!container) return;
    container.innerHTML = '<div class="flex flex-wrap gap-2"></div>';
    const rail = container.firstElementChild;
    availableTabs().forEach((tabKey) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.dataset.tab = tabKey;
        button.className = `rounded-full px-3.5 py-1.5 text-sm font-semibold transition ${state.modalTab === tabKey ? 'bg-sky-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'}`;
        button.textContent = tabDefinitions[tabKey].label;
        button.addEventListener('click', () => { state.modalTab = tabKey; renderModalTabs(); renderModalPanels(); });
        rail.appendChild(button);
    });
};

const renderModalPanels = () => {
    document.querySelectorAll('.settings-modal-panel').forEach((panel) => panel.classList.add('hidden'));
    document.querySelector(`.settings-modal-panel[data-panel="${state.modalTab}"]`)?.classList.remove('hidden');
    const saveButton = byId('settings-modal-save');
    if (saveButton) {
        const disabled = state.modalTab === 'move' && !moveEnabled();
        saveButton.disabled = state.saving || disabled;
        saveButton.classList.toggle('opacity-50', disabled);
        saveButton.classList.toggle('cursor-not-allowed', disabled);
        saveButton.textContent = state.modalTab === 'move' ? text.moveWorkstations : `Save ${tabDefinitions[state.modalTab].label}`;
    }
    const moveWarning = byId('settings-move-warning');
    if (moveWarning) moveWarning.classList.toggle('hidden', moveEnabled());
};

const fillModalSummary = () => {
    const chips = byId('settings-modal-summary-chips');
    if (!chips) return;
    chips.innerHTML = '';
    const summaryItems = [
        `${targetTypeMeta[state.targetType].label} target`,
        `${currentSelections().length} item${currentSelections().length === 1 ? '' : 's'} selected`,
        `${affectedWorkstationIds().length} workstation${affectedWorkstationIds().length === 1 ? '' : 's'} affected`,
    ];
    summaryItems.forEach((item) => {
        const chip = document.createElement('span');
        chip.className = 'inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600';
        chip.textContent = item;
        chips.appendChild(chip);
    });
};

const applyPayloadToFields = async () => {
    const payload = state.currentPayload || { data: {}, options: {}, meta: { mixedFields: [] } };
    const data = payload.data || {};
    const options = payload.options || {};

    const resolvedOptions = {
        Language: optionSource('Language', options.Language),
        DataBaseSynchronizationInterval: optionSource('DataBaseSynchronizationInterval', options.DataBaseSynchronizationInterval),
        RemindMinutes: optionSource('RemindMinutes', options.RemindMinutes),
        backupPeriod: optionSource('backupPeriod', options.backupPeriod),
        units: optionSource('units', options.units),
        LumUnits: optionSource('LumUnits', options.LumUnits),
        AmbientStable: optionSource('AmbientStable', options.AmbientStable),
        CalibrationPresents: optionSource('CalibrationPresents', options.CalibrationPresents),
        CalibrationType: optionSource('CalibrationType', options.CalibrationType),
        ColorTemperatureAdjustment: optionSource('ColorTemperatureAdjustment', options.ColorTemperatureAdjustment_extcombo || options.ColorTemperatureAdjustment),
        WhiteLevel_u_extcombo: optionSource('WhiteLevel_u_extcombo', options.WhiteLevel_u_extcombo),
        UsedRegulation: optionSource('UsedRegulation', options.UsedRegulation),
        workgroup_id: optionSource('workgroup_id', options.workgroup_id),
    };

    populateSelect('Language', resolvedOptions.Language, data.Language, isMixed('Language'), 'Select language');
    populateSelect('DataBaseSynchronizationInterval', resolvedOptions.DataBaseSynchronizationInterval, data.DataBaseSynchronizationInterval, isMixed('DataBaseSynchronizationInterval'), 'Select sync interval');
    populateSelect('RemindMinutes', resolvedOptions.RemindMinutes, data.RemindMinutes, isMixed('RemindMinutes'), 'Select reminder interval');
    populateSelect('backupPeriod', resolvedOptions.backupPeriod, data.backupPeriod, isMixed('backupPeriod'), 'Select backup period');
    populateSelect('units', resolvedOptions.units, data.units, isMixed('units'), 'Select length unit');
    populateSelect('LumUnits', resolvedOptions.LumUnits, data.LumUnits, isMixed('LumUnits'), 'Select luminance unit');
    populateSelect('AmbientStable', resolvedOptions.AmbientStable, data.AmbientStable, isMixed('AmbientStable'), 'Select a value');
    populateSelect('CalibrationPresents', resolvedOptions.CalibrationPresents, data.CalibrationPresents, isMixed('CalibrationPresents'), 'Select a preset');
    populateSelect('CalibrationType', resolvedOptions.CalibrationType, data.CalibrationType, isMixed('CalibrationType'), 'Select luminance response');
    populateSelect('ColorTemperatureAdjustment', resolvedOptions.ColorTemperatureAdjustment, data.ColorTemperatureAdjustment, isMixed('ColorTemperatureAdjustment'), 'Select a color temperature');
    populateSelect('WhiteLevel_u_extcombo', resolvedOptions.WhiteLevel_u_extcombo, data.WhiteLevel_u_extcombo || data.WhiteLevel, isMixed('WhiteLevel_u_extcombo') || isMixed('WhiteLevel'), 'Select max luminance');
    populateSelect('UsedRegulation', resolvedOptions.UsedRegulation, data.UsedRegulation, isMixed('UsedRegulation'), text.selectRegulation);
    populateSelect('workgroup_id', resolvedOptions.workgroup_id, data.workgroup_id, false, moveEnabled() ? text.selectDestinationWorkgroup : text.moveUnavailable);
    const moveSelect = byId('workgroup_id');
    if (moveSelect) moveSelect.disabled = !moveEnabled();

    fieldIds.forEach((fieldId) => {
        if (['Language','DataBaseSynchronizationInterval','RemindMinutes','backupPeriod','units','LumUnits','AmbientStable','CalibrationPresents','CalibrationType','ColorTemperatureAdjustment','WhiteLevel_u_extcombo','UsedRegulation','UsedClassification','workgroup_id'].includes(fieldId)) return;
        setInputValue(fieldId, data[fieldId]);
    });

    fieldIds.forEach((fieldId) => applyMixedState(fieldId, isMixed(fieldId)));

    if (data.UsedRegulation) {
        await refreshClassificationOptions();
    } else {
        populateSelect('UsedClassification', [], '', isMixed('UsedClassification'), 'Select display category');
    }

    const mixedHint = byId('settings-modal-mixed-hint');
    if (mixedHint) mixedHint.classList.toggle('hidden', !(payload.meta?.mixedFields || []).length);
    initSearchableSelects();
    syncConditionalFields();
};

const refreshClassificationOptions = async () => {
    const regulation = byId('UsedRegulation')?.value;
    if (!regulation || !state.currentScope) {
        populateSelect('UsedClassification', [], '', isMixed('UsedClassification'), 'Select display category');
        return;
    }
    const response = await fetch(`/app-settings/get/categories?id=${encodeURIComponent(state.currentScope)}&regulation=${encodeURIComponent(regulation)}`);
    if (!response.ok) return;
    const options = await response.json();
    populateSelect('UsedClassification', options, state.currentPayload?.data?.UsedClassification, isMixed('UsedClassification'), 'Select display category');
    setInputValue('UsedClassificationForLastScheduling', byId('UsedClassification')?.value || state.currentPayload?.data?.UsedClassificationForLastScheduling || '');
    setInputValue('UsedRegulationForLastScheduling', regulation);
    syncConditionalFields();
};

const openModal = async () => {
    if (!currentSelections().length) return;
    state.currentScope = loadScope();
    if (!state.currentScope) return;
    showConfigModal();
    state.modalOpen = true;
    fillModalSummary();
    state.modalTab = availableTabs()[0];
    renderModalTabs();
    renderModalPanels();
    setStatus(text.loadingWorkstationSettings, 'neutral');
    resetForms();
    const response = await fetch(`/app-settings/${state.currentScope}`);
    if (!response.ok) {
        setStatus(text.failedToLoadWorkstationSettings, 'error');
        return;
    }
    state.currentPayload = await response.json();
    await applyPayloadToFields();
    renderModalPanels();
    setStatus('Review the current tab, then save the section you want to apply.', 'neutral');
};

const closeModal = () => {
    hideConfigModal();
    state.modalOpen = false;
};

const saveCurrentTab = async () => {
    if (state.saving) return;
    if (state.modalTab === 'move' && !moveEnabled()) return;
    const actionMap = { application: 'app', calibration: 'dc', qa: 'qa', location: 'location', move: 'app' };
    const formMap = {
        application: 'settings-modal-form-application',
        calibration: 'settings-modal-form-calibration',
        qa: 'settings-modal-form-qa',
        location: 'settings-modal-form-location',
        move: 'settings-modal-form-move',
    };
    const form = byId(formMap[state.modalTab]);
    if (!form) return;

    state.saving = true;
    renderModalPanels();
    setStatus('Saving changes...', 'neutral');

    const saveTargets = saveScopes();
    const checkboxFieldsByTab = {
        application: ['PutDisplaysToEnergySaveMode', 'UseScheduler', 'UpdateSoftwareAutomaticaly'],
        calibration: ['CreateICCICMProfile'],
        qa: ['AutoDailyTests'],
    };
    for (const scope of saveTargets) {
        const formData = new FormData(form);
        (checkboxFieldsByTab[state.modalTab] || []).forEach((fieldId) => {
            const field = byId(fieldId);
            if (field) formData.set(fieldId, field.checked ? '1' : '0');
        });

        const response = await fetch(`/app-settings/save/${actionMap[state.modalTab]}/${scope}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': config.csrf },
            body: formData,
        });

        if (!response.ok) {
            state.saving = false;
            renderModalPanels();
            setStatus(text.failedToSaveSettings, 'error');
            return;
        }
    }

    state.saving = false;
    renderModalPanels();
    setStatus('Settings saved successfully.', 'success');
};

const initEvents = () => {
    byId('settings-banner-close')?.addEventListener('click', () => byId('settings-scope-banner')?.remove());
    byId('settings-browser-search')?.addEventListener('input', (event) => { state.search = event.target.value || ''; renderBrowser(); });
    byId('settings-expand-all')?.addEventListener('click', () => { state.browserExpanded = true; renderBrowser(); });
    byId('settings-collapse-all')?.addEventListener('click', () => { state.browserExpanded = false; renderBrowser(); });
    byId('settings-clear-selection')?.addEventListener('click', () => { state.selections.clear(); renderBrowser(); renderSelectionSummary(); });
    byId('settings-open-config')?.addEventListener('click', openModal);
    byId('settings-config-overlay')?.addEventListener('click', closeModal);
    byId('settings-modal-close')?.addEventListener('click', closeModal);
    byId('settings-modal-cancel')?.addEventListener('click', closeModal);
    byId('settings-modal-save')?.addEventListener('click', saveCurrentTab);

    document.querySelectorAll('.settings-target-type').forEach((button) => {
        button.addEventListener('click', () => {
            if (state.targetType === button.dataset.targetType) return;
            state.targetType = button.dataset.targetType;
            state.selections.clear();
            if (state.modalOpen) closeModal();
            renderTargetTypeButtons();
            renderBrowser();
            renderSelectionSummary();
        });
    });

    byId('settings-browser-panel')?.addEventListener('change', (event) => {
        const checkbox = event.target.closest('input[data-scope]');
        if (!checkbox) return;
        updateSelection(checkbox.dataset.scope, checkbox.checked);
    });

    byId('PutDisplaysToEnergySaveMode')?.addEventListener('change', syncConditionalFields);
    byId('ColorTemperatureAdjustment')?.addEventListener('change', () => {
        syncConditionalFields();
        if (byId('ColorTemperatureAdjustment')?.value !== '20') byId('ColorTemperatureAdjustment_ext').value = '';
    });
    byId('WhiteLevel_u_extcombo')?.addEventListener('change', () => {
        const selected = byId('WhiteLevel_u_extcombo')?.value || '';
        if (selected && selected !== 'custom' && selected !== 'native') {
            byId('WhiteLevel').value = selected;
            byId('SetWhiteLevel').value = 'true';
        } else if (selected === 'native') {
            byId('SetWhiteLevel').value = 'false';
        } else {
            byId('SetWhiteLevel').value = 'true';
        }
        syncConditionalFields();
    });
    byId('WhiteLevel_u_input')?.addEventListener('input', () => {
        if (byId('WhiteLevel_u_extcombo')?.value === 'custom') {
            byId('WhiteLevel').value = byId('WhiteLevel_u_input').value;
            byId('SetWhiteLevel').value = 'true';
        }
    });
    byId('UsedRegulation')?.addEventListener('change', async () => {
        byId('UsedRegulationForLastScheduling').value = byId('UsedRegulation').value;
        await refreshClassificationOptions();
    });
    byId('UsedClassification')?.addEventListener('change', () => {
        byId('UsedClassificationForLastScheduling').value = byId('UsedClassification').value;
    });

    const onDocumentClick = (event) => {
        searchableSelects.forEach((instance, id) => {
            if (!instance.wrapper.contains(event.target)) {
                instance.panel.classList.add('hidden');
            }
        });
    };

    document.addEventListener('click', onDocumentClick);

    return () => {
        document.removeEventListener('click', onDocumentClick);
    };
};

const setText = (id, value) => { const el = byId(id); if (el) el.textContent = value; };

const boot = () => {
    buildHierarchy();
    renderTargetTypeButtons();
    renderBrowser();
    renderSelectionSummary();
    resetForms();
    initSearchableSelects();
    return initEvents();
};

const launch = () => {
    if (window.Perfectlum?.mountMobilePage && document.body?.dataset.surface === 'mobile') {
        window.Perfectlum.mountMobilePage('workstation-settings-editor', boot);
        return;
    }

    boot();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', launch, { once: true });
} else {
    launch();
}
})();
</script>
@endpush
@endonce


