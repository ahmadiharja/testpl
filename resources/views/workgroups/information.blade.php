@include('common.navigations.header')

<div class="flex flex-col gap-6 pb-10">
    <x-page-header title="Workgroup Information" description="Summary and workstation inventory for the selected workgroup." icon="network">
        <x-slot name="actions">
            <a href="{{ url('facility-info/' . $item->facility->id) }}" class="rounded-xl bg-black/5 px-4 py-2 text-[13px] font-semibold text-gray-700 transition hover:bg-black/10">
                Back to Facility
            </a>
        </x-slot>
    </x-page-header>

    <x-bento-card title="{{ $item->name }}" dot-color="sky">
        <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 xl:grid-cols-4">
            <div><p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-gray-400">Workgroup</p><p class="mt-2 text-[14px] font-semibold text-gray-900">{{ $item->name }}</p></div>
            <div><p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-gray-400">Facility</p><p class="mt-2 text-[14px] font-semibold text-gray-900">{{ $item->facility->name }}</p></div>
            <div><p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-gray-400">Address</p><p class="mt-2 text-[14px] text-gray-700">{{ $item->address ?: '-' }}</p></div>
            <div><p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-gray-400">Phone</p><p class="mt-2 text-[14px] text-gray-700">{{ $item->phone ?: '-' }}</p></div>
        </div>
    </x-bento-card>

    <x-data-table id="workgroup-workstations-grid" class="mb-4" />
</div>

<script>
(function () {
    function initWorkgroupWorkstationsGrid() {
        const el = document.getElementById('workgroup-workstations-grid');
        if (!el || el._gi) return;
        el._gi = true;

        Perfectlum.createGrid(el, {
            columns: [
                { name: 'Name', formatter: (c) => gridjs.html(`<a href="/workstations-info/${c.id}" class="font-medium text-sky-600 hover:underline">${c.name}</a>`) },
                { name: 'Sleep Time', formatter: (c) => gridjs.html(`<span class="text-gray-600">${c}</span>`) },
                { name: 'Displays', sort: false, formatter: (c) => gridjs.html(`<span class="font-semibold text-gray-700">${c}</span>`) },
                { name: 'Last Connected', sort: false, formatter: (c) => gridjs.html(`<span class="text-gray-600">${c}</span>`) },
                { name: '', sort: false, width: '60px', formatter: (id) => gridjs.html(`<a href="/workstations-info/${id}" class="text-gray-500 transition hover:text-sky-600"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="2"/><circle cx="12" cy="5" r="2"/><circle cx="12" cy="19" r="2"/></svg></a>`) },
            ],
            server: {
                url: '/api/workstations?workgroup_id={{ $item->id }}&facility_id={{ $item->facility->id }}',
                then: d => d.data.map(r => [
                    { id: r.id, name: r.name },
                    r.sleepTime,
                    r.displaysCount,
                    r.lastConnected,
                    r.id,
                ]),
                total: d => d.total,
            },
            pagination: { enabled: true, limit: 10, server: { url: (prev, pg, lim) => prev + (prev.includes('?') ? '&' : '?') + 'page=' + (pg + 1) + '&limit=' + lim } },
            search: { enabled: true, server: { url: (prev, kw) => prev + (prev.includes('?') ? '&' : '?') + 'search=' + encodeURIComponent(kw) } },
            sort: { multiColumn: false },
        });
    }

    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', initWorkgroupWorkstationsGrid) : initWorkgroupWorkstationsGrid();
})();
</script>

@include('common.navigations.footer')
