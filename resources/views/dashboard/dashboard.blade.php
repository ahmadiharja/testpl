@include('common.navigations.header')

{{-- PREMIER DASHBOARD REDESIGN (Responsive Light/Dark) --}}
<div class="flex flex-col gap-8 pb-12 w-full mx-auto 2xl:max-w-[1600px] xl:max-w-none">

    <x-page-header
        title="Dashboard"
        description="Good morning, {{ $greetingName }}. Monitor display health, task pipeline, and recent calibration activity from one page."
        icon="layout-dashboard"
    >
        <x-slot name="actions">
            <button
                type="button"
                onclick="window.refreshDashboardGrids && window.refreshDashboardGrids()"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                Refresh Data
            </button>
        </x-slot>
    </x-page-header>

    {{-- 2. BENTO STATS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Stat Card: Displays OK --}}
        <a href="{{ url('displays?type=ok') }}" class="group bento-card p-8 flex flex-col justify-between h-48 sm:h-56">
            <div class="flex justify-between items-start">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm"
                     :class="theme === 'perfectlum' ? 'bg-emerald-50 text-emerald-600' : 'bg-emerald-500/10 text-emerald-400'">
                    <i data-lucide="monitor-check" class="w-7 h-7"></i>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-[0.2em] px-2.5 py-1 rounded-md"
                      :class="theme === 'perfectlum' ? 'bg-emerald-100 text-emerald-700' : 'bg-emerald-500/20 text-emerald-400'">
                    Healthy
                </span>
            </div>
            
            <div class="mt-4 relative z-10 w-full text-left">
                <p class="text-[11px] font-bold opacity-40 uppercase tracking-[0.2em] mb-1">Network Health</p>
                <div class="flex items-end gap-3 w-full">
                    <h3 class="text-5xl font-extrabold tracking-tighter leading-none"
                        :class="theme === 'perfectlum' ? 'text-gray-900' : 'text-emerald-400'">
                        {{ $d_ok }}
                    </h3>
                    <span class="text-[13px] font-bold mb-1"
                          :class="theme === 'perfectlum' ? 'text-emerald-600' : 'text-emerald-500/50'">
                        Displays OK
                    </span>
                </div>
            </div>
            @if(session('platform', 'perfectlum') != 'perfectlum')
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-emerald-500/5 blur-3xl rounded-full"></div>
            @endif
        </a>

        {{-- Stat Card: Critical Issues --}}
        <a href="{{ url('displays?type=failed') }}" class="group bento-card p-8 flex flex-col justify-between h-48 sm:h-56"
           :class="theme === 'perfectchroma' ? 'border-red-500/20 hover:border-red-500/40' : 'border-red-200 hover:border-red-300 shadow-[0_8px_30px_rgba(239,68,68,0.06)]'">
            <div class="flex justify-between items-start">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm"
                     :class="theme === 'perfectlum' ? 'bg-red-50 text-red-600' : 'bg-red-500/10 text-red-500'">
                    <i data-lucide="alert-octagon" class="w-7 h-7"></i>
                </div>
                @if($d_fail > 0)
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] px-2.5 py-1 rounded-md animate-pulse"
                          :class="theme === 'perfectlum' ? 'bg-red-100 text-red-700' : 'bg-red-500/20 text-red-400'">
                        Action Req.
                    </span>
                @endif
            </div>
            
            <div class="mt-4 relative z-10 text-left">
                <p class="text-[11px] font-bold opacity-40 uppercase tracking-[0.2em] mb-1">Critical Alerts</p>
                <div class="flex items-end gap-3 w-full">
                    <h3 class="text-5xl font-extrabold tracking-tighter leading-none"
                        :class="theme === 'perfectlum' ? 'text-gray-900' : 'text-red-500'">
                        {{ $d_fail }}
                    </h3>
                    <span class="text-[13px] font-bold mb-1"
                          :class="theme === 'perfectlum' ? 'text-red-500' : 'text-red-500/50'">
                        Needs Repair
                    </span>
                </div>
            </div>
        </a>

        {{-- Stat Card: Pending Tasks --}}
        <a href="{{ url('scheduler') }}" class="group bento-card p-8 flex flex-col justify-between h-48 sm:h-56">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm"
                 :class="theme === 'perfectlum' ? 'bg-amber-50 text-amber-600' : 'bg-amber-500/10 text-amber-500'">
                <i data-lucide="calendar-clock" class="w-7 h-7"></i>
            </div>
            
            <div class="mt-4 relative z-10 text-left">
                <p class="text-[11px] font-bold opacity-40 uppercase tracking-[0.2em] mb-1">Scheduled Maintenance</p>
                <div class="flex items-end gap-3 w-full">
                    <h3 class="text-5xl font-extrabold tracking-tighter leading-none" id="due_tasks_stats"
                        :class="theme === 'perfectlum' ? 'text-gray-900' : 'text-amber-500'">
                        {{ $due_tasks }}
                    </h3>
                    <span class="text-[13px] font-bold mb-1"
                          :class="theme === 'perfectlum' ? 'text-amber-600' : 'text-amber-500/50'">
                        Due Tasks
                    </span>
                </div>
            </div>
        </a>

        {{-- Stat Card: Active Terminals --}}
        <a href="{{ url('workstations') }}" class="group bento-card p-8 flex flex-col justify-between h-48 sm:h-56">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110 shadow-sm"
                 :class="theme === 'perfectlum' ? 'bg-blue-50 text-blue-600' : 'bg-sky-500/10 text-sky-400'">
                <i data-lucide="layout-grid" class="w-7 h-7"></i>
            </div>
            
            <div class="mt-4 relative z-10 text-left">
                <p class="text-[11px] font-bold opacity-40 uppercase tracking-[0.2em] mb-1">Total Clusters</p>
                <div class="flex items-end gap-3 w-full">
                    <h3 class="text-5xl font-extrabold tracking-tighter leading-none"
                        :class="theme === 'perfectlum' ? 'text-gray-900' : 'text-sky-400'">
                        {{ $workstations }}
                    </h3>
                    <span class="text-[13px] font-bold mb-1"
                          :class="theme === 'perfectlum' ? 'text-blue-600' : 'text-sky-500/50'">
                        Workstations
                    </span>
                </div>
            </div>
        </a>
    </div>

    {{-- 3. MAIN WORKSPACE (Bento Grid 2nd Row) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mt-4">

        {{-- Left: Failed Displays (8 Cols) --}}
        <x-dashboard-section title="Displays Not OK" description="Critical displays with the most recent error summary." class="lg:col-span-8">
            <x-slot name="icon">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
            </x-slot>
            <x-slot name="actions">
                <a href="{{ url('displays?type=failed') }}"
                   class="text-[12px] font-bold uppercase tracking-[0.15em] opacity-40 hover:opacity-100 transition-opacity flex items-center gap-2 group theme-lum:text-gray-900 theme-chroma:text-white">
                    View Network <i data-lucide="arrow-right" class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1"></i>
                </a>
            </x-slot>
            <div class="workstation-table-shell flex-1 min-h-[22rem] overflow-hidden">
                @if($d_fail == 0)
                    <div class="flex flex-col items-center justify-center py-24 opacity-60">
                        <div class="w-24 h-24 rounded-[2rem] flex items-center justify-center mb-6 shadow-sm"
                             :class="theme === 'perfectlum' ? 'bg-emerald-50 text-emerald-500' : 'bg-emerald-500/10 text-emerald-500'">
                            <i data-lucide="shield-check" class="w-10 h-10"></i>
                        </div>
                        <p class="text-xl font-extrabold theme-lum:text-gray-900 theme-chroma:text-white">Network is healthy</p>
                        <p class="text-sm font-medium theme-lum:text-gray-500 theme-chroma:text-gray-400 mt-1">No critical display failures detected.</p>
                    </div>
                @else
                    <div id="failed-displays-grid" class="w-full"></div>
                @endif
            </div>
        </x-dashboard-section>

        {{-- Right: Quick Stats & Connections (4 Cols) --}}
        <x-dashboard-section title="Latest Performed" description="Recent completed calibration activity across the current facility." class="lg:col-span-4">
            <x-slot name="icon">
                <i data-lucide="activity" class="w-4 h-4"></i>
            </x-slot>
            <x-slot name="actions">
                <button onclick="window.refreshDashboardGrids && window.refreshDashboardGrids()"
                   class="text-[12px] font-bold uppercase tracking-[0.15em] opacity-40 hover:opacity-100 transition-opacity flex items-center gap-2 group theme-lum:text-gray-900 theme-chroma:text-white">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5 transition-transform group-hover:rotate-180 duration-500"></i>
                </button>
            </x-slot>
            <div class="workstation-table-shell flex-1 flex min-h-[22rem] flex-col overflow-hidden" style="min-height:0">
                <div id="latest-performed-grid" class="w-full flex-1 overflow-y-auto" style="max-height:420px; background:transparent;"></div>
            </div>
        </x-dashboard-section>
    </div>

    {{-- 4. BOTTOM SECTION (Due Tasks Full Width) --}}
    <x-dashboard-section title="Maintenance Pipeline" description="Tasks that are due soon or already overdue." class="mt-4">
        <x-slot name="icon">
            <i data-lucide="flag" class="w-4 h-4"></i>
        </x-slot>
        <x-slot name="actions">
            <a href="{{ url('scheduler') }}" class="text-[12px] font-bold uppercase tracking-[0.15em] opacity-40 hover:opacity-100 transition-opacity flex items-center gap-2 group theme-lum:text-gray-900 theme-chroma:text-white">
                All Tasks <i data-lucide="arrow-right" class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1"></i>
            </a>
        </x-slot>
        <div class="workstation-table-shell overflow-hidden p-0">
            <div id="due-tasks-grid" class="w-full" x-data="{
                init() {
                    const checkGrids = () => {
                        if (window.renderDashboardGrids) {
                            window.renderDashboardGrids();
                        } else {
                            setTimeout(checkGrids, 100);
                        }
                    };
                    checkGrids();
                }
            }"></div>
        </div>

        <script>
            function dashboardHistoryUrl(displayId, historyId = null) {
                if (historyId) {
                    return `/histories/${historyId}`;
                }

                const url = new URL(@json(url('histories-reports')), window.location.origin);
                url.searchParams.set('display_id', displayId);
                return `${url.pathname}${url.search}`;
            }

            window.dashboardGridsRendered = false;
            window.refreshDashboardGrids = function () {
                ['due-tasks-grid', 'failed-displays-grid', 'latest-performed-grid'].forEach((id) => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.innerHTML = '';
                    }
                });
                window.dashboardGridsRendered = false;
                window.renderDashboardGrids();
            };
            window.renderDashboardGrids = function() {
                if (window.dashboardGridsRendered) return;
                if (typeof gridjs === 'undefined') {
                    setTimeout(window.renderDashboardGrids, 100);
                    return;
                }

                // 1. DUE TASKS GRID
                if (document.getElementById("due-tasks-grid")) {
                    Perfectlum.createGrid(document.getElementById("due-tasks-grid"), {
                        columns: [
                            {
                                name: "Display & Location",
                                formatter: (cell, row) => {
                                    const data = row.cells[0].data;
                                    return gridjs.html(`
                                        <div class='flex flex-col font-inter' style='margin-top: -0.3rem; margin-bottom: -0.3rem;'>
                                            <button onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: ${data.displayId} }}))" class='text-left font-bold text-[13px] text-gray-900 group-[.theme-chroma]:text-white hover:text-sky-500 transition-colors duration-300 leading-tight cursor-pointer'>${data.displayName}</button>
                                            <div class='flex items-center gap-2 text-[10px] font-medium text-gray-500 group-[.theme-chroma]:text-gray-400 mt-[1px]'>
                                                <button onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'workstation', id: ${data.wsId} }}))" class='flex items-center gap-1 hover:text-sky-500 transition-colors cursor-pointer'>
                                                    <svg class="w-3.5 h-3.5 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg> ${data.wsName}
                                                </button>
                                                <span class='w-1 h-1 rounded-full bg-gray-300 group-[.theme-chroma]:bg-white/10'></span>
                                                <button onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'workgroup', id: ${data.wgId} }}))" class='flex items-center gap-1 hover:text-sky-500 transition-colors cursor-pointer'>
                                                    <svg class="w-3.5 h-3.5 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> ${data.wgName}
                                                </button>
                                                <span class='w-1 h-1 rounded-full bg-gray-300 group-[.theme-chroma]:bg-white/10'></span>
                                                <button onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'facility', id: ${data.facId} }}))" class='flex items-center gap-1 hover:text-sky-500 transition-colors cursor-pointer'>
                                                    <svg class="w-3.5 h-3.5 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="22"/><line x1="15" y1="22" x2="15" y2="22"/><line x1="9" y1="6" x2="9" y2="6"/><line x1="15" y1="6" x2="15" y2="6"/><line x1="9" y1="10" x2="9" y2="10"/><line x1="15" y1="10" x2="15" y2="10"/><line x1="9" y1="14" x2="9" y2="14"/><line x1="15" y1="14" x2="15" y2="14"/><line x1="9" y1="18" x2="9" y2="18"/><line x1="15" y1="18" x2="15" y2="18"/></svg> ${data.facName}
                                                </button>
                                            </div>
                                        </div>
                                    `);
                                }
                            },
                            { 
                                name: "Task",
                                formatter: (cell) => gridjs.html(`<span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200 group-[.theme-chroma]:bg-amber-500/10 group-[.theme-chroma]:text-amber-400 group-[.theme-chroma]:border-amber-500/20">${cell}</span>`)
                            },
                            { 
                                name: "Schedule",
                                formatter: (cell) => gridjs.html(`<span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-blue-50 text-blue-600 border border-blue-200 group-[.theme-chroma]:bg-sky-500/10 group-[.theme-chroma]:text-sky-400 group-[.theme-chroma]:border-sky-500/20">${cell}</span>`)
                            },
                            { 
                                name: "Due Date",
                                formatter: (cell) => {
                                    const isPast = cell.isPast;
                                    const isToday = cell.isToday;
                                    let bgBase = 'bg-gray-100 text-gray-700 border-gray-200 group-[.theme-chroma]:bg-white/5 group-[.theme-chroma]:text-gray-300 group-[.theme-chroma]:border-white/10';
                                    if (isPast) bgBase = 'bg-red-50 text-red-600 border-red-200 group-[.theme-chroma]:bg-red-500/10 group-[.theme-chroma]:text-red-400 group-[.theme-chroma]:border-red-500/20';
                                    else if (isToday) bgBase = 'bg-amber-50 text-amber-600 border-amber-200 group-[.theme-chroma]:bg-amber-500/10 group-[.theme-chroma]:text-amber-400 group-[.theme-chroma]:border-amber-500/20';
                                    else bgBase = 'bg-emerald-50 text-emerald-600 border-emerald-200 group-[.theme-chroma]:bg-emerald-500/10 group-[.theme-chroma]:text-emerald-400 group-[.theme-chroma]:border-emerald-500/20';
                                    
                                    return gridjs.html(`<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold border ${bgBase}"><svg class="w-3.5 h-3.5 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> ${cell.formatted}</span>`);
                                }
                            },
                            { 
                                name: "Overdue",
                                formatter: (cell, row) => {
                                    const isPast = row.cells[3].data.isPast;
                                    const isToday = row.cells[3].data.isToday;
                                    let colorClass = 'text-emerald-600 group-[.theme-chroma]:text-emerald-400';
                                    if (isPast) colorClass = 'text-red-600 group-[.theme-chroma]:text-red-500';
                                    else if (isToday) colorClass = 'text-amber-600 group-[.theme-chroma]:text-amber-500';
                                    return gridjs.html(`<span class="text-xs font-bold whitespace-nowrap ${colorClass}">${cell}</span>`);
                                }
                            },
                            { 
                                name: "",
                                sort: false,
                                formatter: (cell, row) => {
                                    return gridjs.html(`
                                        <a href="/display-settings/${row.cells[0].data.displayId}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 transition-colors group-[.theme-chroma]:bg-sky-500/10 group-[.theme-chroma]:text-sky-400 group-[.theme-chroma]:hover:bg-sky-500/20">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </a>
                                    `);
                                }
                            }
                        ],
                        server: {
                            url: '/api/due-tasks',
                            then: data => data.map(item => [
                                {
                                    displayName: item.displayName,
                                    wsName: item.wsName,
                                    wgName: item.wgName,
                                    facName: item.facName,
                                    displayId: item.displayId,
                                    wsId: item.wsId,
                                    wgId: item.wgId,
                                    facId: item.facId
                                },
                                item.task,
                                item.schedule,
                                {
                                    formatted: item.dueAt,
                                    isPast: item.isPast,
                                    isToday: item.isToday
                                },
                                item.overdue,
                                null
                            ])
                        },
                        sort: true,
                        pagination: false,
                        search: false,
                        className: {
                            table: 'w-full text-sm text-left',
                            thead: 'bg-gray-50/50 group-[.theme-chroma]:bg-white/5',
                            th: 'px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-400 border-b border-gray-100 group-[.theme-chroma]:border-white/5 bg-transparent',
                            td: 'px-6 py-4 border-b border-gray-50 group-[.theme-chroma]:border-white/5 bg-transparent',
                            container: 'group group-[.theme-chroma]:text-white'
                        },
                        style: {
                            table: { border: 'none' },
                            th: { background: 'transparent', boxShadow: 'none' },
                            td: { background: 'transparent' },
                            footer: { background: 'transparent' }
                        }
                    });
                }

                // 2. FAILED DISPLAYS GRID
                if (document.getElementById("failed-displays-grid")) {
                    Perfectlum.createGrid(document.getElementById("failed-displays-grid"), {
                        columns: [
                            {
                                name: "Display & Location",
                                formatter: (cell, row) => {
                                    const data = row.cells[0].data;
                                    return gridjs.html(`
                                        <div class='flex flex-col font-inter' style='margin-top: -0.3rem; margin-bottom: -0.3rem;'>
                                            <button onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: ${data.displayId} }}))" class='text-left font-bold text-[13px] text-gray-900 group-[.theme-chroma]:text-white hover:text-sky-500 transition-colors duration-300 leading-tight cursor-pointer'>${data.displayName}</button>
                                            <div class='flex items-center gap-2 text-[10px] font-medium text-gray-500 group-[.theme-chroma]:text-gray-400 mt-[1px]'>
                                                <button onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'workstation', id: ${data.wsId} }}))" class='flex items-center gap-1 hover:text-sky-500 transition-colors cursor-pointer'>
                                                    <svg class="w-3.5 h-3.5 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg> ${data.wsName}
                                                </button>
                                                <span class='w-1 h-1 rounded-full bg-gray-300 group-[.theme-chroma]:bg-white/10'></span>
                                                <button onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'workgroup', id: ${data.wgId} }}))" class='flex items-center gap-1 hover:text-sky-500 transition-colors cursor-pointer'>
                                                    <svg class="w-3.5 h-3.5 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> ${data.wgName}
                                                </button>
                                                <span class='w-1 h-1 rounded-full bg-gray-300 group-[.theme-chroma]:bg-white/10'></span>
                                                <button onclick="window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'facility', id: ${data.facId} }}))" class='flex items-center gap-1 hover:text-sky-500 transition-colors cursor-pointer'>
                                                    <svg class="w-3.5 h-3.5 opacity-50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="22"/><line x1="15" y1="22" x2="15" y2="22"/><line x1="9" y1="6" x2="9" y2="6"/><line x1="15" y1="6" x2="15" y2="6"/><line x1="9" y1="10" x2="9" y2="10"/><line x1="15" y1="10" x2="15" y2="10"/><line x1="9" y1="14" x2="9" y2="14"/><line x1="15" y1="14" x2="15" y2="14"/><line x1="9" y1="18" x2="9" y2="18"/><line x1="15" y1="18" x2="15" y2="18"/></svg> ${data.facName}
                                                </button>
                                            </div>
                                        </div>
                                    `);
                                }
                            },
                            { 
                                name: "Last Update",
                                formatter: (cell) => gridjs.html(`<span class="text-xs font-bold text-gray-500 group-[.theme-chroma]:text-gray-400">${cell}</span>`)
                            },
                            { 
                                name: "Error Details",
                                formatter: (cell) => gridjs.html(`<div class='leading-tight text-[12px] opacity-80 whitespace-normal line-clamp-2 max-w-xs' style='margin-top:-0.2rem; margin-bottom:-0.2rem;' title='${cell.replace(/'/g, "&#39;")}'>${cell}</div>`)
                            },
                            { 
                                name: "",
                                sort: false,
                                formatter: (cell, row) => {
                                    return gridjs.html(`
                                        <div class="flex gap-2 justify-end pr-4">
                                            <a href="javascript:window.dispatchEvent(new CustomEvent('open-hierarchy', { detail: { type: 'display', id: ${row.cells[0].data.displayId} } }))" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-100 transition-colors group-[.theme-chroma]:bg-sky-500/10 group-[.theme-chroma]:text-sky-400 group-[.theme-chroma]:hover:bg-sky-500/20">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </a>
                                            <a href="${dashboardHistoryUrl(row.cells[0].data.displayId)}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors group-[.theme-chroma]:bg-amber-500/10 group-[.theme-chroma]:text-amber-400 group-[.theme-chroma]:hover:bg-amber-500/20">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            </a>
                                        </div>
                                    `);
                                }
                            }
                        ],
                        server: {
                            url: '/api/displays-failed',
                            then: data => data.map(item => [
                                {
                                    displayName: item.displayName,
                                    wsName: item.wsName,
                                    wgName: item.wgName,
                                    facName: item.facName,
                                    displayId: item.displayId,
                                    wsId: item.wsId,
                                    wgId: item.wgId,
                                    facId: item.facId
                                },
                                item.updatedAt,
                                item.errorMsg,
                                null
                            ])
                        },
                        sort: true,
                        pagination: false,
                        search: false,
                        className: {
                            table: 'w-full text-sm text-left',
                            thead: 'bg-gray-50/50 group-[.theme-chroma]:bg-white/5',
                            th: 'px-6 py-4 text-xs font-black uppercase tracking-wider text-gray-400 border-b border-gray-100 group-[.theme-chroma]:border-white/5 bg-transparent',
                            td: 'px-6 py-4 border-b border-gray-50 group-[.theme-chroma]:border-white/5 bg-transparent',
                            container: 'group group-[.theme-chroma]:text-white'
                        },
                        style: { table: { border: 'none' }, th: { background: 'transparent', boxShadow: 'none' }, td: { background: 'transparent' }, footer: { background: 'transparent' } }
                    });
                }

                // 3. LATEST PERFORMED GRID (Timeline layout via CSS/Formatter)
                if (document.getElementById("latest-performed-grid")) {
                    Perfectlum.createGrid(document.getElementById("latest-performed-grid"), {
                        columns: [
                            {
                                name: "", // Headerless
                                formatter: (cell, row) => {
                                    const data = row.cells[0].data;
                                    const isOk = data.result === 'ok';
                                    const iconColor = isOk ? 'bg-emerald-50 text-emerald-500 border-white group-[.theme-chroma]:bg-emerald-500/10 group-[.theme-chroma]:border-black' 
                                                           : 'bg-red-50 text-red-500 border-white group-[.theme-chroma]:bg-red-500/10 group-[.theme-chroma]:border-black';
                                    const iconSvg = isOk ? '<path d="M20 6L9 17l-5-5"/>' : '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>';
                                    
                                    return gridjs.html(`
                                        <div class="relative pl-10 pb-4 w-full">
                                            <div class="absolute top-6 left-[11px] w-[1px] h-full bg-gray-100 group-[.theme-chroma]:bg-white/5"></div>
                                            <div class="absolute left-0 top-0.5 w-[22px] h-[22px] rounded-full flex items-center justify-center shrink-0 z-10 border ${iconColor}">
                                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${iconSvg}</svg>
                                            </div>
                                            <div class="flex flex-col gap-0.5 w-full">
                                                <a href="${dashboardHistoryUrl(null, data.historyId)}" class="text-[13px] font-bold theme-lum:text-gray-900 group-[.theme-chroma]:text-white hover:text-sky-500 transition-colors cursor-pointer leading-tight truncate block mb-0.5">
                                                    ${data.name}
                                                </a>
                                                <p class="text-[11px] font-medium opacity-60 theme-lum:text-gray-600 group-[.theme-chroma]:text-gray-400 mt-[1px] truncate block mb-1">
                                                    ${data.displayName}
                                                </p>
                                                <div class="flex items-center gap-2 text-[10px] font-medium mt-[1px] opacity-70 theme-lum:text-gray-500 group-[.theme-chroma]:text-gray-400 truncate">
                                                    <span class="flex items-center gap-1"><svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg> ${data.wsName}</span>
                                                    <span class="w-1 h-1 rounded-full bg-current opacity-30"></span>
                                                    <span class="flex items-center gap-1"><svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg> ${data.wgName}</span>
                                                    <span class="w-1 h-1 rounded-full bg-current opacity-30"></span>
                                                    <span class="flex items-center gap-1"><svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="22"/><line x1="15" y1="22" x2="15" y2="22"/><line x1="9" y1="6" x2="9" y2="6"/><line x1="15" y1="6" x2="15" y2="6"/><line x1="9" y1="10" x2="9" y2="10"/><line x1="15" y1="10" x2="15" y2="10"/><line x1="9" y1="14" x2="9" y2="14"/><line x1="15" y1="14" x2="15" y2="14"/><line x1="9" y1="18" x2="9" y2="18"/><line x1="15" y1="18" x2="15" y2="18"/></svg> ${data.facName}</span>
                                                </div>
                                                <p class="text-[10px] font-bold mt-1 opacity-50 theme-lum:text-gray-400 group-[.theme-chroma]:text-gray-500">
                                                    ${data.timeFormatted}
                                                </p>
                                            </div>
                                        </div>
                                    `);
                                }
                            }
                        ],
                        server: {
                            url: '/api/latest-performed',
                            then: data => data.map(item => [{
                                historyId: item.historyId,
                                result: item.result,
                                name: item.name,
                                displayName: item.displayName,
                                wsName: item.wsName,
                                wgName: item.wgName,
                                facName: item.facName,
                                timeFormatted: item.timeFormatted
                            }])
                        },
                        pagination: false,
                        search: false,
                        sort: false,
                        className: {
                            table: 'w-full text-sm text-left',
                            thead: 'hidden',
                            td: 'border-none',
                            container: 'group group-[.theme-chroma]:text-white'
                        },
                        style: {
                            table: { border: 'none', background: 'transparent' },
                            td: { background: 'transparent', padding: '0.1rem 0' },
                            footer: { background: 'transparent' }
                        }
                    });
                }
                
                window.dashboardGridsRendered = true;
            };
        </script>
    </x-dashboard-section>

    {{-- 5. REMOTE PORTAL CARD (Moved from top) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-4">
        <div class="bento-card p-1 relative overflow-hidden group">
            <div class="px-7 py-6 border-b flex items-center gap-3"
                 :class="theme === 'perfectlum' ? 'bg-gray-50/50 border-gray-100' : 'bg-white/[0.02] border-white/5'">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center shadow-sm"
                     :class="theme === 'perfectlum' ? 'bg-blue-50 text-blue-600' : 'bg-sky-500/10 text-sky-400'">
                    <i data-lucide="globe" class="w-4 h-4"></i>
                </div>
                <h3 class="font-extrabold text-[15px] theme-lum:text-gray-900 theme-chroma:text-white">Remote Portal Info</h3>
            </div>
            
            <div class="p-7 space-y-6">
                <div class="group/field">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40 mb-2 theme-lum:text-gray-600 theme-chroma:text-gray-400">Endpoint URL</p>
                    <div class="flex items-center justify-between gap-4 p-4 rounded-2xl border transition-colors shadow-sm"
                         :class="theme === 'perfectlum' ? 'bg-gray-50 border-gray-200 group-hover/field:border-blue-400' : 'bg-black border-white/5 group-hover/field:border-sky-500/30'">
                        <span class="text-[13px] font-bold truncate theme-lum:text-gray-800 theme-chroma:text-white">{{ url('/') }}</span>
                        <button onclick="copy_field('#endpoint_url')" class="p-2 rounded-lg transition-colors shrink-0"
                                :class="theme === 'perfectlum' ? 'hover:bg-white text-blue-600 shadow-sm' : 'hover:bg-white/10 text-sky-400'">
                            <i data-lucide="copy" class="w-4 h-4"></i>
                        </button>
                        <input id="endpoint_url" type="hidden" value="{{ url('/') }}">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="group/field">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40 mb-2 theme-lum:text-gray-600 theme-chroma:text-gray-400">Service ID</p>
                        <div class="flex items-center justify-between gap-3 p-4 rounded-2xl border transition-colors shadow-sm"
                             :class="theme === 'perfectlum' ? 'bg-gray-50 border-gray-200 group-hover/field:border-violet-400' : 'bg-black border-white/5 group-hover/field:border-violet-500/30'">
                            <span class="text-[13px] font-bold truncate theme-lum:text-gray-800 theme-chroma:text-white">{{ $user->sync_user }}</span>
                            <input id="sync_user" type="hidden" value="{{ $user->sync_user }}">
                            <button onclick="copy_field('#sync_user')" class="transition-opacity opacity-50 hover:opacity-100"
                                    :class="theme === 'perfectlum' ? 'text-violet-600' : 'text-violet-400'">
                                <i data-lucide="copy" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                    <div class="group/field">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40 mb-2 theme-lum:text-gray-600 theme-chroma:text-gray-400">Token PK</p>
                        <div class="flex items-center justify-between gap-3 p-4 rounded-2xl border transition-colors shadow-sm"
                             :class="theme === 'perfectlum' ? 'bg-gray-50 border-gray-200 group-hover/field:border-orange-400' : 'bg-black border-white/5 group-hover/field:border-amber-500/30'">
                            <span class="text-[13px] font-bold truncate theme-lum:text-gray-800 theme-chroma:text-white">{{ $user->sync_password_raw }}</span>
                            <input id="sync_pass" type="hidden" value="{{ $user->sync_password_raw }}">
                            <button onclick="copy_field('#sync_pass')" class="transition-opacity opacity-50 hover:opacity-100"
                                    :class="theme === 'perfectlum' ? 'text-orange-600' : 'text-amber-400'">
                                <i data-lucide="copy" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="https://qubyx.com/product/remote-server/" target="_blank" 
                       class="w-full flex items-center justify-center gap-2.5 h-12 rounded-2xl text-[12px] font-bold tracking-widest transition-all transform hover:-translate-y-0.5 shadow-lg"
                       :class="theme === 'perfectlum' ? 'bg-gray-900 text-white hover:bg-blue-600 hover:shadow-blue-500/30' : 'bg-white text-black hover:bg-sky-400 hover:text-white hover:shadow-sky-500/30'">
                        <i data-lucide="download-cloud" class="w-4 h-4"></i>
                        DOWNLOAD CLIENT
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

@include('common.navigations.footer')
