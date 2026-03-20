@props([
    'title'       => 'Page Title',
    'description' => '',
    'icon'        => 'layout-dashboard',
])

{{--
    USAGE:
    <x-page-header title="User Management" description="Manage system users." icon="users">
        <x-slot name="actions">
            <button ...>Add User</button>
        </x-slot>
    </x-page-header>

    Props:
      title       — string   — page heading text
      description — string   — subtitle/description (optional)
      icon        — string   — lucide icon name (default: layout-dashboard)
    Slots:
      actions     — optional — buttons/dropdowns rendered on the right side
--}}

<div class="flex flex-col gap-4 rounded-[2rem] border p-6 shadow-sm backdrop-blur transition-colors duration-300 sm:flex-row sm:items-center sm:justify-between"
     :class="theme === 'perfectlum' ? 'border-slate-200/80 bg-white/80' : 'border-white/10 bg-white/[0.03]'">
    <div class="min-w-0">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl shadow-sm"
                 :class="theme === 'perfectlum' ? 'bg-sky-50 text-sky-600' : 'bg-sky-500/10 text-sky-400'">
                <i data-lucide="{{ $icon }}" class="h-5 w-5"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-slate-400">{{ __('Admin Workspace') }}</p>
                <h2 class="truncate text-2xl font-bold tracking-tight" :class="theme === 'perfectlum' ? 'text-slate-900' : 'text-white'">{{ __($title) }}</h2>
            </div>
        </div>
        @if($description)
        <p class="mt-3 max-w-3xl text-[13px] leading-6" :class="theme === 'perfectlum' ? 'text-slate-500' : 'text-slate-300/80'">{{ __($description) }}</p>
        @endif
    </div>

    @if(isset($actions))
    <div class="flex items-center gap-3">
        {{ $actions }}
    </div>
    @endif
</div>
