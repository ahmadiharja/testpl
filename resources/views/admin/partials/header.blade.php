@php
    $user = auth()->user();
    $displayName = $user?->fullname ?: $user?->name ?: __('User');
    $profileUsername = $user?->name ?: $displayName;
    $profileRole = session('role', $user?->role ?: 'user');
    $profileRoleLabel = match ($profileRole) {
        'super' => __('super'),
        'admin' => __('admin'),
        default => __('operator'),
    };
    $nameParts = preg_split('/\s+/', trim($profileUsername)) ?: [];
    $firstInitial = strtoupper(substr($nameParts[0] ?? 'U', 0, 1));
    $secondInitial = strtoupper(substr($nameParts[1] ?? '', 0, 1));
    $avatarInitials = trim($firstInitial . $secondInitial) ?: 'U';
    $supportedLocales = config('app.supported_locales', []);
    $currentLocale = app()->getLocale();
    $localeOptions = collect($supportedLocales)->map(function ($meta, $code) {
        return [
            'code' => $code,
            'label' => $meta['label'] ?? strtoupper($code),
            'native' => $meta['native'] ?? ($meta['label'] ?? strtoupper($code)),
            'flag' => $meta['flag'] ?? '🌐',
            'flagUrl' => isset($meta['flag_asset']) ? asset($meta['flag_asset']) : null,
        ];
    })->values()->all();
    $currentLocaleMeta = [
        'code' => $currentLocale,
        'label' => $supportedLocales[$currentLocale]['label'] ?? strtoupper($currentLocale),
        'native' => $supportedLocales[$currentLocale]['native'] ?? strtoupper($currentLocale),
        'flag' => $supportedLocales[$currentLocale]['flag'] ?? '🌐',
        'flagUrl' => isset($supportedLocales[$currentLocale]['flag_asset']) ? asset($supportedLocales[$currentLocale]['flag_asset']) : null,
    ];
    $notificationTranslations = [
        'noUnreadNotifications' => __('No unread notifications'),
        'noNotificationsYet' => __('No notifications yet'),
    ];
    $languageSwitcherTranslations = [
        'language' => __('Language'),
    ];
    $globalSearchTranslations = [
        'facility' => __('Facility'),
        'workgroup' => __('Workgroup'),
        'workstation' => __('Workstation'),
        'display' => __('Display'),
        'record' => __('Record'),
        'openRecord' => __('Open record'),
    ];
@endphp

<!-- TOP HEADER -->
<header id="desktop-top-header"
        data-desktop-shell-signature="{{ $desktopShellSignature ?? '' }}"
        class="h-20 flex items-center justify-between px-6 lg:px-12 shrink-0 border-b relative z-40" 
        :class="theme === 'perfectlum' ? 'border-gray-100 bg-white/80 backdrop-blur-xl' : 'border-white/5 bg-[#0A0A0B]/80 backdrop-blur-xl'">
    
    <div class="flex items-center gap-6">
        <!-- Mobile Sidebar Toggle -->
        <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-2.5 rounded-xl lg:hidden transition-all active:scale-95" 
                :class="theme === 'perfectlum' ? 'bg-gray-100 text-gray-900' : 'bg-white/5 text-white'">
            <i data-lucide="menu" class="w-5 h-5"></i>
        </button>

        <button
            @click="sidebarCollapsed = !sidebarCollapsed"
            type="button"
            class="hidden lg:inline-flex h-10 w-10 items-center justify-center rounded-2xl border transition-all"
            :class="theme === 'perfectlum'
                ? 'border-slate-200 bg-white text-slate-500 hover:bg-slate-50 hover:text-slate-900'
                : 'border-white/10 bg-white/5 text-white/50 hover:bg-white/10 hover:text-white'"
            :title="sidebarCollapsed ? @js(__('Expand sidebar')) : @js(__('Collapse sidebar'))">
            <i data-lucide="panel-left-close" class="h-4 w-4 transition-transform" :class="sidebarCollapsed ? 'rotate-180' : ''"></i>
        </button>

        <!-- Breadcrumb & Context -->
        <div class="flex items-center gap-3">
            <div class="hidden sm:flex items-center justify-center w-10 h-10 rounded-xl transition-all"
                 :class="theme === 'perfectlum' ? 'bg-gray-50 text-gray-400' : 'bg-white/[0.03] text-white/30'">
                <i data-lucide="home" class="w-4 h-4"></i>
            </div>
            
            <div class="flex flex-col">
                <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.1em] opacity-30">
                    <span>{{ __('Admin Console') }}</span>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span x-text="menuLabel(activeMenu)"></span>
                </div>
                <h1 class="text-[15px] font-bold tracking-tight" :class="theme === 'perfectlum' ? 'text-gray-900' : 'text-white'" x-text="menuLabel(activeMenu)"></h1>
            </div>
        </div>
    </div>

    <!-- Header Actions -->
    <div class="flex items-center gap-3 xl:gap-5">
        
        <!-- Search Bar (Premium Design) -->
        <div class="relative hidden md:flex" x-data="globalWorkspaceSearch()" x-init="init()" @click.outside="close()" @keydown.escape.window="close()">
            <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none opacity-40 group-focus-within:opacity-100 transition-opacity">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input
                    x-ref="input"
                    x-model="query"
                    @focus="handleFocus()"
                    @input="handleInput()"
                    @keydown.arrow-down.prevent="focusNext()"
                    @keydown.arrow-up.prevent="focusPrev()"
                    @keydown.enter.prevent="confirmSelection()"
                    type="text"
                    placeholder="{{ __('Search facilities, workgroups, workstations, displays...') }}"
                    class="w-64 xl:w-80 h-11 pl-11 pr-12 rounded-2xl text-[13px] font-semibold border transition-all placeholder-white/20"
                    :class="theme === 'perfectlum' ? 'bg-gray-50 border-gray-100 focus:bg-white focus:ring-4 focus:ring-gray-100 text-gray-900' : 'bg-white/[0.03] border-white/5 focus:bg-white/[0.07] focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500/30 text-white'"
                >

                <button
                    x-show="query.length > 0"
                    type="button"
                    @click="clear()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 inline-flex h-6 w-6 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                >
                    <i data-lucide="x" class="h-3.5 w-3.5"></i>
                </button>

                <div class="absolute right-3 top-1/2 -translate-y-1/2 hidden lg:flex items-center gap-1 opacity-20 group-focus-within:hidden" x-show="query.length === 0">
                    <kbd class="px-1.5 py-0.5 rounded border text-[10px] font-bold">⌘</kbd>
                    <kbd class="px-1.5 py-0.5 rounded border text-[10px] font-bold">K</kbd>
                </div>
            </div>

            <div
                x-cloak
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                class="absolute right-0 top-[calc(100%+0.75rem)] z-[1450] w-[32rem] rounded-[1.6rem] border border-slate-200 bg-white p-3 shadow-[0_28px_80px_-36px_rgba(15,23,42,0.35)]"
            >
                <div class="flex items-start justify-between gap-4 px-1 pb-3">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Global Search') }}</p>
                        <h3 class="mt-1 text-lg font-semibold text-slate-900">{{ __('Jump to workspace records') }}</h3>
                        <p class="mt-1 text-xs text-slate-500">{{ __('Search across facilities, workgroups, workstations, and displays inside your current scope.') }}</p>
                    </div>
                    <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-[11px] font-semibold text-slate-500">
                        <span x-text="results.length"></span>
                        <span class="ml-1">{{ __('results') }}</span>
                    </span>
                </div>

                <div class="max-h-[26rem] overflow-y-auto pr-1">
                    <template x-if="query.trim().length < 2">
                        <div class="rounded-[1.25rem] border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-400 shadow-sm" x-html="iconSvg('search')"></div>
                            <p class="mt-4 text-sm font-semibold text-slate-700">{{ __('Start typing to search') }}</p>
                            <p class="mt-2 text-xs leading-5 text-slate-500">{{ __('Use at least two characters to search facilities, workgroups, workstations, or displays.') }}</p>
                        </div>
                    </template>

                    <template x-if="query.trim().length >= 2 && loading">
                        <div class="space-y-2">
                            <template x-for="index in 4" :key="index">
                                <div class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                                    <div class="h-3 w-24 rounded-full bg-slate-200"></div>
                                    <div class="mt-3 h-3 w-full rounded-full bg-slate-200"></div>
                                    <div class="mt-2 h-3 w-2/3 rounded-full bg-slate-200"></div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="query.trim().length >= 2 && !loading && results.length === 0">
                        <div class="rounded-[1.25rem] border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-400 shadow-sm" x-html="iconSvg('search-x')"></div>
                            <p class="mt-4 text-sm font-semibold text-slate-700">{{ __('No matching records') }}</p>
                            <p class="mt-2 text-xs leading-5 text-slate-500">{{ __('Try a different keyword or broaden the visible scope from the current workspace filters.') }}</p>
                        </div>
                    </template>

                    <div class="space-y-2" x-show="query.trim().length >= 2 && !loading && results.length > 0">
                        <template x-for="(item, index) in results" :key="item.id">
                            <button
                                type="button"
                                @mouseenter="activeIndex = index"
                                @click="openItem(item)"
                                class="flex w-full items-start gap-3 rounded-[1.25rem] border px-4 py-3 text-left transition"
                                :class="activeIndex === index ? 'border-sky-200 bg-sky-50/70' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50'"
                            >
                                <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl" :class="typeSurface(item.type)" x-html="typeIcon(item.type)"></div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-slate-900" x-text="item.title"></p>
                                            <p class="mt-1 truncate text-xs text-slate-500" x-text="item.subtitle || translations.openRecord"></p>
                                        </div>
                                        <span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold" :class="typeTag(item.type)" x-text="typeLabel(item.type)"></span>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative" x-data="languageSwitcher()" @click.outside="close()" @keydown.escape.window="close()">
            <button
                type="button"
                @click="toggle()"
                class="inline-flex h-11 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                :title="translations.language"
            >
                <span class="inline-flex h-7 w-7 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-50 shadow-sm">
                    <template x-if="current.flagUrl">
                        <img :src="current.flagUrl" :alt="current.native" class="h-full w-full object-cover">
                    </template>
                    <template x-if="!current.flagUrl">
                        <span class="text-[15px] leading-none" x-text="current.flag"></span>
                    </template>
                </span>
                <span class="hidden lg:inline-block whitespace-nowrap" x-text="current.native"></span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400"></i>
            </button>

            <div
                x-cloak
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                class="absolute right-0 top-[calc(100%+0.75rem)] z-[1450] w-60 rounded-[1.5rem] border border-slate-200 bg-white p-2 shadow-[0_24px_60px_-30px_rgba(15,23,42,0.35)]"
            >
                <div class="space-y-1">
                    <template x-for="item in locales" :key="item.code">
                        <button
                            type="button"
                            @click="select(item.code)"
                            class="flex w-full items-center gap-3 rounded-2xl px-3 py-2.5 text-left transition"
                            :class="item.code === current.code ? 'bg-sky-50 text-sky-700' : 'text-slate-700 hover:bg-slate-50'"
                        >
                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-white shadow-sm">
                                <template x-if="item.flagUrl">
                                    <img :src="item.flagUrl" :alt="item.native" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!item.flagUrl">
                                    <span class="text-base leading-none" x-text="item.flag"></span>
                                </template>
                            </span>
                            <span class="min-w-0 flex-1 truncate text-sm font-semibold" x-text="item.native"></span>
                            <i x-show="item.code === current.code" data-lucide="check" class="h-4 w-4"></i>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <div class="w-px h-6 mx-1 opacity-5" :class="theme === 'perfectlum' ? 'bg-black' : 'bg-white'"></div>

        <div class="relative" x-data="notificationBell()" x-init="init()" @click.outside="close()" @keydown.escape.window="close()">
            <button
                type="button"
                @click="toggle()"
                class="relative p-2.5 rounded-xl transition-all hover:scale-105 active:scale-95"
                :class="theme === 'perfectlum' ? 'bg-gray-50 text-gray-600 hover:bg-gray-100' : 'bg-white/[0.03] text-white/40 hover:bg-white/10 hover:text-white'">
                <i data-lucide="bell" class="w-5 h-5"></i>
                <template x-if="unreadCount > 0">
                    <span
                        class="absolute -right-1 -top-1 inline-flex min-h-[1.1rem] min-w-[1.1rem] items-center justify-center rounded-full px-1 text-[10px] font-bold text-white shadow-sm"
                        :class="theme === 'perfectlum' ? 'bg-rose-500' : 'bg-sky-500'"
                        x-text="unreadBadge()"></span>
                </template>
            </button>

            <div
                x-cloak
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                class="absolute right-0 top-[calc(100%+0.75rem)] z-[1400] w-[25rem] rounded-[1.6rem] border border-slate-200 bg-white p-3 shadow-[0_28px_80px_-36px_rgba(15,23,42,0.35)]">
                <div class="flex items-start justify-between gap-4 px-1 pb-3">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">{{ __('Notifications') }}</p>
                        <h3 class="mt-1 text-lg font-semibold text-slate-900">{{ __('Workspace updates') }}</h3>
                        <p class="mt-1 text-xs text-slate-500">{{ __('Alerts, account reminders, and activity assigned to your scope.') }}</p>
                    </div>
                    <button
                        x-show="unreadCount > 0"
                        type="button"
                        @click="markAllRead()"
                        class="inline-flex shrink-0 items-center rounded-full border border-slate-200 px-3 py-1.5 text-[11px] font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                        {{ __('Mark all read') }}
                    </button>
                </div>

                <div class="mb-3 flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 p-1">
                    <button type="button" @click="setFilter('unread')" class="flex-1 rounded-[0.9rem] px-3 py-2 text-sm font-semibold transition" :class="tabClasses('unread')">{{ __('Unread') }}</button>
                    <button type="button" @click="setFilter('all')" class="flex-1 rounded-[0.9rem] px-3 py-2 text-sm font-semibold transition" :class="tabClasses('all')">{{ __('All') }}</button>
                </div>

                <div class="max-h-[26rem] overflow-y-auto pr-1">
                    <template x-if="loading">
                        <div class="space-y-2">
                            <template x-for="index in 4" :key="index">
                                <div class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                                    <div class="h-3 w-24 rounded-full bg-slate-200"></div>
                                    <div class="mt-3 h-3 w-full rounded-full bg-slate-200"></div>
                                    <div class="mt-2 h-3 w-3/4 rounded-full bg-slate-200"></div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="!loading && items.length === 0">
                        <div class="rounded-[1.25rem] border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-slate-400 shadow-sm" x-html="iconSvg('bell')"></div>
                            <p class="mt-4 text-sm font-semibold text-slate-700" x-text="filter === 'unread' ? translations.noUnreadNotifications : translations.noNotificationsYet"></p>
                            <p class="mt-2 text-xs leading-5 text-slate-500">{{ __('New alerts and account reminders assigned to your workspace will appear here.') }}</p>
                        </div>
                    </template>

                    <div class="space-y-2" x-show="!loading && items.length > 0">
                        <template x-for="item in items" :key="item.id">
                            <button
                                type="button"
                                @click="openItem(item)"
                                class="flex w-full items-start gap-3 rounded-[1.25rem] border px-4 py-3 text-left transition"
                                :class="item.read ? 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50' : 'border-sky-100 bg-sky-50/60 hover:border-sky-200 hover:bg-sky-50'">
                                <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl" :class="severityClasses(item)" x-html="iconSvg(item.icon)"></div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-400" x-text="item.category"></p>
                                            <p class="mt-1 text-sm font-semibold text-slate-900" x-text="item.title"></p>
                                        </div>
                                        <span x-show="!item.read" class="mt-1 inline-flex h-2.5 w-2.5 shrink-0 rounded-full bg-sky-500"></span>
                                    </div>
                                    <p class="mt-2 text-sm leading-6 text-slate-600" x-text="item.body"></p>
                                    <div class="mt-3 flex items-center gap-2 text-xs text-slate-500">
                                        <span x-show="item.scope" class="inline-flex rounded-full bg-slate-100 px-2 py-1 font-medium text-slate-600" x-text="item.scope"></span>
                                        <span x-text="item.relativeTime"></span>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="mt-3 border-t border-slate-200 px-1 pt-3">
                    <a
                        href="{{ url('notifications') }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                        {{ __('View all notifications') }}
                        <i data-lucide="arrow-right" class="h-4 w-4"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- User Profile Dropdown -->
        <div class="relative ml-1" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
            <button
                type="button"
                @click="open = !open"
                class="group relative flex items-center gap-3 rounded-2xl px-2 py-1.5 transition-all"
                :class="theme === 'perfectlum' ? 'hover:bg-slate-50' : 'hover:bg-white/5'">
                <div class="absolute -inset-1 rounded-2xl bg-gradient-to-tr from-sky-400 to-indigo-600 blur opacity-0 transition-opacity duration-300 group-hover:opacity-20"></div>
                <div
                    class="relative flex h-11 w-11 items-center justify-center rounded-2xl text-sm font-bold text-white shadow-lg shadow-sky-500/20"
                    :class="theme === 'perfectlum' ? 'bg-gradient-to-br from-sky-500 to-indigo-600 ring-2 ring-gray-100' : 'bg-gradient-to-br from-sky-500 to-indigo-600 ring-2 ring-white/10'">
                    {{ $avatarInitials }}
                </div>

                <div class="hidden min-w-0 max-w-[9.5rem] text-left lg:block xl:max-w-[12rem]">
                    <p class="truncate text-[13px] font-semibold" :class="theme === 'perfectlum' ? 'text-slate-900' : 'text-white'" title="{{ $profileUsername }}">{{ $profileUsername }}</p>
                    <p class="truncate text-[11px] font-medium capitalize text-slate-400">{{ $profileRoleLabel }}</p>
                </div>

                <i data-lucide="chevron-down" class="hidden h-4 w-4 text-slate-400 lg:block"></i>
            </button>

            <div
                x-cloak
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                class="absolute right-0 top-[calc(100%+0.75rem)] z-50 w-72 rounded-[1.5rem] border border-slate-200 bg-white p-3 shadow-[0_24px_60px_-30px_rgba(15,23,42,0.35)]">
                <div class="flex items-center gap-3 rounded-[1.25rem] bg-slate-50 p-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-600 text-sm font-bold text-white shadow-lg shadow-sky-500/20">
                        {{ $avatarInitials }}
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-900" title="{{ $profileUsername }}">{{ $profileUsername }}</p>
                        <p class="truncate text-xs capitalize text-slate-500">{{ $profileRoleLabel }}</p>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <a
                        href="{{ url('profile-settings') }}"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
                        <i data-lucide="user-round" class="h-4 w-4"></i>
                        {{ __('Profile Settings') }}
                    </a>
                    <a
                        href="{{ url('logout') }}"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                        <i data-lucide="log-out" class="h-4 w-4"></i>
                        {{ __('Logout') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
if (!window.notificationBell) {
    window.notificationBell = function () {
        return {
            open: false,
            loading: false,
            filter: 'unread',
            items: [],
            unreadCount: 0,
            pollHandle: null,
            focusHandle: null,
            visibilityHandle: null,
            apiUrl: @json(url('api/notifications')),
            readAllUrl: @json(url('api/notifications/read-all')),
            csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            translations: @json($notificationTranslations),

            init() {
                this.load(true);
                this.pollHandle = window.setInterval(() => this.load(true), 15000);
                this.focusHandle = () => this.load(true);
                this.visibilityHandle = () => {
                    if (document.visibilityState === 'visible') {
                        this.load(true);
                    }
                };
                window.addEventListener('focus', this.focusHandle);
                document.addEventListener('visibilitychange', this.visibilityHandle);
            },

            destroy() {
                if (this.pollHandle) {
                    window.clearInterval(this.pollHandle);
                    this.pollHandle = null;
                }
                if (this.focusHandle) {
                    window.removeEventListener('focus', this.focusHandle);
                    this.focusHandle = null;
                }
                if (this.visibilityHandle) {
                    document.removeEventListener('visibilitychange', this.visibilityHandle);
                    this.visibilityHandle = null;
                }
            },

            close() {
                this.open = false;
            },

            toggle() {
                this.open = !this.open;
                if (this.open) {
                    this.load();
                }
            },

            unreadBadge() {
                return this.unreadCount > 9 ? '9+' : String(this.unreadCount);
            },

            tabClasses(target) {
                return target === this.filter
                    ? 'bg-white text-sky-700 shadow-[0_8px_24px_-18px_rgba(14,165,233,0.45)]'
                    : 'text-slate-500 hover:bg-white hover:text-slate-800';
            },

            severityClasses(item) {
                switch (item.severity) {
                    case 'success':
                        return 'bg-emerald-100 text-emerald-700';
                    case 'warning':
                        return 'bg-amber-100 text-amber-700';
                    case 'danger':
                        return 'bg-rose-100 text-rose-700';
                    default:
                        return 'bg-sky-100 text-sky-700';
                }
            },

            iconSvg(name) {
                const icons = {
                    bell: '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .738-1.674C19.41 13.874 18 12.1 18 8a6 6 0 1 0-12 0c0 4.1-1.411 5.874-2.738 7.326"/></svg>',
                    'user-round': '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>',
                    'package-check': '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16.5 9.4 7.55 4.24"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/><path d="m9 17 2 2 4-4"/></svg>',
                    'clipboard-check': '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="2" width="6" height="4" rx="1"/><path d="M4 7a2 2 0 0 1 2-2h2"/><path d="M16 5h2a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z"/><path d="m9 14 2 2 4-4"/></svg>',
                    'clipboard-x': '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="2" width="6" height="4" rx="1"/><path d="M4 7a2 2 0 0 1 2-2h2"/><path d="M16 5h2a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2Z"/><path d="m10 14 4 4"/><path d="m14 14-4 4"/></svg>',
                    'monitor-warning': '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/><path d="m12 8 .01 4"/><path d="M12 15h.01"/></svg>',
                    'monitor-check': '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/><path d="m9 11 2 2 4-4"/></svg>',
                    'plug-zap': '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 6 12h5l-1 10 8-12h-5l0-8Z"/><path d="M10 7H5a2 2 0 0 0-2 2v3"/><path d="M14 7h5a2 2 0 0 1 2 2v3"/></svg>',
                    'calendar-clock': '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/><circle cx="17" cy="17" r="3"/><path d="M17 15.5V17l1 1"/></svg>',
                    'settings-2': '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7h-9"/><path d="M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="7" cy="7" r="3"/></svg>',
                    'mail-search': '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 7.5v6.75a2.25 2.25 0 0 1-2.25 2.25H16"/><path d="M2 7.5v6.75A2.25 2.25 0 0 0 4.25 16.5H8"/><path d="m22 6-8.97 5.7a2 2 0 0 1-2.06 0L2 6"/><path d="M4.25 4h15.5A2.25 2.25 0 0 1 22 6v1.5H2V6A2.25 2.25 0 0 1 4.25 4Z"/><circle cx="11.5" cy="16.5" r="2.5"/><path d="m13.3 18.3 1.7 1.7"/></svg>',
                };

                return icons[name] || icons.bell;
            },

            async load(summaryOnly = false) {
                if (!summaryOnly) {
                    this.loading = true;
                }

                try {
                    const params = new URLSearchParams({
                        filter: summaryOnly ? 'unread' : this.filter,
                        limit: '8',
                    });

                    const response = await fetch(`${this.apiUrl}?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (!response.ok) {
                        throw new Error('Failed to load notifications.');
                    }

                    const payload = await response.json();
                    this.unreadCount = Number(payload.unreadCount || 0);

                    if (!summaryOnly) {
                        this.items = Array.isArray(payload.data) ? payload.data : [];
                    }
                } catch (_) {
                    if (!summaryOnly) {
                        this.items = [];
                    }
                } finally {
                    this.loading = false;
                }
            },

            setFilter(filter) {
                if (this.filter === filter) {
                    return;
                }

                this.filter = filter;
                this.load();
            },

            async post(url) {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Request failed.');
                }

                return response.json();
            },

            async markAllRead() {
                try {
                    const payload = await this.post(this.readAllUrl);
                    this.unreadCount = Number(payload.unreadCount || 0);
                    this.items = this.items.map((item) => ({ ...item, read: true }));
                    if (this.filter === 'unread') {
                        this.load();
                    }
                } catch (_) {}
            },

            async openItem(item) {
                if (!item.read) {
                    try {
                        const payload = await this.post(`${this.apiUrl}/${encodeURIComponent(item.id)}/read`);
                        this.unreadCount = Number(payload.unreadCount || 0);
                        item.read = true;
                    } catch (_) {}
                }

                if (item.url) {
                    window.location.href = item.url;
                }
            },
        };
    };
}

if (!window.languageSwitcher) {
    window.languageSwitcher = function () {
        return {
            open: false,
            locales: @json($localeOptions),
            current: @json($currentLocaleMeta),
            endpoint: @json(route('locale.update')),
            csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            translations: @json($languageSwitcherTranslations),

            toggle() {
                this.open = !this.open;
            },

            close() {
                this.open = false;
            },

            async select(code) {
                if (!code || code === this.current.code) {
                    this.close();
                    return;
                }

                const response = await fetch(this.endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ locale: code }),
                });

                if (!response.ok) {
                    return;
                }

                window.location.reload();
            },
        };
    };
}

if (!window.globalWorkspaceSearch) {
    window.globalWorkspaceSearch = function () {
        return {
            query: '',
            open: false,
            loading: false,
            results: [],
            activeIndex: -1,
            debounceHandle: null,
            searchUrl: @json(url('api/global-search')),
            translations: @json($globalSearchTranslations),

            init() {
                window.addEventListener('keydown', (event) => {
                    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
                        event.preventDefault();
                        this.$refs.input?.focus();
                        this.handleFocus();
                    }
                });
            },

            handleFocus() {
                if (this.query.trim().length > 0) {
                    this.open = true;
                }
            },

            handleInput() {
                this.open = true;
                this.activeIndex = -1;

                window.clearTimeout(this.debounceHandle);
                this.debounceHandle = window.setTimeout(() => {
                    this.load();
                }, 220);
            },

            clear() {
                this.query = '';
                this.results = [];
                this.loading = false;
                this.activeIndex = -1;
                this.open = false;
                this.$refs.input?.focus();
            },

            close() {
                this.open = false;
                this.activeIndex = -1;
            },

            async load() {
                const q = this.query.trim();
                if (q.length < 2) {
                    this.results = [];
                    this.loading = false;
                    return;
                }

                this.loading = true;
                try {
                    const params = new URLSearchParams({
                        q,
                        limit: '12',
                    });
                    const response = await fetch(`${this.searchUrl}?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (!response.ok) {
                        throw new Error('Failed to search.');
                    }

                    const payload = await response.json();
                    this.results = Array.isArray(payload.data) ? payload.data : [];
                    this.activeIndex = this.results.length > 0 ? 0 : -1;
                } catch (_) {
                    this.results = [];
                    this.activeIndex = -1;
                } finally {
                    this.loading = false;
                }
            },

            focusNext() {
                if (!this.open || this.results.length === 0) {
                    return;
                }

                this.activeIndex = this.activeIndex >= this.results.length - 1 ? 0 : this.activeIndex + 1;
            },

            focusPrev() {
                if (!this.open || this.results.length === 0) {
                    return;
                }

                this.activeIndex = this.activeIndex <= 0 ? this.results.length - 1 : this.activeIndex - 1;
            },

            confirmSelection() {
                if (this.activeIndex >= 0 && this.results[this.activeIndex]) {
                    this.openItem(this.results[this.activeIndex]);
                }
            },

            openItem(item) {
                if (!item) {
                    return;
                }

                const supportedType = ['facility', 'workgroup', 'workstation', 'display'].includes(item.type)
                    ? item.type
                    : null;
                const recordId = Number(item.recordId || 0);

                this.open = false;
                this.activeIndex = -1;

                if (supportedType && recordId > 0) {
                    window.dispatchEvent(new CustomEvent('open-hierarchy', {
                        detail: {
                            type: supportedType,
                            id: recordId,
                        }
                    }));
                    return;
                }

                if (item.url) {
                    window.location.href = item.url;
                }
            },

            typeLabel(type) {
                switch (type) {
                    case 'facility': return this.translations.facility;
                    case 'workgroup': return this.translations.workgroup;
                    case 'workstation': return this.translations.workstation;
                    case 'display': return this.translations.display;
                    default: return this.translations.record;
                }
            },

            typeSurface(type) {
                switch (type) {
                    case 'facility': return 'bg-sky-50 text-sky-700';
                    case 'workgroup': return 'bg-violet-50 text-violet-700';
                    case 'workstation': return 'bg-emerald-50 text-emerald-700';
                    case 'display': return 'bg-amber-50 text-amber-700';
                    default: return 'bg-slate-100 text-slate-700';
                }
            },

            typeTag(type) {
                switch (type) {
                    case 'facility': return 'bg-sky-50 text-sky-700';
                    case 'workgroup': return 'bg-violet-50 text-violet-700';
                    case 'workstation': return 'bg-emerald-50 text-emerald-700';
                    case 'display': return 'bg-amber-50 text-amber-700';
                    default: return 'bg-slate-100 text-slate-700';
                }
            },

            typeIcon(type) {
                switch (type) {
                    case 'facility':
                        return this.iconSvg('building');
                    case 'workgroup':
                        return this.iconSvg('git-branch');
                    case 'workstation':
                        return this.iconSvg('computer');
                    case 'display':
                        return this.iconSvg('monitor');
                    default:
                        return this.iconSvg('search');
                }
            },

            iconSvg(name) {
                const icons = {
                    search: '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>',
                    'search-x': '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><path d="m9 9 4 4"/><path d="m13 9-4 4"/></svg>',
                    building: '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 22V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v18"/><path d="M6 12H4a1 1 0 0 0-1 1v9"/><path d="M18 9h2a1 1 0 0 1 1 1v12"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>',
                    'git-branch': '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="3"/><path d="M6 9v12"/><circle cx="18" cy="6" r="3"/><circle cx="18" cy="18" r="3"/><path d="M9 6h6"/><path d="M18 9v6"/><path d="M6 18h9"/></svg>',
                    computer: '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="12" rx="2"/><path d="M12 16v4"/><path d="M8 20h8"/></svg>',
                    monitor: '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/></svg>',
                };

                return icons[name] || icons.search;
            },
        };
    };
}
</script>
