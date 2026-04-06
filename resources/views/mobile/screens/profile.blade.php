@extends('mobile.layouts.app')

@php
    $profileUser = $mobileUser;
    $roleLabel = session('role', $profileUser->role ?? 'user');
    $facilityLabel = $profileUser->facility_name ?: optional($profileUser->facility)->name ?: 'Assigned facility pending';
    $workgroupLabel = $profileUser->workgroup_name ?: 'No workgroup scope';
    $timezoneLabel = $profileUser->timezone ?: 'UTC';
    $profileName = $profileUser->fullname ?: $profileUser->name ?: 'User';
    $profileUsername = $profileUser->name ?: '-';
    $profileEmail = $profileUser->email ?: 'No email configured';
    $remoteUser = $profileUser->sync_user ?: 'Not configured';
    $remotePassword = $profileUser->sync_password_raw ?: '';
    $profileParts = preg_split('/\s+/', trim($profileName)) ?: [];
    $profileInitials = strtoupper(substr($profileParts[0] ?? 'U', 0, 1) . substr($profileParts[1] ?? ($profileUser->name ?: ''), 0, 1));
    $profileImagePath = $profileUser->profile_image ?: null;
    $hasProfileImage = $profileImagePath && file_exists(public_path($profileImagePath));
    $profileImage = $hasProfileImage ? url($profileImagePath) : null;
    $heroCopy = 'Identity, scope, and remote credentials for the mobile workspace.';
@endphp

@push('head')
    <style>
        .mobile-user-shell {
            display: grid;
            gap: 1rem;
        }

        .mobile-user-hero {
            position: relative;
            overflow: hidden;
            padding: 1.2rem;
            border-radius: 1.85rem;
            background:
                radial-gradient(circle at top right, rgba(125, 211, 252, 0.34), transparent 33%),
                radial-gradient(circle at bottom left, rgba(45, 212, 191, 0.22), transparent 30%),
                linear-gradient(145deg, #0f172a 0%, #16365f 52%, #145b63 100%);
            color: #eff6ff;
            box-shadow: 0 24px 60px -34px rgba(15, 23, 42, 0.62);
        }

        .mobile-user-hero::after {
            content: '';
            position: absolute;
            inset: auto -12% -42% auto;
            width: 12rem;
            height: 12rem;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.22), transparent 68%);
            pointer-events: none;
        }

        .mobile-user-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 4.2rem;
            height: 4.2rem;
            overflow: hidden;
            border-radius: 1.35rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.16), rgba(255, 255, 255, 0.08));
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.18);
            font-size: 1.22rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: #fff;
            flex-shrink: 0;
        }

        .mobile-user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .mobile-user-kicker {
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: rgba(226, 232, 240, 0.74);
        }

        .mobile-user-role-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2rem;
            padding: 0.44rem 0.86rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.12);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #f8fafc;
            backdrop-filter: blur(12px);
        }

        .mobile-user-hero-copy {
            margin-top: 0.78rem;
            max-width: 19rem;
            font-size: 0.92rem;
            line-height: 1.6;
            color: rgba(226, 232, 240, 0.82);
        }

        .mobile-user-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
            margin-top: 1rem;
        }

        .mobile-user-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.42rem;
            min-height: 2rem;
            max-width: 100%;
            padding: 0.44rem 0.72rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(255, 255, 255, 0.1);
            font-size: 0.76rem;
            font-weight: 600;
            color: rgba(248, 250, 252, 0.92);
            backdrop-filter: blur(12px);
        }

        .mobile-user-chip strong {
            color: rgba(191, 219, 254, 0.82);
            font-size: 0.62rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .mobile-user-panel {
            overflow: hidden;
            border-radius: 1.6rem;
            border: 1px solid rgba(203, 213, 225, 0.68);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98));
            box-shadow: 0 22px 46px -38px rgba(15, 23, 42, 0.4);
        }

        .mobile-user-panel-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1rem 0;
        }

        .mobile-user-panel-body {
            padding: 1rem;
        }

        .mobile-user-section-kicker {
            font-size: 0.67rem;
            font-weight: 700;
            letter-spacing: 0.24em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-user-section-title {
            margin-top: 0.35rem;
            font-size: 1.16rem;
            font-weight: 700;
            color: #0f172a;
        }

        .mobile-user-section-copy {
            margin-top: 0.35rem;
            font-size: 0.84rem;
            line-height: 1.55;
            color: #64748b;
        }

        .mobile-user-info-grid {
            display: grid;
            gap: 0.78rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .mobile-user-info-card {
            min-width: 0;
            padding: 0.9rem;
            border-radius: 1.2rem;
            border: 1px solid rgba(203, 213, 225, 0.72);
            background: rgba(255, 255, 255, 0.9);
        }

        .mobile-user-info-card.full {
            grid-column: 1 / -1;
        }

        .mobile-user-info-label {
            font-size: 0.66rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-user-info-value {
            margin-top: 0.42rem;
            font-size: 0.94rem;
            font-weight: 700;
            line-height: 1.45;
            color: #0f172a;
            word-break: break-word;
        }

        .mobile-user-info-note {
            margin-top: 0.28rem;
            font-size: 0.75rem;
            line-height: 1.5;
            color: #64748b;
        }

        .mobile-user-credential-row {
            display: grid;
            gap: 0.78rem;
        }

        .mobile-user-credential-card {
            padding: 0.92rem;
            border-radius: 1.25rem;
            border: 1px solid rgba(203, 213, 225, 0.7);
            background: rgba(255, 255, 255, 0.94);
        }

        .mobile-user-credential-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .mobile-user-credential-value {
            margin-top: 0.45rem;
            font-size: 0.95rem;
            font-weight: 700;
            line-height: 1.5;
            color: #0f172a;
            word-break: break-all;
        }

        .mobile-user-credential-value.masked {
            letter-spacing: 0.18em;
            font-weight: 800;
        }

        .mobile-user-inline-actions {
            display: flex;
            gap: 0.55rem;
            margin-top: 0.8rem;
            flex-wrap: wrap;
        }

        .mobile-user-inline-button {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            min-height: 2.15rem;
            padding: 0.48rem 0.82rem;
            border-radius: 999px;
            border: 1px solid rgba(191, 219, 254, 0.9);
            background: rgba(239, 246, 255, 0.9);
            font-size: 0.74rem;
            font-weight: 700;
            color: #0369a1;
        }

        .mobile-user-panel-link {
            display: inline-flex;
            align-items: center;
            gap: 0.34rem;
            padding: 0.22rem 0;
            font-size: 0.78rem;
            font-weight: 700;
            color: #0284c7;
            white-space: nowrap;
        }

        .mobile-user-action-grid {
            display: grid;
            gap: 0.82rem;
        }

        .mobile-user-schedule-list {
            display: grid;
            gap: 0.78rem;
        }

        .mobile-user-schedule-card {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.9rem;
            padding: 0.92rem 0.98rem;
            border-radius: 1.18rem;
            border: 1px solid rgba(203, 213, 225, 0.74);
            background: rgba(255, 255, 255, 0.92);
        }

        .mobile-user-schedule-card:active {
            transform: scale(0.99);
        }

        .mobile-user-schedule-title {
            margin-top: 0.35rem;
            font-size: 0.92rem;
            font-weight: 700;
            line-height: 1.45;
            color: #0f172a;
        }

        .mobile-user-schedule-meta,
        .mobile-user-schedule-scope {
            margin-top: 0.26rem;
            font-size: 0.78rem;
            line-height: 1.5;
            color: #64748b;
        }

        .mobile-user-schedule-date {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.2rem;
            min-width: 4.9rem;
            padding: 0.48rem 0.72rem;
            border-radius: 999px;
            border: 1px solid rgba(191, 219, 254, 0.86);
            background: rgba(239, 246, 255, 0.9);
            font-size: 0.74rem;
            font-weight: 700;
            color: #0369a1;
            text-align: center;
            flex-shrink: 0;
        }

        .mobile-user-schedule-skeleton {
            position: relative;
            overflow: hidden;
            padding: 0.92rem 0.98rem;
            border-radius: 1.18rem;
            border: 1px solid rgba(226, 232, 240, 0.74);
            background: rgba(248, 250, 252, 0.92);
        }

        .mobile-user-schedule-skeleton::after {
            content: '';
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.7), transparent);
            animation: mobileProfileScheduleShimmer 1.35s infinite;
        }

        .mobile-user-schedule-skeleton-pill,
        .mobile-user-schedule-skeleton-line,
        .mobile-user-schedule-skeleton-date {
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(226, 232, 240, 0.88), rgba(241, 245, 249, 0.96));
        }

        .mobile-user-schedule-skeleton-pill {
            width: 5rem;
            height: 0.85rem;
        }

        .mobile-user-schedule-skeleton-line {
            height: 0.82rem;
            margin-top: 0.5rem;
        }

        .mobile-user-schedule-skeleton-line.title {
            width: 72%;
            height: 0.98rem;
        }

        .mobile-user-schedule-skeleton-line.scope {
            width: 84%;
        }

        .mobile-user-schedule-skeleton-line.meta {
            width: 56%;
        }

        .mobile-user-schedule-skeleton-date {
            width: 4.9rem;
            height: 2.2rem;
            flex-shrink: 0;
        }

        @keyframes mobileProfileScheduleShimmer {
            100% {
                transform: translateX(100%);
            }
        }

        .mobile-user-action-card {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.98rem 1rem;
            border-radius: 1.25rem;
            border: 1px solid rgba(191, 219, 254, 0.85);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(239, 246, 255, 0.94));
            box-shadow: 0 18px 40px -34px rgba(2, 132, 199, 0.34);
            transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease;
        }

        .mobile-user-action-card:active {
            transform: scale(0.988);
        }

        .mobile-user-action-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2.85rem;
            height: 2.85rem;
            flex-shrink: 0;
            border-radius: 1rem;
            border: 1px solid rgba(186, 230, 253, 0.9);
            background: rgba(255, 255, 255, 0.92);
            color: #0284c7;
        }

        .mobile-user-action-icon.alert {
            border-color: rgba(253, 164, 175, 0.82);
            color: #be123c;
            background: rgba(255, 241, 242, 0.96);
        }

        .mobile-user-action-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: #0f172a;
        }

        .mobile-user-action-copy {
            margin-top: 0.22rem;
            font-size: 0.79rem;
            line-height: 1.5;
            color: #64748b;
        }

        .mobile-user-divider {
            height: 1px;
            margin: 0 1rem;
            background: linear-gradient(90deg, rgba(226, 232, 240, 0), rgba(226, 232, 240, 0.92), rgba(226, 232, 240, 0));
        }

        @media (max-width: 420px) {
            .mobile-user-info-grid {
                grid-template-columns: minmax(0, 1fr);
            }

            .mobile-user-info-card.full {
                grid-column: auto;
            }
        }
    </style>
@endpush

@section('content')
    <div class="mobile-user-shell">
        <section class="mobile-user-hero">
            <div class="flex items-start justify-between gap-3">
                <div class="flex min-w-0 items-start gap-3">
                    <div class="mobile-user-avatar">
                        @if ($profileImage)
                            <img src="{{ $profileImage }}" alt="{{ $profileName }}">
                        @else
                            <span>{{ $profileInitials ?: 'U' }}</span>
                        @endif
                    </div>

                    <div class="min-w-0">
                        <p class="mobile-user-kicker">Account Console</p>
                        <h1 class="mt-2 break-words text-[1.5rem] font-semibold leading-[1.08] text-white">{{ $profileName }}</h1>
                        <p class="mt-2 break-all text-[0.88rem] font-medium text-slate-200/90">{{ $profileEmail }}</p>
                    </div>
                </div>

                <span class="mobile-user-role-badge">{{ $roleLabel }}</span>
            </div>

            <p class="mobile-user-hero-copy">{{ $heroCopy }}</p>

            <div class="mobile-user-chip-row">
                <span class="mobile-user-chip"><strong>Facility</strong> {{ $facilityLabel }}</span>
                <span class="mobile-user-chip"><strong>Workgroup</strong> {{ $workgroupLabel }}</span>
                <span class="mobile-user-chip"><strong>Timezone</strong> {{ $timezoneLabel }}</span>
                <span class="mobile-user-chip"><strong>Platform</strong> {{ $platformLabel }}</span>
            </div>
        </section>

        <section class="mobile-user-panel">
            <div class="mobile-user-panel-head">
                <div>
                    <p class="mobile-user-section-kicker">Profile Overview</p>
                    <h2 class="mobile-user-section-title">Identity and assigned scope</h2>
                    <p class="mobile-user-section-copy">The same account context shown on desktop, tuned into a tighter mobile profile surface.</p>
                </div>
            </div>

            <div class="mobile-user-panel-body">
                <div class="mobile-user-info-grid">
                    <article class="mobile-user-info-card">
                        <p class="mobile-user-info-label">User name</p>
                        <p class="mobile-user-info-value">{{ $profileUsername }}</p>
                    </article>

                    <article class="mobile-user-info-card">
                        <p class="mobile-user-info-label">User level</p>
                        <p class="mobile-user-info-value">{{ $roleLabel }}</p>
                    </article>

                    <article class="mobile-user-info-card full">
                        <p class="mobile-user-info-label">Email</p>
                        <p class="mobile-user-info-value">{{ $profileEmail }}</p>
                    </article>

                    <article class="mobile-user-info-card">
                        <p class="mobile-user-info-label">Facility</p>
                        <p class="mobile-user-info-value">{{ $facilityLabel }}</p>
                    </article>

                    <article class="mobile-user-info-card">
                        <p class="mobile-user-info-label">Workgroup</p>
                        <p class="mobile-user-info-value">{{ $workgroupLabel }}</p>
                    </article>

                    <article class="mobile-user-info-card">
                        <p class="mobile-user-info-label">Timezone</p>
                        <p class="mobile-user-info-value">{{ $timezoneLabel }}</p>
                    </article>

                    <article class="mobile-user-info-card">
                        <p class="mobile-user-info-label">Remote platform</p>
                        <p class="mobile-user-info-value">{{ $platformLabel }}</p>
                        <p class="mobile-user-info-note">{{ $siteName }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="mobile-user-panel">
            <div class="mobile-user-panel-head">
                <div>
                    <p class="mobile-user-section-kicker">Remote Credentials</p>
                    <h2 class="mobile-user-section-title">Workstation access identity</h2>
                    <p class="mobile-user-section-copy">These values match the desktop settings area and are used by remote workstation connections.</p>
                </div>
            </div>

            <div class="mobile-user-panel-body">
                <div class="mobile-user-credential-row">
                    <article class="mobile-user-credential-card">
                        <div class="mobile-user-credential-top">
                            <div>
                                <p class="mobile-user-info-label">Remote user</p>
                                <p class="mobile-user-credential-value">{{ $remoteUser }}</p>
                            </div>
                            <button type="button" class="mobile-user-inline-button" onclick="window.copyMobileProfileValue && window.copyMobileProfileValue(@js($remoteUser))">
                                <i data-lucide="copy" class="h-3.5 w-3.5"></i>
                                Copy
                            </button>
                        </div>
                    </article>

                    <article class="mobile-user-credential-card">
                        <div class="mobile-user-credential-top">
                            <div class="min-w-0">
                                <p class="mobile-user-info-label">Remote password</p>
                                <p
                                    class="mobile-user-credential-value {{ $remotePassword ? 'masked' : '' }}"
                                    data-password-display
                                    data-password-plain="{{ e($remotePassword) }}"
                                    data-password-masked="{{ $remotePassword ? str_repeat('•', max(8, min(strlen($remotePassword), 16))) : 'Not configured' }}">
                                    {{ $remotePassword ? str_repeat('•', max(8, min(strlen($remotePassword), 16))) : 'Not configured' }}
                                </p>
                            </div>
                            <button type="button" class="mobile-user-inline-button" data-password-toggle>
                                <i data-lucide="eye" class="h-3.5 w-3.5"></i>
                                <span data-password-toggle-label>Reveal</span>
                            </button>
                        </div>

                        <div class="mobile-user-inline-actions">
                            <button type="button" class="mobile-user-inline-button" onclick="window.copyMobileProfileValue && window.copyMobileProfileValue(@js($remotePassword))">
                                <i data-lucide="copy" class="h-3.5 w-3.5"></i>
                                Copy password
                            </button>
                            <a href="{{ route('mobile.profile.settings') }}" class="mobile-user-inline-button">
                                <i data-lucide="settings-2" class="h-3.5 w-3.5"></i>
                                Manage credentials
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section class="mobile-user-panel">
            <div class="mobile-user-panel-head">
                <div>
                    <p class="mobile-user-section-kicker">Scheduled Work</p>
                    <h2 class="mobile-user-section-title">Scheduled work in your scope</h2>
                    <p class="mobile-user-section-copy">The next scheduled items from the same operational scope you oversee in Tasks.</p>
                </div>

                <a href="{{ route('mobile.tasks', ['view' => 'scheduled', 'return_to' => route('mobile.profile')]) }}" class="mobile-user-panel-link">
                    <span>View all</span>
                    <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                </a>
            </div>

            <div class="mobile-user-panel-body">
                <div id="mobile-user-schedule-preview" class="mobile-user-schedule-list">
                    <div class="mobile-user-schedule-skeleton" aria-hidden="true">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="mobile-user-schedule-skeleton-pill"></div>
                                <div class="mobile-user-schedule-skeleton-line title"></div>
                                <div class="mobile-user-schedule-skeleton-line scope"></div>
                                <div class="mobile-user-schedule-skeleton-line meta"></div>
                            </div>
                            <div class="mobile-user-schedule-skeleton-date"></div>
                        </div>
                    </div>
                    <div class="mobile-user-schedule-skeleton" aria-hidden="true">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="mobile-user-schedule-skeleton-pill"></div>
                                <div class="mobile-user-schedule-skeleton-line title"></div>
                                <div class="mobile-user-schedule-skeleton-line scope"></div>
                                <div class="mobile-user-schedule-skeleton-line meta"></div>
                            </div>
                            <div class="mobile-user-schedule-skeleton-date"></div>
                        </div>
                    </div>
                    <div class="mobile-user-schedule-skeleton" aria-hidden="true">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="mobile-user-schedule-skeleton-pill"></div>
                                <div class="mobile-user-schedule-skeleton-line title"></div>
                                <div class="mobile-user-schedule-skeleton-line scope"></div>
                                <div class="mobile-user-schedule-skeleton-line meta"></div>
                            </div>
                            <div class="mobile-user-schedule-skeleton-date"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mobile-user-panel">
            <div class="mobile-user-panel-head">
                <div>
                    <p class="mobile-user-section-kicker">Workspace Actions</p>
                    <h2 class="mobile-user-section-title">Continue from this account</h2>
                    <p class="mobile-user-section-copy">Keep the most useful account actions close without turning this page into a desktop form.</p>
                </div>
            </div>

            <div class="mobile-user-panel-body">
                <div class="mobile-user-action-grid">
                    @if(in_array($mobileRole ?? 'user', ['super', 'admin'], true))
                        <a href="{{ route('mobile.settings', ['return_to' => route('mobile.profile')]) }}" class="mobile-user-action-card">
                            <span class="mobile-user-action-icon">
                                <i data-lucide="settings-2" class="h-5 w-5"></i>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="mobile-user-action-title">Settings</span>
                                <span class="mobile-user-action-copy">Review mobile-ready site, application, and alert configuration in your current operational scope.</span>
                            </span>
                            <i data-lucide="chevron-right" class="h-4 w-4 shrink-0 text-slate-400"></i>
                        </a>
                    @endif

                    <a href="{{ route('mobile.tasks', ['view' => 'scheduled', 'return_to' => route('mobile.profile')]) }}" class="mobile-user-action-card">
                        <span class="mobile-user-action-icon">
                            <i data-lucide="calendar-clock" class="h-5 w-5"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="mobile-user-action-title">Scheduled work</span>
                            <span class="mobile-user-action-copy">Open the scheduled tab inside Tasks for upcoming operational work across your current scope.</span>
                        </span>
                        <i data-lucide="chevron-right" class="h-4 w-4 shrink-0 text-slate-400"></i>
                    </a>

                    <a href="{{ route('mobile.profile.settings', ['return_to' => route('mobile.profile')]) }}" class="mobile-user-action-card">
                        <span class="mobile-user-action-icon">
                            <i data-lucide="user-cog" class="h-5 w-5"></i>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="mobile-user-action-title">Profile settings</span>
                            <span class="mobile-user-action-copy">Manage profile image, password, and remote credentials in the mobile settings workspace.</span>
                        </span>
                        <i data-lucide="chevron-right" class="h-4 w-4 shrink-0 text-slate-400"></i>
                    </a>
                </div>
            </div>

            <div class="mobile-user-divider"></div>

            <div class="mobile-user-panel-body pt-4">
                <a href="{{ url('logout') }}" class="mobile-user-action-card">
                    <span class="mobile-user-action-icon alert">
                        <i data-lucide="log-out" class="h-5 w-5"></i>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="mobile-user-action-title text-rose-700">Sign out</span>
                        <span class="mobile-user-action-copy text-rose-500">End this mobile session and return to the access screen.</span>
                    </span>
                    <i data-lucide="chevron-right" class="h-4 w-4 shrink-0 text-rose-400"></i>
                </a>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const boot = () => window.Perfectlum.mountMobilePage('mobileProfile', () => {
                window.copyMobileProfileValue = function (value) {
                    if (!value) {
                        return;
                    }

                    if (navigator.clipboard?.writeText) {
                        navigator.clipboard.writeText(value).catch(function () {});
                        return;
                    }

                    const field = document.createElement('textarea');
                    field.value = value;
                    field.setAttribute('readonly', '');
                    field.style.position = 'absolute';
                    field.style.left = '-9999px';
                    document.body.appendChild(field);
                    field.select();

                    try {
                        document.execCommand('copy');
                    } catch (error) {}

                    document.body.removeChild(field);
                };

                const toggle = document.querySelector('[data-password-toggle]');
                const display = document.querySelector('[data-password-display]');
                const scheduleRoot = document.getElementById('mobile-user-schedule-preview');
                const scheduleCache = window.__mobileUserSchedulePreviewCache || (window.__mobileUserSchedulePreviewCache = new Map());
                let scheduleRequestToken = 0;

                const escapeHtml = (value) => String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');

                const scheduleSkeleton = () => Array.from({ length: 3 }).map(() => `
                    <div class="mobile-user-schedule-skeleton" aria-hidden="true">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="mobile-user-schedule-skeleton-pill"></div>
                                <div class="mobile-user-schedule-skeleton-line title"></div>
                                <div class="mobile-user-schedule-skeleton-line scope"></div>
                                <div class="mobile-user-schedule-skeleton-line meta"></div>
                            </div>
                            <div class="mobile-user-schedule-skeleton-date"></div>
                        </div>
                    </div>
                `).join('');

                const scheduleEmpty = (message) => `<div class="mobile-empty">${escapeHtml(message)}</div>`;

                const renderScheduleRow = (item) => {
                    const href = item.displayId
                        ? `${@json(url('/m/displays'))}/${item.displayId}?return_to=${encodeURIComponent(`${window.location.pathname}${window.location.search}`)}`
                        : @json(route('mobile.tasks', ['view' => 'scheduled']));

                    return `
                        <a href="${href}" class="mobile-user-schedule-card">
                            <div class="min-w-0 flex-1">
                                <p class="mobile-user-info-label">Scheduled</p>
                                <p class="mobile-user-schedule-title">${escapeHtml(item.taskName || 'Scheduled task')}</p>
                                <p class="mobile-user-schedule-scope">${escapeHtml([item.wsName, item.wgName, item.facName].filter(Boolean).join(' • '))}</p>
                                <p class="mobile-user-schedule-meta">${escapeHtml(item.displayName || 'Open the linked display for more detail.')}</p>
                            </div>
                            <span class="mobile-user-schedule-date">${escapeHtml(item.dueAt || '-')}</span>
                        </a>
                    `;
                };

                const loadSchedulePreview = async () => {
                    if (!scheduleRoot) {
                        return;
                    }

                    const cacheKey = 'scheduled::profile-preview';
                    const cached = scheduleCache.get(cacheKey);
                    const currentRequest = ++scheduleRequestToken;

                    if (cached) {
                        scheduleRoot.innerHTML = cached;
                        return;
                    }

                    scheduleRoot.innerHTML = scheduleSkeleton();

                    try {
                        const response = await window.Perfectlum.request('/api/tasks?sort_mode=due&limit=3&page=1');
                        if (currentRequest !== scheduleRequestToken) {
                            return;
                        }

                        const rows = response.data || [];
                        const html = rows.length
                            ? rows.map(renderScheduleRow).join('')
                            : scheduleEmpty('No scheduled work is queued in your scope right now.');

                        scheduleCache.set(cacheKey, html);
                        scheduleRoot.innerHTML = html;
                    } catch (error) {
                        if (currentRequest !== scheduleRequestToken) {
                            return;
                        }

                        scheduleRoot.innerHTML = scheduleEmpty('Unable to load scheduled work right now.');
                    }
                };

                if (toggle && display) {
                    toggle.addEventListener('click', function () {
                        const masked = display.getAttribute('data-password-masked') || 'Not configured';
                        const plain = display.getAttribute('data-password-plain') || 'Not configured';
                        const showingMasked = display.textContent.trim() === masked;
                        const icon = toggle.querySelector('[data-lucide]');

                        display.textContent = showingMasked ? plain : masked;
                        display.classList.toggle('masked', !showingMasked);

                        if (icon) {
                            icon.setAttribute('data-lucide', showingMasked ? 'eye-off' : 'eye');
                        }

                        const label = toggle.querySelector('[data-password-toggle-label]');
                        if (label) {
                            label.textContent = showingMasked ? 'Hide' : 'Reveal';
                        }

                        if (window.lucide?.createIcons) {
                            window.lucide.createIcons();
                        }
                    });
                }

                loadSchedulePreview();
            });

            if (window.Perfectlum?.mountMobilePage) {
                boot();
                return;
            }

            (window.__perfectlumPageBoot = window.__perfectlumPageBoot || []).push(boot);
        })();
    </script>
@endpush
