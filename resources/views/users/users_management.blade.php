@php
    $userText = [
        'allFacilities' => __('All facilities'),
        'searchFacilities' => __('Search facilities...'),
        'resetFilters' => __('Reset Filters'),
        'editUser' => __('Edit User'),
        'deleteUser' => __('Delete User'),
        'updateUserDetails' => __('Update user details'),
        'adjustUserDetails' => __('Adjust account, role, and facility assignment without leaving the table.'),
        'loadingUserForm' => __('Loading user form...'),
        'username' => __('Username'),
        'fullName' => __('Full Name'),
        'email' => __('Email'),
        'userLevel' => __('User Level'),
        'password' => __('Password'),
        'confirmPassword' => __('Confirm Password'),
        'facility' => __('Facility'),
        'enableUserAccount' => __('Enable user account'),
        'cancel' => __('Cancel'),
        'saveChanges' => __('Save Changes'),
        'deleteThisUser' => __('Delete this user?'),
        'unableToLoadUserForm' => __('Unable to load user form.'),
        'createNewUser' => __('Create a new user'),
        'createUserSubtitle' => __('Create a new account and assign its facility scope and role.'),
        'selectUserLevel' => __('Select user level'),
        'selectFacility' => __('Select facility'),
        'searchUsers' => __('Search users...'),
        'active' => __('Active'),
        'disabled' => __('Disabled'),
        'saving' => __('Saving...'),
        'deleting' => __('Deleting...'),
        'unableToSaveUser' => __('Unable to save user.'),
        'unableToDeleteUser' => __('Unable to delete user.'),
        'userDetails' => __('User Details'),
        'reviewUserProfile' => __('Review account profile, access role, and facility scope.'),
        'accountStatus' => __('Account Status'),
        'statusLabel' => __('Status'),
        'scopeLabel' => __('Scope'),
        'roleLabel' => __('Role'),
        'timezone' => __('Timezone'),
        'lastPasswordChanged' => __('Last Password Change'),
        'defaultWorkgroup' => __('Default Workgroup'),
        'accessFootprint' => __('Access Footprint'),
        'scopeAccess' => __('Scope Access'),
        'facilitiesCount' => __('Facilities'),
        'workgroupsCount' => __('Workgroups'),
        'workstationsCount' => __('Workstations'),
        'displaysCount' => __('Displays'),
        'relatedFacilities' => __('Facility Access'),
        'relatedWorkgroups' => __('Related Workgroups'),
        'relatedWorkstations' => __('Related Workstations'),
        'relatedDisplays' => __('Related Displays'),
        'globalAccess' => __('Global access'),
        'facilityBoundAccess' => __('Facility-bound access'),
        'noAssignedScope' => __('No assigned scope'),
        'loadingUser' => __('Loading user...'),
        'notAvailable' => __('Not available'),
        'roleSummarySuper' => __('Full platform administration'),
        'roleSummaryAdmin' => __('Facility-level administration'),
        'roleSummaryDefault' => __('Scoped operational access'),
        'optionCount' => __('options'),
        'noOptionsFound' => __('No options found'),
    ];
@endphp
@include('common.navigations.header')

<style>
    .users-directory-shell {
        border-radius: 2rem;
        border: 1px solid #d5e0ec;
        background: linear-gradient(180deg, #f7fbff 0%, #ffffff 100%);
        box-shadow: 0 26px 64px -46px rgba(15, 23, 42, 0.34);
    }
    .users-create-shell {
        border-radius: 1.5rem;
        border: 1px solid #dce8f4;
        background: linear-gradient(180deg, #f9fcff 0%, #ffffff 100%);
        box-shadow: 0 14px 38px -28px rgba(15, 23, 42, 0.22);
    }
    .users-jobs-shell {
        border-radius: 2rem;
        border: 1px solid #d5e0ec;
        background: linear-gradient(180deg, #f7fbff 0%, #ffffff 100%);
        box-shadow: 0 26px 64px -46px rgba(15, 23, 42, 0.34);
        overflow: hidden;
        padding: 14px 18px 16px;
    }
    .users-table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 2px 2px 0;
    }
    .users-table-search {
        width: min(320px, 100%);
        height: 42px;
        border-radius: 999px;
        border: 1px solid #c9d8e8;
        padding: 0 16px 0 40px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        background: #fff;
    }
    .users-table-search:focus {
        outline: none;
        border-color: #1d9bf0;
        box-shadow: 0 0 0 3px rgba(29, 155, 240, 0.16);
    }
    .users-table-wrap {
        overflow-x: auto;
        overflow-y: hidden;
        border-radius: 1.5rem;
        border: 1px solid #dce8f4;
        background: #fff;
        box-shadow: 0 16px 44px rgba(15,23,42,0.06);
    }
    .users-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 1080px;
        table-layout: fixed;
    }
    .users-table th {
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
    .users-table td {
        padding: 11px 16px;
        border-bottom: 1px solid #edf2f8;
        font-size: 14px;
        color: #334155;
        vertical-align: middle;
        background: #fff;
    }
    .users-table tbody tr:hover td {
        background: #f7fbff;
    }
    .users-table tbody tr[data-user-row] {
        cursor: pointer;
    }
    .users-table th:nth-child(1), .users-table td:nth-child(1) { width: 15%; }
    .users-table th:nth-child(2), .users-table td:nth-child(2) { width: 17%; }
    .users-table th:nth-child(3), .users-table td:nth-child(3) { width: 24%; }
    .users-table th:nth-child(4), .users-table td:nth-child(4) { width: 16%; }
    .users-table th:nth-child(5), .users-table td:nth-child(5) { width: 10%; }
    .users-table th:nth-child(6), .users-table td:nth-child(6) { width: 8%; text-align: center; }
    .users-table th:nth-child(7), .users-table td:nth-child(7) { width: 10%; text-align: center; }
    .users-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 16px 14px;
        border-top: 1px solid #dbe7f3;
        background: #f7fbff;
    }
    .users-pager {
        display: inline-flex;
        gap: 8px;
        align-items: center;
    }
    .users-page-btn {
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
    .users-page-btn:hover:not(:disabled) {
        border-color: #1d9bf0;
        color: #0f5f9f;
        background: #f0f8ff;
    }
    .users-page-btn:disabled {
        opacity: .45;
        cursor: not-allowed;
    }
    .users-empty {
        padding: 24px 16px;
        text-align: center;
        color: #5f7388;
        font-size: 14px;
        border-bottom: 1px solid #edf2f8;
    }
    .user-view-shell {
        width: min(100%, 1120px);
        height: min(920px, calc(100vh - 2rem));
        max-height: calc(100vh - 2rem);
        border-radius: 2rem;
        border: 1px solid #dce8f4;
        background:
            radial-gradient(circle at top right, rgba(56, 189, 248, 0.12), transparent 26%),
            linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        box-shadow: 0 36px 90px rgba(15, 23, 42, 0.22);
        overflow: hidden;
    }
    .user-view-hero {
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 16px;
        align-items: start;
        padding: 22px 24px;
        border-bottom: 1px solid #dce8f4;
        background:
            radial-gradient(circle at top left, rgba(14, 165, 233, 0.18), transparent 28%),
            linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }
    .user-view-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 999px;
        background: linear-gradient(135deg, #0ea5e9, #2563eb);
        color: #fff;
        font-size: 22px;
        font-weight: 800;
        letter-spacing: .04em;
        box-shadow: 0 16px 34px -20px rgba(37, 99, 235, 0.7);
    }
    .user-view-kicker {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .26em;
        color: #0ea5e9;
    }
    .user-view-title {
        margin-top: 8px;
        font-size: clamp(1.7rem, 2.7vw, 2.4rem);
        line-height: 1.05;
        font-weight: 800;
        color: #0f172a;
    }
    .user-view-handle {
        margin-top: 6px;
        font-size: 14px;
        font-weight: 700;
        color: #64748b;
    }
    .user-view-copy {
        margin-top: 8px;
        max-width: 54ch;
        font-size: 14px;
        color: #64748b;
    }
    .user-view-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 16px;
    }
    .user-view-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 34px;
        padding: 0 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        border: 1px solid transparent;
    }
    .user-view-badge.role {
        background: #e0f2fe;
        border-color: #bae6fd;
        color: #0369a1;
    }
    .user-view-badge.active {
        background: #dcfce7;
        border-color: #bbf7d0;
        color: #15803d;
    }
    .user-view-badge.disabled {
        background: #fee2e2;
        border-color: #fecaca;
        color: #dc2626;
    }
    .user-view-section-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(280px, .8fr);
        gap: 16px;
    }
    .user-view-block {
        border-radius: 1.6rem;
        border: 1px solid #dce8f4;
        background: rgba(255,255,255,0.9);
        box-shadow: 0 14px 36px rgba(15,23,42,0.06);
        padding: 18px;
    }
    .user-view-block-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .22em;
        color: #94a3b8;
    }
    .user-view-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 14px;
    }
    .user-view-detail-grid-single {
        grid-template-columns: 1fr;
    }
    .user-view-detail-card {
        border-radius: 1.2rem;
        border: 1px solid #dce8f4;
        background: #f8fbff;
        padding: 14px 16px;
    }
    .user-view-detail-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .18em;
        color: #94a3b8;
    }
    .user-view-detail-value {
        margin-top: 8px;
        font-size: 16px;
        line-height: 1.45;
        font-weight: 700;
        color: #1e293b;
        word-break: break-word;
    }
    .user-view-detail-value.subtle {
        font-weight: 600;
        color: #475569;
    }
    .user-view-scope-note {
        margin-top: 10px;
        font-size: 13px;
        color: #64748b;
    }
    .user-view-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-top: 16px;
    }
    .user-view-metric {
        border-radius: 1.3rem;
        border: 1px solid #dce8f4;
        background: #ffffff;
        padding: 16px;
    }
    .user-view-metric-label {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .18em;
        color: #94a3b8;
    }
    .user-view-metric-value {
        margin-top: 10px;
        font-size: 30px;
        line-height: 1;
        font-weight: 800;
        color: #0f172a;
    }
    .user-view-metric-copy {
        margin-top: 8px;
        font-size: 12px;
        color: #64748b;
    }
    .user-view-preview-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-top: 16px;
    }
    .user-view-preview-block {
        border-radius: 1.4rem;
        border: 1px solid #dce8f4;
        background: #ffffff;
        padding: 16px;
    }
    .user-view-preview-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 14px;
    }
    .user-view-preview-item {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        padding: 10px 12px;
        border-radius: 1rem;
        background: #f8fbff;
        border: 1px solid #e3edf7;
    }
    .user-view-preview-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 32px;
        width: 32px;
        height: 32px;
        border-radius: 999px;
        background: #0ea5e9;
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .06em;
    }
    .user-view-preview-item span:last-child {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }
    .user-view-empty {
        margin-top: 14px;
        border-radius: 1rem;
        border: 1px dashed #dce8f4;
        padding: 16px 14px;
        text-align: center;
        font-size: 13px;
        color: #94a3b8;
        background: #f8fbff;
    }
    .user-view-body {
        flex: 1 1 auto;
        overflow-y: auto;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
        padding: 20px 24px;
    }
    @media (max-width: 768px) {
        .users-table-toolbar {
            flex-direction: column;
            align-items: stretch;
        }
        .users-table-search {
            width: 100%;
        }
        .users-table-footer {
            flex-direction: column;
            align-items: stretch;
        }
        .users-pager {
            justify-content: flex-end;
        }
        .user-view-shell {
            height: min(96vh, calc(100vh - 1rem));
            max-height: calc(100vh - 1rem);
            border-radius: 1.5rem;
        }
        .user-view-hero {
            grid-template-columns: 1fr;
            padding: 18px 18px 16px;
        }
        .user-view-section-grid,
        .user-view-detail-grid,
        .user-view-metrics,
        .user-view-preview-grid {
            grid-template-columns: 1fr;
        }
        .user-view-body {
            padding: 16px;
        }
    }
</style>

<div class="flex flex-col gap-6 pb-8">
    <x-page-header title="{{ __('User Management') }}" description="{{ __('Manage user accounts, facility scope, and role assignments.') }}" icon="users">
        <x-slot name="actions">
            <button
                id="create-user-button"
                type="button"
                class="inline-flex h-12 items-center gap-2 rounded-2xl bg-sky-500 px-4 text-sm font-semibold text-white transition hover:bg-sky-400">
                <i data-lucide="user-plus" class="h-4 w-4"></i>
                {{ __('Add User') }}
            </button>
        </x-slot>
    </x-page-header>

    <section class="users-directory-shell p-6">
    <div class="users-create-shell mb-6 p-6">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,240px)_1fr]">
            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Facility') }}</label>
                <div class="relative">
                    <button
                        id="facility-filter-trigger"
                        type="button"
                        class="flex h-12 w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 text-left text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 disabled:cursor-not-allowed disabled:bg-slate-50 disabled:text-slate-400">
                        <span id="facility-filter-label" class="truncate">{{ __('All facilities') }}</span>
                        <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
                    </button>

                    <div
                        id="facility-filter-panel"
                        class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                        <input
                            id="facility-filter-search"
                            type="text"
                            placeholder="{{ __('Search facilities...') }}"
                            class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                        <p id="facility-filter-hint" class="mb-2 text-[11px] font-medium text-slate-400"></p>
                        <div id="facility-filter-options" class="max-h-56 space-y-1 overflow-y-auto"></div>
                    </div>
                </div>
            </div>

            <div class="flex items-end justify-end">
                <button
                    id="reset-user-filters"
                    type="button"
                    class="inline-flex h-12 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">
                    <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                    {{ __('Reset Filters') }}
                </button>
            </div>
        </div>
    </div>

    <div class="users-jobs-shell">
        <div class="users-table-toolbar mb-4">
            <div class="space-y-2">
                <p class="text-[11px] font-black uppercase tracking-[0.28em] text-slate-400">{{ __('Users Directory') }}</p>
                <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ __('Users and Roles') }}</h2>
                <p class="mt-1 max-w-3xl text-sm text-slate-500">{{ __('Manage account profile, facility scope, role assignment, and active status.') }}</p>
            </div>
            <div class="w-full max-w-[320px] space-y-2">
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input id="users-table-search" type="text" class="users-table-search transition-all placeholder-gray-400" placeholder="{{ __('Search users...') }}">
                </div>
                <div id="users-table-meta" class="text-right text-[12px] font-semibold text-slate-500"></div>
            </div>
        </div>

        <div class="users-table-wrap">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>{{ __('Username') }}</th>
                        <th>{{ __('Full Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Facility') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="users-table-body"></tbody>
            </table>
            <div class="users-table-footer">
                <div id="users-table-summary" class="text-[12px] font-semibold text-slate-500"></div>
                <div class="users-pager">
                    <button id="users-page-prev" type="button" class="users-page-btn">{{ __('Previous') }}</button>
                    <span id="users-page-label" class="text-[12px] font-semibold text-slate-500"></span>
                    <button id="users-page-next" type="button" class="users-page-btn">{{ __('Next') }}</button>
                </div>
            </div>
        </div>
    </div>
    </section>
</div>

<div id="user-action-overlay" class="pointer-events-none fixed inset-0 z-[1200] hidden">
    <div
        id="user-action-menu"
        class="pointer-events-auto fixed hidden w-52 rounded-2xl border border-slate-200 bg-white p-2 shadow-[0_20px_60px_-28px_rgba(15,23,42,0.35)]">
        <button id="user-action-edit" type="button" class="flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
            <i data-lucide="pencil-line" class="h-4 w-4"></i>
            {{ __('Edit User') }}
        </button>
        <button id="user-action-delete" type="button" class="flex w-full items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-left text-sm font-medium text-rose-600 transition hover:bg-rose-50">
            <i data-lucide="trash-2" class="h-4 w-4"></i>
            {{ __('Delete User') }}
        </button>
    </div>
</div>

<div id="user-view-modal" class="fixed inset-0 hidden" style="z-index:4000;">
    <div data-user-view-overlay class="absolute inset-0 bg-slate-950/45 backdrop-blur-[2px] opacity-0 transition-opacity duration-200"></div>
    <div data-user-view-stage class="absolute inset-0 overflow-hidden px-4 py-4 md:px-6 md:py-6">
        <div class="flex h-full items-center justify-center">
        <div data-user-view-panel class="user-view-shell relative flex translate-y-4 scale-[0.985] flex-col opacity-0 transition-all duration-200">
            <div class="user-view-hero">
                <div id="user-view-avatar" class="user-view-avatar">U</div>
                <div class="min-w-0">
                    <p class="user-view-kicker">{{ __('User Details') }}</p>
                    <h2 id="user-view-title" class="user-view-title truncate">{{ __('Loading user...') }}</h2>
                    <p id="user-view-handle" class="user-view-handle">@username</p>
                    <p id="user-view-subtitle" class="user-view-copy">{{ __('Review account profile, access role, and facility scope.') }}</p>
                    <div class="user-view-badges">
                        <span id="user-view-role-badge" class="user-view-badge role">{{ __('Role') }}</span>
                        <span id="user-view-status-badge" class="user-view-badge active">{{ __('Active') }}</span>
                    </div>
                </div>
                <button type="button" data-user-view-close class="inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-slate-200 bg-white/90 text-slate-400 transition hover:border-slate-300 hover:text-slate-700">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <div data-user-view-body class="user-view-body">
                <div class="user-view-section-grid">
                    <section class="user-view-block">
                        <p class="user-view-block-title">{{ __('Profile Summary') }}</p>
                        <div class="user-view-detail-grid">
                            <div class="user-view-detail-card">
                                <p class="user-view-detail-label">{{ __('Username') }}</p>
                                <p id="user-view-username" class="user-view-detail-value">-</p>
                            </div>
                            <div class="user-view-detail-card">
                                <p class="user-view-detail-label">{{ __('Full Name') }}</p>
                                <p id="user-view-fullname" class="user-view-detail-value">-</p>
                            </div>
                            <div class="user-view-detail-card">
                                <p class="user-view-detail-label">{{ __('Email') }}</p>
                                <p id="user-view-email" class="user-view-detail-value subtle">-</p>
                            </div>
                            <div class="user-view-detail-card">
                                <p class="user-view-detail-label">{{ __('Timezone') }}</p>
                                <p id="user-view-timezone" class="user-view-detail-value">-</p>
                            </div>
                            <div class="user-view-detail-card">
                                <p class="user-view-detail-label">{{ __('Last Password Change') }}</p>
                                <p id="user-view-password-changed" class="user-view-detail-value subtle">-</p>
                            </div>
                            <div class="user-view-detail-card">
                                <p class="user-view-detail-label">{{ __('Account Status') }}</p>
                                <p id="user-view-status" class="user-view-detail-value">-</p>
                            </div>
                        </div>
                    </section>
                    <section class="user-view-block">
                        <p class="user-view-block-title">{{ __('Access & Scope') }}</p>
                        <div class="user-view-detail-grid user-view-detail-grid-single">
                            <div class="user-view-detail-card">
                                <p class="user-view-detail-label">{{ __('Role') }}</p>
                                <p id="user-view-role" class="user-view-detail-value">-</p>
                            </div>
                            <div class="user-view-detail-card">
                                <p class="user-view-detail-label">{{ __('Facility') }}</p>
                                <p id="user-view-facility" class="user-view-detail-value">-</p>
                            </div>
                            <div class="user-view-detail-card">
                                <p class="user-view-detail-label">{{ __('Default Workgroup') }}</p>
                                <p id="user-view-default-workgroup" class="user-view-detail-value subtle">-</p>
                            </div>
                        </div>
                        <p id="user-view-scope-note" class="user-view-scope-note">{{ __('Review account profile, access role, and facility scope.') }}</p>
                    </section>
                </div>

                <section class="user-view-block mt-4">
                    <p class="user-view-block-title">{{ __('Access Footprint') }}</p>
                    <div class="user-view-metrics">
                        <div class="user-view-metric">
                            <p class="user-view-metric-label">{{ __('Facilities') }}</p>
                            <p id="user-view-count-facilities" class="user-view-metric-value">0</p>
                            <p class="user-view-metric-copy">{{ __('Accessible in current scope') }}</p>
                        </div>
                        <div class="user-view-metric">
                            <p class="user-view-metric-label">{{ __('Workgroups') }}</p>
                            <p id="user-view-count-workgroups" class="user-view-metric-value">0</p>
                            <p class="user-view-metric-copy">{{ __('Accessible in current scope') }}</p>
                        </div>
                        <div class="user-view-metric">
                            <p class="user-view-metric-label">{{ __('Workstations') }}</p>
                            <p id="user-view-count-workstations" class="user-view-metric-value">0</p>
                            <p class="user-view-metric-copy">{{ __('Accessible in current scope') }}</p>
                        </div>
                        <div class="user-view-metric">
                            <p class="user-view-metric-label">{{ __('Displays') }}</p>
                            <p id="user-view-count-displays" class="user-view-metric-value">0</p>
                            <p class="user-view-metric-copy">{{ __('Accessible in current scope') }}</p>
                        </div>
                    </div>
                </section>

                <section class="user-view-preview-grid">
                    <div class="user-view-preview-block">
                        <p class="user-view-block-title">{{ __('Related Workgroups') }}</p>
                        <div id="user-view-list-workgroups" class="user-view-preview-list"></div>
                    </div>
                    <div class="user-view-preview-block">
                        <p class="user-view-block-title">{{ __('Related Workstations') }}</p>
                        <div id="user-view-list-workstations" class="user-view-preview-list"></div>
                    </div>
                    <div class="user-view-preview-block">
                        <p class="user-view-block-title">{{ __('Related Displays') }}</p>
                        <div id="user-view-list-displays" class="user-view-preview-list"></div>
                    </div>
                </section>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-200 px-6 py-4">
                <button type="button" data-user-view-close class="inline-flex h-11 items-center rounded-2xl bg-sky-500 px-5 text-sm font-semibold text-white transition hover:bg-sky-600">
                    {{ __('Close') }}
                </button>
            </div>
        </div>
        </div>
    </div>
</div>

<div id="user-edit-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="flex max-h-[88vh] w-full max-w-3xl flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
            <div>
                <p id="user-edit-kicker" class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Edit User') }}</p>
                <h3 id="user-edit-title" class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Update user details') }}</h3>
                <p id="user-edit-subtitle" class="mt-2 text-sm text-slate-500">{{ __('Adjust account, role, and facility assignment without leaving the table.') }}</p>
            </div>
            <button id="user-edit-close" type="button" class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6">
            <div id="user-edit-loading" class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-sm text-slate-500">{{ __('Loading user form...') }}</div>
            <div id="user-edit-error" class="hidden rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700"></div>
            <form id="user-edit-form" class="hidden space-y-5">
                <div class="grid gap-5 md:grid-cols-2">
                    <label class="space-y-2"><span class="text-sm font-semibold text-slate-700">{{ __('Username') }}</span><input id="user-name" name="name" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></label>
                    <label class="space-y-2"><span class="text-sm font-semibold text-slate-700">{{ __('Full Name') }}</span><input id="user-fullname" name="fullname" type="text" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></label>
                    <label class="space-y-2"><span class="text-sm font-semibold text-slate-700">{{ __('Email') }}</span><input id="user-email" name="email" type="email" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></label>
                    <label class="space-y-2"><span class="text-sm font-semibold text-slate-700">{{ __('User Level') }}</span><select id="user-role" name="user_level" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></select></label>
                    <label class="space-y-2"><span class="text-sm font-semibold text-slate-700">{{ __('Password') }}</span><input id="user-password" name="password" type="password" autocomplete="new-password" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></label>
                    <label class="space-y-2"><span class="text-sm font-semibold text-slate-700">{{ __('Confirm Password') }}</span><input id="user-password-confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></label>
                    <label class="space-y-2 md:col-span-2"><span class="text-sm font-semibold text-slate-700">{{ __('Facility') }}</span><select id="user-facility" name="facility_id" class="h-12 w-full rounded-2xl border border-slate-200 px-4 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20"></select></label>
                </div>

                <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm font-semibold text-slate-700">
                    <input id="user-enabled" name="enabled" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500">
                    {{ __('Enable user account') }}
                </label>

                <input id="user-id" name="id" type="hidden" value="0">
            </form>
        </div>

        <div class="flex shrink-0 justify-end gap-3 border-t border-slate-200 px-6 py-5">
            <button id="user-edit-cancel" type="button" class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">{{ __('Cancel') }}</button>
            <button id="user-edit-save" type="button" class="inline-flex h-11 items-center gap-2 rounded-2xl bg-sky-500 px-4 text-sm font-semibold text-white transition hover:bg-sky-400 disabled:cursor-not-allowed disabled:opacity-60">
                <i data-lucide="save" class="h-4 w-4"></i>
                <span id="user-edit-save-label">{{ __('Save Changes') }}</span>
            </button>
        </div>
    </div>
</div>

<div id="user-delete-modal" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/40 p-6">
    <div class="w-full max-w-lg rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_-44px_rgba(15,23,42,0.55)]">
        <div class="border-b border-slate-200 px-6 py-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rose-400">{{ __('Delete User') }}</p>
            <h3 class="mt-2 text-2xl font-semibold text-slate-900">{{ __('Delete this user?') }}</h3>
            <p class="mt-3 text-sm text-slate-500">{{ __('This action will permanently remove') }} <span id="user-delete-name" class="font-semibold text-slate-700"></span>.</p>
        </div>

        <div class="flex justify-end gap-3 px-6 py-5">
            <button id="user-delete-cancel" type="button" class="inline-flex h-11 items-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">{{ __('Cancel') }}</button>
            <button id="user-delete-confirm" type="button" class="inline-flex h-11 items-center rounded-2xl bg-rose-500 px-4 text-sm font-semibold text-white transition hover:bg-rose-400 disabled:cursor-not-allowed disabled:opacity-60">{{ __('Delete User') }}</button>
        </div>
    </div>
</div>

<script id="users-filters-data" type="application/json">@json($filters)</script>
<script id="users-text" type="application/json">@json($userText)</script>
<script>
(function () {
    const text = JSON.parse(document.getElementById('users-text')?.textContent || '{}');
    let initialized = false;
    const state = {
        config: { canChooseFacility: false, facilities: [], selectedFacilityId: '' },
        selectedFacilityId: '',
        facilitySearch: '',
        activeDropdown: null,
        actionTarget: null,
        deleteTarget: null,
        view: { id: 0, requestToken: 0 },
        table: {
            page: 1,
            limit: 10,
            total: 0,
            rows: [],
            search: '',
            searchTimer: null,
            loading: false,
            fetching: false,
        },
        edit: { id: 0, loading: false, saving: false, payload: null },
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
            state.config = JSON.parse(document.getElementById('users-filters-data')?.textContent || '{}');
        } catch (error) {
            state.config = { canChooseFacility: false, facilities: [], selectedFacilityId: '' };
        }

        state.selectedFacilityId = state.config.selectedFacilityId || '';

        bindElements();
        bindEvents();
        renderFilters();
        initUsersTable();
        window.usersPage = { toggleActionMenu, openViewModalById, openEditModal, openDeleteModal };
        window.usersPageCleanup = cleanupUsersPage;
        window.lucide?.createIcons();
    }

    function bindElements() {
        els.createButton = document.getElementById('create-user-button');
        els.facilityTrigger = document.getElementById('facility-filter-trigger');
        els.facilityLabel = document.getElementById('facility-filter-label');
        els.facilityPanel = document.getElementById('facility-filter-panel');
        els.facilitySearch = document.getElementById('facility-filter-search');
        els.facilityHint = document.getElementById('facility-filter-hint');
        els.facilityOptions = document.getElementById('facility-filter-options');
        els.resetFilters = document.getElementById('reset-user-filters');
        els.tableSearch = document.getElementById('users-table-search');
        els.tableBody = document.getElementById('users-table-body');
        els.tableMeta = document.getElementById('users-table-meta');
        els.tableSummary = document.getElementById('users-table-summary');
        els.pageLabel = document.getElementById('users-page-label');
        els.pagePrev = document.getElementById('users-page-prev');
        els.pageNext = document.getElementById('users-page-next');

        els.actionOverlay = document.getElementById('user-action-overlay');
        els.actionMenu = document.getElementById('user-action-menu');
        els.actionEdit = document.getElementById('user-action-edit');
        els.actionDelete = document.getElementById('user-action-delete');

        els.viewModal = document.getElementById('user-view-modal');
        els.viewOverlay = els.viewModal?.querySelector('[data-user-view-overlay]') || null;
        els.viewStage = els.viewModal?.querySelector('[data-user-view-stage]') || null;
        els.viewPanel = els.viewModal?.querySelector('[data-user-view-panel]') || null;
        els.viewBody = els.viewModal?.querySelector('[data-user-view-body]') || null;
        els.viewAvatar = document.getElementById('user-view-avatar');
        els.viewTitle = document.getElementById('user-view-title');
        els.viewHandle = document.getElementById('user-view-handle');
        els.viewSubtitle = document.getElementById('user-view-subtitle');
        els.viewRoleBadge = document.getElementById('user-view-role-badge');
        els.viewStatusBadge = document.getElementById('user-view-status-badge');
        els.viewUsername = document.getElementById('user-view-username');
        els.viewFullname = document.getElementById('user-view-fullname');
        els.viewEmail = document.getElementById('user-view-email');
        els.viewTimezone = document.getElementById('user-view-timezone');
        els.viewPasswordChanged = document.getElementById('user-view-password-changed');
        els.viewRole = document.getElementById('user-view-role');
        els.viewFacility = document.getElementById('user-view-facility');
        els.viewDefaultWorkgroup = document.getElementById('user-view-default-workgroup');
        els.viewStatus = document.getElementById('user-view-status');
        els.viewScopeNote = document.getElementById('user-view-scope-note');
        els.viewCountFacilities = document.getElementById('user-view-count-facilities');
        els.viewCountWorkgroups = document.getElementById('user-view-count-workgroups');
        els.viewCountWorkstations = document.getElementById('user-view-count-workstations');
        els.viewCountDisplays = document.getElementById('user-view-count-displays');
        els.viewListWorkgroups = document.getElementById('user-view-list-workgroups');
        els.viewListWorkstations = document.getElementById('user-view-list-workstations');
        els.viewListDisplays = document.getElementById('user-view-list-displays');

        els.editModal = document.getElementById('user-edit-modal');
        els.editClose = document.getElementById('user-edit-close');
        els.editCancel = document.getElementById('user-edit-cancel');
        els.editSave = document.getElementById('user-edit-save');
        els.editSaveLabel = document.getElementById('user-edit-save-label');
        els.editLoading = document.getElementById('user-edit-loading');
        els.editError = document.getElementById('user-edit-error');
        els.editForm = document.getElementById('user-edit-form');
        els.editKicker = document.getElementById('user-edit-kicker');
        els.editTitle = document.getElementById('user-edit-title');
        els.editSubtitle = document.getElementById('user-edit-subtitle');
        els.userId = document.getElementById('user-id');
        els.username = document.getElementById('user-name');
        els.fullname = document.getElementById('user-fullname');
        els.email = document.getElementById('user-email');
        els.password = document.getElementById('user-password');
        els.passwordConfirmation = document.getElementById('user-password-confirmation');
        els.role = document.getElementById('user-role');
        els.facility = document.getElementById('user-facility');
        els.enabled = document.getElementById('user-enabled');

        els.deleteModal = document.getElementById('user-delete-modal');
        els.deleteName = document.getElementById('user-delete-name');
        els.deleteCancel = document.getElementById('user-delete-cancel');
        els.deleteConfirm = document.getElementById('user-delete-confirm');
    }

    function bindEvents() {
        els.createButton?.addEventListener('click', () => openEditModal(0));
        els.facilityTrigger?.addEventListener('click', () => toggleDropdown('facility'));
        els.facilitySearch?.addEventListener('input', (event) => {
            state.facilitySearch = event.target.value || '';
            renderFacilityOptions();
        });
        els.resetFilters?.addEventListener('click', resetFilters);
        els.tableSearch?.addEventListener('input', (event) => {
            clearTimeout(state.table.searchTimer);
            state.table.searchTimer = window.setTimeout(() => {
                state.table.search = String(event.target.value || '').trim();
                state.table.page = 1;
                loadUsersTable();
            }, 320);
        });
        els.pagePrev?.addEventListener('click', () => {
            if (state.table.page <= 1 || state.table.loading || state.table.fetching) return;
            state.table.page -= 1;
            loadUsersTable();
        });
        els.pageNext?.addEventListener('click', () => {
            const totalPages = Math.max(1, Math.ceil(state.table.total / state.table.limit));
            if (state.table.page >= totalPages || state.table.loading || state.table.fetching) return;
            state.table.page += 1;
            loadUsersTable();
        });
        els.tableBody?.addEventListener('click', (event) => {
            const button = event.target.closest('[data-user-action]');
            if (button) {
                toggleActionMenu(event, Number(button.dataset.userId || 0), button.dataset.userName || '');
                return;
            }

            const mailLink = event.target.closest('a[href^="mailto:"]');
            if (mailLink) {
                event.stopPropagation();
                return;
            }

            const row = event.target.closest('[data-user-row]');
            if (!row) return;
            openViewModalById(Number(row.dataset.userId || 0));
        });

        document.addEventListener('click', (event) => {
            if (state.activeDropdown === 'facility' && !els.facilityPanel.contains(event.target) && !els.facilityTrigger.contains(event.target)) {
                closeDropdown();
            }
            if (els.actionOverlay && !els.actionMenu.contains(event.target)) {
                closeActionMenu();
            }
        });

        els.actionOverlay?.addEventListener('click', closeActionMenu);
        els.actionEdit?.addEventListener('click', () => state.actionTarget && openEditModal(state.actionTarget.id));
        els.actionDelete?.addEventListener('click', () => state.actionTarget && openDeleteModal(state.actionTarget.id, state.actionTarget.name));

        els.viewModal?.querySelectorAll('[data-user-view-close]').forEach((button) => {
            button.addEventListener('click', closeViewModal);
        });
        els.viewOverlay?.addEventListener('click', closeViewModal);
        els.viewStage?.addEventListener('click', (event) => {
            if (event.target === els.viewStage) {
                closeViewModal();
            }
        });

        els.editClose?.addEventListener('click', closeEditModal);
        els.editCancel?.addEventListener('click', closeEditModal);
        els.editSave?.addEventListener('click', saveEditModal);
        els.editModal?.addEventListener('click', (event) => {
            if (event.target === els.editModal) closeEditModal();
        });

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

    function findOptionLabel(options, value, fallback) {
        const match = options.find((item) => String(item.id) === String(value));
        return match?.name || fallback;
    }

    function renderFilters() {
        const facilities = getFacilityOptions();
        els.facilityTrigger.disabled = !state.config.canChooseFacility && facilities.length <= 1;
        els.facilityLabel.textContent = state.selectedFacilityId
            ? findOptionLabel(facilities, state.selectedFacilityId, text.selectFacility)
            : text.allFacilities;
        renderFacilityOptions();
    }

    function renderFacilityOptions() {
        const facilities = getFacilityOptions();
        const query = state.facilitySearch.trim().toLowerCase();
        let options = facilities.filter((item) => item.name.toLowerCase().includes(query));
        if (state.config.canChooseFacility) {
            options = [{ id: '', name: 'All facilities' }, ...options];
        }

        els.facilityHint.textContent = options.length
            ? `${options.length} ${text.optionCount}`
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
                state.facilitySearch = '';
                if (els.facilitySearch) els.facilitySearch.value = '';
                closeDropdown();
                renderFilters();
                reloadGrid();
            });
        });
    }

    function toggleDropdown(type) {
        if (type === 'facility' && els.facilityTrigger.disabled) return;
        state.activeDropdown = state.activeDropdown === type ? null : type;
        els.facilityPanel.classList.toggle('hidden', state.activeDropdown !== 'facility');
        if (state.activeDropdown === 'facility') {
            els.facilitySearch?.focus();
        }
    }

    function closeDropdown() {
        state.activeDropdown = null;
        els.facilityPanel.classList.add('hidden');
    }

    function resetFilters() {
        state.selectedFacilityId = state.config.canChooseFacility ? '' : (getFacilityOptions()[0] ? String(getFacilityOptions()[0].id) : '');
        state.facilitySearch = '';
        if (els.facilitySearch) els.facilitySearch.value = '';
        closeDropdown();
        renderFilters();
        reloadGrid();
    }

    function usersApiUrl() {
        const url = new URL('/users-list', window.location.origin);
        if (state.selectedFacilityId) {
            url.searchParams.set('facility_id', state.selectedFacilityId);
        }
        url.searchParams.set('page', String(state.table.page));
        url.searchParams.set('limit', String(state.table.limit));
        if (state.table.search) {
            url.searchParams.set('search', state.table.search);
        }
        return `${url.pathname}${url.search}`;
    }

    function renderUsersRows() {
        if (!els.tableBody) return;
        if (state.table.loading) {
            els.tableBody.innerHTML = `<tr><td colspan="7" class="users-empty">Loading...</td></tr>`;
            return;
        }
        if (!state.table.rows.length) {
            els.tableBody.innerHTML = `<tr><td colspan="7" class="users-empty">No matching records found</td></tr>`;
            return;
        }

        els.tableBody.innerHTML = state.table.rows.map((row) => `
            <tr data-user-row="1" data-user-id="${Number(row.id) || 0}">
                <td><span class="font-semibold text-gray-800">${Perfectlum.escapeHtml(row.username || '-')}</span></td>
                <td><span class="text-gray-600">${Perfectlum.escapeHtml(row.fullname || '-')}</span></td>
                <td><a href="mailto:${Perfectlum.escapeHtml(row.email || '')}" class="text-sky-600 hover:underline">${Perfectlum.escapeHtml(row.email || '-')}</a></td>
                <td><span class="text-gray-600">${Perfectlum.escapeHtml(row.facility || '-')}</span></td>
                <td>${Perfectlum.badge(row.role || '-', 'info')}</td>
                <td>${row.enabled ? Perfectlum.badge(text.active, 'success') : Perfectlum.badge(text.disabled, 'danger')}</td>
                <td>
                    <div class="flex justify-center">
                        <button
                            type="button"
                            data-user-action="1"
                            data-user-id="${Number(row.id) || 0}"
                            data-user-name="${Perfectlum.escapeHtml(row.fullname || row.username || '')}"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="2"/><circle cx="12" cy="5" r="2"/><circle cx="12" cy="19" r="2"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function renderUsersPager() {
        const totalPages = Math.max(1, Math.ceil(state.table.total / state.table.limit));
        const currentPage = Math.min(state.table.page, totalPages);
        const from = state.table.total === 0 ? 0 : ((currentPage - 1) * state.table.limit) + 1;
        const to = Math.min(state.table.total, currentPage * state.table.limit);
        if (els.tableMeta) els.tableMeta.textContent = `${state.table.total} results`;
        if (els.tableSummary) els.tableSummary.textContent = `Showing ${from}-${to} of ${state.table.total} results`;
        if (els.pageLabel) els.pageLabel.textContent = `Page ${currentPage} / ${totalPages}`;
        if (els.pagePrev) els.pagePrev.disabled = state.table.loading || state.table.fetching || currentPage <= 1;
        if (els.pageNext) els.pageNext.disabled = state.table.loading || state.table.fetching || currentPage >= totalPages;
    }

    function findUserRow(id) {
        return state.table.rows.find((row) => Number(row.id || 0) === Number(id || 0)) || null;
    }

    function getUserInitials(row, payload = null) {
        const source = String(payload?.fullname || row?.fullname || payload?.username || row?.username || 'U').trim();
        const parts = source.split(/\s+/).filter(Boolean);
        if (parts.length === 1) {
            return parts[0].slice(0, 2).toUpperCase();
        }
        return parts.slice(0, 2).map((part) => part.charAt(0)).join('').toUpperCase();
    }

    function roleSummary(role) {
        const key = String(role || '').toLowerCase();
        if (key === 'super') return text.roleSummarySuper;
        if (key === 'admin') return text.roleSummaryAdmin;
        return text.roleSummaryDefault;
    }

    function setUserStatusBadge(statusText) {
        if (!els.viewStatusBadge) return;
        const normalized = String(statusText || '').toLowerCase();
        els.viewStatusBadge.textContent = statusText || text.notAvailable;
        els.viewStatusBadge.classList.remove('active', 'disabled');
        els.viewStatusBadge.classList.add(normalized === String(text.active).toLowerCase() ? 'active' : 'disabled');
    }

    function renderUserPreviewList(container, items, badge) {
        if (!container) return;
        const values = Array.isArray(items) ? items.filter(Boolean) : [];

        if (!values.length) {
            container.innerHTML = `<div class="user-view-empty">${Perfectlum.escapeHtml(text.notAvailable)}</div>`;
            return;
        }

        container.innerHTML = values.map((item) => `
            <div class="user-view-preview-item">
                <span class="user-view-preview-icon">${Perfectlum.escapeHtml(badge)}</span>
                <span>${Perfectlum.escapeHtml(item)}</span>
            </div>
        `).join('');
    }

    function ensureUserViewModalRoot() {
        if (!els.viewModal || !document.body || els.viewModal.parentElement === document.body) {
            return;
        }

        document.body.appendChild(els.viewModal);
    }

    async function openViewModalById(id) {
        const row = findUserRow(id);
        if (!row) return;
        openViewModal(row);

        const requestToken = Date.now();
        state.view.id = id;
        state.view.requestToken = requestToken;

        try {
            const payload = await Perfectlum.request(`/api/user-modal/${id}`);
            if (state.view.requestToken !== requestToken) {
                return;
            }
            hydrateUserViewModal(row, payload);
        } catch (error) {
            if (state.view.requestToken !== requestToken) {
                return;
            }
            els.viewScopeNote.textContent = error.message || text.reviewUserProfile;
        }
    }

    function openViewModal(row) {
        if (!els.viewModal) return;

        closeActionMenu();
        ensureUserViewModalRoot();

        const displayName = row.fullname || row.username || text.loadingUser;
        const facilityName = row.facility || '-';
        const statusText = row.enabled ? text.active : text.disabled;
        const roleText = row.role || text.notAvailable;

        els.viewAvatar.textContent = getUserInitials(row);
        els.viewTitle.textContent = displayName;
        els.viewHandle.textContent = `@${row.username || '-'}`;
        els.viewSubtitle.textContent = roleSummary(roleText);
        els.viewRoleBadge.textContent = roleText;
        setUserStatusBadge(statusText);
        els.viewUsername.textContent = row.username || '-';
        els.viewFullname.textContent = row.fullname || '-';
        els.viewEmail.textContent = row.email || '-';
        els.viewTimezone.textContent = text.notAvailable;
        els.viewPasswordChanged.textContent = text.notAvailable;
        els.viewRole.textContent = row.role || '-';
        els.viewFacility.textContent = facilityName;
        els.viewDefaultWorkgroup.textContent = text.notAvailable;
        els.viewStatus.textContent = statusText;
        els.viewScopeNote.textContent = text.reviewUserProfile;
        els.viewCountFacilities.textContent = '0';
        els.viewCountWorkgroups.textContent = '0';
        els.viewCountWorkstations.textContent = '0';
        els.viewCountDisplays.textContent = '0';
        renderUserPreviewList(els.viewListWorkgroups, [], 'WG');
        renderUserPreviewList(els.viewListWorkstations, [], 'WS');
        renderUserPreviewList(els.viewListDisplays, [], 'D');
        if (els.viewBody) els.viewBody.scrollTop = 0;

        els.viewModal.classList.remove('hidden');
        requestAnimationFrame(() => {
            els.viewOverlay?.classList.remove('opacity-0');
            els.viewPanel?.classList.remove('translate-y-4', 'scale-[0.985]', 'opacity-0');
        });
        window.lucide?.createIcons();
    }

    function hydrateUserViewModal(row, payload) {
        const view = payload?.view || {};
        const footprint = view.footprint || {};
        const previews = footprint.previews || {};
        const counts = footprint.counts || {};
        const roleText = payload?.user_level || row?.role || text.notAvailable;
        const statusText = payload?.enabled ? text.active : text.disabled;

        els.viewAvatar.textContent = getUserInitials(row, payload);
        els.viewTitle.textContent = payload?.fullname || payload?.username || row?.fullname || row?.username || text.notAvailable;
        els.viewHandle.textContent = `@${payload?.username || row?.username || '-'}`;
        els.viewSubtitle.textContent = roleSummary(roleText);
        els.viewRoleBadge.textContent = roleText;
        setUserStatusBadge(statusText);
        els.viewUsername.textContent = payload?.username || row?.username || '-';
        els.viewFullname.textContent = payload?.fullname || row?.fullname || '-';
        els.viewEmail.textContent = payload?.email || row?.email || '-';
        els.viewTimezone.textContent = view.timezone || text.notAvailable;
        els.viewPasswordChanged.textContent = view.lastPasswordChanged || text.notAvailable;
        els.viewRole.textContent = roleText;
        els.viewFacility.textContent = row?.facility || text.notAvailable;
        els.viewDefaultWorkgroup.textContent = view.defaultWorkgroup || text.notAvailable;
        els.viewStatus.textContent = statusText;
        els.viewScopeNote.textContent = footprint.scopeLabel || text.reviewUserProfile;
        els.viewCountFacilities.textContent = String(counts.facilities ?? 0);
        els.viewCountWorkgroups.textContent = String(counts.workgroups ?? 0);
        els.viewCountWorkstations.textContent = String(counts.workstations ?? 0);
        els.viewCountDisplays.textContent = String(counts.displays ?? 0);
        renderUserPreviewList(els.viewListWorkgroups, previews.workgroups, 'WG');
        renderUserPreviewList(els.viewListWorkstations, previews.workstations, 'WS');
        renderUserPreviewList(els.viewListDisplays, previews.displays, 'D');
    }

    function closeViewModal() {
        if (!els.viewModal || els.viewModal.classList.contains('hidden')) return;

        state.view.requestToken = 0;
        els.viewOverlay?.classList.add('opacity-0');
        els.viewPanel?.classList.add('translate-y-4', 'scale-[0.985]', 'opacity-0');

        window.setTimeout(() => {
            if (!els.viewModal) return;
            els.viewModal.classList.add('hidden');
        }, 200);
    }

    async function loadUsersTable() {
        if (state.table.fetching) return;
        state.table.fetching = true;
        state.table.loading = true;
        renderUsersPager();
        renderUsersRows();

        try {
            const payload = await Perfectlum.request(usersApiUrl());
            state.table.rows = Array.isArray(payload?.data) ? payload.data : [];
            state.table.total = Number(payload?.total || state.table.rows.length || 0);
            const totalPages = Math.max(1, Math.ceil(state.table.total / state.table.limit));
            if (state.table.page > totalPages) {
                state.table.page = totalPages;
                return loadUsersTable();
            }
        } catch (error) {
            state.table.rows = [];
            state.table.total = 0;
            if (els.tableBody) {
                els.tableBody.innerHTML = `<tr><td colspan="7" class="users-empty text-rose-600">${Perfectlum.escapeHtml(error.message || 'Unable to load data')}</td></tr>`;
            }
        } finally {
            state.table.fetching = false;
            state.table.loading = false;
            renderUsersRows();
            renderUsersPager();
            window.lucide?.createIcons();
        }
    }

    function initUsersTable() {
        loadUsersTable();
    }

    function reloadGrid() {
        closeActionMenu();
        state.table.page = 1;
        loadUsersTable();
    }

    function toggleActionMenu(event, id, name) {
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

    function fillSelect(select, options, currentValue, placeholder) {
        const html = [
            placeholder ? `<option value="">${Perfectlum.escapeHtml(placeholder)}</option>` : '',
            ...(options || []).map((item) => `<option value="${Perfectlum.escapeHtml(String(item.id))}" ${String(item.id) === String(currentValue ?? '') ? 'selected' : ''}>${Perfectlum.escapeHtml(item.name)}</option>`),
        ].join('');
        select.innerHTML = html;
    }

    async function openEditModal(id) {
        closeActionMenu();
        state.edit.id = id || 0;
        state.edit.payload = null;
        state.edit.loading = true;
        state.edit.saving = false;

        els.editModal.classList.remove('hidden');
        els.editModal.classList.add('flex');
        els.editLoading.classList.remove('hidden');
        els.editError.classList.add('hidden');
        els.editError.textContent = '';
        els.editForm.classList.add('hidden');
        els.editSave.disabled = false;
        els.editSaveLabel.textContent = text.saveChanges;

        try {
            const payload = await Perfectlum.request(`/api/user-modal/${id || ''}`);
            state.edit.payload = payload;

            els.editKicker.textContent = payload.is_existing ? text.editUser : @js(__('Add User'));
            els.editTitle.textContent = payload.is_existing ? (payload.fullname || payload.username || text.updateUserDetails) : text.createNewUser;
            els.editSubtitle.textContent = payload.is_existing
                ? text.adjustUserDetails
                : text.createUserSubtitle;

            els.userId.value = payload.id || 0;
            els.username.value = payload.username || '';
            els.username.readOnly = !!payload.is_existing;
            els.fullname.value = payload.fullname || '';
            els.email.value = payload.email || '';
            els.password.value = '';
            els.passwordConfirmation.value = '';
            fillSelect(els.role, payload.options?.roles || [], payload.user_level, text.selectUserLevel);
            fillSelect(els.facility, payload.options?.facilities || [], payload.facility_id, payload.can_choose_facility ? text.selectFacility : '');
            els.facility.disabled = !payload.can_choose_facility;
            els.enabled.checked = !!payload.enabled;

            els.editForm.classList.remove('hidden');
        } catch (error) {
            els.editError.textContent = error.message || text.unableToLoadUserForm;
            els.editError.classList.remove('hidden');
        } finally {
            state.edit.loading = false;
            els.editLoading.classList.add('hidden');
        }
    }

    async function saveEditModal() {
        if (state.edit.saving || state.edit.loading) return;
        state.edit.saving = true;
        els.editSave.disabled = true;
        els.editSaveLabel.textContent = text.saving;
        els.editError.classList.add('hidden');

        try {
            const formData = new FormData();
            formData.append('_token', csrfToken());
            formData.append('id', els.userId.value || '0');
            formData.append('name', els.username.value || '');
            formData.append('fullname', els.fullname.value || '');
            formData.append('email', els.email.value || '');
            formData.append('password', els.password.value || '');
            formData.append('password_confirmation', els.passwordConfirmation.value || '');
            formData.append('user_level', els.role.value || '');
            formData.append('facility_id', els.facility.value || '');
            if (els.enabled.checked) {
                formData.append('enabled', '1');
            }

            const payload = await Perfectlum.postForm('/api/user-modal/save', formData);
            if (!payload.success) {
                throw new Error(payload.message || text.unableToSaveUser);
            }
            closeEditModal();
            reloadGrid();
        } catch (error) {
            els.editError.textContent = error.message || text.unableToSaveUser;
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
        els.editForm.classList.add('hidden');
        els.editSave.disabled = false;
        els.editSaveLabel.textContent = text.saveChanges;
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
        els.deleteConfirm.textContent = text.deleteUser;
    }

    function cleanupUsersPage() {
        closeDropdown();
        closeActionMenu();
        closeViewModal();
        closeEditModal();
        closeDeleteModal();

        if (els.viewModal?.parentElement === document.body) {
            els.viewModal.remove();
        }
    }

    async function confirmDelete() {
        if (!state.deleteTarget?.id || els.deleteConfirm.disabled) return;
        els.deleteConfirm.disabled = true;
        els.deleteConfirm.textContent = text.deleting;

        try {
            const formData = new FormData();
            formData.append('_token', csrfToken());
            formData.append('id', state.deleteTarget.id);
            const payload = await Perfectlum.postForm('/delete-user', formData);
            if (!payload.success) {
                throw new Error(payload.msg || text.unableToDeleteUser);
            }
            closeDeleteModal();
            reloadGrid();
        } catch (error) {
            window.alert(error.message || text.unableToDeleteUser);
            els.deleteConfirm.disabled = false;
            els.deleteConfirm.textContent = text.deleteUser;
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
