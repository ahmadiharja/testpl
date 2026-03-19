@include('common.navigations.header')

<div x-data="notificationsPage()" x-init="init()" class="flex flex-col gap-6 pb-10">
    <x-page-header
        title="Notifications"
        description="Review alerts, reminders, and operational updates assigned to your workspace."
        icon="bell-ring"
    >
        <x-slot name="actions">
            <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-bold uppercase tracking-[0.18em] text-slate-600">
                <span x-text="unreadCount"></span>
                <span class="ml-2">Unread</span>
            </span>
            <button
                type="button"
                @click="markAllRead()"
                class="inline-flex h-12 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                <i data-lucide="mail-check" class="h-4 w-4"></i>
                Mark all read
            </button>
        </x-slot>
    </x-page-header>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.18)]">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end">
            <div class="space-y-2">
                <label class="block text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">View</label>
                <div class="grid h-12 max-w-sm grid-cols-2 rounded-2xl border border-slate-200 bg-slate-50 p-1">
                    <button
                        type="button"
                        @click="setFilter('unread')"
                        class="rounded-[0.9rem] px-4 text-sm font-semibold transition"
                        :class="filter === 'unread' ? 'bg-white text-sky-700 shadow-[0_8px_24px_-18px_rgba(14,165,233,0.45)]' : 'text-slate-500 hover:bg-white hover:text-slate-800'"
                    >
                        Unread
                    </button>
                    <button
                        type="button"
                        @click="setFilter('all')"
                        class="rounded-[0.9rem] px-4 text-sm font-semibold transition"
                        :class="filter === 'all' ? 'bg-white text-sky-700 shadow-[0_8px_24px_-18px_rgba(14,165,233,0.45)]' : 'text-slate-500 hover:bg-white hover:text-slate-800'"
                    >
                        All
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button
                    type="button"
                    @click="load(1)"
                    class="inline-flex h-12 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                    Refresh
                </button>
            </div>
        </div>
    </section>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_18px_60px_-32px_rgba(15,23,42,0.18)]">
        <div x-show="loading" class="space-y-3">
            <template x-for="index in 6" :key="`skeleton-${index}`">
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-5 py-4">
                    <div class="h-3 w-24 rounded-full bg-slate-200"></div>
                    <div class="mt-4 h-4 w-2/5 rounded-full bg-slate-200"></div>
                    <div class="mt-3 h-3 w-full rounded-full bg-slate-200"></div>
                    <div class="mt-2 h-3 w-3/4 rounded-full bg-slate-200"></div>
                </div>
            </template>
        </div>

        <div x-show="!loading && items.length === 0" class="flex min-h-[20rem] flex-col items-center justify-center rounded-[1.75rem] border border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-[1.5rem] bg-white text-slate-400 shadow-sm" x-html="iconSvg('bell')"></div>
            <h3 class="mt-5 text-xl font-bold tracking-tight text-slate-900" x-text="filter === 'unread' ? 'No unread notifications' : 'No notifications yet'"></h3>
            <p class="mt-2 max-w-lg text-sm leading-6 text-slate-500">Alerts, reminders, and workspace activity assigned to your account will appear here as they happen.</p>
        </div>

        <div x-show="!loading && items.length > 0" class="space-y-3">
            <template x-for="item in items" :key="item.id">
                <article
                    class="rounded-[1.5rem] border px-5 py-4 transition"
                    :class="item.read ? 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/60' : 'border-sky-100 bg-sky-50/50 hover:border-sky-200 hover:bg-sky-50'"
                >
                    <div class="flex items-start gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl" :class="severityClasses(item)" x-html="iconSvg(item.icon)"></div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400" x-text="item.category"></p>
                                        <span x-show="!item.read" class="inline-flex h-2.5 w-2.5 rounded-full bg-sky-500"></span>
                                    </div>
                                    <h3 class="mt-1 text-lg font-semibold tracking-tight text-slate-900" x-text="item.title"></h3>
                                    <p class="mt-2 text-sm leading-6 text-slate-600" x-text="item.body"></p>
                                </div>

                                <div class="flex shrink-0 flex-wrap items-center gap-2">
                                    <span x-show="item.scope" class="inline-flex rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600" x-text="item.scope"></span>
                                    <span class="inline-flex rounded-full px-3 py-1.5 text-xs font-semibold" :class="severityPillClasses(item)" x-text="severityLabel(item)"></span>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-4 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span x-text="item.relativeTime"></span>
                                    <span class="text-slate-300">•</span>
                                    <span x-text="item.createdAt"></span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button
                                        x-show="!item.read"
                                        type="button"
                                        @click.stop="markRead(item.id)"
                                        class="inline-flex items-center rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50"
                                    >
                                        Mark as read
                                    </button>
                                    <button
                                        type="button"
                                        @click="openItem(item)"
                                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50"
                                    >
                                        <span x-text="item.url ? 'Open' : 'View'"></span>
                                        <i data-lucide="arrow-up-right" class="h-3.5 w-3.5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </template>
        </div>

        <div x-show="!loading && items.length > 0" class="mt-5 flex flex-col gap-4 border-t border-slate-200 pt-5 lg:flex-row lg:items-center lg:justify-between">
            <p class="text-sm text-slate-500" x-text="showingText()"></p>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    @click="goToPage(pagination.currentPage - 1)"
                    :disabled="pagination.currentPage <= 1"
                    class="inline-flex items-center rounded-full px-3 py-2 text-sm font-medium transition"
                    :class="pagination.currentPage <= 1 ? 'cursor-not-allowed text-slate-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                >
                    Prev
                </button>

                <template x-for="token in pageTokens()" :key="`page-${token}`">
                    <template x-if="token === 'ellipsis'">
                        <span class="px-2 text-sm text-slate-400">...</span>
                    </template>
                    <template x-if="token !== 'ellipsis'">
                        <button
                            type="button"
                            @click="goToPage(token)"
                            class="inline-flex h-10 min-w-[2.5rem] items-center justify-center rounded-full px-3 text-sm font-semibold transition"
                            :class="token === pagination.currentPage ? 'bg-sky-500 text-white shadow-[0_12px_30px_-18px_rgba(14,165,233,0.55)]' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                            x-text="token"
                        ></button>
                    </template>
                </template>

                <button
                    type="button"
                    @click="goToPage(pagination.currentPage + 1)"
                    :disabled="pagination.currentPage >= pagination.lastPage"
                    class="inline-flex items-center rounded-full px-3 py-2 text-sm font-medium transition"
                    :class="pagination.currentPage >= pagination.lastPage ? 'cursor-not-allowed text-slate-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                >
                    Next
                </button>
            </div>
        </div>
    </section>
</div>

<script>
if (!window.notificationsPage) {
    window.notificationsPage = function () {
        return {
            loading: false,
            filter: 'all',
            items: [],
            unreadCount: 0,
            pagination: {
                currentPage: 1,
                lastPage: 1,
                from: 0,
                to: 0,
                total: 0,
                perPage: 12,
            },
            apiUrl: @json(url('api/notifications')),
            readAllUrl: @json(url('api/notifications/read-all')),
            csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',

            init() {
                this.load(1);
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

            severityPillClasses(item) {
                switch (item.severity) {
                    case 'success':
                        return 'bg-emerald-50 text-emerald-700';
                    case 'warning':
                        return 'bg-amber-50 text-amber-700';
                    case 'danger':
                        return 'bg-rose-50 text-rose-700';
                    default:
                        return 'bg-sky-50 text-sky-700';
                }
            },

            severityLabel(item) {
                switch (item.severity) {
                    case 'success':
                        return 'Resolved';
                    case 'warning':
                        return 'Needs review';
                    case 'danger':
                        return 'Attention';
                    default:
                        return 'Update';
                }
            },

            async load(page = 1) {
                this.loading = true;

                try {
                    const params = new URLSearchParams({
                        filter: this.filter,
                        limit: String(this.pagination.perPage),
                        page: String(page),
                    });

                    const response = await fetch(`${this.apiUrl}?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load notifications.');
                    }

                    const payload = await response.json();
                    this.items = Array.isArray(payload.data) ? payload.data : [];
                    this.unreadCount = Number(payload.unreadCount || 0);

                    const meta = payload.meta || {};
                    this.pagination = {
                        currentPage: Number(meta.currentPage || 1),
                        lastPage: Number(meta.lastPage || 1),
                        from: Number(meta.from || 0),
                        to: Number(meta.to || 0),
                        total: Number(meta.total || 0),
                        perPage: Number(meta.perPage || this.pagination.perPage),
                    };
                } catch (_) {
                    this.items = [];
                    this.pagination = {
                        currentPage: 1,
                        lastPage: 1,
                        from: 0,
                        to: 0,
                        total: 0,
                        perPage: this.pagination.perPage,
                    };
                } finally {
                    this.loading = false;
                }
            },

            setFilter(filter) {
                if (this.filter === filter) {
                    return;
                }

                this.filter = filter;
                this.load(1);
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
                    await this.post(this.readAllUrl);
                    this.load(this.filter === 'unread' ? 1 : this.pagination.currentPage);
                } catch (_) {}
            },

            async markRead(id) {
                try {
                    await this.post(`${this.apiUrl}/${encodeURIComponent(id)}/read`);
                    this.load(this.pagination.currentPage);
                } catch (_) {}
            },

            async openItem(item) {
                if (!item.read) {
                    try {
                        await this.post(`${this.apiUrl}/${encodeURIComponent(item.id)}/read`);
                    } catch (_) {}
                }

                if (item.url) {
                    window.location.href = item.url;
                    return;
                }

                this.load(this.pagination.currentPage);
            },

            goToPage(page) {
                if (page < 1 || page > this.pagination.lastPage || page === this.pagination.currentPage) {
                    return;
                }

                this.load(page);
            },

            showingText() {
                if (!this.pagination.total) {
                    return 'No notifications available';
                }

                return `Showing ${this.pagination.from} to ${this.pagination.to} of ${this.pagination.total} notifications`;
            },

            pageTokens() {
                const current = this.pagination.currentPage;
                const last = this.pagination.lastPage;

                if (last <= 7) {
                    return Array.from({ length: last }, (_, index) => index + 1);
                }

                const tokens = [1];
                const start = Math.max(2, current - 1);
                const end = Math.min(last - 1, current + 1);

                if (start > 2) {
                    tokens.push('ellipsis');
                }

                for (let page = start; page <= end; page += 1) {
                    tokens.push(page);
                }

                if (end < last - 1) {
                    tokens.push('ellipsis');
                }

                tokens.push(last);

                return tokens;
            },
        };
    };
}
</script>

@include('common.navigations.footer')
