@extends('mobile.layouts.app')

@push('head')
    <style>
        .mapp-page { display:grid; gap:1rem; }
        .mapp-intro { display:grid; gap:.28rem; padding:.15rem .1rem 0; }
        .mapp-kicker { font-size:10px; font-weight:700; letter-spacing:.24em; text-transform:uppercase; color:#94a3b8; }
        .mapp-title { font-size:1.46rem; font-weight:800; line-height:1.05; letter-spacing:-.045em; color:#0f172a; }
        .mapp-copy { max-width:19rem; font-size:13px; line-height:1.55; color:#64748b; }
        .mapp-card { border:1px solid rgba(226,232,240,.96); border-radius:1.5rem; background:#fff; box-shadow:0 14px 34px rgba(15,23,42,.04); padding:1rem; }
        .mapp-head { display:flex; align-items:flex-start; justify-content:space-between; gap:.75rem; }
        .mapp-head h2 { margin-top:.18rem; font-size:1.02rem; font-weight:800; line-height:1.15; color:#0f172a; }
        .mapp-count { display:inline-flex; align-items:center; justify-content:center; min-width:4.5rem; padding:.38rem .72rem; border-radius:999px; background:#eff6ff; color:#2563eb; font-size:11px; font-weight:700; white-space:nowrap; }
        .mapp-segment { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:.45rem; margin-top:.85rem; padding:.28rem; border:1px solid rgba(226,232,240,.96); border-radius:1rem; background:#f8fafc; }
        .mapp-segment button { min-width:0; min-height:42px; border:1px solid transparent; border-radius:.82rem; padding:.55rem .5rem; color:#64748b; font-size:13px; font-weight:700; transition:.18s ease; }
        .mapp-segment button.active { border-color:rgba(147,197,253,.96); background:#fff; color:#0f172a; box-shadow:0 8px 18px rgba(15,23,42,.06); }
        .mapp-search { display:flex; align-items:center; gap:.7rem; margin-top:.8rem; padding:.78rem .9rem; border:1px solid rgba(226,232,240,.96); border-radius:999px; background:#fff; }
        .mapp-search svg { width:1rem; height:1rem; color:#94a3b8; flex:0 0 auto; }
        .mapp-search input { width:100%; border:0; background:transparent; padding:0; font-size:13.5px; color:#0f172a; outline:none; }
        .mapp-browser { display:grid; gap:.75rem; margin-top:.82rem; }
        .mapp-empty { padding:1.05rem .95rem; border:1px dashed rgba(203,213,225,.96); border-radius:1rem; background:#f8fafc; text-align:center; font-size:13px; color:#64748b; }
        .mapp-row { display:grid; grid-template-columns:auto minmax(0,1fr); gap:.8rem; align-items:start; padding:.92rem; border:1px solid rgba(226,232,240,.96); border-radius:1.15rem; background:linear-gradient(180deg,#fff,#f8fbff); }
        .mapp-row input[type=checkbox] { width:1rem; height:1rem; margin-top:.18rem; border-radius:.35rem; border-color:#cbd5e1; color:#0ea5e9; }
        .mapp-kind { display:inline-flex; align-items:center; justify-content:center; padding:.2rem .46rem; border-radius:999px; background:#e0f2fe; color:#0284c7; font-size:10px; font-weight:800; letter-spacing:.12em; text-transform:uppercase; }
        .mapp-name { margin-top:.34rem; font-size:15px; font-weight:700; line-height:1.3; color:#0f172a; }
        .mapp-meta { margin-top:.24rem; font-size:12.5px; line-height:1.5; color:#64748b; }
        .mapp-stats, .mapp-chips, .mapp-picked { display:flex; flex-wrap:wrap; gap:.45rem; }
        .mapp-stats { margin-top:.58rem; }
        .mapp-stats span, .mapp-chips span, .mapp-picked span { display:inline-flex; align-items:center; padding:.36rem .6rem; border:1px solid rgba(226,232,240,.96); border-radius:999px; background:#fff; color:#475569; font-size:11px; font-weight:700; }
        .mapp-summary { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:.62rem; margin-top:.9rem; }
        .mapp-summary div { border:1px solid rgba(226,232,240,.96); border-radius:1rem; background:#f8fafc; padding:.76rem; }
        .mapp-summary span { display:block; font-size:10px; font-weight:700; letter-spacing:.22em; text-transform:uppercase; color:#94a3b8; }
        .mapp-summary strong { display:block; margin-top:.3rem; font-size:1rem; font-weight:800; line-height:1.08; color:#0f172a; }
        .mapp-chips, .mapp-picked { margin-top:.82rem; }
        .mapp-picked button { display:inline-flex; align-items:center; justify-content:center; width:1.12rem; height:1.12rem; margin-left:.2rem; border-radius:999px; background:#eff6ff; color:#2563eb; font-size:12px; font-weight:800; }
        .mapp-actions, .mapp-sheet-actions { display:flex; gap:.62rem; margin-top:.9rem; }
        .mapp-actions button, .mapp-sheet-actions button { min-height:44px; padding:.74rem 1rem; border-radius:999px; font-size:13px; font-weight:800; transition:.18s ease; }
        .mapp-secondary { min-width:5.4rem; border:1px solid rgba(226,232,240,.96); background:#fff; color:#475569; }
        .mapp-primary { flex:1 1 auto; background:linear-gradient(135deg,#0ea5e9,#0284c7); color:#fff; box-shadow:0 16px 30px rgba(14,165,233,.18); }
        .mapp-secondary:disabled, .mapp-primary:disabled { opacity:.45; cursor:not-allowed; box-shadow:none; }
        .mapp-modal[aria-hidden=true] { display:none; }
        .mapp-modal { position:fixed; inset:0; z-index:160; }
        .mapp-overlay { position:absolute; inset:0; background:rgba(15,23,42,.5); backdrop-filter:blur(6px); }
        .mapp-shell { position:absolute; inset:auto 0 0 0; display:flex; align-items:flex-end; justify-content:center; pointer-events:none; }
        .mapp-sheet { pointer-events:auto; width:100%; max-width:31rem; height:min(92svh,48rem); display:flex; flex-direction:column; border-radius:1.6rem 1.6rem 0 0; background:#fff; box-shadow:0 -20px 40px rgba(15,23,42,.16); transform:translateY(14px); opacity:0; transition:transform .2s ease, opacity .2s ease; }
        .mapp-modal.open .mapp-sheet { transform:translateY(0); opacity:1; }
        .mapp-sheet-head { position:sticky; top:0; z-index:2; padding:1rem 1rem .78rem; border-bottom:1px solid rgba(226,232,240,.94); background:rgba(255,255,255,.98); backdrop-filter:blur(12px); }
        .mapp-handle { width:3.2rem; height:.28rem; margin:0 auto .88rem; border-radius:999px; background:#cbd5e1; }
        .mapp-sheet-top { display:flex; align-items:flex-start; justify-content:space-between; gap:.72rem; }
        .mapp-sheet-top h3 { font-size:1.02rem; font-weight:800; color:#0f172a; }
        .mapp-sheet-top p { margin-top:.22rem; font-size:12.5px; line-height:1.5; color:#64748b; }
        .mapp-close { display:inline-flex; align-items:center; justify-content:center; width:2.35rem; height:2.35rem; border:1px solid rgba(226,232,240,.96); border-radius:999px; background:#fff; color:#64748b; font-size:1.2rem; }
        .mapp-mixed { margin:.78rem 1rem 0; padding:.76rem .88rem; border:1px solid rgba(253,230,138,.96); border-radius:1rem; background:#fefce8; color:#854d0e; font-size:12.5px; line-height:1.5; }
        .mapp-tabs { display:flex; gap:.45rem; overflow-x:auto; padding:.8rem 1rem 0; }
        .mapp-tabs button { flex:0 0 auto; min-height:38px; padding:.56rem .86rem; border:1px solid rgba(226,232,240,.96); border-radius:999px; background:#f8fafc; color:#64748b; font-size:12.5px; font-weight:700; white-space:nowrap; }
        .mapp-tabs button.active { border-color:rgba(14,165,233,.9); background:#0f172a; color:#fff; }
        .mapp-sheet-body { flex:1 1 auto; overflow-y:auto; padding:1rem; display:grid; gap:1rem; }
        .mapp-panel { display:none; gap:.9rem; }
        .mapp-panel.active { display:grid; }
        .mapp-panel-info { padding:.82rem .88rem; border:1px solid rgba(226,232,240,.96); border-radius:1rem; background:#f8fafc; }
        .mapp-panel-info strong { display:block; font-size:13px; font-weight:800; color:#0f172a; }
        .mapp-panel-info span { display:block; margin-top:.2rem; font-size:12.5px; line-height:1.5; color:#64748b; }
        .mapp-grid { display:grid; gap:.8rem; }
        .mapp-grid.two { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .mapp-field label { display:grid; gap:.42rem; }
        .mapp-field label span, .mapp-toggle span:first-child { font-size:10px; font-weight:700; letter-spacing:.22em; text-transform:uppercase; color:#94a3b8; }
        .mapp-field input, .mapp-field select { width:100%; min-height:44px; padding:.8rem .88rem; border:1px solid rgba(226,232,240,.96); border-radius:1rem; background:#fff; font-size:14px; color:#0f172a; outline:none; transition:border-color .18s ease, box-shadow .18s ease; }
        .mapp-field input:focus, .mapp-field select:focus { border-color:rgba(14,165,233,.78); box-shadow:0 0 0 4px rgba(14,165,233,.12); }
        .mapp-field input.mixed, .mapp-field select.mixed { border-color:rgba(251,191,36,.85); background:#fffbeb; }
        .mapp-toggle { display:flex; align-items:center; justify-content:space-between; gap:.78rem; padding:.92rem; border:1px solid rgba(226,232,240,.96); border-radius:1rem; background:#fff; }
        .mapp-toggle strong { display:block; font-size:14px; font-weight:700; color:#0f172a; }
        .mapp-toggle small { display:block; margin-top:.16rem; font-size:12px; line-height:1.45; color:#64748b; }
        .mapp-toggle input[type=checkbox] { width:1.05rem; height:1.05rem; border-radius:.35rem; border-color:#cbd5e1; color:#0ea5e9; }
        .mapp-sheet-foot { padding:.9rem 1rem calc(.95rem + env(safe-area-inset-bottom,0px)); border-top:1px solid rgba(226,232,240,.94); background:rgba(255,255,255,.98); backdrop-filter:blur(12px); }
        .mapp-sheet-foot p { font-size:12.5px; line-height:1.5; color:#64748b; }
        .mapp-dock[aria-hidden=true] { display:none; }
        .mapp-dock { position:fixed; left:50%; bottom:calc(5.3rem + env(safe-area-inset-bottom,0px)); z-index:140; transform:translateX(-50%); width:min(calc(100% - 1.25rem), 27rem); padding:.72rem; border:1px solid rgba(226,232,240,.96); border-radius:1.35rem; background:rgba(255,255,255,.98); box-shadow:0 18px 40px rgba(15,23,42,.12); backdrop-filter:blur(16px); }
        .mapp-dock-row { display:flex; align-items:center; gap:.65rem; }
        .mapp-dock-trigger { flex:1 1 auto; min-width:0; display:grid; gap:.18rem; text-align:left; }
        .mapp-dock-kicker { font-size:10px; font-weight:700; letter-spacing:.22em; text-transform:uppercase; color:#94a3b8; }
        .mapp-dock-title { font-size:14px; font-weight:800; line-height:1.1; color:#0f172a; }
        .mapp-dock-meta { font-size:12px; line-height:1.45; color:#64748b; }
        .mapp-dock .mapp-primary { flex:0 0 auto; min-width:9.4rem; box-shadow:none; }
        .mapp-selection-sheet { height:auto; max-height:min(78svh,34rem); }
        .mapp-selection-body { display:grid; gap:.9rem; padding:1rem; overflow-y:auto; }
        .mapp-selection-actions { display:flex; gap:.62rem; padding:0 1rem calc(.95rem + env(safe-area-inset-bottom,0px)); }
        @media (max-width:380px) { .mapp-title{font-size:1.36rem;} .mapp-summary,.mapp-grid.two{grid-template-columns:1fr;} }
    </style>
@endpush

@section('content')
    <div id="mobile-bulk-settings-page" class="mapp-page">
        <section class="mapp-intro">
            <p class="mapp-kicker">{{ __('Application Settings') }}</p>
            <h1 class="mapp-title">{{ __('Bulk workstation settings') }}</h1>
            <p class="mapp-copy">{{ __('Pick targets and save one section at a time.') }}</p>
        </section>

        <section class="mapp-card">
            <div class="mapp-head">
                <div>
                    <p class="mapp-kicker">{{ __('Target level') }}</p>
                    <h2>{{ __('Pick targets') }}</h2>
                    <p class="mapp-copy">{{ __('Choose one level at a time.') }}</p>
                </div>
                <span id="mapp-browser-count" class="mapp-count">0 {{ __('items') }}</span>
            </div>

            <div id="mapp-types" class="mapp-segment">
                <button type="button" class="active" data-target-type="facility">{{ __('Facility') }}</button>
                <button type="button" data-target-type="workgroup">{{ __('Workgroup') }}</button>
                <button type="button" data-target-type="workstation">{{ __('Workstation') }}</button>
            </div>

            <label class="mapp-search">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
                </svg>
                <input id="mapp-search" type="text" placeholder="{{ __('Search the visible level...') }}">
            </label>

            <div id="mapp-browser" class="mapp-browser"></div>
        </section>

        <div id="mapp-modal" class="mapp-modal" aria-hidden="true">
            <div id="mapp-overlay" class="mapp-overlay"></div>
            <div class="mapp-shell">
                <div class="mapp-sheet">
                    <div class="mapp-sheet-head">
                        <div class="mapp-handle"></div>
                        <div class="mapp-sheet-top">
                            <div>
                                <h3>{{ __('Bulk configure') }}</h3>
                                <p>{{ __('Review one section, then save it.') }}</p>
                            </div>
                            <button id="mapp-close" type="button" class="mapp-close" aria-label="{{ __('Close bulk configure') }}">&times;</button>
                        </div>
                        <div id="mapp-modal-summary" class="mapp-chips" style="margin-top:.8rem;"></div>
                    </div>

                    <div id="mapp-mixed" class="mapp-mixed" style="display:none;">{{ __('Mixed values were found in this selection.') }}</div>
                    <div id="mapp-tabs" class="mapp-tabs"></div>

                    <div class="mapp-sheet-body">
                        <section class="mapp-panel" data-panel="application">
                            <div class="mapp-panel-info"><strong>{{ __('Application') }}</strong><span>{{ __('Core workstation defaults.') }}</span></div>
                            <form id="mapp-form-application" class="mapp-field">
                                {{ csrf_field() }}
                                <div class="mapp-grid two">
                                    <label><span>{{ __('Language') }}</span><select id="Language" name="Language"></select></label>
                                    <label><span>{{ __('Database Synchronization Interval') }}</span><select id="DataBaseSynchronizationInterval" name="DataBaseSynchronizationInterval"></select></label>
                                    <label><span>{{ __('Reminder Interval') }}</span><select id="RemindMinutes" name="RemindMinutes"></select><small>{{ __('Applied on the workstation after the next client sync.') }}</small></label>
                                    <label><span>{{ __('Backup Period') }}</span><select id="backupPeriod" name="backupPeriod"></select></label>
                                    <label><span>{{ __('Units of Length') }}</span><select id="units" name="units"></select></label>
                                    <label><span>{{ __('Units of Luminance') }}</span><select id="LumUnits" name="LumUnits"></select></label>
                                    <label><span>{{ __('Veiling Luminance') }}</span><input id="AmbientLight" name="AmbientLight" type="text"></label>
                                    <label><span>{{ __('Ambient Conditions Stable') }}</span><select id="AmbientStable" name="AmbientStable"></select></label>
                                </div>
                                <div class="mapp-toggle">
                                    <div><span>{{ __('Scheduler') }}</span><strong>{{ __('Enable scheduler') }}</strong><small>{{ __('Allow scheduled workstation actions.') }}</small></div>
                                    <input id="UseScheduler" name="UseScheduler" type="checkbox" value="1">
                                </div>
                                <div class="mapp-toggle">
                                    <div><span>{{ __('Software updates') }}</span><strong>{{ __('Update automatically') }}</strong><small>{{ __('Allow automatic software updates.') }}</small></div>
                                    <input id="UpdateSoftwareAutomaticaly" name="UpdateSoftwareAutomaticaly" type="checkbox" value="1">
                                </div>
                                <div class="mapp-toggle">
                                    <div><span>{{ __('Energy save mode') }}</span><strong>{{ __('Enable schedule window') }}</strong><small>{{ __('Apply start and end times.') }}</small></div>
                                    <input id="PutDisplaysToEnergySaveMode" name="PutDisplaysToEnergySaveMode" type="checkbox" value="1">
                                </div>
                                <div id="mapp-energy-fields" class="mapp-grid two">
                                    <label><span>{{ __('Start') }}</span><input id="StartEnergySaveMode" name="StartEnergySaveMode" type="text"></label>
                                    <label><span>{{ __('End') }}</span><input id="EndEnergySaveMode" name="EndEnergySaveMode" type="text"></label>
                                </div>
                            </form>
                        </section>

                        <section class="mapp-panel" data-panel="calibration">
                            <div class="mapp-panel-info"><strong>{{ __('Display Calibration') }}</strong><span>{{ __('Presets and luminance defaults.') }}</span></div>
                            <form id="mapp-form-calibration" class="mapp-field">
                                {{ csrf_field() }}
                                <input id="Gamma" name="Gamma" type="hidden">
                                <input id="gamut_name" name="gamut_name" type="hidden">
                                <input id="BlackLevel" name="BlackLevel" type="hidden">
                                <input id="SetBlackLevel" name="SetBlackLevel" type="hidden">
                                <input id="WhiteLevel" name="WhiteLevel" type="hidden">
                                <input id="SetWhiteLevel" name="SetWhiteLevel" type="hidden">
                                <div class="mapp-grid two">
                                    <label><span>{{ __('Preset') }}</span><select id="CalibrationPresents" name="CalibrationPresents"></select></label>
                                    <label><span>{{ __('Luminance Response') }}</span><select id="CalibrationType" name="CalibrationType"></select></label>
                                    <label><span>{{ __('Color Temperature') }}</span><select id="ColorTemperatureAdjustment" name="ColorTemperatureAdjustment"></select></label>
                                    <label id="mapp-color-wrap"><span>{{ __('Custom Color Temperature') }}</span><input id="ColorTemperatureAdjustment_ext" name="ColorTemperatureAdjustment_ext" type="text"></label>
                                    <label><span>{{ __('Max Luminance') }}</span><select id="WhiteLevel_u_extcombo" name="WhiteLevel_u_extcombo"></select></label>
                                    <label id="mapp-white-wrap"><span>{{ __('Custom White Level') }}</span><input id="WhiteLevel_u_input" name="WhiteLevel_u_input" type="text"></label>
                                </div>
                                <div class="mapp-toggle">
                                    <div><span>{{ __('ICC profile') }}</span><strong>{{ __('Create profile') }}</strong><small>{{ __('Generate a profile during calibration.') }}</small></div>
                                    <input id="CreateICCICMProfile" name="CreateICCICMProfile" type="checkbox" value="1">
                                </div>
                            </form>
                        </section>

                        <section class="mapp-panel" data-panel="qa">
                            <div class="mapp-panel-info"><strong>{{ __('Quality Assurance') }}</strong><span>{{ __('Regulation and QA defaults.') }}</span></div>
                            <form id="mapp-form-qa" class="mapp-field">
                                {{ csrf_field() }}
                                <input id="UsedClassificationForLastScheduling" name="UsedClassificationForLastScheduling" type="hidden">
                                <input id="UsedRegulationForLastScheduling" name="UsedRegulationForLastScheduling" type="hidden">
                                <div class="mapp-grid two">
                                    <label><span>{{ __('Regulation') }}</span><select id="UsedRegulation" name="UsedRegulation"></select></label>
                                    <label><span id="mapp-classification-label">{{ __('Display Category') }}</span><select id="UsedClassification" name="UsedClassification"></select></label>
                                </div>
                                <div id="mapp-body-wrap" class="mapp-grid">
                                    <label><span>{{ __('Body Region') }}</span><input id="bodyRegion" name="bodyRegion" type="text"></label>
                                </div>
                                <div class="mapp-toggle">
                                    <div><span>{{ __('Daily automation') }}</span><strong>{{ __('Auto daily tests') }}</strong><small>{{ __('Start daily tests automatically.') }}</small></div>
                                    <input id="AutoDailyTests" name="AutoDailyTests" type="checkbox" value="1">
                                </div>
                            </form>
                        </section>

                        <section class="mapp-panel" data-panel="location">
                            <div class="mapp-panel-info"><strong>{{ __('Location') }}</strong><span>{{ __('Administrative workstation details.') }}</span></div>
                            <form id="mapp-form-location" class="mapp-field">
                                {{ csrf_field() }}
                                <div class="mapp-grid">
                                    <label><span>{{ __('Facility Label') }}</span><input id="Facility" name="Facility" type="text"></label>
                                    <label><span>{{ __('Department') }}</span><input id="Department" name="Department" type="text"></label>
                                    <label><span>{{ __('Room') }}</span><input id="Room" name="Room" type="text"></label>
                                    <label><span>{{ __('Responsible Person Name') }}</span><input id="ResponsiblePersonName" name="ResponsiblePersonName" type="text"></label>
                                    <label><span>{{ __('Responsible Person City') }}</span><input id="ResponsiblePersonCity" name="ResponsiblePersonCity" type="text"></label>
                                    <label><span>{{ __('Responsible Person Address') }}</span><input id="ResponsiblePersonAddress" name="ResponsiblePersonAddress" type="text"></label>
                                    <label><span>{{ __('Responsible Person Email') }}</span><input id="ResponsiblePersonEmail" name="ResponsiblePersonEmail" type="text"></label>
                                    <label><span>{{ __('Responsible Person Phone Number') }}</span><input id="ResponsiblePersonPhoneNumber" name="ResponsiblePersonPhoneNumber" type="text"></label>
                                </div>
                            </form>
                        </section>

                        <section class="mapp-panel" data-panel="move">
                            <div class="mapp-panel-info"><strong>{{ __('Move to Workgroup') }}</strong><span>{{ __('Move the selected workstations to one destination.') }}</span></div>
                            <div id="mapp-move-warning" class="mapp-mixed" style="display:none;margin:0;">{{ __('Move is only available when all selected workstations belong to the same facility.') }}</div>
                            <form id="mapp-form-move" class="mapp-field">
                                {{ csrf_field() }}
                                <div class="mapp-grid">
                                    <label><span>{{ __('Destination Workgroup') }}</span><select id="workgroup_id" name="workgroup_id"></select></label>
                                </div>
                            </form>
                        </section>
                    </div>

                    <div class="mapp-sheet-foot">
                        <p id="mapp-status">{{ __('Choose a section to begin.') }}</p>
                        <div class="mapp-sheet-actions">
                            <button id="mapp-cancel" type="button" class="mapp-secondary">{{ __('Cancel') }}</button>
                            <button id="mapp-save" type="button" class="mapp-primary">{{ __('Save') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="mapp-selection-modal" class="mapp-modal" aria-hidden="true">
            <div id="mapp-selection-overlay" class="mapp-overlay"></div>
            <div class="mapp-shell">
                <div class="mapp-sheet mapp-selection-sheet">
                    <div class="mapp-sheet-head">
                        <div class="mapp-handle"></div>
                        <div class="mapp-sheet-top">
                            <div>
                                <h3>{{ __('Selected set') }}</h3>
                                <p>{{ __('Review targets before you continue.') }}</p>
                            </div>
                            <button id="mapp-selection-close" type="button" class="mapp-close" aria-label="{{ __('Close selected set') }}">&times;</button>
                        </div>
                    </div>
                    <div class="mapp-selection-body">
                        <div class="mapp-summary">
                            <div><span>{{ __('Target') }}</span><strong id="mapp-summary-target">{{ __('Facility') }}</strong></div>
                            <div><span>{{ __('Selected') }}</span><strong id="mapp-summary-count">0</strong></div>
                            <div><span>{{ __('Modes') }}</span><strong id="mapp-summary-modes">2</strong></div>
                        </div>
                        <span id="mapp-affected" class="mapp-count">0 {{ __('workstations') }}</span>
                        <div id="mapp-mode-chips" class="mapp-chips"></div>
                        <div id="mapp-selected-empty" class="mapp-empty">{{ __('No target has been checked yet.') }}</div>
                        <div id="mapp-picked" class="mapp-picked" style="display:none;"></div>
                    </div>
                    <div class="mapp-selection-actions">
                        <button id="mapp-clear" type="button" class="mapp-secondary" disabled>{{ __('Clear') }}</button>
                        <button id="mapp-selection-configure" type="button" class="mapp-primary" disabled>{{ __('Bulk configure') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="mapp-dock" class="mapp-dock" aria-hidden="true">
            <div class="mapp-dock-row">
                <button id="mapp-selection-open" type="button" class="mapp-dock-trigger">
                    <span class="mapp-dock-kicker">{{ __('Selected set') }}</span>
                    <strong id="mapp-dock-title" class="mapp-dock-title">0 {{ __('items') }}</strong>
                    <small id="mapp-dock-meta" class="mapp-dock-meta">0 {{ __('workstations') }}</small>
                </button>
                <button id="mapp-open" type="button" class="mapp-primary" disabled>{{ __('Bulk configure') }}</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const cfg = {
                treeData: @json($treeData ?? []),
                optionCatalog: @json($optionCatalog ?? []),
                csrf: @json(csrf_token()),
            };
            const txt = {
                noMatch: @json(__('No matching targets were found for the current search.')),
                noSelected: @json(__('No target has been checked yet.')),
                loading: @json(__('Loading workstation settings...')),
                loadFailed: @json(__('Failed to load workstation settings.')),
                saveFailed: @json(__('Failed to save settings for one or more selected targets.')),
                saved: @json(__('Settings saved successfully.')),
                saving: @json(__('Saving changes...')),
                selectReg: @json(__('Select regulation')),
                selectCat: @json(__('Select display category')),
                selectLen: @json(__('Select length unit')),
                selectLum: @json(__('Select luminance unit')),
                selectVal: @json(__('Select a value')),
                selectPreset: @json(__('Select a preset')),
                selectResp: @json(__('Select luminance response')),
                selectCt: @json(__('Select a color temperature')),
                selectWl: @json(__('Select max luminance')),
                selectDest: @json(__('Select a destination workgroup')),
                moveOff: @json(__('Move unavailable')),
                mixed: @json(__('Mixed values')),
                roomClass: @json(__('Room Class')),
                displayCategory: @json(__('Display Category')),
                facility: @json(__('Facility')),
                workgroup: @json(__('Workgroup')),
                workstation: @json(__('Workstation')),
                item: @json(__('item')),
                items: @json(__('items')),
                ws1: @json(__('workstation')),
                wsN: @json(__('workstations')),
                application: @json(__('Application')),
                calibration: @json(__('Display Calibration')),
                qa: @json(__('Quality Assurance')),
                location: @json(__('Location')),
                move: @json(__('Move to Workgroup')),
            };

            const boot = () => {
                const root = document.getElementById('mobile-bulk-settings-page');
                if (!root) return () => {};
                const byId = (id) => document.getElementById(id);
                const esc = (v) => String(v ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                const norm = (v) => String(v || '').trim().toLowerCase();
                const idOf = (scope) => String(scope || '').replace(/^[a-zA-Z-]+/, '');
                const st = {
                    targetType: 'facility',
                    search: '',
                    selections: new Set(),
                    facilities: [],
                    entityMaps: { facility: new Map(), workgroup: new Map(), workstation: new Map() },
                    modalTab: 'application',
                    modalOpen: false,
                    selectionOpen: false,
                    currentScope: null,
                    currentPayload: { data: {}, options: {}, meta: { mixedFields: [] } },
                    saving: false,
                };
                const types = {
                    facility: { label: txt.facility, badge: 'FA' },
                    workgroup: { label: txt.workgroup, badge: 'WG' },
                    workstation: { label: txt.workstation, badge: 'WS' },
                };
                const tabs = {
                    application: { label: txt.application, action: 'app' },
                    calibration: { label: txt.calibration, action: 'dc' },
                    qa: { label: txt.qa, action: 'qa' },
                    location: { label: txt.location, action: 'location' },
                    move: { label: txt.move, action: 'app' },
                };
                const fieldIds = ['Language','DataBaseSynchronizationInterval','RemindMinutes','backupPeriod','UseScheduler','UpdateSoftwareAutomaticaly','units','LumUnits','AmbientLight','AmbientStable','PutDisplaysToEnergySaveMode','StartEnergySaveMode','EndEnergySaveMode','CalibrationPresents','CalibrationType','Gamma','gamut_name','ColorTemperatureAdjustment','ColorTemperatureAdjustment_ext','WhiteLevel_u_extcombo','WhiteLevel_u_input','WhiteLevel','BlackLevel','SetWhiteLevel','SetBlackLevel','CreateICCICMProfile','UsedRegulation','UsedClassification','UsedClassificationForLastScheduling','UsedRegulationForLastScheduling','bodyRegion','AutoDailyTests','Facility','Department','Room','ResponsiblePersonName','ResponsiblePersonAddress','ResponsiblePersonCity','ResponsiblePersonEmail','ResponsiblePersonPhoneNumber','workgroup_id'];
                const curMap = () => st.entityMaps[st.targetType];
                const curSel = () => Array.from(st.selections).map((scope) => curMap().get(scope)).filter(Boolean);
                const isMixed = (field) => (st.currentPayload?.meta?.mixedFields || []).includes(field);
                const meaningful = (raw) => {
                    if (raw === null || raw === undefined || raw === '') return false;
                    let parsed = raw;
                    if (typeof raw === 'string') {
                        try { parsed = JSON.parse(raw); } catch (_) { return String(raw).trim() !== ''; }
                    }
                    if (Array.isArray(parsed)) return parsed.some((item) => item && typeof item === 'object' ? Object.values(item).some((v) => String(v ?? '').trim() !== '') : String(item ?? '').trim() !== '');
                    if (parsed && typeof parsed === 'object') return Object.entries(parsed).some(([k, v]) => String(k ?? '').trim() !== '' || String(v ?? '').trim() !== '');
                    return String(parsed).trim() !== '';
                };
                const optSrc = (key, raw) => meaningful(raw) ? raw : (cfg.optionCatalog?.[key] ?? raw);
                const normOpts = (raw) => {
                    if (!raw) return [];
                    let parsed = raw;
                    if (typeof raw === 'string') {
                        try { parsed = JSON.parse(raw); } catch (_) { return []; }
                    }
                    if (Array.isArray(parsed)) {
                        return parsed.map((item) => typeof item === 'object' ? { value: item.key ?? item.value ?? '', label: item.value ?? item.label ?? item.key ?? '' } : { value: item, label: item })
                            .filter((item) => String(item.value ?? '').trim() !== '' || String(item.label ?? '').trim() !== '');
                    }
                    return Object.entries(parsed).map(([value, label]) => ({ value, label }))
                        .filter((item) => String(item.value ?? '').trim() !== '' || String(item.label ?? '').trim() !== '');
                };
                const fillSelect = (id, raw, selected = '', mixed = false, placeholder = '') => {
                    const el = byId(id); if (!el) return;
                    const opts = normOpts(raw); const pick = selected == null ? '' : String(selected); const values = opts.map((o) => String(o.value));
                    el.innerHTML = '';
                    const first = document.createElement('option');
                    first.value = ''; first.textContent = mixed ? txt.mixed : placeholder; first.selected = mixed || pick === ''; el.appendChild(first);
                    opts.forEach((o) => { const n = document.createElement('option'); n.value = o.value; n.textContent = o.label; if (!mixed && pick !== '' && String(o.value) === pick) n.selected = true; el.appendChild(n); });
                    if (!mixed && pick !== '' && !values.includes(pick)) { const c = document.createElement('option'); c.value = pick; c.textContent = pick; c.selected = true; el.appendChild(c); }
                };
                const setVal = (id, value) => {
                    const el = byId(id); if (!el) return;
                    if (el.type === 'checkbox') { el.indeterminate = false; el.checked = value === true || value === 1 || value === '1' || value === 'true'; return; }
                    el.value = value ?? '';
                };
                const setMixed = (id, mixed) => {
                    const el = byId(id); if (!el) return;
                    el.classList.remove('mixed');
                    if (el.type === 'checkbox') { el.indeterminate = mixed; return; }
                    if (mixed) el.classList.add('mixed');
                    if (el.tagName === 'INPUT' && el.type !== 'hidden') { el.placeholder = mixed ? txt.mixed : ''; if (mixed) el.value = ''; }
                };
                const syncFields = () => {
                    byId('mapp-energy-fields')?.classList.toggle('hidden', !byId('PutDisplaysToEnergySaveMode')?.checked);
                    const reminderSelect = byId('RemindMinutes');
                    const schedulerEnabled = !!byId('UseScheduler')?.checked;
                    if (reminderSelect) {
                        reminderSelect.disabled = !schedulerEnabled;
                        reminderSelect.classList.toggle('disabled', !schedulerEnabled);
                    }
                    byId('mapp-color-wrap')?.classList.toggle('hidden', byId('ColorTemperatureAdjustment')?.value !== '20');
                    byId('mapp-white-wrap')?.classList.toggle('hidden', byId('WhiteLevel_u_extcombo')?.value !== 'custom');
                    const reg = byId('UsedRegulation')?.value;
                    byId('mapp-body-wrap')?.classList.toggle('hidden', reg !== 'DIN 6868-157');
                    const label = byId('mapp-classification-label');
                    if (label) label.textContent = reg === 'DIN 6868-157' ? txt.roomClass : txt.displayCategory;
                };
                const build = () => {
                    const facilities = (cfg.treeData || []).filter((n) => n.type === 'facility').sort((a, b) => String(a.text).localeCompare(String(b.text)));
                    const workgroups = (cfg.treeData || []).filter((n) => n.type === 'workgroup').sort((a, b) => String(a.text).localeCompare(String(b.text)));
                    const workstations = (cfg.treeData || []).filter((n) => n.type === 'workstation').sort((a, b) => String(a.text).localeCompare(String(b.text)));
                    st.facilities = facilities.map((node) => ({ scope: node.id, id: idOf(node.id), name: node.text, workgroups: [], workstationIds: [] }));
                    st.entityMaps.facility.clear(); st.entityMaps.workgroup.clear(); st.entityMaps.workstation.clear();
                    const facMap = new Map();
                    st.facilities.forEach((facility) => { facMap.set(facility.scope, facility); st.entityMaps.facility.set(facility.scope, facility); });
                    const wgMap = new Map();
                    workgroups.forEach((node) => {
                        const fac = facMap.get(node.parent); if (!fac) return;
                        const wg = { scope: node.id, id: idOf(node.id), name: node.text, facilityScope: fac.scope, facilityName: fac.name, workstations: [], workstationIds: [] };
                        fac.workgroups.push(wg); wgMap.set(wg.scope, wg); st.entityMaps.workgroup.set(wg.scope, wg);
                    });
                    workstations.forEach((node) => {
                        const wg = wgMap.get(node.parent); if (!wg) return;
                        const ws = { scope: node.id, id: idOf(node.id), name: node.text, facilityScope: wg.facilityScope, facilityName: wg.facilityName, workgroupScope: wg.scope, workgroupName: wg.name, workstationIds: [idOf(node.id)] };
                        wg.workstations.push(ws); st.entityMaps.workstation.set(ws.scope, ws);
                    });
                    st.facilities.forEach((fac) => {
                        fac.workgroups.sort((a, b) => a.name.localeCompare(b.name));
                        fac.workgroups.forEach((wg) => { wg.workstations.sort((a, b) => a.name.localeCompare(b.name)); wg.workstationIds = wg.workstations.map((ws) => ws.id); });
                        fac.workstationIds = fac.workgroups.flatMap((wg) => wg.workstationIds);
                    });
                };
                const match = (...parts) => parts.some((part) => norm(part).includes(norm(st.search)));
                const visWs = (wg) => wg.workstations.filter((ws) => !st.search || match(ws.name, wg.name, wg.facilityName));
                const visWg = (fac) => fac.workgroups.filter((wg) => !st.search || match(wg.name, fac.name) || wg.workstations.some((ws) => match(ws.name, wg.name, fac.name)));
                const visFac = () => st.facilities.filter((fac) => !st.search || match(fac.name) || fac.workgroups.some((wg) => match(wg.name, fac.name) || wg.workstations.some((ws) => match(ws.name, wg.name, fac.name))));
                const items = () => st.targetType === 'facility' ? visFac() : st.targetType === 'workgroup' ? visFac().flatMap((fac) => visWg(fac)) : visFac().flatMap((fac) => visWg(fac).flatMap((wg) => visWs(wg)));
                const affWs = () => { const ids = new Set(); curSel().forEach((item) => (item.workstationIds || []).forEach((id) => ids.add(String(id)))); return Array.from(ids); };
                const saveScopes = () => curSel().map((item) => item.scope);
                const loadScope = () => { const picks = curSel(); if (!picks.length) return null; if (picks.length === 1) return picks[0].scope; const ids = affWs(); return ids.length ? `list-${ids.join(',')}` : null; };
                const availTabs = () => st.targetType === 'workstation' ? ['application', 'calibration', 'qa', 'location', 'move'] : ['application', 'calibration'];
                const moveEnabled = () => { const ids = new Set(curSel().map((item) => item.facilityScope).filter(Boolean)); return st.targetType === 'workstation' && ids.size === 1 && curSel().length > 0; };
                const setStatus = (message, tone = 'neutral') => { const el = byId('mapp-status'); if (!el) return; el.textContent = message; el.className = tone === 'success' ? 'text-emerald-600' : tone === 'error' ? 'text-rose-600' : 'text-slate-500'; };
                const lockBody = () => document.body.classList.add('overflow-hidden');
                const unlockBody = () => { if (!st.modalOpen && !st.selectionOpen) document.body.classList.remove('overflow-hidden'); };
                const renderTypes = () => {
                    root.querySelectorAll('#mapp-types button').forEach((button) => button.classList.toggle('active', button.dataset.targetType === st.targetType));
                    byId('mapp-summary-target').textContent = types[st.targetType].label;
                };
                const renderBrowser = () => {
                    const list = items(); const wrap = byId('mapp-browser');
                    byId('mapp-browser-count').textContent = `${list.length} ${list.length === 1 ? txt.item : txt.items}`;
                    if (!list.length) { wrap.innerHTML = `<div class="mapp-empty">${esc(txt.noMatch)}</div>`; return; }
                    wrap.innerHTML = list.map((item) => {
                        const checked = st.selections.has(item.scope) ? 'checked' : '';
                        const badge = types[st.targetType].badge;
                        const meta = st.targetType === 'facility' ? `${item.workgroups.length} workgroups · ${item.workstationIds.length} workstations` : st.targetType === 'workgroup' ? item.facilityName : `${item.workgroupName} · ${item.facilityName}`;
                        const stats = st.targetType === 'facility' ? `<span>${item.workgroups.length} workgroups</span><span>${item.workstationIds.length} workstations</span>` : st.targetType === 'workgroup' ? `<span>${item.workstationIds.length} workstations</span>` : `<span>${item.facilityName}</span>`;
                        return `<label class="mapp-row"><input type="checkbox" data-scope="${esc(item.scope)}" ${checked}><div><span class="mapp-kind">${badge}</span><p class="mapp-name">${esc(item.name)}</p><p class="mapp-meta">${esc(meta)}</p><div class="mapp-stats">${stats}</div></div></label>`;
                    }).join('');
                };
                const renderSummary = () => {
                    const selected = curSel(); const affected = affWs().length;
                    byId('mapp-summary-count').textContent = String(selected.length);
                    byId('mapp-summary-modes').textContent = String(availTabs().length);
                    byId('mapp-affected').textContent = `${affected} ${affected === 1 ? txt.ws1 : txt.wsN}`;
                    byId('mapp-dock-title').textContent = `${selected.length} ${selected.length === 1 ? txt.item : txt.items}`;
                    byId('mapp-dock-meta').textContent = `${affected} ${affected === 1 ? txt.ws1 : txt.wsN}`;
                    byId('mapp-mode-chips').innerHTML = availTabs().map((tab) => `<span>${esc(tabs[tab].label)}</span>`).join('');
                    const empty = byId('mapp-selected-empty'); const picked = byId('mapp-picked'); const clearBtn = byId('mapp-clear'); const openBtn = byId('mapp-open');
                    const dock = byId('mapp-dock'); const selectionOpenBtn = byId('mapp-selection-open'); const selectionConfigure = byId('mapp-selection-configure');
                    clearBtn.disabled = selected.length === 0; openBtn.disabled = selected.length === 0;
                    selectionOpenBtn.disabled = selected.length === 0; selectionConfigure.disabled = selected.length === 0;
                    dock.setAttribute('aria-hidden', selected.length === 0 ? 'true' : 'false');
                    if (!selected.length) { 
                        empty.style.display = 'block'; picked.style.display = 'none'; picked.innerHTML = '';
                        if (st.selectionOpen) closeSelection();
                        return; 
                    }
                    empty.style.display = 'none'; picked.style.display = 'flex';
                    picked.innerHTML = selected.map((item) => `<span>${esc(st.targetType === 'facility' ? item.name : st.targetType === 'workgroup' ? `${item.facilityName} / ${item.name}` : item.name)}<button type="button" data-remove="${esc(item.scope)}">&times;</button></span>`).join('');
                };
                const updateSelection = (scope, checked) => { if (checked) st.selections.add(scope); else st.selections.delete(scope); renderBrowser(); renderSummary(); };
                const resetForms = () => {
                    fieldIds.forEach((id) => { setVal(id, ''); setMixed(id, false); });
                    fillSelect('Language', optSrc('Language', null), '', false, @json(__('Select language')));
                    fillSelect('DataBaseSynchronizationInterval', optSrc('DataBaseSynchronizationInterval', null), '', false, @json(__('Select sync interval')));
                    fillSelect('RemindMinutes', optSrc('RemindMinutes', null), '', false, @json(__('Select reminder interval')));
                    fillSelect('backupPeriod', optSrc('backupPeriod', null), '', false, @json(__('Select backup period')));
                    fillSelect('units', optSrc('units', null), '', false, txt.selectLen);
                    fillSelect('LumUnits', optSrc('LumUnits', null), '', false, txt.selectLum);
                    fillSelect('AmbientStable', optSrc('AmbientStable', null), '', false, txt.selectVal);
                    fillSelect('CalibrationPresents', optSrc('CalibrationPresents', null), '', false, txt.selectPreset);
                    fillSelect('CalibrationType', optSrc('CalibrationType', null), '', false, txt.selectResp);
                    fillSelect('ColorTemperatureAdjustment', optSrc('ColorTemperatureAdjustment', null), '', false, txt.selectCt);
                    fillSelect('WhiteLevel_u_extcombo', optSrc('WhiteLevel_u_extcombo', null), '', false, txt.selectWl);
                    fillSelect('UsedRegulation', optSrc('UsedRegulation', null), '', false, txt.selectReg);
                    fillSelect('UsedClassification', [], '', false, txt.selectCat);
                    fillSelect('workgroup_id', [], '', false, txt.selectDest);
                    syncFields();
                };
                const renderTabs = () => { byId('mapp-tabs').innerHTML = availTabs().map((tab) => `<button type="button" data-tab="${tab}" class="${st.modalTab === tab ? 'active' : ''}">${esc(tabs[tab].label)}</button>`).join(''); };
                const renderPanels = () => {
                    modal?.querySelectorAll('.mapp-panel').forEach((panel) => panel.classList.toggle('active', panel.dataset.panel === st.modalTab));
                    byId('mapp-move-warning').style.display = st.modalTab === 'move' && !moveEnabled() ? 'block' : 'none';
                    const saveBtn = byId('mapp-save'); const disabled = st.modalTab === 'move' && !moveEnabled();
                    saveBtn.disabled = st.saving || disabled;
                    saveBtn.textContent = st.modalTab === 'move' ? @json(__('Move workstations')) : `${@json(__('Save'))} ${tabs[st.modalTab].label}`;
                };
                const renderModalSummary = () => {
                    const selected = curSel(); const affected = affWs().length;
                    byId('mapp-modal-summary').innerHTML = [`${types[st.targetType].label} target`, `${selected.length} ${selected.length === 1 ? txt.item : txt.items}`, `${affected} ${affected === 1 ? txt.ws1 : txt.wsN}`].map((entry) => `<span>${esc(entry)}</span>`).join('');
                };
                const applyPayload = async () => {
                    const payload = st.currentPayload || { data: {}, options: {}, meta: { mixedFields: [] } };
                    const data = payload.data || {}; const opts = payload.options || {};
                    fillSelect('Language', optSrc('Language', opts.Language), data.Language, isMixed('Language'), @json(__('Select language')));
                    fillSelect('DataBaseSynchronizationInterval', optSrc('DataBaseSynchronizationInterval', opts.DataBaseSynchronizationInterval), data.DataBaseSynchronizationInterval, isMixed('DataBaseSynchronizationInterval'), @json(__('Select sync interval')));
                    fillSelect('RemindMinutes', optSrc('RemindMinutes', opts.RemindMinutes), data.RemindMinutes, isMixed('RemindMinutes'), @json(__('Select reminder interval')));
                    fillSelect('backupPeriod', optSrc('backupPeriod', opts.backupPeriod), data.backupPeriod, isMixed('backupPeriod'), @json(__('Select backup period')));
                    fillSelect('units', optSrc('units', opts.units), data.units, isMixed('units'), txt.selectLen);
                    fillSelect('LumUnits', optSrc('LumUnits', opts.LumUnits), data.LumUnits, isMixed('LumUnits'), txt.selectLum);
                    fillSelect('AmbientStable', optSrc('AmbientStable', opts.AmbientStable), data.AmbientStable, isMixed('AmbientStable'), txt.selectVal);
                    fillSelect('CalibrationPresents', optSrc('CalibrationPresents', opts.CalibrationPresents), data.CalibrationPresents, isMixed('CalibrationPresents'), txt.selectPreset);
                    fillSelect('CalibrationType', optSrc('CalibrationType', opts.CalibrationType), data.CalibrationType, isMixed('CalibrationType'), txt.selectResp);
                    fillSelect('ColorTemperatureAdjustment', optSrc('ColorTemperatureAdjustment', opts.ColorTemperatureAdjustment_extcombo || opts.ColorTemperatureAdjustment), data.ColorTemperatureAdjustment, isMixed('ColorTemperatureAdjustment'), txt.selectCt);
                    fillSelect('WhiteLevel_u_extcombo', optSrc('WhiteLevel_u_extcombo', opts.WhiteLevel_u_extcombo), data.WhiteLevel_u_extcombo || data.WhiteLevel, isMixed('WhiteLevel_u_extcombo') || isMixed('WhiteLevel'), txt.selectWl);
                    fillSelect('UsedRegulation', optSrc('UsedRegulation', opts.UsedRegulation), data.UsedRegulation, isMixed('UsedRegulation'), txt.selectReg);
                    fillSelect('workgroup_id', optSrc('workgroup_id', opts.workgroup_id), data.workgroup_id, false, moveEnabled() ? txt.selectDest : txt.moveOff);
                    const moveSelect = byId('workgroup_id'); if (moveSelect) moveSelect.disabled = !moveEnabled();
                    fieldIds.forEach((id) => {
                        if (['Language','DataBaseSynchronizationInterval','RemindMinutes','backupPeriod','units','LumUnits','AmbientStable','CalibrationPresents','CalibrationType','ColorTemperatureAdjustment','WhiteLevel_u_extcombo','UsedRegulation','UsedClassification','workgroup_id'].includes(id)) return;
                        setVal(id, data[id]);
                    });
                    fieldIds.forEach((id) => setMixed(id, isMixed(id)));
                    if (data.UsedRegulation) await refreshClassificationOptions(); else fillSelect('UsedClassification', [], '', isMixed('UsedClassification'), txt.selectCat);
                    byId('mapp-mixed').style.display = (payload.meta?.mixedFields || []).length ? 'block' : 'none';
                    syncFields();
                };
                const refreshClassificationOptions = async () => {
                    const regulation = byId('UsedRegulation')?.value;
                    if (!regulation || !st.currentScope) { fillSelect('UsedClassification', [], '', isMixed('UsedClassification'), txt.selectCat); return; }
                    const response = await fetch(`/app-settings/get/categories?id=${encodeURIComponent(st.currentScope)}&regulation=${encodeURIComponent(regulation)}`);
                    if (!response.ok) return;
                    const options = await response.json();
                    fillSelect('UsedClassification', options, st.currentPayload?.data?.UsedClassification, isMixed('UsedClassification'), txt.selectCat);
                    setVal('UsedClassificationForLastScheduling', byId('UsedClassification')?.value || st.currentPayload?.data?.UsedClassificationForLastScheduling || '');
                    setVal('UsedRegulationForLastScheduling', regulation);
                    syncFields();
                };
                const openModal = async () => {
                    if (!curSel().length) return;
                    st.currentScope = loadScope(); if (!st.currentScope) return;
                    closeSelection();
                    st.modalOpen = true; st.modalTab = availTabs()[0];
                    renderModalSummary(); renderTabs(); renderPanels(); resetForms(); setStatus(txt.loading);
                    const modal = byId('mapp-modal'); modal.setAttribute('aria-hidden', 'false'); modal.classList.add('open'); lockBody();
                    const response = await fetch(`/app-settings/${st.currentScope}`);
                    if (!response.ok) { setStatus(txt.loadFailed, 'error'); return; }
                    st.currentPayload = await response.json();
                    await applyPayload(); renderPanels(); setStatus(@json(__('Review one section, then save it.')));
                };
                const closeModal = () => {
                    st.modalOpen = false;
                    const modal = byId('mapp-modal'); modal.classList.remove('open'); modal.setAttribute('aria-hidden', 'true');
                    unlockBody();
                };
                const openSelection = () => {
                    if (!curSel().length) return;
                    st.selectionOpen = true;
                    const modal = byId('mapp-selection-modal'); modal.setAttribute('aria-hidden', 'false'); modal.classList.add('open');
                    lockBody();
                };
                const closeSelection = () => {
                    st.selectionOpen = false;
                    const modal = byId('mapp-selection-modal'); modal.classList.remove('open'); modal.setAttribute('aria-hidden', 'true');
                    unlockBody();
                };
                const saveCurrent = async () => {
                    if (st.saving) return;
                    if (st.modalTab === 'move' && !moveEnabled()) return;
                    const forms = { application:'mapp-form-application', calibration:'mapp-form-calibration', qa:'mapp-form-qa', location:'mapp-form-location', move:'mapp-form-move' };
                    const form = byId(forms[st.modalTab]); if (!form) return;
                    st.saving = true; renderPanels(); setStatus(txt.saving);
                    const checkboxFieldsByTab = {
                        application: ['PutDisplaysToEnergySaveMode', 'UseScheduler', 'UpdateSoftwareAutomaticaly'],
                        calibration: ['CreateICCICMProfile'],
                        qa: ['AutoDailyTests'],
                    };
                    for (const scope of saveScopes()) {
                        const body = new FormData(form);
                        (checkboxFieldsByTab[st.modalTab] || []).forEach((id) => {
                            const field = byId(id);
                            if (field) body.set(id, field.checked ? '1' : '0');
                        });
                        const response = await fetch(`/app-settings/save/${tabs[st.modalTab].action}/${scope}`, { method:'POST', headers:{ 'X-CSRF-TOKEN': cfg.csrf }, body });
                        if (!response.ok) { st.saving = false; renderPanels(); setStatus(txt.saveFailed, 'error'); return; }
                    }
                    st.saving = false; renderPanels(); setStatus(txt.saved, 'success');
                };
                const modal = byId('mapp-modal');
                const selectionModal = byId('mapp-selection-modal');
                if (modal && modal.parentElement !== document.body) document.body.appendChild(modal);
                if (selectionModal && selectionModal.parentElement !== document.body) document.body.appendChild(selectionModal);
                const dock = byId('mapp-dock');
                if (dock && dock.parentElement !== document.body) document.body.appendChild(dock);
                const clickHandler = (event) => {
                    const typeButton = event.target.closest('#mapp-types button');
                    if (typeButton) {
                        const nextType = typeButton.dataset.targetType;
                        if (nextType && nextType !== st.targetType) {
                            st.targetType = nextType; st.selections.clear(); if (st.modalOpen) closeModal();
                            renderTypes(); renderBrowser(); renderSummary();
                        }
                        return;
                    }
                    const remove = event.target.closest('[data-remove]');
                    if (remove) { st.selections.delete(remove.dataset.remove); renderBrowser(); renderSummary(); return; }
                };
                const modalClickHandler = (event) => {
                    const tabButton = event.target.closest('#mapp-tabs button[data-tab]');
                    if (tabButton) { st.modalTab = tabButton.dataset.tab; renderTabs(); renderPanels(); }
                };
                const changeHandler = (event) => {
                    const rowCheckbox = event.target.closest('#mapp-browser input[data-scope]');
                    if (rowCheckbox) updateSelection(rowCheckbox.dataset.scope, rowCheckbox.checked);
                };
                const searchHandler = (event) => { st.search = event.target.value || ''; renderBrowser(); };
                const keyHandler = (event) => {
                    if (event.key !== 'Escape') return;
                    if (st.modalOpen) closeModal();
                    else if (st.selectionOpen) closeSelection();
                };
                root.addEventListener('click', clickHandler);
                root.addEventListener('change', changeHandler);
                modal?.addEventListener('click', modalClickHandler);
                byId('mapp-search')?.addEventListener('input', searchHandler);
                byId('mapp-clear')?.addEventListener('click', () => { st.selections.clear(); renderBrowser(); renderSummary(); });
                byId('mapp-open')?.addEventListener('click', openModal);
                byId('mapp-selection-open')?.addEventListener('click', openSelection);
                byId('mapp-selection-configure')?.addEventListener('click', openModal);
                byId('mapp-overlay')?.addEventListener('click', closeModal);
                byId('mapp-close')?.addEventListener('click', closeModal);
                byId('mapp-cancel')?.addEventListener('click', closeModal);
                byId('mapp-save')?.addEventListener('click', saveCurrent);
                byId('mapp-selection-overlay')?.addEventListener('click', closeSelection);
                byId('mapp-selection-close')?.addEventListener('click', closeSelection);
                ['PutDisplaysToEnergySaveMode', 'ColorTemperatureAdjustment', 'WhiteLevel_u_extcombo'].forEach((id) => byId(id)?.addEventListener('change', syncFields));
                byId('UsedRegulation')?.addEventListener('change', async () => { setVal('UsedRegulationForLastScheduling', byId('UsedRegulation')?.value || ''); await refreshClassificationOptions(); });
                byId('UsedClassification')?.addEventListener('change', () => setVal('UsedClassificationForLastScheduling', byId('UsedClassification')?.value || ''));
                document.addEventListener('keydown', keyHandler);
                build(); renderTypes(); renderBrowser(); renderSummary(); resetForms(); syncFields();
                return () => {
                    root.removeEventListener('click', clickHandler);
                    root.removeEventListener('change', changeHandler);
                    modal?.removeEventListener('click', modalClickHandler);
                    byId('mapp-search')?.removeEventListener('input', searchHandler);
                    document.removeEventListener('keydown', keyHandler);
                    closeModal();
                    closeSelection();
                    const moved = byId('mapp-modal');
                    if (moved?.parentElement === document.body) moved.remove();
                    const movedSelection = byId('mapp-selection-modal');
                    if (movedSelection?.parentElement === document.body) movedSelection.remove();
                    const movedDock = byId('mapp-dock');
                    if (movedDock?.parentElement === document.body) movedDock.remove();
                };
            };

            if (window.Perfectlum?.mountMobilePage) {
                window.Perfectlum.mountMobilePage('mobile-application-settings-v3', boot);
            } else {
                boot();
            }
        })();
    </script>
@endpush
