@include('common.navigations.header')

<div class="flex flex-col gap-6 pb-10">
    <x-page-header title="{{ __('Workstation Information') }}" description="{{ __('Summary and display inventory for the selected workstation.') }}" icon="monitor-speaker">
        <x-slot name="actions">
            <a href="{{ url('workgroups-info/' . $item->workgroup->id) }}" class="rounded-xl bg-black/5 px-4 py-2 text-[13px] font-semibold text-gray-700 transition hover:bg-black/10">
                {{ __('Back to Workgroup') }}
            </a>
        </x-slot>
    </x-page-header>

    <x-bento-card title="{{ $item->name }}" dot-color="sky">
        <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2 xl:grid-cols-4">
            <div><p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-gray-400">{{ __('Workstation') }}</p><p class="mt-2 text-[14px] font-semibold text-gray-900">{{ $item->name }}</p></div>
            <div><p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-gray-400">{{ __('Workgroup') }}</p><p class="mt-2 text-[14px] font-semibold text-gray-900">{{ $item->workgroup->name }}</p></div>
            <div><p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-gray-400">{{ __('Facility') }}</p><p class="mt-2 text-[14px] font-semibold text-gray-900">{{ $item->workgroup->facility->name }}</p></div>
            <div><p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-gray-400">{{ __('Department') }}</p><p class="mt-2 text-[14px] text-gray-700">{{ $item->department ?: '-' }}</p></div>
            <div><p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-gray-400">{{ __('Room Number') }}</p><p class="mt-2 text-[14px] text-gray-700">{{ $item->location ?: '-' }}</p></div>
            <div><p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-gray-400">{{ __('Last Connected') }}</p><p class="mt-2 text-[14px] text-gray-700">{{ $item->last_connected ?: '-' }}</p></div>
            <div><p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-gray-400">{{ __('Calibrate License') }}</p><p class="mt-2 text-[14px] text-gray-700">{{ $item->preference('Cal_license') != '' ? __('Yes') : __('No') }}</p></div>
            <div><p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-gray-400">{{ __('QA License') }}</p><p class="mt-2 text-[14px] text-gray-700">{{ $item->preference('QA_license') != '' ? __('Yes') : __('No') }}</p></div>
            @if ($license = $item->preference('QA_license') != '' ? $item->preference('QA_license') : ($item->preference('Cal_license') != '' ? $item->preference('Cal_license') : ''))
                <div><p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-gray-400">{{ __('License Code') }}</p><p class="mt-2 text-[14px] text-gray-700">{{ $license }}</p></div>
            @endif
            <div><p class="text-[12px] font-semibold uppercase tracking-[0.12em] text-gray-400">{{ __('Client Version') }}</p><p class="mt-2 text-[14px] text-gray-700">{{ $item->client_version ?: '-' }}</p></div>
        </div>
    </x-bento-card>

    <x-data-table id="workstation-displays-grid" class="mb-4" />
</div>

<script>
(function () {
    function initWorkstationDisplaysGrid() {
        const el = document.getElementById('workstation-displays-grid');
        if (!el || el._gi) return;
        el._gi = true;

        Perfectlum.createGrid(el, {
            columns: [
                { name: @json(__('Display Name')), formatter: (c) => gridjs.html(`<a href="/display-settings/${c.id}" class="font-medium text-sky-600 hover:underline">${c.displayName}</a>`) },
                { name: @json(__('Workgroup')), formatter: (c) => gridjs.html(`<span class="text-gray-600">${c}</span>`) },
                { name: @json(__('Facility')), formatter: (c) => gridjs.html(`<span class="text-gray-600">${c}</span>`) },
                { name: @json(__('Status')), formatter: (c) => gridjs.html(`<span class="inline-flex rounded-full ${Number(c) === 1 ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500'} px-2.5 py-1 text-[11px] font-semibold">${Number(c) === 1 ? @js(__('OK')) : @js(__('Failed'))}</span>`) },
                { name: '', sort: false, width: '60px', formatter: (id) => gridjs.html(`<a href="/display-settings/${id}" class="text-gray-500 transition hover:text-sky-600"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="2"/><circle cx="12" cy="5" r="2"/><circle cx="12" cy="19" r="2"/></svg></a>`) },
            ],
            server: {
                url: '/api/displays?workstation_id={{ $item->id }}&facility_id={{ $item->workgroup->facility->id }}',
                then: d => d.data.map(r => [
                    { id: r.id, displayName: r.displayName },
                    r.wgName,
                    r.facName,
                    r.status,
                    r.id,
                ]),
                total: d => d.total,
            },
            pagination: { enabled: true, limit: 10, server: { url: (prev, pg, lim) => prev + (prev.includes('?') ? '&' : '?') + 'page=' + (pg + 1) + '&limit=' + lim } },
            search: { enabled: true, server: { url: (prev, kw) => prev + (prev.includes('?') ? '&' : '?') + 'search=' + encodeURIComponent(kw) } },
            sort: { multiColumn: false },
        });
    }

    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', initWorkstationDisplaysGrid) : initWorkstationDisplaysGrid();
})();
</script>

@include('common.navigations.footer')
