@props([
    'title',
    'description' => '',
    'type' => 'ok',
    'gridId' => 'display-status-grid',
])

@include('common.navigations.header')

<main class="main-vertical-layout">
    <div class="container-fluid">
        <section class="space-y-6 py-4">
            <x-page-header :title="$title" :description="$description" />

            <x-data-table :id="$gridId" class="shadow-sm">
                <div class="border-t border-slate-200/70 px-6 py-4 text-sm text-slate-500" id="{{ $gridId }}-summary">
                    Memuat data display...
                </div>
            </x-data-table>
        </section>
    </div>
</main>

@include('common.navigations.footer')

<script>
    (() => {
        const gridId = @js($gridId);
        const type = @js($type);
        const summaryId = `${gridId}-summary`;

        const statusBadge = (status) => {
            const isOk = Number(status) === 1;
            const classes = isOk
                ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                : 'bg-rose-50 text-rose-700 ring-rose-200';

            return gridjs.html(`<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset ${classes}">${isOk ? 'OK' : 'Not OK'}</span>`);
        };

        const renderErrors = (errors) => {
            if (!Array.isArray(errors) || errors.length === 0) {
                return gridjs.html('<span class="text-slate-400">No errors</span>');
            }

            const items = errors
                .map((error) => `<span class="inline-flex rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-200">${String(error)}</span>`)
                .join('');

            return gridjs.html(`<div class="flex flex-wrap gap-2">${items}</div>`);
        };

        const grid = Perfectlum.createGrid(gridId, {
            columns: [
                {
                    id: 'displayName',
                    name: 'Display',
                    formatter: (_, row) => gridjs.html(`
                        <div class="space-y-1">
                            <p class="font-semibold text-slate-900">${Perfectlum.escapeHtml(row.cells[0].data)}</p>
                            <p class="text-xs text-slate-500">${Perfectlum.escapeHtml(row.cells[1].data)} / ${Perfectlum.escapeHtml(row.cells[2].data)}</p>
                        </div>
                    `),
                },
                { id: 'wsName', hidden: true },
                { id: 'wgName', hidden: true },
                {
                    id: 'status',
                    name: 'Status',
                    formatter: (cell) => statusBadge(cell),
                },
                {
                    id: 'location',
                    name: 'Location',
                    formatter: (cell) => gridjs.html(`<span class="text-sm text-slate-600">${cell || '-'}</span>`),
                },
                {
                    id: 'errors',
                    name: 'Errors',
                    formatter: (cell) => renderErrors(cell),
                },
                {
                    id: 'updatedAt',
                    name: 'Updated',
                    formatter: (cell) => gridjs.html(`<span class="text-sm text-slate-600">${cell || '-'}</span>`),
                },
            ],
            pagination: {
                enabled: true,
                limit: 10,
                server: {
                    url: (prev, page, limit) => {
                        const query = new URLSearchParams({
                            page: String(page + 1),
                            limit: String(limit),
                            type,
                            sort: 'updated_at',
                            order: 'desc',
                        });

                        return `${prev}?${query.toString()}`;
                    },
                },
            },
            sort: true,
            search: {
                enabled: true,
                server: {
                    url: (prev, keyword) => {
                        const url = new URL(prev, window.location.origin);
                        if (keyword) {
                            url.searchParams.set('search', keyword);
                        } else {
                            url.searchParams.delete('search');
                        }
                        return url.pathname + url.search;
                    },
                },
            },
            server: {
                url: '/api/displays',
                then: (payload) => {
                    const summary = document.getElementById(summaryId);
                    if (summary) {
                        summary.textContent = `Menampilkan ${payload.data.length} dari ${payload.total} display.`;
                    }
                    return payload.data;
                },
                total: (payload) => payload.total,
            },
            language: {
                search: {
                    placeholder: 'Cari display, workstation, workgroup...',
                },
                pagination: {
                    previous: 'Prev',
                    next: 'Next',
                    showing: 'Menampilkan',
                    results: () => 'hasil',
                },
            },
        });
    })();
</script>
