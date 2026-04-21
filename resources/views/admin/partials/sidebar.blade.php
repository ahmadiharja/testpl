@php
    $sidebarRole = $role ?? null;
    $canManageDesktop = in_array($sidebarRole, ['super', 'admin'], true);
    $canManageUsersDesktop = $canManageDesktop;

    $primaryLinks = array_values(array_filter([
        ['name' => 'Dashboard', 'icon' => 'layout-grid', 'url' => 'dashboard'],
        ['name' => 'Facilities', 'icon' => 'building-2', 'url' => route('facilities.management')],
        ['name' => 'Workgroups', 'icon' => 'network', 'url' => route('workgroups.management')],
        ['name' => 'Workstations', 'icon' => 'monitor-speaker', 'url' => route('workstations.management')],
        ['name' => 'Displays', 'icon' => 'monitor', 'url' => route('displays.management')],
        $canManageDesktop ? ['name' => 'Calibrate Display', 'icon' => 'crosshair', 'url' => route('displays.calibration')] : null,
        $canManageDesktop ? ['name' => 'Scheduler', 'icon' => 'calendar-days', 'url' => route('displays.scheduler')] : null,
        ['name' => 'History & Reports', 'icon' => 'files', 'url' => route('history.reports')],
        $canManageUsersDesktop ? ['name' => 'Users', 'icon' => 'users', 'url' => 'users-management'] : null,
    ]));

    $settingsLinks = array_values(array_filter([
        $sidebarRole === 'super' ? ['name' => 'Site Settings', 'icon' => 'settings-2'] : null,
        $canManageDesktop ? ['name' => 'Application Settings', 'icon' => 'sliders-horizontal'] : null,
        $canManageDesktop ? ['name' => 'Alert Settings', 'icon' => 'bell-ring'] : null,
        $sidebarRole === 'super' ? ['name' => 'Scope Explorer', 'icon' => 'folder-tree'] : null,
        $sidebarRole === 'super' ? ['name' => 'Client Monitor', 'icon' => 'terminal'] : null,
    ]));
@endphp

<aside id="desktop-sidebar-shell"
       data-desktop-shell-signature="{{ $desktopShellSignature ?? '' }}"
       class="h-screen shrink-0 overflow-hidden border-r transition-all duration-500"
       :class="[sidebarCollapsed ? 'w-[92px]' : 'w-[300px]', theme === 'perfectlum' ? 'border-slate-200 bg-white/95' : 'border-white/5 bg-[#0A0A0B]/95']">
    <div class="flex h-full flex-col px-4 pb-6 pt-6">
        <div class="mb-8 flex items-center justify-between gap-3" :class="sidebarCollapsed ? 'px-1 justify-center' : 'px-3'">
            <a href="{{ url('dashboard') }}" class="flex min-w-0 items-center gap-3 text-left" :class="sidebarCollapsed ? 'justify-center' : ''">
                <img
                    x-show="sidebarCollapsed"
                    x-cloak
                    src="{{ $desktopBrandCompactLogo ?? asset('assets/images/perfectlum_circle.png') }}"
                    alt="{{ $desktopBrandName ?? 'PerfectLum' }}"
                    class="h-11 w-11 shrink-0 object-contain"
                >
                <img
                    x-show="!sidebarCollapsed"
                    x-cloak
                    src="{{ $desktopBrandLogo ?? asset('assets/images/perfectlum-logo.png') }}"
                    alt="{{ $desktopBrandName ?? 'PerfectLum' }}"
                    class="h-11 w-auto max-w-[180px] shrink-0 object-contain"
                >
            </a>
        </div>

        <div class="flex-1 overflow-y-auto px-1 no-scrollbar">
            <div class="mb-3 px-3" x-show="!sidebarCollapsed" x-cloak>
                <p class="text-[10px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ __('Workspace') }}</p>
            </div>

            <div class="space-y-1">
                @foreach ($primaryLinks as $item)
                    <a href="{{ $item['url'] }}"
                       class="group flex items-center rounded-2xl transition-all duration-200"
                       :class="[
                           sidebarCollapsed ? 'mx-auto h-14 w-14 justify-center' : 'gap-3 px-4 py-3.5',
                           isActive(@js($item['name']))
                               ? (theme === 'perfectlum' ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'bg-sky-500 text-white shadow-[0_0_30px_-8px_rgba(14,165,233,0.6)]')
                               : (theme === 'perfectlum' ? 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' : 'text-white/45 hover:bg-white/5 hover:text-white')
                       ]"
                       title="{{ __($item['name']) }}">
                        <i data-lucide="{{ $item['icon'] }}" class="h-[18px] w-[18px] shrink-0"></i>
                        <span x-show="!sidebarCollapsed" x-cloak class="text-[13px] font-semibold tracking-wide">{{ __($item['name']) }}</span>
                    </a>
                @endforeach
            </div>

            @if(count($settingsLinks))
                <div class="my-6 h-px bg-slate-200/80" :class="theme === 'perfectlum' ? 'bg-slate-200/80' : 'bg-white/5'"></div>

                <div class="space-y-1">
                    <button type="button"
                            @click="settingsExpanded = !settingsExpanded"
                            class="group flex w-full items-center rounded-2xl transition-all duration-200"
                            :class="[
                                sidebarCollapsed ? 'mx-auto h-14 w-14 justify-center' : 'gap-3 px-4 py-3.5',
                                ['Site Settings', 'Application Settings', 'Alert Settings', 'Scope Explorer', 'Client Monitor'].includes(activeMenu)
                                    ? (theme === 'perfectlum' ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'bg-sky-500 text-white shadow-[0_0_30px_-8px_rgba(14,165,233,0.6)]')
                                    : (theme === 'perfectlum' ? 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' : 'text-white/45 hover:bg-white/5 hover:text-white')
                            ]"
                            title="{{ __('Settings') }}">
                        <i data-lucide="settings" class="h-[18px] w-[18px] shrink-0"></i>
                        <span x-show="!sidebarCollapsed" x-cloak class="text-[13px] font-semibold tracking-wide">{{ __('Settings') }}</span>
                        <i x-show="!sidebarCollapsed" x-cloak data-lucide="chevron-down" class="ml-auto h-4 w-4 transition-transform" :class="settingsExpanded ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="settingsExpanded && !sidebarCollapsed" x-cloak class="space-y-1 pl-4">
                        @foreach ($settingsLinks as $item)
                            <a href="{{ url(match ($item['name']) {
                                'Site Settings' => 'site-settings',
                                'Application Settings' => 'global-settings',
                                'Alert Settings' => 'alert-settings',
                                'Scope Explorer' => 'scope-explorer',
                                'Client Monitor' => 'client-monitor',
                                default => 'site-settings',
                            }) }}"
                               class="flex items-center gap-3 rounded-2xl px-4 py-3 text-[13px] font-medium transition-all duration-200"
                               :class="isActive(@js($item['name']))
                                    ? (theme === 'perfectlum' ? 'bg-slate-100 text-slate-900' : 'bg-white/10 text-white')
                                    : (theme === 'perfectlum' ? 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' : 'text-white/45 hover:bg-white/5 hover:text-white')">
                                <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4 shrink-0"></i>
                                <span>{{ __($item['name']) }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-6 px-3" x-show="!sidebarCollapsed" x-cloak>
            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-400">
                {{ __('Powered by') }}
            </p>
            <img
                src="{{ asset('assets/images/qubyx-black.png') }}"
                alt="Qubyx"
                class="mt-2 h-6 w-auto object-contain"
            >
        </div>
    </div>
</aside>
