@php
    $tabs = [
        ['key' => 'dashboard', 'label' => 'Home', 'icon' => 'house', 'route' => route('mobile.dashboard')],
        ['key' => 'workspace', 'label' => 'Workspace', 'icon' => 'blocks', 'route' => route('mobile.workspace')],
        ['key' => 'tasks', 'label' => 'Tasks', 'icon' => 'list-checks', 'route' => route('mobile.tasks')],
        ['key' => 'reports', 'label' => 'Reports', 'icon' => 'file-text', 'route' => route('mobile.reports')],
        ['key' => 'alerts', 'label' => 'Alerts', 'icon' => 'bell-ring', 'route' => route('mobile.alerts')],
    ];
@endphp

<nav id="mobile-bottom-nav" class="fixed bottom-0 left-1/2 z-[60] w-full max-w-[440px] -translate-x-1/2">
    <div id="mobile-bottom-nav-surface" class="mobile-nav-surface px-2 pb-[max(0.55rem,env(safe-area-inset-bottom))] pt-2 opacity-100">
        <div class="mobile-bottom-bar">
        @foreach ($tabs as $tab)
            <a href="{{ $tab['route'] }}"
               data-mobile-bottom-key="{{ $tab['key'] }}"
               class="mobile-bottom-link {{ ($activeTab ?? 'dashboard') === $tab['key'] ? 'active' : '' }}">
                <span class="mobile-bottom-icon">
                    <i data-lucide="{{ $tab['icon'] }}" class="h-4 w-4"></i>
                </span>
                <span>{{ $tab['label'] }}</span>
            </a>
        @endforeach
        </div>
    </div>
</nav>
