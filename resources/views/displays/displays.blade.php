@include('common.navigations.header')

@php
    $type = request('type', '');
    $role = session('role');
    $canManageDisplays = in_array($role, ['super', 'admin'], true);
    $initialDisplayStatus = in_array($type, ['ok', 'failed'], true) ? $type : '';
    $displayText = [
        'allFacilities' => __('All facilities'),
        'allWorkgroups' => __('All workgroups'),
        'allWorkstations' => __('All workstations'),
        'selectFacility' => __('Select facility'),
        'selectWorkgroup' => __('Select workgroup'),
        'selectWorkstation' => __('Select workstation'),
        'option' => __('option'),
        'options' => __('options'),
        'noOptionsFound' => __('No options found'),
        'display' => __('Display'),
        'displayName' => __('Display Name'),
        'inventoryNumber' => __('Inventory Number'),
        'workstation' => __('Workstation'),
        'workgroup' => __('Workgroup'),
        'facility' => __('Facility'),
        'issue' => __('Attention'),
        'displayHours' => __('Runtime'),
        'updated' => __('Updated'),
        'lastActivity' => __('Latest activity'),
        'added' => __('Added'),
        'noRecordedActivity' => __('No recorded activity'),
        'status' => __('Status'),
        'actions' => __('Actions'),
        'noActiveIssue' => __('No active alert'),
        'noAlertDetail' => __('No alert detail'),
        'needsAttention' => __('Needs review'),
        'noFailureDetail' => __('No failure detail recorded'),
        'runningHours' => __('Reported runtime'),
        'hoursSync' => __('Runtime sync'),
        'noHoursData' => __('No runtime reported'),
        'noHoursSync' => __('No runtime sync recorded'),
        'online' => __('Online'),
        'offline' => __('Offline'),
        'latestIssue' => __('Attention now'),
        'latestFailedCheck' => __('Latest failed check'),
        'latestFailedRun' => __('Latest failed run'),
        'liveState' => __('Current state'),
        'searchDisplays' => __('Search displays...'),
        'searchFacilities' => __('Search facilities...'),
        'searchWorkgroups' => __('Search workgroups...'),
        'searchWorkstations' => __('Search workstations...'),
        'previous' => __('Previous'),
        'next' => __('Next'),
        'showing' => __('Showing'),
        'results' => __('results'),
        'loading' => __('Loading...'),
        'noMatchingRecordsFound' => __('No matching records found'),
        'unableToLoadData' => __('Unable to load data'),
        'saveChanges' => __('Save Changes'),
        'saving' => __('Saving...'),
        'deleteDisplay' => __('Delete Display'),
        'quickDisplayUpdate' => __('Quick display update'),
        'loadingDisplayForm' => __('Loading display form...'),
        'generalSettings' => __('General settings'),
        'calibration' => __('Calibration'),
        'manufacturer' => __('Manufacturer'),
        'model' => __('Model'),
        'serialNumber' => __('Serial Number'),
        'typeOfDisplay' => __('Type of Display'),
        'displayTechnology' => __('Display Technology'),
        'screenSize' => __('Screen Size'),
        'currentLutIndex' => __('Current LUT Index'),
        'resolutionHorizontal' => __('Resolution Horizontal'),
        'resolutionVertical' => __('Resolution Vertical'),
        'installationDate' => __('Installation Date'),
        'calibrationOptions' => __('Calibration Options'),
        'excludeDisplayFromTesting' => __('Exclude display from testing / calibration'),
        'useGraphicboardLutsOnly' => __('Use graphicboard LUTs only'),
        'useInternalSensorIfPossible' => __('Use internal sensor if possible'),
        'financial' => __('Financial'),
        'lifecycleValues' => __('Lifecycle values'),
        'purchaseDate' => __('Purchase Date'),
        'expectedReplacementDate' => __('Expected Replacement Date'),
        'initialValue' => __('Initial Value'),
        'expectedValue' => __('Expected Value'),
        'annualStraightLine' => __('Annual Straight Line'),
        'monthlyStraightLine' => __('Monthly Straight Line'),
        'currentValue' => __('Current Value'),
        'deleteThisDisplay' => __('Delete this display?'),
        'thisActionWillPermanentlyRemove' => __('This action will permanently remove'),
        'unableToLoadDisplayForm' => __('Unable to load display form.'),
        'unableToUpdateDisplay' => __('Unable to update display.'),
        'unableToDeleteDisplay' => __('Unable to delete display.'),
    ];
@endphp

<style>
    .desktop-display-filter-panel {
        border: 1px solid rgba(226, 232, 240, 0.95);
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.94));
        box-shadow: 0 18px 60px -36px rgba(15, 23, 42, 0.2);
    }

    .desktop-display-filter-head {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.1rem;
    }

    .desktop-display-panel-kicker {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .desktop-display-panel-title {
        margin-top: 0.38rem;
        font-size: 1.1rem;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: -0.02em;
        color: #0f172a;
    }

    .desktop-display-panel-copy {
        margin-top: 0.38rem;
        font-size: 13px;
        line-height: 1.45;
        color: #64748b;
    }

    .desktop-display-filter-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .desktop-display-table-block {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .desktop-display-grid-shell {
        position: relative;
    }

    .desktop-display-grid-shell.is-loading {
        min-height: 34rem;
    }

    .desktop-display-grid-shell.is-loading #displays-grid,
    .desktop-display-grid-shell.is-loading .gridjs-head,
    .desktop-display-grid-shell.is-loading .gridjs-footer {
        opacity: 0.26;
        filter: saturate(0.9);
        transition: opacity 180ms ease, filter 180ms ease;
        pointer-events: none;
    }

    .desktop-display-grid-loading {
        position: absolute;
        inset: 0;
        z-index: 20;
        display: flex;
        align-items: stretch;
        justify-content: stretch;
        padding: 0.28rem;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 180ms ease, visibility 180ms ease;
    }

    .desktop-display-grid-shell.is-loading .desktop-display-grid-loading {
        opacity: 1;
        visibility: visible;
    }

    .desktop-display-grid-loading-surface {
        width: 100%;
        min-height: 34rem;
        border: 1px solid rgba(226, 232, 240, 0.94);
        border-radius: 1.65rem;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.95));
        backdrop-filter: blur(12px);
        box-shadow:
            inset 0 1px 0 rgba(255, 255, 255, 0.78),
            0 18px 44px -34px rgba(15, 23, 42, 0.22);
        padding: 1.05rem 1.2rem 1.25rem;
    }

    .desktop-display-grid-skeleton-toolbar {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 1rem;
    }

    .desktop-display-grid-skeleton-search {
        width: 15.5rem;
        height: 2.6rem;
        border-radius: 999px;
    }

    .desktop-display-grid-skeleton-head,
    .desktop-display-grid-skeleton-row {
        display: grid;
        grid-template-columns: minmax(0, 1.8fr) minmax(220px, 0.9fr) 144px 132px 96px 68px;
        column-gap: 1rem;
        align-items: center;
    }

    .desktop-display-grid-skeleton-head {
        margin-bottom: 0.55rem;
        padding: 0 0 0.75rem;
        border-bottom: 1px solid rgba(226, 232, 240, 0.96);
    }

    .desktop-display-grid-skeleton-row {
        min-height: 6.05rem;
        padding: 0.92rem 0;
        border-bottom: 1px solid rgba(226, 232, 240, 0.9);
    }

    .desktop-display-grid-skeleton-cell {
        min-width: 0;
    }

    .desktop-display-grid-skeleton-display,
    .desktop-display-grid-skeleton-issue {
        display: flex;
        flex-direction: column;
        gap: 0.42rem;
    }

    .desktop-display-grid-skeleton-updated,
    .desktop-display-grid-skeleton-hours,
    .desktop-display-grid-skeleton-status,
    .desktop-display-grid-skeleton-actions {
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .desktop-display-skeleton-block {
        display: block;
        border-radius: 999px;
        background:
            linear-gradient(90deg, rgba(226, 232, 240, 0.58) 0%, rgba(255, 255, 255, 0.94) 50%, rgba(226, 232, 240, 0.58) 100%);
        background-size: 200% 100%;
        animation: desktop-display-skeleton-shimmer 1.18s linear infinite;
    }

    .desktop-display-skeleton-headline {
        height: 0.86rem;
        width: 7.5rem;
    }

    .desktop-display-skeleton-title {
        height: 1.06rem;
        width: 68%;
        border-radius: 0.7rem;
    }

    .desktop-display-skeleton-meta {
        height: 0.82rem;
        width: 48%;
        border-radius: 0.68rem;
    }

    .desktop-display-skeleton-issue-title {
        height: 0.82rem;
        width: 6.6rem;
    }

    .desktop-display-skeleton-issue-main {
        height: 1rem;
        width: 72%;
        border-radius: 0.7rem;
    }

    .desktop-display-skeleton-issue-note {
        height: 0.82rem;
        width: 42%;
        border-radius: 0.68rem;
    }

    .desktop-display-skeleton-hours-main {
        height: 1rem;
        width: 5.8rem;
        border-radius: 0.7rem;
    }

    .desktop-display-skeleton-hours-note {
        height: 0.82rem;
        width: 6.8rem;
        margin-top: 0.28rem;
        border-radius: 0.68rem;
    }

    .desktop-display-skeleton-date {
        height: 0.96rem;
        width: 6rem;
        border-radius: 0.68rem;
    }

    .desktop-display-skeleton-time {
        height: 0.8rem;
        width: 3rem;
        margin-top: 0.28rem;
        border-radius: 0.68rem;
    }

    .desktop-display-skeleton-pill {
        width: 3.2rem;
        height: 1.55rem;
        border-radius: 999px;
    }

    .desktop-display-skeleton-circle {
        width: 2.35rem;
        height: 2.35rem;
        border-radius: 999px;
    }

    @keyframes desktop-display-skeleton-shimmer {
        0% {
            background-position: 200% 0;
        }

        100% {
            background-position: -200% 0;
        }
    }

    #displays-grid .gridjs-wrapper,
    #displays-grid .gridjs-table,
    #displays-grid .gridjs-thead,
    #displays-grid .gridjs-tbody {
        border: 0;
        box-shadow: none;
        background: transparent;
    }

    #displays-grid .gridjs-table {
        display: block;
        width: 100% !important;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
    }

    #displays-grid .gridjs-thead,
    #displays-grid .gridjs-tbody {
        display: block;
        width: 100%;
    }

    #displays-grid .gridjs-thead .gridjs-tr,
    #displays-grid .gridjs-tbody .gridjs-tr {
        display: grid;
        grid-template-columns: minmax(0, 1.8fr) minmax(220px, 0.94fr) 144px 132px 96px 68px;
        column-gap: 1rem;
        width: 100%;
        align-items: start;
    }

    #displays-grid .gridjs-th {
        border-bottom: 0;
        background: transparent;
        padding: 0 0.25rem 0.8rem;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: #94a3b8;
        white-space: nowrap;
    }

    #displays-grid .gridjs-td {
        border-bottom: 0;
        background: transparent;
        padding: 0.92rem 0.25rem;
        vertical-align: top;
    }

    #displays-grid .gridjs-thead .gridjs-tr {
        position: relative;
        padding: 0 0.15rem 0.55rem;
    }

    #displays-grid .gridjs-thead .gridjs-tr::after {
        content: '';
        position: absolute;
        inset: auto 0 0;
        height: 1px;
        background: rgba(226, 232, 240, 0.95);
    }

    #displays-grid .gridjs-th:first-child,
    #displays-grid .gridjs-td:first-child {
        padding-left: 0;
    }

    #displays-grid .gridjs-th:last-child,
    #displays-grid .gridjs-td:last-child {
        padding-right: 0;
    }

    #displays-grid .gridjs-head {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 1rem;
        padding: 0.45rem 0.4rem 0.9rem !important;
    }

    #displays-grid .gridjs-search {
        margin-left: auto;
    }

    #displays-grid .gridjs-search-input {
        width: 100% !important;
        min-width: 16rem;
        max-width: 16rem;
        border-color: rgba(203, 213, 225, 0.9) !important;
        background: rgba(248, 250, 252, 0.96) !important;
        padding: 0.82rem 1rem !important;
        font-size: 13px !important;
        color: #334155 !important;
    }

    .desktop-display-table-shell {
        padding: 0.28rem;
    }

    .desktop-display-table-shell .gridjs-head,
    .desktop-display-table-shell .gridjs-wrapper,
    .desktop-display-table-shell .gridjs-footer {
        border-radius: 1.55rem;
    }

    .desktop-display-table-shell .gridjs-wrapper {
        padding: 0 0.2rem;
    }

    .desktop-display-table-shell .gridjs-footer {
        padding-top: 0.45rem !important;
    }

    #displays-grid .gridjs-tbody .gridjs-tr {
        position: relative;
        padding: 0.22rem 0.15rem;
        border-radius: 1.3rem;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.86), rgba(248, 250, 252, 0.72));
        border: 1px solid transparent;
        transition: border-color 180ms ease, box-shadow 180ms ease, background 180ms ease;
    }

    #displays-grid .gridjs-tbody .gridjs-tr::after {
        content: '';
        position: absolute;
        inset: auto 0.15rem 0;
        height: 1px;
        background: rgba(226, 232, 240, 0.78);
    }

    #displays-grid .gridjs-tbody .gridjs-tr:last-child::after {
        opacity: 0;
    }

    #displays-grid .gridjs-tbody .gridjs-tr:hover {
        border-color: rgba(226, 232, 240, 0.92);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.94));
        box-shadow: 0 16px 36px -28px rgba(15, 23, 42, 0.18);
    }

    .desktop-display-summary {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-width: 0;
        min-height: 4.2rem;
        padding-block: 0;
        max-width: 28rem;
    }

    .desktop-display-heading {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        min-width: 0;
    }

    .desktop-display-kicker-dot {
        height: 0.5rem;
        width: 0.5rem;
        flex: 0 0 auto;
        border-radius: 999px;
        background: #94a3b8;
        margin-top: 0.12rem;
    }

    .desktop-display-kicker-dot.healthy {
        background: #10b981;
    }

    .desktop-display-kicker-dot.alert {
        background: #f43f5e;
    }

    .desktop-display-title {
        margin-top: 0;
        display: inline-block;
        max-width: 100%;
        min-width: 0;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.24;
        letter-spacing: -0.02em;
        color: #0f172a;
    }

    .desktop-display-title:hover {
        color: #0284c7;
    }

    .desktop-display-meta {
        margin-top: 0.28rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.2rem 0.36rem;
        font-size: 11px;
        color: #94a3b8;
    }

    .desktop-display-meta-button {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border: 0;
        background: transparent;
        padding: 0;
        font: inherit;
        font-weight: 600;
        color: #64748b;
        transition: color 160ms ease;
    }

    .desktop-display-meta-button:hover {
        color: #0284c7;
    }

    .desktop-display-separator {
        color: #cbd5e1;
        font-weight: 700;
    }

    .desktop-display-detail {
        margin-top: 0.24rem;
        font-size: 11.5px;
        line-height: 1.4;
        color: #475569;
    }

    .desktop-display-detail.problem {
        color: #334155;
    }

    .desktop-display-updated {
        display: inline-flex;
        white-space: nowrap;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
    }

    .desktop-display-issue {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-width: 0;
        min-height: 4.2rem;
        max-width: 14.5rem;
        padding-top: 0;
    }

    .desktop-display-issue-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .desktop-display-issue-kicker::before {
        content: '';
        width: 0.4rem;
        height: 0.4rem;
        border-radius: 999px;
        background: #cbd5e1;
        flex: 0 0 auto;
    }

    .desktop-display-issue-kicker.healthy::before {
        background: #10b981;
    }

    .desktop-display-issue-kicker.alert::before {
        background: #f43f5e;
    }

    .desktop-display-issue-text {
        margin-top: 0.24rem;
        display: -webkit-box;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.42;
        color: #0f172a;
    }

    .desktop-display-issue-text.muted {
        color: #334155;
    }

    .desktop-display-issue-text.placeholder {
        margin-top: 0;
        color: #94a3b8;
        font-weight: 500;
    }

    .desktop-display-issue-note {
        margin-top: 0.16rem;
        font-size: 11px;
        line-height: 1.4;
        color: #64748b;
    }

    .desktop-display-hours {
        display: flex;
        min-width: 0;
        flex-direction: column;
        justify-content: center;
        min-height: 4.2rem;
        gap: 0.16rem;
        padding-top: 0;
    }

    .desktop-display-hours-kicker {
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .desktop-display-hours-text {
        font-size: 12px;
        font-weight: 700;
        line-height: 1.35;
        color: #0f172a;
    }

    .desktop-display-hours-text.empty {
        color: #334155;
        font-weight: 600;
    }

    .desktop-display-hours-note {
        font-size: 11px;
        line-height: 1.4;
        color: #64748b;
    }

    .desktop-display-updated-block {
        display: inline-flex;
        min-width: 5.9rem;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        min-height: 4.2rem;
        gap: 0.1rem;
    }

    .desktop-display-updated-date {
        font-size: 11.5px;
        font-weight: 700;
        line-height: 1.2;
        color: #334155;
    }

    .desktop-display-updated-time {
        font-size: 10.5px;
        font-weight: 600;
        line-height: 1.2;
        color: #94a3b8;
    }

    .desktop-display-status-cell,
    .desktop-display-actions-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 4.2rem;
    }

    .desktop-display-status-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0.32rem 0.66rem;
        font-size: 9.5px;
        font-weight: 700;
        line-height: 1;
        letter-spacing: 0.02em;
    }

    @media (max-width: 1439px) {
        #displays-grid .gridjs-thead .gridjs-tr,
        #displays-grid .gridjs-tbody .gridjs-tr {
            grid-template-columns: minmax(0, 1.65fr) minmax(210px, 0.92fr) 114px 88px 60px;
        }
    }

    .desktop-display-status-pill.success {
        background: #dcfce7;
        color: #047857;
    }

    .desktop-display-status-pill.danger {
        background: #ffe4e6;
        color: #e11d48;
    }

    @media (max-width: 1279px) {
        .desktop-display-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .desktop-display-filter-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    /* Stable editorial ledger override */
    #displays-grid .gridjs-table {
        display: table !important;
        width: 100% !important;
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 0;
    }

    #displays-grid .gridjs-thead {
        display: table-header-group !important;
        width: auto !important;
    }

    #displays-grid .gridjs-tbody {
        display: table-row-group !important;
        width: auto !important;
    }

    #displays-grid .gridjs-thead .gridjs-tr,
    #displays-grid .gridjs-tbody .gridjs-tr {
        display: table-row !important;
        width: auto !important;
        position: static;
        padding: 0 !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    #displays-grid .gridjs-thead .gridjs-tr::after,
    #displays-grid .gridjs-tbody .gridjs-tr::after {
        display: none !important;
    }

    #displays-grid .gridjs-th,
    #displays-grid .gridjs-td {
        display: table-cell !important;
    }

    #displays-grid .gridjs-th {
        padding: 0 0.8rem 0.9rem !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.95) !important;
        vertical-align: bottom;
    }

    #displays-grid .gridjs-th-content {
        display: inline-flex;
        align-items: center;
        min-height: 1rem;
        vertical-align: middle;
    }

    #displays-grid .gridjs-sort {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        vertical-align: middle;
        margin-left: 0.32rem;
        color: #94a3b8 !important;
    }

    #displays-grid .gridjs-td {
        padding: 0.95rem 0.8rem !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.9) !important;
        vertical-align: top;
    }

    #displays-grid .gridjs-th:first-child,
    #displays-grid .gridjs-td:first-child {
        width: 47%;
        padding-left: 0 !important;
    }

    #displays-grid .gridjs-th:nth-child(2),
    #displays-grid .gridjs-td:nth-child(2) {
        width: 25%;
    }

    #displays-grid .gridjs-th:nth-child(3),
    #displays-grid .gridjs-td:nth-child(3) {
        width: 12%;
    }

    #displays-grid .gridjs-th:nth-child(4),
    #displays-grid .gridjs-td:nth-child(4) {
        width: 8%;
    }

    #displays-grid .gridjs-th:nth-child(5),
    #displays-grid .gridjs-td:nth-child(5) {
        width: 8%;
        padding-right: 0 !important;
    }

    #displays-grid .gridjs-tr:hover .gridjs-td {
        background: rgba(248, 250, 252, 0.88) !important;
    }

    .desktop-display-table-shell .gridjs-wrapper {
        padding: 0 !important;
    }

    .desktop-display-table-shell .gridjs-head {
        padding: 0.2rem 0 0.85rem !important;
    }

    #displays-grid .gridjs-search-input {
        min-width: 15rem;
        max-width: 15rem;
    }

    .desktop-display-summary {
        max-width: none;
        min-height: 4rem;
    }

    .desktop-display-issue {
        max-width: none;
        min-height: 4rem;
    }

    .desktop-display-heading {
        gap: 0.45rem;
    }

    .desktop-display-title {
        font-size: 14.5px;
    }

    .desktop-display-meta {
        margin-top: 0.24rem;
        gap: 0.18rem 0.34rem;
    }

    .desktop-display-detail {
        margin-top: 0.18rem;
    }

    .desktop-display-issue-text {
        margin-top: 0.2rem;
    }

    .desktop-display-issue-note {
        margin-top: 0.14rem;
    }

    .desktop-display-updated-block {
        min-width: 0;
        min-height: 4rem;
    }

    .desktop-display-status-cell,
    .desktop-display-actions-cell {
        align-items: center;
        justify-content: flex-start;
        min-height: 4rem;
    }

    .desktop-display-hours {
        min-height: 4rem;
    }

    /* Final header/table normalization */
    #displays-grid table.gridjs-table,
    #displays-grid .gridjs-table {
        display: table !important;
        width: 100% !important;
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 0;
    }

    #displays-grid thead.gridjs-thead,
    #displays-grid .gridjs-thead {
        display: table-header-group !important;
        width: auto !important;
    }

    #displays-grid tbody.gridjs-tbody,
    #displays-grid .gridjs-tbody {
        display: table-row-group !important;
        width: auto !important;
    }

    #displays-grid tr.gridjs-tr,
    #displays-grid .gridjs-thead .gridjs-tr,
    #displays-grid .gridjs-tbody .gridjs-tr {
        display: table-row !important;
        width: auto !important;
        position: static !important;
        padding: 0 !important;
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    #displays-grid tr.gridjs-tr::after,
    #displays-grid .gridjs-thead .gridjs-tr::after,
    #displays-grid .gridjs-tbody .gridjs-tr::after {
        display: none !important;
    }

    #displays-grid th.gridjs-th,
    #displays-grid td.gridjs-td {
        display: table-cell !important;
    }

    #displays-grid th.gridjs-th {
        position: relative;
        padding: 0 0.8rem 0.9rem !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.95) !important;
        background: transparent !important;
        vertical-align: bottom;
    }

    #displays-grid td.gridjs-td {
        padding: 0.95rem 0.8rem !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.9) !important;
        background: transparent !important;
        vertical-align: top;
    }

    #displays-grid tr.gridjs-tr:hover td.gridjs-td {
        background: rgba(248, 250, 252, 0.88) !important;
    }

    #displays-grid th.gridjs-th:first-child,
    #displays-grid td.gridjs-td:first-child {
        width: 39%;
        padding-left: 0 !important;
    }

    #displays-grid th.gridjs-th:nth-child(2),
    #displays-grid td.gridjs-td:nth-child(2) {
        width: 22%;
    }

    #displays-grid th.gridjs-th:nth-child(3),
    #displays-grid td.gridjs-td:nth-child(3) {
        width: 13%;
    }

    #displays-grid th.gridjs-th:nth-child(4),
    #displays-grid td.gridjs-td:nth-child(4) {
        width: 11%;
    }

    #displays-grid th.gridjs-th:nth-child(5),
    #displays-grid td.gridjs-td:nth-child(5) {
        width: 7%;
    }

    #displays-grid th.gridjs-th:nth-child(6),
    #displays-grid td.gridjs-td:nth-child(6) {
        width: 8%;
        padding-right: 0 !important;
    }

    #displays-grid th.gridjs-th .gridjs-th-content {
        display: block !important;
        width: auto !important;
        float: none !important;
        overflow: visible !important;
        text-overflow: clip !important;
        white-space: nowrap;
        line-height: 1.1;
    }

    #displays-grid th.gridjs-th-sort {
        padding-right: 1.7rem !important;
    }

    #displays-grid th.gridjs-th-sort button.gridjs-sort {
        float: none !important;
        position: absolute;
        top: 50%;
        right: 0.65rem;
        transform: translateY(-48%);
        margin: 0 !important;
    }

    #displays-grid .gridjs-head {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 1rem;
        padding: 0.2rem 0 0.85rem !important;
    }

    .desktop-display-table-shell .gridjs-wrapper {
        padding: 0 !important;
    }

    /* Ultimate sortable table stabilization */
    #displays-grid .gridjs-table,
    #displays-grid table.gridjs-table {
        table-layout: fixed !important;
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }

    #displays-grid .gridjs-head {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        padding: 0.2rem 0 0.85rem !important;
    }

    #displays-grid th.gridjs-th,
    #displays-grid td.gridjs-td {
        position: relative;
        vertical-align: middle !important;
    }

    #displays-grid th.gridjs-th {
        padding: 0 1rem 0.9rem !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.95) !important;
    }

    #displays-grid td.gridjs-td {
        padding: 1rem 1rem !important;
        border-bottom: 1px solid rgba(226, 232, 240, 0.9) !important;
        vertical-align: middle !important;
    }

    #displays-grid th.gridjs-th:first-child,
    #displays-grid td.gridjs-td:first-child {
        width: 40% !important;
        padding-left: 0 !important;
    }

    #displays-grid th.gridjs-th:nth-child(2),
    #displays-grid td.gridjs-td:nth-child(2) {
        width: 16% !important;
    }

    #displays-grid th.gridjs-th:nth-child(3),
    #displays-grid td.gridjs-td:nth-child(3) {
        width: 14% !important;
    }

    #displays-grid th.gridjs-th:nth-child(4),
    #displays-grid td.gridjs-td:nth-child(4) {
        width: 12% !important;
    }

    #displays-grid th.gridjs-th:nth-child(5),
    #displays-grid td.gridjs-td:nth-child(5) {
        width: 8% !important;
    }

    #displays-grid th.gridjs-th:nth-child(6),
    #displays-grid td.gridjs-td:nth-child(6) {
        width: 6% !important;
        padding-right: 0 !important;
    }

    #displays-grid th.gridjs-th .gridjs-th-content {
        display: inline-flex !important;
        align-items: center !important;
        min-height: 1.1rem !important;
        line-height: 1 !important;
        white-space: nowrap !important;
        float: none !important;
        overflow: visible !important;
        text-overflow: clip !important;
    }

    #displays-grid th.gridjs-th-sort {
        padding-right: 2rem !important;
    }

    #displays-grid th.gridjs-th:nth-child(4).gridjs-th-sort {
        padding-right: 2.4rem !important;
    }

    #displays-grid th.gridjs-th:nth-child(4) .gridjs-th-content {
        padding-right: 0.15rem;
    }

    #displays-grid th.gridjs-th-sort .gridjs-sort,
    #displays-grid th.gridjs-th-sort button.gridjs-sort {
        position: absolute !important;
        top: 50% !important;
        right: 0.7rem !important;
        transform: translateY(-50%) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 !important;
        float: none !important;
        color: #94a3b8 !important;
    }

    #displays-grid th.gridjs-th-sort .gridjs-sort svg,
    #displays-grid th.gridjs-th-sort button.gridjs-sort svg {
        display: block;
    }

    #displays-grid th.gridjs-th-sort .gridjs-sort:focus-visible,
    #displays-grid th.gridjs-th-sort button.gridjs-sort:focus-visible {
        outline: 2px solid rgba(14, 165, 233, 0.35);
        outline-offset: 2px;
        border-radius: 999px;
    }

    #displays-grid .gridjs-tr:hover .gridjs-td {
        background: rgba(248, 250, 252, 0.88) !important;
    }

    .desktop-display-updated-label {
        font-size: 9.5px;
        font-weight: 700;
        line-height: 1.15;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #94a3b8;
    }

    .desktop-display-updated-date.empty {
        color: #64748b;
        font-weight: 600;
    }
</style>

<div class="flex flex-col gap-6 pb-8">
    <x-page-header title="{{ __('All Displays') }}" description="{{ __('Manage and monitor all diagnostic displays across facilities.') }}" icon="monitor">
        <x-slot name="actions">
            <x-export-dropdown
                excel-url="{{ url('reports/displays?export_type=excel&type=' . $type) }}"
                pdf-url="{{ url('reports/displays?export_type=pdf&type=' . $type) }}"
                label="{{ __('Export Report') }}" />
        </x-slot>
    </x-page-header>

    <section class="desktop-display-filter-panel rounded-[2rem] p-5">
        <div class="desktop-display-filter-head">
            <div>
                <p class="desktop-display-panel-kicker">{{ __('Fleet filters') }}</p>
                <h2 class="desktop-display-panel-title">{{ __('Refine by scope and status') }}</h2>
                <p class="desktop-display-panel-copy">{{ __('Focus the display fleet by hierarchy and condition before scanning the list below.') }}</p>
            </div>

            <div class="flex items-start justify-end">
                <button
                    id="reset-display-filters"
                    type="button"
                    class="inline-flex h-11 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    {{ __('Reset Filters') }}
                </button>
            </div>
        </div>

        <div class="desktop-display-filter-grid">
            <div class="min-w-0 space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Facility') }}</label>
                <div class="relative">
                    <button
                        id="display-facility-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400">
                        <span id="display-facility-label" class="truncate">{{ __('All facilities') }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>
                    <div id="display-facility-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input id="display-facility-search" type="text" placeholder="{{ __('Search facilities...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="display-facility-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="display-facility-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="min-w-0 space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Workgroup') }}</label>
                <div class="relative">
                    <button
                        id="display-workgroup-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400">
                        <span id="display-workgroup-label" class="truncate">{{ __('All workgroups') }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>
                    <div id="display-workgroup-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input id="display-workgroup-search" type="text" placeholder="{{ __('Search workgroups...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="display-workgroup-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="display-workgroup-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="min-w-0 space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Workstation') }}</label>
                <div class="relative">
                    <button
                        id="display-workstation-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400">
                        <span id="display-workstation-label" class="truncate">{{ __('All workstations') }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>
                    <div id="display-workstation-panel" class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input id="display-workstation-search" type="text" placeholder="{{ __('Search workstations...') }}" class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="display-workstation-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="display-workstation-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="min-w-0 space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Status') }}</label>
                <div class="grid h-12 grid-cols-3 rounded-2xl border border-slate-200 bg-white p-1">
                    <button
                        id="display-status-all"
                        type="button"
                        data-status=""
                        class="rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="layers-3" class="h-4 w-4"></i>
                            <span>{{ __('All') }}</span>
                        </span>
                    </button>
                    <button
                        id="display-status-ok"
                        type="button"
                        data-status="ok"
                        class="rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="badge-check" class="h-4 w-4"></i>
                            <span>{{ __('OK') }}</span>
                        </span>
                    </button>
                    <button
                        id="display-status-failed"
                        type="button"
                        data-status="failed"
                        class="rounded-[0.9rem] px-3 text-sm font-semibold text-slate-600 transition">
                        <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <i data-lucide="triangle-alert" class="h-4 w-4"></i>
                            <span>{{ __('Not OK') }}</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <div class="desktop-display-table-block">
        <div id="displays-grid-shell" class="desktop-display-grid-shell is-loading" aria-busy="true">
            <x-data-table id="displays-grid" class="mb-10 workstation-table-shell desktop-display-table-shell" />

            <div id="displays-grid-loading" class="desktop-display-grid-loading" aria-hidden="true">
                <div class="desktop-display-grid-loading-surface">
                    <div class="desktop-display-grid-skeleton-toolbar">
                        <span class="desktop-display-skeleton-block desktop-display-grid-skeleton-search"></span>
                    </div>

                    <div class="desktop-display-grid-skeleton-head">
                        <div class="desktop-display-grid-skeleton-cell"><span class="desktop-display-skeleton-block desktop-display-skeleton-headline"></span></div>
                        <div class="desktop-display-grid-skeleton-cell"><span class="desktop-display-skeleton-block desktop-display-skeleton-headline"></span></div>
                        <div class="desktop-display-grid-skeleton-cell"><span class="desktop-display-skeleton-block desktop-display-skeleton-headline"></span></div>
                        <div class="desktop-display-grid-skeleton-cell"><span class="desktop-display-skeleton-block desktop-display-skeleton-headline"></span></div>
                        <div class="desktop-display-grid-skeleton-cell"><span class="desktop-display-skeleton-block desktop-display-skeleton-headline"></span></div>
                        <div class="desktop-display-grid-skeleton-cell"><span class="desktop-display-skeleton-block desktop-display-skeleton-headline"></span></div>
                    </div>

                    @for($i = 0; $i < 5; $i++)
                        <div class="desktop-display-grid-skeleton-row">
                            <div class="desktop-display-grid-skeleton-cell desktop-display-grid-skeleton-display">
                                <span class="desktop-display-skeleton-block desktop-display-skeleton-title"></span>
                                <span class="desktop-display-skeleton-block desktop-display-skeleton-meta"></span>
                            </div>
                            <div class="desktop-display-grid-skeleton-cell desktop-display-grid-skeleton-issue">
                                <span class="desktop-display-skeleton-block desktop-display-skeleton-issue-title"></span>
                                <span class="desktop-display-skeleton-block desktop-display-skeleton-issue-main"></span>
                                <span class="desktop-display-skeleton-block desktop-display-skeleton-issue-note"></span>
                            </div>
                            <div class="desktop-display-grid-skeleton-cell desktop-display-grid-skeleton-hours">
                                <div>
                                    <span class="desktop-display-skeleton-block desktop-display-skeleton-hours-main"></span>
                                    <span class="desktop-display-skeleton-block desktop-display-skeleton-hours-note"></span>
                                </div>
                            </div>
                            <div class="desktop-display-grid-skeleton-cell desktop-display-grid-skeleton-updated">
                                <div>
                                    <span class="desktop-display-skeleton-block desktop-display-skeleton-date"></span>
                                    <span class="desktop-display-skeleton-block desktop-display-skeleton-time"></span>
                                </div>
                            </div>
                            <div class="desktop-display-grid-skeleton-cell desktop-display-grid-skeleton-status">
                                <span class="desktop-display-skeleton-block desktop-display-skeleton-pill"></span>
                            </div>
                            <div class="desktop-display-grid-skeleton-cell desktop-display-grid-skeleton-actions">
                                <span class="desktop-display-skeleton-block desktop-display-skeleton-circle"></span>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

<div id="display-action-overlay" class="pointer-events-none fixed inset-0 z-[1200] hidden">
    <div id="display-action-menu" class="pointer-events-auto fixed hidden w-56 rounded-2xl border border-slate-200 bg-white p-2 shadow-[0_20px_60px_-28px_rgba(15,23,42,0.35)]">
        @if($canManageDisplays)
            <button id="display-action-edit" type="button" class="flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
                <i data-lucide="pencil-line" class="h-4 w-4"></i>
                {{ __('Edit Display') }}
            </button>
            <button id="display-action-delete" type="button" class="flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
                {{ __('Delete Display') }}
            </button>
        @endif
    </div>
</div>

<div id="display-edit-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="flex max-h-[88vh] w-full max-w-4xl flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="flex shrink-0 items-start justify-between border-b border-slate-200 px-6 py-5">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Edit Display') }}</p>
                <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Quick display update') }}</h3>
                <p id="display-edit-subtitle" class="mt-2 text-sm text-slate-500"></p>
            </div>
            <button id="display-edit-close" type="button" class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <form id="display-edit-form" class="flex min-h-0 flex-1 flex-col px-6 py-6">
            <div id="display-edit-loading" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">
                {{ __('Loading display form...') }}
            </div>
            <div id="display-edit-error" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700"></div>

            <div id="display-edit-body" class="hidden min-h-0 flex-1 space-y-5 overflow-y-auto pr-1">
                <section class="rounded-[1.5rem] border border-slate-200 bg-slate-50/70 p-4">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Calibration') }}</p>
                            <h4 class="mt-1 text-base font-semibold text-slate-900">{{ __('General settings') }}</h4>
                        </div>
                    </div>

                    <div class="grid gap-4 lg:grid-cols-2">
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Manufacturer') }}</span>
                            <input name="Manufacturer" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Model') }}</span>
                            <input name="Model" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Serial Number') }}</span>
                            <input name="SerialNumber" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Inventory Number') }}</span>
                            <input name="InventoryNumber" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Type of Display') }}</span>
                            <select name="TypeOfDisplay" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"></select>
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Display Technology') }}</span>
                            <select name="DisplayTechnology" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"></select>
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Screen Size') }}</span>
                            <input name="ScreenSize" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Current LUT Index') }}</span>
                            <input name="CurrentLUTIndex" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Resolution Horizontal') }}</span>
                            <input name="ResolutionHorizontal" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Resolution Vertical') }}</span>
                            <input name="ResolutionVertical" type="text" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Installation Date') }}</span>
                            <input name="InstalationDate" type="date" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 lg:col-span-2">
                            <p class="text-sm font-medium text-slate-700">{{ __('Calibration Options') }}</p>
                            <div class="mt-4 space-y-3">
                                <label class="flex items-center gap-3 text-sm text-slate-600">
                                    <input name="exclude" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                    {{ __('Exclude display from testing / calibration') }}
                                </label>
                                <label class="flex items-center gap-3 text-sm text-slate-600">
                                    <input name="graphicboardOnly" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                    {{ __('Use graphicboard LUTs only') }}
                                </label>
                                <label class="flex items-center gap-3 text-sm text-slate-600">
                                    <input name="InternalSensor" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                                    {{ __('Use internal sensor if possible') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-[1.5rem] border border-slate-200 bg-slate-50/70 p-4">
                    <div class="mb-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Financial') }}</p>
                        <h4 class="mt-1 text-base font-semibold text-slate-900">{{ __('Lifecycle values') }}</h4>
                    </div>
                    <div class="grid gap-4 lg:grid-cols-2">
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Purchase Date') }}</span>
                            <input name="purchase_date" type="date" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Expected Replacement Date') }}</span>
                            <input name="expected_replacement_date" type="date" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Initial Value') }}</span>
                            <input name="initial_value" type="number" step="0.01" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Expected Value') }}</span>
                            <input name="expected_value" type="number" step="0.01" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Annual Straight Line') }}</span>
                            <input name="annual_straight_line" type="number" step="0.01" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Monthly Straight Line') }}</span>
                            <input name="monthly_straight_line" type="number" step="0.01" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                        <label class="space-y-2 lg:col-span-2">
                            <span class="block text-sm font-medium text-slate-700">{{ __('Current Value') }}</span>
                            <input name="current_value" type="number" step="0.01" class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-700 outline-none transition focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                        </label>
                    </div>
                </section>
            </div>

            <div class="mt-5 flex shrink-0 justify-end gap-3 border-t border-slate-200 pt-5">
                <button id="display-edit-cancel" type="button" class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    {{ __('Cancel') }}
                </button>
                <button id="display-edit-save" type="submit" class="inline-flex h-11 items-center rounded-2xl bg-sky-500 px-4 text-sm font-semibold text-white transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-60">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
</div>

<div id="display-delete-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="w-full max-w-lg rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="border-b border-slate-200 px-6 py-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rose-400">{{ __('Delete Display') }}</p>
            <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Delete this display?') }}</h3>
            <p class="mt-3 text-sm text-slate-500">
                {{ __('This action will permanently remove') }} <span id="display-delete-name" class="font-semibold text-slate-700"></span>.
            </p>
        </div>

        <div class="flex justify-end gap-3 px-6 py-5">
            <button id="display-delete-cancel" type="button" class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                {{ __('Cancel') }}
            </button>
            <button id="display-delete-confirm" type="button" class="inline-flex h-11 items-center rounded-2xl bg-rose-500 px-4 text-sm font-semibold text-white transition hover:bg-rose-400 disabled:cursor-not-allowed disabled:opacity-60">
                {{ __('Delete Display') }}
            </button>
        </div>
    </div>
</div>

<script id="display-filters-data" type="application/json">@json($filters)</script>
<script>
(function () {
    const text = @json($displayText);
    const canManageDisplays = @json($canManageDisplays);
    let initialized = false;
    const state = {
        config: { canChooseFacility: false, facilities: [], workgroupsByFacility: {}, workstationsByWorkgroup: {}, selectedFacilityId: '', selectedWorkgroupId: '', selectedWorkstationId: '' },
        selectedFacilityId: '',
        selectedWorkgroupId: '',
        selectedWorkstationId: '',
        defaultStatus: @json($initialDisplayStatus),
        selectedStatus: @json($initialDisplayStatus),
        sortKey: 'updated_at',
        sortOrder: 'desc',
        facilitySearch: '',
        workgroupSearch: '',
        workstationSearch: '',
        activeDropdown: null,
        actionTarget: null,
        deleteTarget: null,
        editTarget: null,
        grid: null,
        gridBatch: 0,
        gridLoadingHideTimer: null,
        gridLoadingStartedAt: 0,
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
        els.gridShell = document.getElementById('displays-grid-shell');
        els.gridLoading = document.getElementById('displays-grid-loading');
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

        els.facilityLabel.textContent = state.selectedFacilityId ? findOptionLabel(facilities, state.selectedFacilityId, text.selectFacility) : text.allFacilities;
        els.workgroupLabel.textContent = state.selectedWorkgroupId ? findOptionLabel(workgroups, state.selectedWorkgroupId, text.selectWorkgroup) : text.allWorkgroups;
        els.workstationLabel.textContent = state.selectedWorkstationId ? findOptionLabel(workstations, state.selectedWorkstationId, text.selectWorkstation) : text.allWorkstations;

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
        if (state.config.canChooseFacility) options = [{ id: '', name: text.allFacilities }, ...options];
        els.facilityHint.textContent = options.length ? `${options.length} ${options.length === 1 ? text.option : text.options}` : text.noOptionsFound;
        els.facilityOptions.innerHTML = options.length ? options.map((item) => `
            <button type="button" data-id="${String(item.id)}" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm ${String(item.id) === String(state.selectedFacilityId) ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-sky-50 hover:text-sky-700'}">
                ${Perfectlum.escapeHtml(item.name)}
            </button>`).join('') : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">${Perfectlum.escapeHtml(text.noOptionsFound)}</div>`;

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

    function primeGridLoading() {
        if (!els.gridShell) return;
        window.clearTimeout(state.gridLoadingHideTimer);
        state.gridLoadingStartedAt = performance.now();
        els.gridShell.classList.add('is-loading');
        els.gridShell.setAttribute('aria-busy', 'true');
    }

    function clearGridLoading(batch) {
        if (batch !== state.gridBatch || !els.gridShell) {
            return;
        }

        const elapsed = performance.now() - (state.gridLoadingStartedAt || 0);
        const minVisible = batch === 1 ? 220 : 140;
        const delay = Math.max(0, minVisible - elapsed);

        window.clearTimeout(state.gridLoadingHideTimer);
        state.gridLoadingHideTimer = window.setTimeout(() => {
            if (batch !== state.gridBatch || !els.gridShell) {
                return;
            }

            els.gridShell.classList.remove('is-loading');
            els.gridShell.setAttribute('aria-busy', 'false');
        }, delay);
    }

    function renderWorkgroupOptions() {
        const workgroups = getWorkgroupOptions();
        const query = state.workgroupSearch.trim().toLowerCase();
        const options = [{ id: '', name: text.allWorkgroups }, ...workgroups.filter((item) => item.name.toLowerCase().includes(query))];
        els.workgroupHint.textContent = options.length ? `${options.length} ${options.length === 1 ? text.option : text.options}` : text.noOptionsFound;
        els.workgroupOptions.innerHTML = options.length ? options.map((item) => `
            <button type="button" data-id="${String(item.id)}" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm ${String(item.id) === String(state.selectedWorkgroupId) ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-sky-50 hover:text-sky-700'}">
                ${Perfectlum.escapeHtml(item.name)}
            </button>`).join('') : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">${Perfectlum.escapeHtml(text.noOptionsFound)}</div>`;

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
        const options = [{ id: '', name: text.allWorkstations }, ...workstations.filter((item) => item.name.toLowerCase().includes(query))];
        els.workstationHint.textContent = options.length ? `${options.length} ${options.length === 1 ? text.option : text.options}` : text.noOptionsFound;
        els.workstationOptions.innerHTML = options.length ? options.map((item) => `
            <button type="button" data-id="${String(item.id)}" class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm ${String(item.id) === String(state.selectedWorkstationId) ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-sky-50 hover:text-sky-700'}">
                ${Perfectlum.escapeHtml(item.name)}
            </button>`).join('') : `<div class="rounded-xl bg-slate-50 px-3 py-3 text-sm text-slate-500">${Perfectlum.escapeHtml(text.noOptionsFound)}</div>`;

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
        const url = new URL('/api/displays', window.location.origin);
        const params = {
            type: state.selectedStatus || '',
            facility_id: state.selectedFacilityId || '',
            workgroup_id: state.selectedWorkgroupId || '',
            workstation_id: state.selectedWorkstationId || '',
            sort: state.sortKey || 'updated_at',
            order: state.sortOrder || 'desc',
            ...extra,
        };

        Object.entries(params).forEach(([key, value]) => {
            if (value !== undefined && value !== null && value !== '') {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        });

        return `${url.pathname}${url.search}`;
    }

    function resolveSortState(columns = []) {
        const active = Array.isArray(columns)
            ? columns.find((column) => column && typeof column.index === 'number' && [1, -1, 'asc', 'desc'].includes(column.direction))
            : null;
        if (!active) {
            return { key: 'updated_at', order: 'desc' };
        }

        const key = ({
            0: 'display_name',
            2: 'display_hours',
            3: 'updated_at',
            4: 'status',
        })[active.index] || 'updated_at';

        return {
            key,
            order: active.direction === -1 || active.direction === 'desc' ? 'desc' : 'asc',
        };
    }

    function extractErrorText(errors) {
        if (!Array.isArray(errors) || !errors.length) {
            return '';
        }

        const latest = errors[errors.length - 1];
        if (typeof latest === 'string') {
            return latest;
        }

        if (latest && typeof latest === 'object') {
            return latest.text || latest.error || latest.message || latest.name || '';
        }

        return '';
    }

    function renderHierarchyMetaButton(type, id, label) {
        if (!label || label === '-') {
            return '';
        }

        return `<button type="button" onclick="window.dispatchEvent(new CustomEvent('open-hierarchy',{detail:{type:'${type}',id:${Number(id) || 0}}}))" class="desktop-display-meta-button">${Perfectlum.escapeHtml(label)}</button>`;
    }

    function buildUniqueTextParts(parts = []) {
        const seen = new Set();

        return parts.filter((part) => {
            const value = String(part || '').trim();
            if (!value || value === '-') {
                return false;
            }

            const key = value.toLowerCase();
            if (seen.has(key)) {
                return false;
            }

            seen.add(key);
            return true;
        });
    }

    function renderDisplayOverviewCell(item) {
        const healthy = Number(item.status) === 1;
        const workstation = renderHierarchyMetaButton('workstation', item.wsId, item.wsName);
        const workgroup = renderHierarchyMetaButton('workgroup', item.wgId, item.wgName);
        const facility = renderHierarchyMetaButton('facility', item.facId, item.facName);
        const contextParts = [workstation, workgroup, facility].filter(Boolean);
        const detailParts = buildUniqueTextParts([
            item.location && String(item.location).trim().toLowerCase() !== String(item.facName || '').trim().toLowerCase() ? item.location : '',
        ]);

        return `
            <div class="desktop-display-summary">
                <div class="desktop-display-heading">
                    <span class="desktop-display-kicker-dot ${healthy ? 'healthy' : 'alert'}"></span>
                    <button type="button" onclick="window.openDisplayModal(${item.id})" class="desktop-display-title text-left">
                        ${Perfectlum.escapeHtml(item.displayName)}
                    </button>
                </div>
                ${contextParts.length ? `
                    <div class="desktop-display-meta">
                        ${contextParts.join('<span class="desktop-display-separator">•</span>')}
                    </div>
                ` : ''}
                ${detailParts.length ? `
                    <div class="desktop-display-detail ${healthy ? '' : 'problem'}">
                        ${Perfectlum.escapeHtml(detailParts.join(' • '))}
                    </div>
                ` : ''}
            </div>
        `;
    }

    function renderDisplayIssueCell(item) {
        const healthy = Number(item.status) === 1;
        const attentionText = String(item.attentionText || '').trim();
        const attentionMode = String(item.attentionMode || '').trim();
        const issueText = extractErrorText(item.errors);
        const failedCheckText = String(item.latestFailedCheckText || '').trim();
        const failedHistoryText = String(item.latestFailedHistoryName || '').trim();
        const connectionState = item.connected ? text.online : text.offline;
        const detailParts = buildUniqueTextParts([
            item.location && String(item.location).trim().toLowerCase() !== String(item.facName || '').trim().toLowerCase() ? item.location : '',
            connectionState,
        ]);

        if (!healthy && attentionMode === 'placeholder') {
            return `
                <div class="desktop-display-issue">
                    <div class="desktop-display-issue-text placeholder">
                        ${Perfectlum.escapeHtml(attentionText || text.noAlertDetail)}
                    </div>
                </div>
            `;
        }

        if (!healthy && !issueText && !failedCheckText && !failedHistoryText) {
            return `
                <div class="desktop-display-issue">
                    <div class="desktop-display-issue-text placeholder">
                        ${Perfectlum.escapeHtml(text.noAlertDetail)}
                    </div>
                </div>
            `;
        }

        const primary = healthy
            ? text.noActiveIssue
            : (attentionText || issueText || failedCheckText || failedHistoryText);
        const kicker = healthy
            ? text.liveState
            : ({
                live: text.latestIssue,
                failed_check: text.latestFailedCheck,
                failed_history: text.latestFailedRun,
            }[attentionMode] || (issueText
                ? text.latestIssue
                : (failedCheckText ? text.latestFailedCheck : text.latestFailedRun)));
        const note = detailParts.join(' • ');

        return `
            <div class="desktop-display-issue">
                <div class="desktop-display-issue-kicker ${healthy ? 'healthy' : 'alert'}">
                    ${Perfectlum.escapeHtml(kicker)}
                </div>
                <div class="desktop-display-issue-text ${healthy ? 'muted' : ''}">
                    ${Perfectlum.escapeHtml(primary)}
                </div>
                ${note ? `<div class="desktop-display-issue-note">${Perfectlum.escapeHtml(note)}</div>` : ''}
            </div>
        `;
    }

    function renderDisplayHoursCell(item) {
        const hasHours = item.latestHoursDuration !== null && item.latestHoursDuration !== undefined && item.latestHoursDuration !== '';
        const primary = hasHours ? (item.latestHoursFormatted || '-') : text.noHoursData;
        const runtimeSyncedAt = item.latestHoursSyncedAt || item.latestHoursAt;
        const note = runtimeSyncedAt && runtimeSyncedAt !== '-'
            ? `${text.hoursSync}: ${runtimeSyncedAt}`
            : text.noHoursSync;

        return `
            <div class="desktop-display-hours">
                <div class="desktop-display-hours-kicker">${Perfectlum.escapeHtml(text.runningHours)}</div>
                <div class="desktop-display-hours-text ${hasHours ? '' : 'empty'}">${Perfectlum.escapeHtml(primary)}</div>
                <div class="desktop-display-hours-note">${Perfectlum.escapeHtml(note)}</div>
            </div>
        `;
    }

    function renderUpdatedCell(value) {
        const payload = value && typeof value === 'object'
            ? value
            : { mode: 'activity', updatedAt: value };

        if (payload.mode === 'none') {
            return `
                <div class="desktop-display-updated-block">
                    <span class="desktop-display-updated-date empty">${Perfectlum.escapeHtml(text.noRecordedActivity)}</span>
                </div>
            `;
        }

        const raw = String(payload.mode === 'created' ? (payload.createdAt || payload.updatedAt || '') : (payload.updatedAt || '')).trim();
        const normalized = raw || '-';
        const match = normalized.match(/^(.*)\s(\d{2}:\d{2})$/);
        const date = match ? match[1] : normalized;
        const time = match ? match[2] : '';

        const label = payload.mode === 'created'
            ? `<span class="desktop-display-updated-label">${Perfectlum.escapeHtml(text.added)}</span>`
            : '';

        return `
            <div class="desktop-display-updated-block">
                ${label}
                <span class="desktop-display-updated-date">${Perfectlum.escapeHtml(date || '-')}</span>
                ${time ? `<span class="desktop-display-updated-time">${Perfectlum.escapeHtml(time)}</span>` : ''}
            </div>
        `;
    }

    function renderStatusCell(value) {
        const healthy = Number(value) === 1;

        return `
            <div class="desktop-display-status-cell">
                <span class="desktop-display-status-pill ${healthy ? 'success' : 'danger'}">
                    ${Perfectlum.escapeHtml(healthy ? 'OK' : 'Failed')}
                </span>
            </div>
        `;
    }

    function mapRows(d) {
        return d.data.map(r => [
            {
                id: r.id,
                displayName: r.displayName,
                wsName: r.wsName,
                wsId: r.wsId,
                wgName: r.wgName,
                wgId: r.wgId,
                facName: r.facName,
                facId: r.facId,
                status: r.status,
                connected: r.connected,
                location: r.location,
                updatedAt: r.updatedAt,
                errors: r.errors,
            },
            {
                facName: r.facName,
                status: r.status,
                connected: r.connected,
                location: r.location,
                errors: r.errors,
                attentionText: r.attentionText,
                attentionMode: r.attentionMode,
                latestFailedCheckText: r.latestFailedCheckText,
                latestFailedHistoryName: r.latestFailedHistoryName,
            },
            {
                latestHoursDuration: r.latestHoursDuration,
                latestHoursFormatted: r.latestHoursFormatted,
                latestHoursAt: r.latestHoursAt,
                latestHoursSyncedAt: r.latestHoursSyncedAt,
            },
            {
                updatedAt: r.updatedAt,
                createdAt: r.createdAt,
                mode: r.latestActivityMode || r.latestActivitySource || 'activity',
            },
            r.status,
            { id: r.id, name: r.displayName },
        ]);
    }

    function initGrid() {
        if (!els.grid || state.grid) return;

        const gridBatch = ++state.gridBatch;
        primeGridLoading();

        state.grid = Perfectlum.createGrid(els.grid, {
            columns: [
                {
                    name: text.display,
                    sort: {},
                    formatter: (cell) => gridjs.html(renderDisplayOverviewCell(cell)),
                },
                {
                    name: text.issue,
                    sort: false,
                    width: '240px',
                    formatter: (cell) => gridjs.html(renderDisplayIssueCell(cell)),
                },
                {
                    name: text.displayHours,
                    width: '152px',
                    sort: {},
                    formatter: (cell) => gridjs.html(renderDisplayHoursCell(cell)),
                },
                {
                    name: text.lastActivity,
                    width: '146px',
                    sort: {},
                    formatter: (cell) => gridjs.html(renderUpdatedCell(cell)),
                },
                {
                    name: text.status,
                    width: '106px',
                    sort: {},
                    formatter: (cell) => gridjs.html(renderStatusCell(cell)),
                },
                {
                    name: text.actions,
                    sort: false,
                    width: '96px',
                    formatter: (c) => !canManageDisplays ? '' : gridjs.html(`
                        <div class="desktop-display-actions-cell">
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
                data: async (opts) => {
                    try {
                        const payload = await Perfectlum.request(opts.url);
                        return {
                            data: mapRows(payload),
                            total: Number(payload?.total || 0),
                        };
                    } finally {
                        clearGridLoading(gridBatch);
                    }
                },
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
            sort: {
                multiColumn: false,
                server: {
                    url: (prevUrl, columns) => {
                        const nextSort = resolveSortState(columns);
                        state.sortKey = nextSort.key;
                        state.sortOrder = nextSort.order;

                        const prev = new URL(prevUrl, window.location.origin);
                        const search = prev.searchParams.get('search') || '';
                        const limit = prev.searchParams.get('limit') || '';

                        return buildGridUrl({
                            search,
                            limit,
                            page: 1,
                        });
                    },
                },
            },
            language: {
                search: { placeholder: text.searchDisplays },
                pagination: {
                    previous: text.previous,
                    next: text.next,
                    showing: text.showing,
                    results: () => text.results,
                },
                loading: text.loading,
                noRecordsFound: text.noMatchingRecordsFound,
                error: text.unableToLoadData,
            },
        });
    }

    function reloadGrid() {
        closeActionMenu();
        state.grid = null;
        if (!els.grid) {
            initGrid();
            return;
        }

        primeGridLoading();
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
        els.editSave.textContent = text.saveChanges;

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
            els.editError.textContent = error.message || text.unableToLoadDisplayForm;
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
        els.editSave.textContent = text.saveChanges;
    }

    async function submitEditForm(event) {
        event.preventDefault();
        if (!state.editTarget?.id || els.editSave.disabled) return;

        els.editSave.disabled = true;
        els.editSave.textContent = text.saving;
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
            if (!payload.success) throw new Error(payload.message || text.unableToUpdateDisplay);
            closeEditModal();
            reloadGrid();
        } catch (error) {
            els.editError.textContent = error.message || text.unableToUpdateDisplay;
            els.editError.classList.remove('hidden');
            els.editSave.disabled = false;
            els.editSave.textContent = text.saveChanges;
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
        els.deleteConfirm.textContent = text.deleteDisplay;
    }

    async function confirmDelete() {
        if (!state.deleteTarget?.id || els.deleteConfirm.disabled) return;
        els.deleteConfirm.disabled = true;
        els.deleteConfirm.textContent = text.saving;
        try {
            const formData = new FormData();
            formData.append('_token', csrfToken());
            formData.append('id', state.deleteTarget.id);
            const payload = await Perfectlum.postForm('/delete-display', formData);
            if (!payload.success) throw new Error(payload.msg || text.unableToDeleteDisplay);
            closeDeleteModal();
            reloadGrid();
        } catch (error) {
            window.alert(error.message || text.unableToDeleteDisplay);
            els.deleteConfirm.disabled = false;
            els.deleteConfirm.textContent = text.deleteDisplay;
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
