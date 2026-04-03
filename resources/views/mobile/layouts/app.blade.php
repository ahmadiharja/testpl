<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Mobile Workspace' }} | {{ $siteName ?? ($settings['Site name'] ?? 'PerfectLum') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --mobile-bg: #f6f8fc;
            --mobile-surface: #ffffff;
            --mobile-surface-2: #f8fafc;
            --mobile-border: rgba(148, 163, 184, 0.14);
            --mobile-muted: #64748b;
            --mobile-text: #0f172a;
            --mobile-accent: #0ea5e9;
            --mobile-card-radius: 1rem;
            --mobile-panel-radius: 1.1rem;
            --mobile-control-height: 2.7rem;
            --mobile-section-gap: 0.95rem;
            --mobile-heading-size: 15px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top, rgba(14, 165, 233, 0.08), transparent 26%),
                linear-gradient(180deg, #f8fbff 0%, #f6f8fc 34%, #f5f7fb 100%);
        }

        .mobile-shell {
            background: linear-gradient(180deg, rgba(250, 252, 255, 0.96) 0%, rgba(245, 247, 251, 0.98) 100%);
        }

        .mobile-card {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid var(--mobile-border);
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.035);
        }

        .mobile-chip {
            background: rgba(14, 165, 233, 0.08);
            border: 1px solid rgba(14, 165, 233, 0.14);
            color: #0369a1;
        }

        .mobile-input {
            background: rgba(255, 255, 255, 0.98);
            border: 1px solid rgba(148, 163, 184, 0.22);
            color: #0f172a;
        }

        .mobile-input:focus {
            border-color: rgba(56, 189, 248, 0.7);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
            outline: none;
        }

        .mobile-panel {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid var(--mobile-border);
            border-radius: var(--mobile-panel-radius);
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.035);
        }

        .mobile-panel.compact {
            padding: 0.9rem 0.95rem;
            border-radius: var(--mobile-card-radius);
        }

        .mobile-search-shell {
            background: rgba(255, 255, 255, 0.76);
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: var(--mobile-card-radius);
            padding: 0.68rem 0.74rem;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.025);
            backdrop-filter: blur(12px);
        }

        .mobile-searchbar {
            position: relative;
        }

        .mobile-search-input {
            width: 100%;
            height: var(--mobile-control-height);
            border-radius: 999px;
            padding-left: 2.35rem;
            padding-right: 0.92rem;
            font-size: 12.5px;
            line-height: 1.2;
            color: #0f172a;
        }

        .mobile-search-input::placeholder {
            color: #94a3b8;
        }

        .mobile-searchbar-icon {
            position: absolute;
            left: 0.78rem;
            top: 50%;
            height: 0.9rem;
            width: 0.9rem;
            transform: translateY(-50%);
            color: #94a3b8;
            pointer-events: none;
        }

        .mobile-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.65rem;
        }

        .mobile-action-link {
            color: #0284c7;
            font-size: 11px;
            font-weight: 600;
        }

        .mobile-stack {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid var(--mobile-border);
            border-radius: var(--mobile-card-radius);
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.03);
        }

        .mobile-list-item {
            display: block;
            padding: 0.72rem 0.8rem;
        }

        .mobile-list-item.compact {
            padding: 0.64rem 0.78rem;
        }

        .mobile-list-item + .mobile-list-item {
            border-top: 1px solid rgba(148, 163, 184, 0.12);
        }

        .mobile-empty {
            border-radius: var(--mobile-card-radius);
            border: 1px dashed rgba(148, 163, 184, 0.26);
            padding: 0.8rem;
            color: #64748b;
            font-size: 12px;
            background: rgba(255, 255, 255, 0.72);
        }

        .mobile-pager {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
            border-radius: var(--mobile-card-radius);
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(255, 255, 255, 0.94);
            padding: 0.68rem 0.78rem;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.03);
        }

        .mobile-pager-meta {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
        }

        .mobile-pager-actions {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .mobile-pager-status {
            min-width: 4.8rem;
            text-align: center;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
        }

        .mobile-pager-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 3rem;
            border-radius: 999px;
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(248, 250, 252, 0.95);
            padding: 0.42rem 0.7rem;
            font-size: 11px;
            font-weight: 700;
            color: #334155;
        }

        .mobile-pager-button:disabled {
            opacity: 0.42;
        }

        .mobile-stat-rail {
            display: flex;
            gap: 0.6rem;
            overflow-x: auto;
            padding-bottom: 0.1rem;
        }

        .mobile-stat-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.55rem;
        }

        .mobile-stat-tile {
            min-width: 98px;
            border-radius: var(--mobile-card-radius);
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(248, 250, 252, 0.96);
            padding: 0.66rem 0.72rem;
        }

        .mobile-section-gap {
            margin-top: var(--mobile-section-gap);
        }

        .mobile-section-title {
            font-size: var(--mobile-heading-size);
            font-weight: 650;
            line-height: 1.32;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .mobile-section-copy {
            margin-top: 0.2rem;
            font-size: 12px;
            line-height: 1.45;
            color: #64748b;
        }

        .mobile-stat-tile.primary {
            border-color: rgba(56, 189, 248, 0.24);
            background: rgba(14, 165, 233, 0.1);
        }

        .mobile-stat-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #7b8ca1;
        }

        .mobile-stat-value {
            margin-top: 0.35rem;
            font-size: 1.08rem;
            font-weight: 700;
            line-height: 1.1;
            color: #0f172a;
        }

        .mobile-stat-note {
            margin-top: 0.18rem;
            font-size: 10px;
            color: #64748b;
        }

        .mobile-type-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 9999px;
            padding: 0.16rem 0.42rem;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .mobile-clamp-1,
        .mobile-clamp-2 {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .mobile-clamp-1 {
            -webkit-line-clamp: 1;
        }

        .mobile-clamp-2 {
            -webkit-line-clamp: 2;
        }

        .mobile-meta {
            font-size: 11px;
            line-height: 1.35;
            color: #64748b;
        }

        .mobile-type-pill.due {
            background: rgba(14, 165, 233, 0.1);
            color: #0369a1;
        }

        .mobile-type-pill.alert {
            background: rgba(244, 63, 94, 0.1);
            color: #be123c;
        }

        .mobile-type-pill.sync {
            background: rgba(245, 158, 11, 0.12);
            color: #b45309;
        }

        .mobile-type-pill.run {
            background: rgba(16, 185, 129, 0.1);
            color: #047857;
        }

        .mobile-filter-chip {
            white-space: nowrap;
            border-radius: 9999px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(248, 250, 252, 0.95);
            padding: 0.42rem 0.76rem;
            font-size: 10.5px;
            font-weight: 600;
            color: #475569;
        }

        .mobile-filter-chip.active {
            border-color: rgba(56, 189, 248, 0.24);
            background: rgba(14, 165, 233, 0.14);
            color: #0c4a6e;
        }

        .mobile-metric-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.7rem;
        }

        .mobile-metric {
            border-radius: var(--mobile-card-radius);
            border: 1px solid rgba(148, 163, 184, 0.16);
            background: rgba(248, 250, 252, 0.94);
            padding: 0.85rem;
        }

        .mobile-accordion-item + .mobile-accordion-item {
            border-top: 1px solid rgba(148, 163, 184, 0.12);
        }

        .mobile-accordion-trigger {
            width: 100%;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.82rem 0.82rem 0.78rem;
            text-align: left;
            transition: background 160ms ease;
        }

        .mobile-accordion-trigger:active {
            background: rgba(14, 165, 233, 0.06);
        }

        .mobile-accordion-item.open .mobile-accordion-trigger {
            background: rgba(248, 250, 252, 0.72);
        }

        .mobile-accordion-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 1.65rem;
            width: 1.65rem;
            flex: 0 0 auto;
            border-radius: 999px;
            background: #0ea5e9;
            color: white;
            box-shadow: 0 6px 14px rgba(14, 165, 233, 0.22);
            transition: transform 180ms ease, background 180ms ease;
        }

        .mobile-accordion-toggle.alert {
            background: #f43f5e;
            box-shadow: 0 6px 14px rgba(244, 63, 94, 0.2);
        }

        .mobile-accordion-toggle.sync {
            background: #f59e0b;
            box-shadow: 0 6px 14px rgba(245, 158, 11, 0.2);
        }

        .mobile-accordion-toggle.run {
            background: #10b981;
            box-shadow: 0 6px 14px rgba(16, 185, 129, 0.2);
        }

        .mobile-accordion-item.open .mobile-accordion-toggle {
            transform: rotate(180deg);
        }

        .mobile-accordion-panel {
            display: none;
            padding: 0 0.82rem 0.88rem 3.22rem;
        }

        .mobile-accordion-item.open .mobile-accordion-panel {
            display: block;
        }

        .mobile-accordion-detail {
            display: grid;
            grid-template-columns: 84px minmax(0, 1fr);
            gap: 0.42rem 0.7rem;
            border-radius: 0.95rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(248, 250, 252, 0.9);
            padding: 0.8rem;
        }

        .mobile-accordion-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #7b8ca1;
        }

        .mobile-accordion-value {
            min-width: 0;
            font-size: 12px;
            line-height: 1.45;
            color: #0f172a;
        }

        .mobile-accordion-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 0.7rem;
            border-radius: 999px;
            border: 1px solid rgba(56, 189, 248, 0.22);
            background: rgba(14, 165, 233, 0.08);
            padding: 0.46rem 0.85rem;
            font-size: 11px;
            font-weight: 700;
            color: #0369a1;
        }

        .mobile-accordion-action.secondary {
            border-color: rgba(148, 163, 184, 0.18);
            background: rgba(248, 250, 252, 0.9);
            color: #334155;
        }

        .mobile-accordion-action:active {
            transform: scale(0.992);
        }

        .mobile-sheet-backdrop {
            background: rgba(15, 23, 42, 0.16);
            backdrop-filter: blur(3px);
        }

        .mobile-sheet {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
            border: 1px solid rgba(148, 163, 184, 0.18);
            border-top-left-radius: 1.35rem;
            border-top-right-radius: 1.35rem;
            box-shadow: 0 -18px 36px rgba(15, 23, 42, 0.1);
        }

        .mobile-sheet.compact {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            padding-bottom: calc(1rem + env(safe-area-inset-bottom));
        }

        .mobile-sheet-handle {
            height: 0.28rem;
            width: 2.8rem;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.45);
            margin: 0 auto;
        }

        .mobile-sheet-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.8rem;
            border-radius: var(--mobile-card-radius);
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(255, 255, 255, 0.88);
            padding: 0.88rem 0.92rem;
        }

        .mobile-sheet-link:active {
            transform: scale(0.995);
        }

        .mobile-quick-sheet-grid {
            display: grid;
            grid-template-columns: 92px minmax(0, 1fr);
            gap: 0.5rem 0.75rem;
            border-radius: var(--mobile-card-radius);
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(248, 250, 252, 0.94);
            padding: 0.9rem;
        }

        .mobile-sheet-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 2.2rem;
            width: 2.2rem;
            flex: 0 0 auto;
            border-radius: 999px;
        }

        .mobile-nav-surface {
            background: rgba(255, 255, 255, 0.92);
            border-top: 1px solid rgba(148, 163, 184, 0.18);
            backdrop-filter: blur(16px);
            box-shadow: 0 -16px 28px rgba(148, 163, 184, 0.12);
            border-top-left-radius: 1.05rem;
            border-top-right-radius: 1.05rem;
        }

        .mobile-bottom-bar {
            display: flex;
            align-items: center;
            justify-content: space-around;
            gap: 0.2rem;
        }

        .mobile-bottom-link {
            position: relative;
            flex: 1 1 0;
            min-width: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.18rem;
            padding: 0.42rem 0.18rem 0.38rem;
            border-radius: 0.8rem;
            color: #64748b;
            font-size: 9px;
            font-weight: 600;
            transition: all 180ms ease;
        }

        .mobile-bottom-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 1.9rem;
            width: 1.9rem;
            border-radius: 999px;
            color: inherit;
            transition: all 180ms ease;
        }

        .mobile-bottom-link.active {
            color: #0f172a;
            background: transparent;
        }

        .mobile-bottom-link.active .mobile-bottom-icon {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            box-shadow: 0 8px 16px rgba(14, 165, 233, 0.2);
        }

        .mobile-appbar {
            position: relative;
            z-index: 1;
            padding: max(0.72rem, env(safe-area-inset-top)) 0.95rem 0.55rem;
        }

        .mobile-appbar.compact {
            padding-bottom: 0.3rem;
        }

        .mobile-appbar-button {
            display: inline-flex;
            height: 2.15rem;
            width: 2.15rem;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.92);
            background: rgba(255, 255, 255, 0.78);
            color: #334155;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.03);
            backdrop-filter: blur(12px);
        }

        .mobile-appbar-title {
            font-size: 1rem;
            font-weight: 650;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .mobile-appbar-shell {
            position: sticky;
            top: 0;
            z-index: 20;
            isolation: isolate;
        }

        .mobile-appbar-shell::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: calc(100% + 1.9rem);
            pointer-events: none;
            opacity: 0;
            transition: opacity 180ms ease;
            -webkit-mask-image: linear-gradient(180deg, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 0.92) 48%, rgba(0, 0, 0, 0.45) 76%, rgba(0, 0, 0, 0) 100%);
            mask-image: linear-gradient(180deg, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 0.92) 48%, rgba(0, 0, 0, 0.45) 76%, rgba(0, 0, 0, 0) 100%);
        }

        .mobile-appbar-shell.seamless::before {
            opacity: 1;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.34) 0%, rgba(248, 250, 252, 0.24) 42%, rgba(248, 250, 252, 0.12) 72%, rgba(248, 250, 252, 0) 100%);
            backdrop-filter: blur(14px) saturate(1.08);
            -webkit-backdrop-filter: blur(14px) saturate(1.08);
        }

        .mobile-appbar-shell.default::before {
            opacity: 1;
            background:
                linear-gradient(180deg, rgba(248, 250, 252, 0.84) 0%, rgba(248, 250, 252, 0.68) 62%, rgba(248, 250, 252, 0.12) 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(16px) saturate(1.06);
            -webkit-backdrop-filter: blur(16px) saturate(1.06);
        }

        .mobile-page-stage {
            will-change: transform, opacity;
            transform: translateX(0);
            opacity: 1;
            overflow-x: clip;
        }

        .mobile-page-enter-forward {
            animation: mobilePageEnterForward 320ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        .mobile-page-enter-back {
            animation: mobilePageEnterBack 320ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        .mobile-page-exit-forward {
            animation: mobilePageExitForward 220ms cubic-bezier(0.4, 0, 1, 1) forwards;
        }

        .mobile-page-exit-back {
            animation: mobilePageExitBack 220ms cubic-bezier(0.4, 0, 1, 1) forwards;
        }

        @keyframes mobilePageEnterForward {
            from {
                transform: translateX(100%);
                opacity: 0.92;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes mobilePageEnterBack {
            from {
                transform: translateX(-42%);
                opacity: 0.92;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes mobilePageExitForward {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(-22%);
                opacity: 0.74;
            }
        }

        @keyframes mobilePageExitBack {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(22%);
                opacity: 0.74;
            }
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    <!-- mobile-page-head-start -->
    @stack('head')
    <!-- mobile-page-head-end -->
</head>
<body class="min-h-screen text-slate-900"
      data-surface="mobile"
      data-idle-logout-minutes="{{ config('session.idle_timeout', 30) }}"
      data-idle-heartbeat-seconds="{{ config('session.idle_heartbeat_seconds', 60) }}"
      data-idle-heartbeat-url="{{ url('session/heartbeat') }}"
      data-idle-logout-url="{{ url('logout?reason=inactive') }}"
      data-idle-login-url="{{ route('mobile.login', ['surface' => 'mobile']) }}">
    @php
        $seamlessHeader = !empty($seamlessHeader);
    @endphp
    <div class="mx-auto min-h-screen w-full max-w-[440px] mobile-shell">
        <div id="mobile-page-stage" class="mobile-page-stage">
            <header id="mobile-appbar-shell" class="mobile-appbar-shell {{ $seamlessHeader ? 'seamless' : 'default' }}">
                <div class="mobile-appbar {{ $seamlessHeader ? 'compact' : '' }}">
                    <div class="flex items-center justify-between gap-3">
                        <div id="mobile-appbar-leading" class="min-w-0 flex items-center gap-2.5">
                            <a id="mobile-appbar-back"
                               href="{{ $backUrl ?? '#' }}"
                               data-mobile-nav="back"
                               class="mobile-appbar-button shrink-0 {{ empty($backUrl) ? 'hidden' : '' }}">
                                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                            </a>

                            <div class="min-w-0">
                                <h1 id="mobile-appbar-title" class="mobile-appbar-title truncate">{{ $screenTitle ?? 'Workspace' }}</h1>
                            </div>
                        </div>

                        <a id="mobile-appbar-profile" href="{{ route('mobile.profile') }}" class="mobile-appbar-button shrink-0">
                            <i data-lucide="user-round" class="h-4 w-4"></i>
                        </a>
                    </div>
                </div>
            </header>

            <main id="mobile-main-content" class="overflow-x-hidden px-3.5 pb-[calc(5rem+env(safe-area-inset-bottom))] {{ $seamlessHeader ? 'pt-0.25' : 'pt-2' }}">
                @yield('content')
            </main>
        </div>

        <div id="mobile-modal-root">
            @stack('modals')
        </div>

        @include('tasks.schedule_task_modal')
        @include('common.modals.action-confirm-modal')

        @include('mobile.partials.bottom-nav', ['activeTab' => $activeTab ?? 'dashboard'])
    </div>

    <div id="mobile-notification-center" class="pointer-events-none fixed inset-x-0 top-4 z-[9700] mx-auto flex w-full max-w-[440px] flex-col gap-2 px-4"></div>

    <!-- mobile-page-scripts-start -->
    @stack('scripts')
    <!-- mobile-page-scripts-end -->
    <div id="mobile-page-runtime-scripts" hidden></div>
    <script>
        if (typeof window.notify !== 'function') {
            window.notify = function (type, msg) {
                const container = document.getElementById('mobile-notification-center');
                if (!container) {
                    return;
                }

                const tone = type === 'success'
                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                    : 'border-rose-200 bg-rose-50 text-rose-700';
                const icon = type === 'success' ? 'check-circle-2' : 'circle-alert';
                const alertId = `mobile-alert-${Date.now()}`;
                const html = `
                    <div id="${alertId}" class="pointer-events-auto translate-y-2 opacity-0 transition-all duration-300 rounded-2xl border ${tone} px-4 py-3 shadow-[0_18px_40px_rgba(15,23,42,0.12)]">
                        <div class="flex items-start gap-3">
                            <i data-lucide="${icon}" class="mt-0.5 h-4 w-4 shrink-0"></i>
                            <p class="text-sm font-semibold leading-5">${String(msg || '')}</p>
                        </div>
                    </div>
                `;

                container.insertAdjacentHTML('beforeend', html);
                lucide.createIcons();

                const el = document.getElementById(alertId);
                requestAnimationFrame(() => {
                    el?.classList.remove('translate-y-2', 'opacity-0');
                });

                setTimeout(() => {
                    el?.classList.add('translate-y-2', 'opacity-0');
                    setTimeout(() => el?.remove(), 260);
                }, 3800);
            };
        }
    </script>
    <script>
        window.Perfectlum?.bootMobileShell?.();
        lucide.createIcons();
    </script>
</body>
</html>
