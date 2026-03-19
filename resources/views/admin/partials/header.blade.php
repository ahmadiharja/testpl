@php
    $user = auth()->user();
    $displayName = $user?->fullname ?: $user?->name ?: 'User';
    $nameParts = preg_split('/\s+/', trim($displayName)) ?: [];
    $firstInitial = strtoupper(substr($nameParts[0] ?? 'U', 0, 1));
    $secondInitial = strtoupper(substr($nameParts[1] ?? ($user?->name ?: ''), 0, 1));
    $avatarInitials = trim($firstInitial . $secondInitial) ?: 'U';
@endphp

<!-- TOP HEADER -->
<header class="h-20 flex items-center justify-between px-6 lg:px-12 shrink-0 border-b relative z-40" 
        :class="theme === 'perfectlum' ? 'border-gray-100 bg-white/80 backdrop-blur-xl' : 'border-white/5 bg-[#0A0A0B]/80 backdrop-blur-xl'">
    
    <div class="flex items-center gap-6">
        <!-- Mobile Sidebar Toggle -->
        <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-2.5 rounded-xl lg:hidden transition-all active:scale-95" 
                :class="theme === 'perfectlum' ? 'bg-gray-100 text-gray-900' : 'bg-white/5 text-white'">
            <i data-lucide="menu" class="w-5 h-5"></i>
        </button>

        <!-- Breadcrumb & Context -->
        <div class="flex items-center gap-3">
            <div class="hidden sm:flex items-center justify-center w-10 h-10 rounded-xl transition-all"
                 :class="theme === 'perfectlum' ? 'bg-gray-50 text-gray-400' : 'bg-white/[0.03] text-white/30'">
                <i data-lucide="home" class="w-4 h-4"></i>
            </div>
            
            <div class="flex flex-col">
                <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.1em] opacity-30">
                    <span>Admin Console</span>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span x-text="activeMenu"></span>
                </div>
                <h1 class="text-[15px] font-bold tracking-tight" :class="theme === 'perfectlum' ? 'text-gray-900' : 'text-white'" x-text="activeMenu"></h1>
            </div>
        </div>
    </div>

    <!-- Header Actions -->
    <div class="flex items-center gap-5">
        
        <!-- Search Bar (Premium Design) -->
        <div class="hidden md:flex relative group">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none opacity-40 group-focus-within:opacity-100 transition-opacity">
                <i data-lucide="search" class="w-4 h-4"></i>
            </div>
            <input type="text" placeholder="Jump to anything..." 
                   class="w-72 h-11 pl-11 pr-5 rounded-2xl text-[13px] font-semibold border transition-all placeholder-white/20"
                   :class="theme === 'perfectlum' ? 'bg-gray-50 border-gray-100 focus:bg-white focus:ring-4 focus:ring-gray-100 text-gray-900' : 'bg-white/[0.03] border-white/5 focus:bg-white/[0.07] focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500/30 text-white'">
            
            <div class="absolute right-3 top-1/2 -translate-y-1/2 hidden lg:flex items-center gap-1 opacity-20 group-focus-within:hidden">
                <kbd class="px-1.5 py-0.5 rounded border text-[10px] font-bold">⌘</kbd>
                <kbd class="px-1.5 py-0.5 rounded border text-[10px] font-bold">K</kbd>
            </div>
        </div>

        <div class="w-px h-6 mx-1 opacity-5" :class="theme === 'perfectlum' ? 'bg-black' : 'bg-white'"></div>

        <!-- Notifications -->
        <button class="relative p-2.5 rounded-xl transition-all hover:scale-105 active:scale-95" 
                :class="theme === 'perfectlum' ? 'bg-gray-50 text-gray-600 hover:bg-gray-100' : 'bg-white/[0.03] text-white/40 hover:bg-white/10 hover:text-white'">
            <i data-lucide="bell" class="w-5 h-5"></i>
            <span class="absolute top-2 right-2 w-2 h-2 rounded-full border-2 animate-pulse" 
                  :class="theme === 'perfectlum' ? 'bg-red-500 border-white' : 'bg-sky-500 border-[#0A0A0B]'"></span>
        </button>

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

                <div class="hidden min-w-0 text-left lg:block">
                    <p class="truncate text-[13px] font-semibold" :class="theme === 'perfectlum' ? 'text-slate-900' : 'text-white'">{{ $displayName }}</p>
                    <p class="truncate text-[11px] font-medium text-slate-400">{{ $user?->email ?: 'Profile' }}</p>
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
                        <p class="truncate text-sm font-semibold text-slate-900">{{ $displayName }}</p>
                        <p class="truncate text-xs text-slate-500">{{ $user?->email ?: 'No email configured' }}</p>
                    </div>
                </div>

                <div class="mt-3 space-y-1">
                    <a
                        href="{{ url('profile-settings') }}"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
                        <i data-lucide="user-round" class="h-4 w-4"></i>
                        Profile Settings
                    </a>
                    <a
                        href="{{ url('logout') }}"
                        class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-rose-600 transition hover:bg-rose-50">
                        <i data-lucide="log-out" class="h-4 w-4"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
