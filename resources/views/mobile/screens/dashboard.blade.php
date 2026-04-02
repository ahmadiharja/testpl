@extends('mobile.layouts.app')

@php
    $summary = $dashboardSummary ?? [
        'displaysOk' => 0,
        'displaysFailed' => 0,
        'workstations' => 0,
        'dueTasks' => 0,
        'staleWorkstations' => 0,
    ];
    $dashboardRole = session('role');
    $isUserDashboard = $dashboardRole === 'user';
    $isAdminDashboard = $dashboardRole === 'admin';
    $isSuperDashboard = $dashboardRole === 'super';

    $dashboardDescription = $isSuperDashboard
        ? 'Monitor cross-facility display health, fleet activity, and upcoming tasks from one operational surface.'
        : ($isAdminDashboard
            ? 'Track display health, recent activity, and schedule pressure inside your assigned facility.'
            : 'Review display health, recent activity, and upcoming tasks in a read-only workspace.');

    $failedSectionDescription = $isUserDashboard
        ? 'The latest ten displays with active issues inside your visible scope.'
        : 'The most recent ten failed displays across the visible scope.';

    $recentSectionDescription = $isUserDashboard
        ? 'The latest ten completed test results available to your account.'
        : 'The latest ten completed test results in the current scope.';

    $dueSectionDescription = $isUserDashboard
        ? 'The next ten due tasks in a read-only schedule overview.'
        : 'The next ten due tasks across calibration and QA schedules.';

    $scopePill = $isSuperDashboard
        ? ['label' => 'Super scope', 'class' => 'mobile-chip']
        : ($isAdminDashboard
            ? ['label' => 'Facility scope', 'class' => 'rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-[11px] font-semibold text-emerald-700']
            : ['label' => 'Read only', 'class' => 'rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] font-semibold text-slate-600']);
@endphp

@push('head')
    <style>
        @keyframes mobile-dashboard-shimmer {
            100% {
                transform: translateX(100%);
            }
        }

        .mobile-dashboard-screen {
            display: grid;
            gap: var(--mobile-section-gap);
        }

        .mobile-dashboard-hero {
            position: relative;
            overflow: hidden;
            padding: 1rem;
            background:
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.18), transparent 34%),
                radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.1), transparent 30%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.94));
            border-color: rgba(191, 219, 254, 0.45);
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.06);
        }

        .mobile-dashboard-hero::before {
            content: '';
            position: absolute;
            inset: -32% auto auto 52%;
            height: 12rem;
            width: 12rem;
            transform: translateX(-50%);
            border-radius: 999px;
            background: radial-gradient(circle, rgba(125, 211, 252, 0.28), transparent 72%);
            pointer-events: none;
        }

        .mobile-dashboard-hero::after {
            content: '';
            position: absolute;
            inset: auto -18% -42% auto;
            height: 14rem;
            width: 14rem;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.12), transparent 72%);
            pointer-events: none;
        }

        .mobile-dashboard-hero-head,
        .mobile-dashboard-hero-foot {
            position: relative;
            z-index: 1;
        }

        .mobile-dashboard-hero-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.9rem;
        }

        .mobile-dashboard-ribbon {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            border-radius: 999px;
            border: 1px solid rgba(191, 219, 254, 0.9);
            background: rgba(255, 255, 255, 0.8);
            padding: 0.34rem 0.66rem;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #0369a1;
            backdrop-filter: blur(10px);
        }

        .mobile-dashboard-hero-title {
            margin-top: 0.68rem;
            font-size: 1.78rem;
            font-weight: 780;
            letter-spacing: -0.045em;
            line-height: 0.98;
            color: #0f172a;
        }

        .mobile-dashboard-hero-copy {
            margin-top: 0.7rem;
            max-width: 18.5rem;
            font-size: 12.5px;
            line-height: 1.58;
            color: #475569;
        }

        .mobile-dashboard-refresh {
            display: inline-flex;
            height: 2.7rem;
            width: 2.7rem;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: rgba(255, 255, 255, 0.9);
            color: #475569;
            box-shadow: 0 14px 24px rgba(15, 23, 42, 0.06);
            backdrop-filter: blur(10px);
            transition: transform 160ms ease, box-shadow 160ms ease;
        }

        .mobile-dashboard-refresh:active {
            transform: scale(0.97);
        }

        .mobile-dashboard-hero-status {
            display: inline-flex;
            align-items: center;
            gap: 0.46rem;
            margin-top: 0.82rem;
            font-size: 11.5px;
            font-weight: 600;
            line-height: 1.4;
            color: #334155;
        }

        .mobile-dashboard-scope-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.88rem;
        }

        .mobile-dashboard-chip {
            font-size: 11px;
            font-weight: 700;
        }

        .mobile-dashboard-screen .mobile-stat-grid {
            gap: 0.65rem;
        }

        .mobile-dashboard-screen .mobile-stat-tile {
            position: relative;
            overflow: hidden;
            min-width: 0;
            border-radius: 1.12rem;
            border: 1px solid rgba(203, 213, 225, 0.72);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.94));
            padding: 0.86rem 0.88rem;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.045);
        }

        .mobile-dashboard-screen .mobile-stat-tile::after {
            content: '';
            position: absolute;
            inset: auto -18% -35% auto;
            height: 4rem;
            width: 4rem;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(148, 163, 184, 0.12), transparent 72%);
            pointer-events: none;
        }

        .mobile-dashboard-screen .mobile-stat-tile.primary {
            border-color: rgba(125, 211, 252, 0.52);
            background:
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.18), transparent 42%),
                linear-gradient(180deg, rgba(239, 246, 255, 0.98), rgba(250, 252, 255, 0.98));
        }

        .mobile-dashboard-screen .mobile-stat-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.18em;
            color: #94a3b8;
        }

        .mobile-dashboard-screen .mobile-stat-value {
            margin-top: 0.42rem;
            font-size: 1.92rem;
            font-weight: 760;
            letter-spacing: -0.045em;
            line-height: 0.98;
            color: #0f172a;
        }

        .mobile-dashboard-screen .mobile-stat-note {
            margin-top: 0.32rem;
            max-width: 7rem;
            font-size: 11px;
            line-height: 1.42;
            color: #64748b;
        }

        .mobile-dashboard-module {
            padding: 0.95rem;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(250, 252, 255, 0.94));
            border-color: rgba(226, 232, 240, 0.9);
        }

        .mobile-dashboard-section-bar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.8rem;
            margin-bottom: 0.8rem;
        }

        .mobile-dashboard-section-title {
            font-size: 15px;
            font-weight: 720;
            letter-spacing: -0.025em;
            color: #0f172a;
        }

        .mobile-dashboard-section-copy {
            margin-top: 0.25rem;
            max-width: 18rem;
            font-size: 12px;
            line-height: 1.48;
            color: #64748b;
        }

        .mobile-dashboard-section-tools {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            flex: 0 0 auto;
        }

        .mobile-dashboard-section-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.2rem;
            height: 2rem;
            border-radius: 999px;
            border: 1px solid rgba(191, 219, 254, 0.95);
            background: rgba(239, 246, 255, 0.96);
            padding: 0 0.68rem;
            font-size: 11px;
            font-weight: 800;
            line-height: 1;
            color: #0f172a;
        }

        .mobile-dashboard-section-count.neutral {
            border-color: rgba(226, 232, 240, 0.95);
            background: rgba(248, 250, 252, 0.98);
            color: #475569;
        }

        .mobile-dashboard-section-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 2rem;
            min-width: 2rem;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: rgba(255, 255, 255, 0.96);
            padding: 0 0.72rem;
            font-size: 11px;
            font-weight: 700;
            color: #334155;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.04);
            transition: transform 160ms ease, box-shadow 160ms ease;
        }

        .mobile-dashboard-section-action.icon-only {
            width: 2rem;
            padding: 0;
        }

        .mobile-dashboard-section-action:active {
            transform: scale(0.97);
        }

        .mobile-dashboard-section-action.text {
            min-width: auto;
            padding: 0 0.86rem;
        }

        .mobile-dashboard-list,
        .mobile-dashboard-skeleton-list {
            display: grid;
            gap: 0.58rem;
        }

        .mobile-dashboard-card,
        .mobile-dashboard-skeleton-card {
            position: relative;
            overflow: hidden;
            border-radius: 1.02rem;
            border: 1px solid rgba(226, 232, 240, 0.88);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.94));
            padding: 0.88rem 0.9rem;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.035);
        }

        .mobile-dashboard-card::before,
        .mobile-dashboard-skeleton-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0.78rem;
            bottom: 0.78rem;
            width: 3px;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.3);
        }

        .mobile-dashboard-card.run::before,
        .mobile-dashboard-skeleton-card.run::before {
            background: linear-gradient(180deg, rgba(16, 185, 129, 0.9), rgba(5, 150, 105, 0.45));
        }

        .mobile-dashboard-card.alert::before,
        .mobile-dashboard-skeleton-card.alert::before {
            background: linear-gradient(180deg, rgba(244, 63, 94, 0.92), rgba(225, 29, 72, 0.46));
        }

        .mobile-dashboard-card.due::before,
        .mobile-dashboard-skeleton-card.due::before {
            background: linear-gradient(180deg, rgba(14, 165, 233, 0.9), rgba(2, 132, 199, 0.45));
        }

        .mobile-dashboard-card.sync::before,
        .mobile-dashboard-skeleton-card.sync::before {
            background: linear-gradient(180deg, rgba(245, 158, 11, 0.92), rgba(217, 119, 6, 0.46));
        }

        .mobile-dashboard-card.is-link {
            transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease;
        }

        .mobile-dashboard-card.is-link:active {
            transform: scale(0.992);
        }

        .mobile-dashboard-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 0.82rem;
            align-items: center;
        }

        .mobile-dashboard-skeleton-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 0.82rem;
            align-items: center;
        }

        .mobile-dashboard-card-main,
        .mobile-dashboard-skeleton-main {
            min-width: 0;
        }

        .mobile-dashboard-card-side,
        .mobile-dashboard-skeleton-side {
            display: flex;
            min-width: 4.85rem;
            flex-direction: column;
            align-items: flex-end;
            justify-content: space-between;
            gap: 0.48rem;
            align-self: stretch;
        }

        .mobile-dashboard-card-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.38rem;
            min-width: 0;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-dashboard-card-dot {
            height: 0.42rem;
            width: 0.42rem;
            border-radius: 999px;
            flex: 0 0 auto;
            background: #94a3b8;
        }

        .mobile-dashboard-card-dot.run {
            background: #10b981;
        }

        .mobile-dashboard-card-dot.alert {
            background: #f43f5e;
        }

        .mobile-dashboard-card-dot.due {
            background: #0ea5e9;
        }

        .mobile-dashboard-card-dot.sync {
            background: #f59e0b;
        }

        .mobile-dashboard-card-title {
            margin-top: 0.36rem;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
            font-size: 13.5px;
            font-weight: 700;
            line-height: 1.34;
            letter-spacing: -0.015em;
            color: #0f172a;
        }

        .mobile-dashboard-card-context,
        .mobile-dashboard-card-detail {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .mobile-dashboard-card-context {
            margin-top: 0.24rem;
            -webkit-line-clamp: 1;
            font-size: 11.5px;
            line-height: 1.42;
            color: #64748b;
        }

        .mobile-dashboard-card-detail {
            margin-top: 0.3rem;
            -webkit-line-clamp: 2;
            font-size: 12px;
            line-height: 1.48;
            color: #475569;
        }

        .mobile-dashboard-card-detail.problem {
            color: #dc2626;
            font-weight: 600;
        }

        .mobile-dashboard-card-time {
            text-align: right;
            white-space: nowrap;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.35;
            color: #64748b;
        }

        .mobile-dashboard-card-state {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 4.2rem;
            border-radius: 999px;
            padding: 0.3rem 0.58rem;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            border: 1px solid rgba(148, 163, 184, 0.16);
            color: #475569;
            background: rgba(248, 250, 252, 0.95);
        }

        .mobile-dashboard-card-state.run {
            border-color: rgba(16, 185, 129, 0.18);
            color: #047857;
            background: rgba(16, 185, 129, 0.08);
        }

        .mobile-dashboard-card-state.alert {
            border-color: rgba(244, 63, 94, 0.18);
            color: #be123c;
            background: rgba(244, 63, 94, 0.08);
        }

        .mobile-dashboard-card-state.due {
            border-color: rgba(14, 165, 233, 0.18);
            color: #0369a1;
            background: rgba(14, 165, 233, 0.08);
        }

        .mobile-dashboard-card-state.sync {
            border-color: rgba(245, 158, 11, 0.18);
            color: #b45309;
            background: rgba(245, 158, 11, 0.08);
        }

        .mobile-dashboard-skeleton-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.75), transparent);
            transform: translateX(-100%);
            animation: mobile-dashboard-shimmer 1.35s infinite;
        }

        .mobile-dashboard-skeleton-pill,
        .mobile-dashboard-skeleton-line,
        .mobile-dashboard-skeleton-badge {
            display: block;
            border-radius: 999px;
            background: rgba(226, 232, 240, 0.92);
        }

        .mobile-dashboard-skeleton-pill {
            height: 0.7rem;
            width: 4.9rem;
        }

        .mobile-dashboard-skeleton-line {
            margin-top: 0.46rem;
            height: 0.74rem;
        }

        .mobile-dashboard-skeleton-line.lg {
            width: 76%;
        }

        .mobile-dashboard-skeleton-line.md {
            width: 58%;
        }

        .mobile-dashboard-skeleton-line.sm {
            width: 42%;
        }

        .mobile-dashboard-skeleton-line.xs {
            width: 3.2rem;
            margin-top: auto;
            align-self: flex-end;
        }

        .mobile-dashboard-skeleton-badge {
            height: 1.45rem;
            width: 4.2rem;
            margin-top: 0.18rem;
        }

        .mobile-dashboard-screen .mobile-accordion-label {
            font-size: 10px;
            letter-spacing: 0.14em;
        }

        .mobile-dashboard-screen .mobile-accordion-value {
            font-size: 12px;
            line-height: 1.5;
        }

        .mobile-dashboard-screen .mobile-panel p.text-\[10px\],
        .mobile-dashboard-screen .rounded-\[1rem\] p.text-\[10px\],
        .mobile-dashboard-screen .rounded-\[1\.1rem\] p.text-\[10px\] {
            font-size: 10px;
        }

        .mobile-dashboard-screen .mobile-panel p.text-\[12px\],
        .mobile-dashboard-screen .rounded-\[1rem\] p.text-\[12px\],
        .mobile-dashboard-screen .rounded-\[1\.1rem\] p.text-\[12px\] {
            font-size: 12px;
            line-height: 1.45;
        }

        .mobile-dashboard-viewall-shell {
            position: fixed;
            inset: 0;
            z-index: 55;
            pointer-events: none;
        }

        .mobile-dashboard-viewall-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.18);
            opacity: 0;
            transition: opacity 220ms ease;
        }

        .mobile-dashboard-viewall-drawer {
            position: absolute;
            left: 50%;
            top: var(--mobile-dashboard-overlay-top, 4.4rem);
            bottom: calc(5rem + env(safe-area-inset-bottom) + 0.25rem);
            width: min(100%, 440px);
            transform: translate3d(calc(-50% + 108%), 0, 0);
            opacity: 0;
            transition: transform 320ms cubic-bezier(0.22, 1, 0.36, 1), opacity 220ms ease;
            border-radius: 1.35rem;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.96));
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 20px 44px rgba(15, 23, 42, 0.16);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            pointer-events: auto;
            overscroll-behavior: contain;
            touch-action: pan-y;
        }

        .mobile-dashboard-viewall-shell.open {
            pointer-events: auto;
        }

        .mobile-dashboard-viewall-shell.open .mobile-dashboard-viewall-backdrop {
            opacity: 1;
        }

        .mobile-dashboard-viewall-shell.open .mobile-dashboard-viewall-drawer {
            transform: translate3d(-50%, 0, 0);
            opacity: 1;
        }

        .mobile-dashboard-viewall-head {
            position: sticky;
            top: 0;
            z-index: 2;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.8rem;
            padding: 0.95rem 0.95rem 0.78rem;
            border-bottom: 1px solid rgba(226, 232, 240, 0.82);
            background:
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.12), transparent 42%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.94));
            backdrop-filter: blur(16px);
        }

        .mobile-dashboard-viewall-nav {
            display: inline-flex;
            align-items: center;
            gap: 0.42rem;
            margin-bottom: 0.58rem;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.92);
            background: rgba(255, 255, 255, 0.92);
            padding: 0.42rem 0.72rem;
            font-size: 11px;
            font-weight: 700;
            color: #334155;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.04);
        }

        .mobile-dashboard-viewall-head-copy {
            min-width: 0;
            flex: 1 1 auto;
        }

        .mobile-dashboard-viewall-kicker {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-dashboard-viewall-title {
            margin-top: 0.35rem;
            font-size: 1.05rem;
            font-weight: 760;
            letter-spacing: -0.025em;
            color: #0f172a;
        }

        .mobile-dashboard-viewall-copy {
            margin-top: 0.3rem;
            font-size: 12px;
            line-height: 1.48;
            color: #64748b;
        }

        .mobile-dashboard-viewall-body {
            display: flex;
            min-height: 0;
            max-height: 100%;
            flex: 1 1 auto;
            flex-direction: column;
            gap: 0.8rem;
            overflow-y: auto;
            padding: 0.85rem 0.95rem 0.95rem;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
            touch-action: pan-y;
        }

        .mobile-dashboard-viewall-more {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            align-self: center;
            min-width: 8rem;
            height: 2.45rem;
            border-radius: 999px;
            border: 1px solid rgba(191, 219, 254, 0.95);
            background: rgba(239, 246, 255, 0.96);
            padding: 0 1rem;
            font-size: 11px;
            font-weight: 800;
            color: #0369a1;
            box-shadow: 0 10px 18px rgba(14, 165, 233, 0.08);
        }
    </style>
@endpush

@section('content')
    <div class="mobile-dashboard-screen">
        <section class="mobile-dashboard-hero mobile-panel">
            <div class="mobile-dashboard-hero-head">
                <div class="min-w-0">
                    <span class="mobile-dashboard-ribbon">Operations desk</span>
                    <h1 class="mobile-dashboard-hero-title">Dashboard</h1>
                    <p class="mobile-dashboard-hero-copy">{{ $dashboardDescription }}</p>
                </div>
                <button
                    type="button"
                    onclick="window.refreshMobileDashboard && window.refreshMobileDashboard()"
                    class="mobile-dashboard-refresh"
                    aria-label="Refresh dashboard"
                >
                    <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                </button>
            </div>

            <div class="mobile-dashboard-hero-foot">
                <p class="mobile-dashboard-hero-status">
                    <span class="mobile-dashboard-card-dot {{ $summary['displaysFailed'] > 0 ? 'alert' : 'run' }}"></span>
                    <span>{{ $summary['displaysFailed'] }} displays need follow-up in the current scope.</span>
                </p>

                <div class="mobile-dashboard-scope-row">
                    <span class="{{ $scopePill['class'] }} mobile-dashboard-chip">{{ $scopePill['label'] }}</span>
                    @if (optional($mobileUser?->facility)->name)
                        <span class="mobile-dashboard-chip rounded-full border border-slate-200 bg-white/90 px-3 py-1.5 text-slate-600 shadow-sm">{{ $mobileUser->facility->name }}</span>
                    @endif
                </div>
            </div>
        </section>

        <section class="mobile-stat-grid">
        <article class="mobile-stat-tile">
            <p class="mobile-stat-label">Displays OK</p>
            <p class="mobile-stat-value">{{ $summary['displaysOk'] }}</p>
            <p class="mobile-stat-note">in service</p>
        </article>
        <article class="mobile-stat-tile primary">
            <p class="mobile-stat-label">Displays Not OK</p>
            <p class="mobile-stat-value">{{ $summary['displaysFailed'] }}</p>
            <p class="mobile-stat-note">require follow-up</p>
        </article>
        <article class="mobile-stat-tile">
            <p class="mobile-stat-label">Workstations</p>
            <p class="mobile-stat-value">{{ $summary['workstations'] }}</p>
            <p class="mobile-stat-note">active records</p>
        </article>
        <article class="mobile-stat-tile">
            <p class="mobile-stat-label">Due Tasks</p>
            <p class="mobile-stat-value">{{ $summary['dueTasks'] }}</p>
            <p class="mobile-stat-note">scheduled items</p>
        </article>
        </section>

        <section class="mobile-section-gap mobile-dashboard-module mobile-panel" id="mobile-dashboard-failed-section" data-dashboard-section="failed">
            <div class="mobile-dashboard-section-bar">
                <div class="min-w-0">
                    <h2 class="mobile-dashboard-section-title">Displays that need attention</h2>
                    <p class="mobile-dashboard-section-copy">{{ $failedSectionDescription }}</p>
                </div>
                <div class="mobile-dashboard-section-tools">
                    <span class="mobile-dashboard-section-count">{{ $summary['displaysFailed'] }}</span>
                    <button type="button" class="mobile-dashboard-section-action text" data-dashboard-open="failed">View All</button>
                </div>
            </div>
            <div id="mobile-dashboard-failed-displays" class="mobile-dashboard-list">
                @for ($i = 0; $i < 3; $i++)
                    <div class="mobile-dashboard-skeleton-card alert">
                        <div class="mobile-dashboard-skeleton-main">
                            <span class="mobile-dashboard-skeleton-pill"></span>
                            <span class="mobile-dashboard-skeleton-line lg"></span>
                            <span class="mobile-dashboard-skeleton-line md"></span>
                            <span class="mobile-dashboard-skeleton-line sm"></span>
                        </div>
                        <div class="mobile-dashboard-skeleton-side">
                            <span class="mobile-dashboard-skeleton-badge"></span>
                            <span class="mobile-dashboard-skeleton-line xs"></span>
                        </div>
                    </div>
                @endfor
            </div>
        </section>

        <section class="mobile-section-gap mobile-dashboard-module mobile-panel" id="mobile-dashboard-latest-section" data-dashboard-section="latest">
            <div class="mobile-dashboard-section-bar">
                <div class="min-w-0">
                    <h2 class="mobile-dashboard-section-title">Recent activity</h2>
                    <p class="mobile-dashboard-section-copy">{{ $recentSectionDescription }}</p>
                </div>
                <div class="mobile-dashboard-section-tools">
                    <span id="mobile-dashboard-latest-count" class="mobile-dashboard-section-count neutral">0</span>
                    <button type="button" class="mobile-dashboard-section-action text" data-dashboard-open="latest">View All</button>
                </div>
            </div>
            <div id="mobile-dashboard-latest-performed" class="mobile-dashboard-list">
                @for ($i = 0; $i < 3; $i++)
                    <div class="mobile-dashboard-skeleton-card run">
                        <div class="mobile-dashboard-skeleton-main">
                            <span class="mobile-dashboard-skeleton-pill"></span>
                            <span class="mobile-dashboard-skeleton-line lg"></span>
                            <span class="mobile-dashboard-skeleton-line md"></span>
                            <span class="mobile-dashboard-skeleton-line sm"></span>
                        </div>
                        <div class="mobile-dashboard-skeleton-side">
                            <span class="mobile-dashboard-skeleton-badge"></span>
                            <span class="mobile-dashboard-skeleton-line xs"></span>
                        </div>
                    </div>
                @endfor
            </div>
        </section>

        <section class="mobile-section-gap mobile-dashboard-module mobile-panel" id="mobile-dashboard-due-section" data-dashboard-section="due">
            <div class="mobile-dashboard-section-bar">
                <div class="min-w-0">
                    <h2 class="mobile-dashboard-section-title">Upcoming maintenance pipeline</h2>
                    <p class="mobile-dashboard-section-copy">{{ $dueSectionDescription }}</p>
                </div>
                <div class="mobile-dashboard-section-tools">
                    <span id="mobile-dashboard-due-count" class="mobile-dashboard-section-count">{{ $summary['dueTasks'] }}</span>
                    <button type="button" class="mobile-dashboard-section-action text" data-dashboard-open="due">View All</button>
                </div>
            </div>
            <div id="mobile-dashboard-due-tasks" class="mobile-dashboard-list">
                @for ($i = 0; $i < 3; $i++)
                    <div class="mobile-dashboard-skeleton-card due">
                        <div class="mobile-dashboard-skeleton-main">
                            <span class="mobile-dashboard-skeleton-pill"></span>
                            <span class="mobile-dashboard-skeleton-line lg"></span>
                            <span class="mobile-dashboard-skeleton-line md"></span>
                            <span class="mobile-dashboard-skeleton-line sm"></span>
                        </div>
                        <div class="mobile-dashboard-skeleton-side">
                            <span class="mobile-dashboard-skeleton-badge"></span>
                            <span class="mobile-dashboard-skeleton-line xs"></span>
                        </div>
                    </div>
                @endfor
            </div>
        </section>

        <section class="mobile-section-gap mobile-dashboard-module mobile-panel" id="mobile-dashboard-watchlist-section" data-dashboard-section="watchlist">
            <div class="mobile-dashboard-section-bar">
                <div class="min-w-0">
                    <h2 class="mobile-dashboard-section-title">Offline / stale workstations</h2>
                    <p class="mobile-dashboard-section-copy">Workstations that have not reported a recent sync heartbeat.</p>
                </div>
                <div class="mobile-dashboard-section-tools">
                    <span id="mobile-dashboard-watchlist-count" class="mobile-dashboard-section-count">{{ $summary['staleWorkstations'] }}</span>
                    <button type="button" class="mobile-dashboard-section-action text" data-dashboard-open="watchlist">View All</button>
                </div>
            </div>
            <div id="mobile-dashboard-watchlist" class="mobile-dashboard-list">
                @for ($i = 0; $i < 2; $i++)
                    <div class="mobile-dashboard-skeleton-card sync">
                        <div class="mobile-dashboard-skeleton-main">
                            <span class="mobile-dashboard-skeleton-pill"></span>
                            <span class="mobile-dashboard-skeleton-line lg"></span>
                            <span class="mobile-dashboard-skeleton-line md"></span>
                            <span class="mobile-dashboard-skeleton-line sm"></span>
                        </div>
                        <div class="mobile-dashboard-skeleton-side">
                            <span class="mobile-dashboard-skeleton-badge"></span>
                            <span class="mobile-dashboard-skeleton-line xs"></span>
                        </div>
                    </div>
                @endfor
            </div>
        </section>

    @unless($isUserDashboard)
        <section class="mobile-section-gap mobile-dashboard-module mobile-panel">
            <div class="mobile-dashboard-section-bar">
                <div class="min-w-0">
                    <h2 class="mobile-dashboard-section-title">Remote Portal Info</h2>
                    <p class="mobile-dashboard-section-copy">Use these values to connect a remote client to this environment without leaving the dashboard.</p>
                </div>
                <div class="mobile-dashboard-section-tools">
                    <span class="mobile-dashboard-section-count neutral">Sync</span>
                    <a href="https://qubyx.com/product/remote-server/" target="_blank" class="mobile-dashboard-section-action icon-only" aria-label="Download remote client">
                        <i data-lucide="download" class="h-4 w-4"></i>
                    </a>
                </div>
            </div>

            <div class="pt-1">
                <div class="rounded-[1.1rem] border border-slate-200 bg-slate-50/80 p-3.5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Endpoint URL</p>
                            <p class="mt-2 break-all text-[12px] font-semibold leading-5 text-slate-700">{{ url('/') }}</p>
                        </div>
                        <button type="button" class="mobile-accordion-action secondary" style="margin-top:0" onclick="window.copyMobileDashboardValue && window.copyMobileDashboardValue(@js(url('/')))">Copy</button>
                    </div>
                </div>

                <div class="mt-3 grid grid-cols-2 gap-3">
                    <div class="rounded-[1rem] border border-slate-200 bg-slate-50/80 p-3.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Service ID</p>
                                <p class="mt-2 break-all text-[12px] font-semibold leading-5 text-slate-700">{{ $mobileUser->sync_user ?? '-' }}</p>
                            </div>
                            <button type="button" class="mobile-accordion-action secondary" style="margin-top:0" onclick="window.copyMobileDashboardValue && window.copyMobileDashboardValue(@js($mobileUser->sync_user ?? ''))">Copy</button>
                        </div>
                    </div>

                    <div class="rounded-[1rem] border border-slate-200 bg-slate-50/80 p-3.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Token PK</p>
                                <p class="mt-2 break-all text-[12px] font-semibold leading-5 text-slate-700">{{ $mobileUser->sync_password_raw ?? '-' }}</p>
                            </div>
                            <button type="button" class="mobile-accordion-action secondary" style="margin-top:0" onclick="window.copyMobileDashboardValue && window.copyMobileDashboardValue(@js($mobileUser->sync_password_raw ?? ''))">Copy</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endunless

    @if($isAdminDashboard)
        <section class="mobile-section-gap mobile-dashboard-module mobile-panel">
            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Facility Admin Scope</p>
            <h3 class="mt-2 mobile-section-title">You are managing one facility workspace</h3>
            <p class="mt-1 text-[12px] leading-5 text-slate-500">Bulk actions, task scheduling, and workstation changes remain scoped to {{ optional($mobileUser?->facility)->name ?? 'your assigned facility' }}.</p>
            <div class="mt-3 grid grid-cols-3 gap-2">
                <div class="rounded-[1rem] border border-slate-200 bg-slate-50/80 px-3 py-2.5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Facility</p>
                    <p class="mt-2 text-[12px] font-semibold leading-5 text-slate-800">{{ optional($mobileUser?->facility)->name ?? 'Assigned facility' }}</p>
                </div>
                <div class="rounded-[1rem] border border-slate-200 bg-slate-50/80 px-3 py-2.5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Workstations</p>
                    <p class="mt-2 text-[12px] font-semibold leading-5 text-slate-800">{{ $summary['workstations'] }} active</p>
                </div>
                <div class="rounded-[1rem] border border-slate-200 bg-slate-50/80 px-3 py-2.5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Due Tasks</p>
                    <p class="mt-2 text-[12px] font-semibold leading-5 text-slate-800">{{ $summary['dueTasks'] }} scheduled</p>
                </div>
            </div>
        </section>
    @elseif($isUserDashboard)
        <section class="mobile-section-gap mobile-dashboard-module mobile-panel">
            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Read-only Workspace</p>
            <h3 class="mt-2 mobile-section-title">This dashboard is designed for monitoring</h3>
            <p class="mt-1 text-[12px] leading-5 text-slate-500">You can review workstation health, schedule pressure, and recent activity, but task management stays hidden.</p>
            <div class="mt-3 grid grid-cols-3 gap-2">
                <div class="rounded-[1rem] border border-slate-200 bg-slate-50/80 px-3 py-2.5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Facility</p>
                    <p class="mt-2 text-[12px] font-semibold leading-5 text-slate-800">{{ optional($mobileUser?->facility)->name ?? 'Assigned facility' }}</p>
                </div>
                <div class="rounded-[1rem] border border-slate-200 bg-slate-50/80 px-3 py-2.5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Displays Not OK</p>
                    <p class="mt-2 text-[12px] font-semibold leading-5 text-slate-800">{{ $summary['displaysFailed'] }} to review</p>
                </div>
                <div class="rounded-[1rem] border border-slate-200 bg-slate-50/80 px-3 py-2.5">
                    <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-slate-400">Due Tasks</p>
                    <p class="mt-2 text-[12px] font-semibold leading-5 text-slate-800">{{ $summary['dueTasks'] }} upcoming</p>
                </div>
            </div>
        </section>
    @endif

    </div>

    @push('modals')
        <div id="mobile-dashboard-viewall-shell" class="mobile-dashboard-viewall-shell" aria-hidden="true">
            <div class="mobile-dashboard-viewall-backdrop" data-dashboard-close="1"></div>
            <section class="mobile-dashboard-viewall-drawer" aria-label="Dashboard detail panel">
                <div class="mobile-dashboard-viewall-head">
                    <button type="button" class="mobile-dashboard-section-action icon-only" data-dashboard-close="1" aria-label="Back to dashboard">
                        <i data-lucide="arrow-left" class="h-4 w-4"></i>
                    </button>
                    <div class="mobile-dashboard-viewall-head-copy">
                        <button type="button" class="mobile-dashboard-viewall-nav" data-dashboard-close="1" aria-label="Back to dashboard">
                            <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
                            <span>Back to dashboard</span>
                        </button>
                        <p id="mobile-dashboard-viewall-kicker" class="mobile-dashboard-viewall-kicker">Dashboard detail</p>
                        <h2 id="mobile-dashboard-viewall-title" class="mobile-dashboard-viewall-title">All items</h2>
                        <p id="mobile-dashboard-viewall-copy" class="mobile-dashboard-viewall-copy">Review the full list without leaving the dashboard.</p>
                    </div>
                    <span id="mobile-dashboard-viewall-count" class="mobile-dashboard-section-count neutral">0</span>
                </div>
                <div class="mobile-dashboard-viewall-body">
                    <div id="mobile-dashboard-viewall-list" class="mobile-dashboard-list"></div>
                    <button id="mobile-dashboard-viewall-more" type="button" class="mobile-dashboard-viewall-more hidden">Load more</button>
                </div>
            </section>
        </div>
    @endpush

    @push('scripts')
        <script>
            (() => {
                const boot = () => window.Perfectlum.mountMobilePage('mobileDashboard', () => {
                    const failedRoot = document.getElementById('mobile-dashboard-failed-displays');
                    const latestRoot = document.getElementById('mobile-dashboard-latest-performed');
                    const dueRoot = document.getElementById('mobile-dashboard-due-tasks');
                    const watchlistRoot = document.getElementById('mobile-dashboard-watchlist');
                    const latestCount = document.getElementById('mobile-dashboard-latest-count');
                    const dueCount = document.getElementById('mobile-dashboard-due-count');
                    const watchlistCount = document.getElementById('mobile-dashboard-watchlist-count');
                    const viewAllShell = document.getElementById('mobile-dashboard-viewall-shell');
                    const viewAllKicker = document.getElementById('mobile-dashboard-viewall-kicker');
                    const viewAllTitle = document.getElementById('mobile-dashboard-viewall-title');
                    const viewAllCopy = document.getElementById('mobile-dashboard-viewall-copy');
                    const viewAllCount = document.getElementById('mobile-dashboard-viewall-count');
                    const viewAllList = document.getElementById('mobile-dashboard-viewall-list');
                    const viewAllMore = document.getElementById('mobile-dashboard-viewall-more');
                    const viewAllBody = viewAllShell?.querySelector('.mobile-dashboard-viewall-body') || null;
                    const appbarBack = document.getElementById('mobile-appbar-back');
                    const appbarTitle = document.getElementById('mobile-appbar-title');
                    const escapeHtml = window.Perfectlum.escapeHtml;

                    let sectionObserver = null;
                    let currentViewAllKey = null;
                    let viewAllState = null;
                    let previousHtmlOverflow = '';
                    let previousBodyOverflow = '';
                    let appbarCloseHandler = null;
                    const appbarSnapshot = {
                        title: appbarTitle?.textContent || 'Dashboard',
                        backHref: appbarBack?.getAttribute('href') || '',
                        backHidden: appbarBack?.classList.contains('hidden') || false,
                    };

                    const emptyState = (message) => `<div class="mobile-empty">${escapeHtml(message)}</div>`;
                    const skeletonMarkup = (count = 3, tone = 'run') => `
                        <div class="mobile-dashboard-skeleton-list">
                            ${Array.from({ length: count }, () => `
                                <div class="mobile-dashboard-skeleton-card ${tone}">
                                    <div class="mobile-dashboard-skeleton-main">
                                        <span class="mobile-dashboard-skeleton-pill"></span>
                                        <span class="mobile-dashboard-skeleton-line lg"></span>
                                        <span class="mobile-dashboard-skeleton-line md"></span>
                                        <span class="mobile-dashboard-skeleton-line sm"></span>
                                    </div>
                                    <div class="mobile-dashboard-skeleton-side">
                                        <span class="mobile-dashboard-skeleton-badge"></span>
                                        <span class="mobile-dashboard-skeleton-line xs"></span>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;

                    const compactCard = ({
                        tone = 'due',
                        label = '',
                        title,
                        context = '',
                        detail = '',
                        time = '',
                        status = '',
                        href = null,
                    }) => {
                        const Tag = href ? 'a' : 'div';
                        const hrefAttr = href ? ` href="${href}"` : '';
                        const roleAttr = href ? '' : ' role="group"';
                        return `
                            <${Tag}${hrefAttr}${roleAttr} class="mobile-dashboard-card ${tone} ${href ? 'is-link' : ''}">
                                <div class="mobile-dashboard-card-main">
                                    ${label ? `<span class="mobile-dashboard-card-kicker"><span class="mobile-dashboard-card-dot ${tone}"></span>${escapeHtml(label)}</span>` : ''}
                                    <p class="mobile-dashboard-card-title">${escapeHtml(title)}</p>
                                    ${context ? `<p class="mobile-dashboard-card-context">${escapeHtml(context)}</p>` : ''}
                                    ${detail ? `<p class="mobile-dashboard-card-detail ${tone === 'alert' ? 'problem' : ''}">${escapeHtml(detail)}</p>` : ''}
                                </div>
                                <div class="mobile-dashboard-card-side">
                                    ${time ? `<p class="mobile-dashboard-card-time">${escapeHtml(time)}</p>` : ''}
                                    ${status ? `<span class="mobile-dashboard-card-state ${tone}">${escapeHtml(status)}</span>` : ''}
                                </div>
                            </${Tag}>
                        `;
                    };

                    const renderFailedDisplays = (rows) => {
                        failedRoot.innerHTML = rows.length
                            ? rows.map((item) => compactCard({
                                tone: 'alert',
                                label: 'Display',
                                title: item.displayName,
                                context: [item.wsName, item.wgName, item.facName].filter(Boolean).join(' • '),
                                detail: item.attentionText || 'No alert detail',
                                time: item.updatedAt,
                                status: 'Failed',
                                href: `${@json(url('/m/displays'))}/${item.id}`,
                            })).join('')
                            : emptyState('No failed displays detected.');
                    };

                    const renderLatestPerformed = (rows) => {
                        if (latestCount) {
                            latestCount.textContent = rows.length;
                        }
                        latestRoot.innerHTML = rows.length
                            ? rows.map((item) => compactCard({
                                tone: item.result === 'ok' ? 'run' : 'alert',
                                label: 'Run',
                                title: item.name,
                                context: item.displayName,
                                detail: [item.wsName, item.wgName, item.facName].filter(Boolean).join(' • '),
                                time: item.timeFormatted,
                                status: item.result === 'ok' ? 'Pass' : 'Fail',
                                href: item.displayId ? `${@json(url('/m/displays'))}/${item.displayId}` : null,
                            })).join('')
                            : emptyState('No recent activity is available.');
                    };

                    const renderDueTasks = (rows) => {
                        dueRoot.innerHTML = rows.length
                            ? rows.map((item) => compactCard({
                                tone: 'due',
                                label: 'Task',
                                title: item.taskName,
                                context: item.displayName,
                                detail: [item.wsName, item.wgName, item.scheduleName].filter(Boolean).join(' • '),
                                time: item.dueAt,
                                status: item.overdue || item.status || 'Due',
                                href: `${@json(url('/m/displays'))}/${item.displayId}`,
                            })).join('')
                            : emptyState('No due tasks are waiting right now.');
                    };

                    const renderWatchlist = (rows) => {
                        watchlistRoot.innerHTML = rows.length
                            ? rows.map((item) => compactCard({
                                tone: 'sync',
                                label: 'Client',
                                title: item.name,
                                context: [item.wgName, item.facName].filter(Boolean).join(' • '),
                                detail: item.lastConnected && item.lastConnected !== '-'
                                    ? `Last seen ${item.lastSeenRelative}`
                                    : 'No sync data received',
                                time: item.lastConnected && item.lastConnected !== '-' ? item.lastConnected : 'No sync',
                                status: `${item.displaysCount} displays`,
                                href: `${@json(route('mobile.displays'))}?facility_id=${encodeURIComponent(item.facId)}&workgroup_id=${encodeURIComponent(item.wgId)}&workstation_id=${encodeURIComponent(item.id)}&facility_name=${encodeURIComponent(item.facName || '')}&workgroup_name=${encodeURIComponent(item.wgName || '')}&workstation_name=${encodeURIComponent(item.name || '')}`,
                            })).join('')
                            : emptyState('No stale connection records were found.');
                    };

                    const sectionDefs = {
                        failed: {
                            root: failedRoot,
                            countEl: null,
                            countValue: null,
                            tone: 'alert',
                            skeletonCount: 3,
                            endpoint: '/api/displays?type=failed&sort=updated_at&order=desc&limit=10&page=1',
                            transform: (payload) => payload.data || [],
                            render: renderFailedDisplays,
                            errorMessage: 'Unable to load failed displays.',
                        },
                        latest: {
                            root: latestRoot,
                            countEl: latestCount,
                            countValue: (_payload, rows) => rows.length,
                            tone: 'run',
                            skeletonCount: 3,
                            endpoint: '/api/latest-performed?limit=10',
                            transform: (payload) => payload || [],
                            render: renderLatestPerformed,
                            errorMessage: 'Unable to load recent activity.',
                        },
                        due: {
                            root: dueRoot,
                            countEl: dueCount,
                            countValue: (payload, rows) => payload.total ?? rows.length,
                            tone: 'due',
                            skeletonCount: 3,
                            endpoint: '/api/due-tasks?limit=10',
                            transform: (payload) => payload.data || [],
                            render: renderDueTasks,
                            errorMessage: 'Unable to load due tasks.',
                        },
                        watchlist: {
                            root: watchlistRoot,
                            countEl: watchlistCount,
                            countValue: (payload, rows) => payload.total ?? rows.length,
                            tone: 'sync',
                            skeletonCount: 2,
                            endpoint: '/api/connection-watchlist?limit=5',
                            transform: (payload) => payload.data || [],
                            render: renderWatchlist,
                            errorMessage: 'Unable to load watchlist.',
                        },
                    };

                    const viewAllDefs = {
                        failed: {
                            kicker: 'Home / displays',
                            title: 'All displays needing attention',
                            copy: 'Review the full failed-display queue without leaving the dashboard.',
                            countLabel: 'Displays',
                            tone: 'alert',
                            mode: 'page',
                            pageSize: 20,
                            buildUrl: (page, pageSize) => `/api/displays?type=failed&sort=updated_at&order=desc&limit=${pageSize}&page=${page}`,
                            transform: (payload, state) => ({
                                rows: payload.data || [],
                                total: payload.total ?? (payload.data || []).length,
                                hasMore: (payload.total ?? 0) > ((state?.page || 1) * (state?.pageSize || 20)),
                            }),
                            renderRows: (rows) => rows.map((item) => compactCard({
                                tone: 'alert',
                                label: 'Display',
                                title: item.displayName,
                                context: [item.wsName, item.wgName, item.facName].filter(Boolean).join(' • '),
                                detail: item.attentionText || 'No alert detail',
                                time: item.updatedAt,
                                status: 'Failed',
                                href: `${@json(url('/m/displays'))}/${item.id}`,
                            })).join(''),
                            empty: 'No failed displays detected.',
                            error: 'Unable to load the full failed-display list.',
                        },
                        latest: {
                            kicker: 'Home / activity',
                            title: 'All recent activity',
                            copy: 'Browse the latest completed runs in the current scope.',
                            countLabel: 'Runs',
                            tone: 'run',
                            mode: 'single',
                            buildUrl: () => '/api/latest-performed?limit=50',
                            transform: (payload) => ({
                                rows: payload || [],
                                total: (payload || []).length,
                                hasMore: false,
                            }),
                            renderRows: (rows) => rows.map((item) => compactCard({
                                tone: item.result === 'ok' ? 'run' : 'alert',
                                label: 'Run',
                                title: item.name,
                                context: item.displayName,
                                detail: [item.wsName, item.wgName, item.facName].filter(Boolean).join(' • '),
                                time: item.timeFormatted,
                                status: item.result === 'ok' ? 'Pass' : 'Fail',
                                href: item.displayId ? `${@json(url('/m/displays'))}/${item.displayId}` : null,
                            })).join(''),
                            empty: 'No recent activity is available.',
                            error: 'Unable to load the full recent activity list.',
                        },
                        due: {
                            kicker: 'Home / maintenance',
                            title: 'All upcoming maintenance',
                            copy: 'Work through the full due-task pipeline from the dashboard surface.',
                            countLabel: 'Tasks',
                            tone: 'due',
                            mode: 'page',
                            pageSize: 20,
                            buildUrl: (page, pageSize) => `/api/due-tasks?limit=${pageSize}&page=${page}`,
                            transform: (payload, state) => ({
                                rows: payload.data || [],
                                total: payload.total ?? (payload.data || []).length,
                                hasMore: (payload.total ?? 0) > ((state?.page || 1) * (state?.pageSize || 20)),
                            }),
                            renderRows: (rows) => rows.map((item) => compactCard({
                                tone: 'due',
                                label: 'Task',
                                title: item.taskName,
                                context: item.displayName,
                                detail: [item.wsName, item.wgName, item.scheduleName].filter(Boolean).join(' • '),
                                time: item.dueAt,
                                status: item.overdue || item.status || 'Due',
                                href: `${@json(url('/m/displays'))}/${item.displayId}`,
                            })).join(''),
                            empty: 'No due tasks are waiting right now.',
                            error: 'Unable to load the full maintenance pipeline.',
                        },
                        watchlist: {
                            kicker: 'Home / connections',
                            title: 'All stale connections',
                            copy: 'Scan the full watchlist of workstations with stale or missing sync activity.',
                            countLabel: 'Clients',
                            tone: 'sync',
                            mode: 'single',
                            buildUrl: () => '/api/connection-watchlist?limit=20',
                            transform: (payload) => ({
                                rows: payload.data || [],
                                total: payload.total ?? (payload.data || []).length,
                                hasMore: false,
                            }),
                            renderRows: (rows) => rows.map((item) => compactCard({
                                tone: 'sync',
                                label: 'Client',
                                title: item.name,
                                context: [item.wgName, item.facName].filter(Boolean).join(' • '),
                                detail: item.lastConnected && item.lastConnected !== '-'
                                    ? `Last seen ${item.lastSeenRelative}`
                                    : 'No sync data received',
                                time: item.lastConnected && item.lastConnected !== '-' ? item.lastConnected : 'No sync',
                                status: `${item.displaysCount} displays`,
                                href: `${@json(route('mobile.displays'))}?facility_id=${encodeURIComponent(item.facId)}&workgroup_id=${encodeURIComponent(item.wgId)}&workstation_id=${encodeURIComponent(item.id)}&facility_name=${encodeURIComponent(item.facName || '')}&workgroup_name=${encodeURIComponent(item.wgName || '')}&workstation_name=${encodeURIComponent(item.name || '')}`,
                            })).join(''),
                            empty: 'No stale connection records were found.',
                            error: 'Unable to load the full connection watchlist.',
                        },
                    };

                    const renderSkeleton = (key) => {
                        const section = sectionDefs[key];
                        if (!section?.root) {
                            return;
                        }

                        section.root.innerHTML = skeletonMarkup(section.skeletonCount, section.tone);
                    };

                    const loadSection = async (key, { force = false } = {}) => {
                        const section = sectionDefs[key];
                        if (!section?.root) {
                            return;
                        }

                        if (section.loading) {
                            return;
                        }

                        if (section.loaded && !force) {
                            return;
                        }

                        section.loading = true;
                        renderSkeleton(key);

                        try {
                            const payload = await window.Perfectlum.request(section.endpoint);
                            const rows = section.transform(payload);
                            section.render(rows);

                            if (section.countEl && typeof section.countValue === 'function') {
                                section.countEl.textContent = section.countValue(payload, rows);
                            }

                            section.loaded = true;
                        } catch (error) {
                            section.root.innerHTML = emptyState(section.errorMessage);
                        } finally {
                            section.loading = false;
                        }
                    };

                    const setupLazySections = () => {
                        if (sectionObserver) {
                            sectionObserver.disconnect();
                        }

                        sectionObserver = new IntersectionObserver((entries) => {
                            entries.forEach((entry) => {
                                if (!entry.isIntersecting) {
                                    return;
                                }

                                const key = entry.target.dataset.dashboardSection;
                                if (!key || !sectionDefs[key]) {
                                    return;
                                }

                                loadSection(key);
                                sectionObserver.unobserve(entry.target);
                            });
                        }, { rootMargin: '180px 0px' });

                        ['due', 'watchlist'].forEach((key) => {
                            const node = document.querySelector(`[data-dashboard-section="${key}"]`);
                            if (node) {
                                sectionObserver.observe(node);
                            }
                        });
                    };

                    const setViewAllLoading = (key) => {
                        const view = viewAllDefs[key];
                        if (!view || !viewAllList) {
                            return;
                        }

                        viewAllList.innerHTML = skeletonMarkup(view.mode === 'page' ? 5 : 4, view.tone);
                        if (viewAllMore) {
                            viewAllMore.classList.add('hidden');
                            viewAllMore.disabled = true;
                            viewAllMore.textContent = 'Load more';
                        }
                    };

                    const lockDashboardScroll = () => {
                        previousHtmlOverflow = document.documentElement.style.overflow;
                        previousBodyOverflow = document.body.style.overflow;
                        document.documentElement.style.overflow = 'hidden';
                        document.body.style.overflow = 'hidden';
                    };

                    const unlockDashboardScroll = () => {
                        document.documentElement.style.overflow = previousHtmlOverflow;
                        document.body.style.overflow = previousBodyOverflow;
                    };

                    const restoreAppbar = () => {
                        if (appbarTitle) {
                            appbarTitle.textContent = appbarSnapshot.title;
                        }

                        if (appbarBack) {
                            appbarBack.setAttribute('href', appbarSnapshot.backHref || '#');
                            if (appbarSnapshot.backHidden) {
                                appbarBack.classList.add('hidden');
                            } else {
                                appbarBack.classList.remove('hidden');
                            }

                            if (appbarCloseHandler) {
                                appbarBack.removeEventListener('click', appbarCloseHandler);
                                appbarCloseHandler = null;
                            }
                        }
                    };

                    const activateViewAllAppbar = (view) => {
                        if (appbarTitle) {
                            appbarTitle.textContent = view.title;
                        }

                        if (!appbarBack) {
                            return;
                        }

                        appbarBack.classList.remove('hidden');
                        appbarBack.setAttribute('href', '#');

                        if (appbarCloseHandler) {
                            appbarBack.removeEventListener('click', appbarCloseHandler);
                        }

                        appbarCloseHandler = (event) => {
                            event.preventDefault();
                            closeViewAll();
                        };

                        appbarBack.addEventListener('click', appbarCloseHandler);
                    };

                    const closeViewAll = () => {
                        if (!viewAllShell) {
                            return;
                        }

                        viewAllShell.classList.remove('open');
                        viewAllShell.setAttribute('aria-hidden', 'true');
                        currentViewAllKey = null;
                        viewAllState = null;
                        unlockDashboardScroll();
                        restoreAppbar();
                    };

                    const fetchViewAll = async ({ append = false } = {}) => {
                        if (!currentViewAllKey || !viewAllState || !viewAllList) {
                            return;
                        }

                        const view = viewAllDefs[currentViewAllKey];
                        if (!view || viewAllState.loading) {
                            return;
                        }

                        viewAllState.loading = true;

                        if (!append) {
                            setViewAllLoading(currentViewAllKey);
                        } else if (viewAllMore) {
                            viewAllMore.disabled = true;
                            viewAllMore.textContent = 'Loading...';
                        }

                        try {
                            const payload = await window.Perfectlum.request(view.buildUrl(viewAllState.page, viewAllState.pageSize));
                            const result = view.transform(payload, viewAllState);
                            const rows = result.rows || [];

                            viewAllState.rows = append ? viewAllState.rows.concat(rows) : rows;
                            viewAllState.total = result.total ?? viewAllState.rows.length;
                            viewAllState.hasMore = Boolean(result.hasMore);

                            viewAllList.innerHTML = viewAllState.rows.length
                                ? view.renderRows(viewAllState.rows)
                                : emptyState(view.empty);

                            if (viewAllCount) {
                                viewAllCount.textContent = viewAllState.total ?? viewAllState.rows.length;
                            }

                            if (viewAllMore) {
                                if (viewAllState.hasMore) {
                                    viewAllMore.classList.remove('hidden');
                                    viewAllMore.disabled = false;
                                    viewAllMore.textContent = 'Load more';
                                } else {
                                    viewAllMore.classList.add('hidden');
                                }
                            }
                        } catch (error) {
                            viewAllList.innerHTML = emptyState(view.error);
                            if (viewAllMore) {
                                viewAllMore.classList.add('hidden');
                            }
                        } finally {
                            viewAllState.loading = false;
                        }
                    };

                    const openViewAll = (key) => {
                        const view = viewAllDefs[key];
                        if (!view || !viewAllShell || !viewAllTitle || !viewAllCopy || !viewAllCount) {
                            return;
                        }

                        currentViewAllKey = key;
                        viewAllState = {
                            key,
                            page: 1,
                            pageSize: view.pageSize || 50,
                            rows: [],
                            total: 0,
                            hasMore: false,
                            loading: false,
                        };

                        if (viewAllKicker) {
                            viewAllKicker.textContent = view.kicker || 'Dashboard detail';
                        }
                        viewAllTitle.textContent = view.title;
                        viewAllCopy.textContent = view.copy;
                        viewAllCount.textContent = '0';
                        viewAllShell.setAttribute('aria-hidden', 'false');
                        viewAllShell.classList.add('open');
                        if (viewAllBody) {
                            viewAllBody.scrollTop = 0;
                        }

                        const appbarBottom = document.getElementById('mobile-appbar-shell')?.getBoundingClientRect().bottom || 64;
                        viewAllShell.style.setProperty('--mobile-dashboard-overlay-top', `${Math.max(16, appbarBottom + 6)}px`);

                        lockDashboardScroll();
                        activateViewAllAppbar(view);
                        fetchViewAll();
                    };

                    const loadDashboardSections = async ({ force = false, eagerAll = false } = {}) => {
                        Object.keys(sectionDefs).forEach((key) => {
                            if (force) {
                                sectionDefs[key].loaded = false;
                            }
                            renderSkeleton(key);
                        });

                        await Promise.all([
                            loadSection('failed', { force }),
                            loadSection('latest', { force }),
                        ]);

                        if (eagerAll) {
                            await Promise.all([
                                loadSection('due', { force }),
                                loadSection('watchlist', { force }),
                            ]);
                            return;
                        }

                        setupLazySections();
                    };

                    window.refreshMobileDashboard = () => loadDashboardSections({ force: true, eagerAll: true });
                    window.copyMobileDashboardValue = async (value) => {
                        if (!value) {
                            return;
                        }

                        try {
                            await navigator.clipboard.writeText(value);
                        } catch (error) {
                            const helper = document.createElement('textarea');
                            helper.value = value;
                            document.body.appendChild(helper);
                            helper.select();
                            document.execCommand('copy');
                            helper.remove();
                        }
                    };

                    document.querySelectorAll('[data-dashboard-open]').forEach((button) => {
                        button.addEventListener('click', () => {
                            openViewAll(button.dataset.dashboardOpen);
                        });
                    });

                    document.querySelectorAll('[data-dashboard-close]').forEach((button) => {
                        button.addEventListener('click', closeViewAll);
                    });

                    if (viewAllMore) {
                        viewAllMore.addEventListener('click', async () => {
                            if (!currentViewAllKey || !viewAllState) {
                                return;
                            }

                            if (viewAllDefs[currentViewAllKey]?.mode !== 'page') {
                                return;
                            }

                            viewAllState.page += 1;
                            await fetchViewAll({ append: true });
                        });
                    }

                    lucide.createIcons();
                    loadDashboardSections();
                    return () => {
                        if (sectionObserver) {
                            sectionObserver.disconnect();
                        }
                        closeViewAll();
                        delete window.refreshMobileDashboard;
                        delete window.copyMobileDashboardValue;
                    };
                });

                if (window.Perfectlum?.mountMobilePage) {
                    boot();
                    return;
                }

                (window.__perfectlumPageBoot = window.__perfectlumPageBoot || []).push(boot);
            })();
        </script>
    @endpush
@endsection
