@extends('mobile.layouts.app')

@php
    $summary = $settingsSummary ?? [];
    $role = $summary['role'] ?? ($mobileRole ?? 'user');
    $canSeeSite = $role === 'super';
    $canManage = in_array($role, ['super', 'admin'], true);
    $scopeLabel = $summary['application']['scopeLabel'] ?? 'Current scope';
@endphp

@push('head')
    <style>
        .mobile-settings-hub {
            display: grid;
            gap: 1rem;
        }

        .mobile-settings-intro,
        .mobile-settings-nav {
            border-radius: 1.35rem;
            border: 1px solid rgba(148, 163, 184, 0.14);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 12px 26px rgba(15, 23, 42, 0.04);
        }

        .mobile-settings-intro {
            padding: 1rem;
        }

        .mobile-settings-kicker {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .mobile-settings-title {
            margin-top: 0.28rem;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.08;
            letter-spacing: -0.04em;
            color: #0f172a;
        }

        .mobile-settings-copy {
            margin-top: 0.55rem;
            font-size: 12.5px;
            line-height: 1.65;
            color: #64748b;
        }

        .mobile-settings-scope-row {
            margin-top: 0.85rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.55rem;
        }

        .mobile-settings-pill {
            display: inline-flex;
            align-items: center;
            min-height: 1.95rem;
            border-radius: 999px;
            border: 1px solid rgba(191, 219, 254, 0.82);
            background: rgba(239, 246, 255, 0.9);
            padding: 0.36rem 0.7rem;
            font-size: 11px;
            font-weight: 700;
            color: #0369a1;
        }

        .mobile-settings-nav {
            overflow: hidden;
        }

        .mobile-settings-link {
            display: block;
            padding: 1rem;
        }

        .mobile-settings-link + .mobile-settings-link {
            border-top: 1px solid rgba(148, 163, 184, 0.14);
        }

        .mobile-settings-link-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.8rem;
        }

        .mobile-settings-link-title {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.03em;
            color: #0f172a;
        }

        .mobile-settings-link-copy {
            margin-top: 0.22rem;
            font-size: 12px;
            line-height: 1.55;
            color: #64748b;
        }

        .mobile-settings-link-meta {
            margin-top: 0.6rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
            font-size: 10.5px;
            color: #64748b;
        }

        .mobile-settings-link-meta span {
            display: inline-flex;
            align-items: center;
            min-height: 1.7rem;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            background: rgba(248, 250, 252, 0.94);
            padding: 0.24rem 0.58rem;
        }

        .mobile-settings-access {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            border: 1px solid rgba(191, 219, 254, 0.82);
            background: rgba(239, 246, 255, 0.95);
            padding: 0.26rem 0.62rem;
            font-size: 10px;
            font-weight: 700;
            color: #0369a1;
            white-space: nowrap;
        }

        .mobile-settings-note {
            border-radius: 1.15rem;
            border: 1px dashed rgba(148, 163, 184, 0.26);
            background: rgba(248, 250, 252, 0.7);
            padding: 0.9rem;
            font-size: 12px;
            line-height: 1.6;
            color: #64748b;
        }
    </style>
@endpush

@section('content')
    <div class="mobile-settings-hub">
        <section class="mobile-settings-intro">
            <p class="mobile-settings-kicker">Settings workspace</p>
            <h2 class="mobile-settings-title">Mobile settings</h2>
            <p class="mobile-settings-copy">The same configuration surfaces available on desktop are available here too. Pick a settings area below, then edit it in a mobile-ready workspace.</p>

            <div class="mobile-settings-scope-row">
                <span class="mobile-settings-pill">{{ strtoupper($role) }}</span>
                <span class="mobile-settings-pill">{{ $scopeLabel }}</span>
            </div>
        </section>

        <section class="mobile-settings-nav">
            @if($canSeeSite)
                <a href="{{ route('mobile.settings.site', ['return_to' => route('mobile.settings')]) }}" class="mobile-settings-link">
                    <div class="mobile-settings-link-top">
                        <div class="min-w-0 flex-1">
                            <p class="mobile-settings-link-title">Site Settings</p>
                            <p class="mobile-settings-link-copy">Branding, sender defaults, SMTP transport, and release creation for the full platform.</p>
                        </div>
                        <span class="mobile-settings-access">Super only</span>
                    </div>
                    <div class="mobile-settings-link-meta">
                        <span>Branding</span>
                        <span>SMTP</span>
                        <span>Release builder</span>
                    </div>
                </a>
            @endif

            @if($canManage)
                <a href="{{ route('mobile.settings.application', ['return_to' => route('mobile.settings')]) }}" class="mobile-settings-link">
                    <div class="mobile-settings-link-top">
                        <div class="min-w-0 flex-1">
                            <p class="mobile-settings-link-title">Application Settings</p>
                            <p class="mobile-settings-link-copy">Bulk workstation configuration with the same target browser, bulk editor, and save flow used on desktop.</p>
                        </div>
                        <span class="mobile-settings-access">{{ $role === 'super' ? 'Super' : 'Admin' }}</span>
                    </div>
                    <div class="mobile-settings-link-meta">
                        <span>Bulk selection</span>
                        <span>Application</span>
                        <span>Calibration</span>
                        <span>Quality assurance</span>
                    </div>
                </a>

                <a href="{{ route('mobile.settings.alerts', ['return_to' => route('mobile.settings')]) }}" class="mobile-settings-link">
                    <div class="mobile-settings-link-top">
                        <div class="min-w-0 flex-1">
                            <p class="mobile-settings-link-title">Alert Settings</p>
                            <p class="mobile-settings-link-copy">Alert recipients, error limits, and SMTP delivery with the same role rules as desktop.</p>
                        </div>
                        <span class="mobile-settings-access">{{ $role === 'super' ? 'Full access' : 'Scoped access' }}</span>
                    </div>
                    <div class="mobile-settings-link-meta">
                        <span>Email recipients</span>
                        <span>Error limits</span>
                        @if($role === 'super')
                            <span>SMTP delivery</span>
                        @endif
                    </div>
                </a>
            @endif
        </section>

        <div class="mobile-settings-note">
            Settings access follows the same rules as desktop: super can open all settings, admin can open application and alert settings inside their scope, and user accounts remain blocked from this workspace.
        </div>
    </div>
@endsection
