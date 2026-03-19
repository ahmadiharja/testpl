@include('common.navigations.header')

<div class="flex flex-col gap-6 pb-10" x-data="facilityPage()">
    <x-page-header title="Facility Information" description="Update the facility and manage its workgroups from one place." icon="building-2">
        <x-slot name="actions">
            <button type="button" @click="openWorkgroupForm('0')" class="rounded-xl bg-sky-500 px-4 py-2 text-[13px] font-semibold text-white transition hover:bg-sky-400">
                Add Workgroup
            </button>
        </x-slot>
    </x-page-header>

    <x-bento-card title="Facility Details" dot-color="sky">
        <div class="p-6">
            <form method="post" action="" class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                {{ csrf_field() }}
                <input type="hidden" name="facility_update" value="{{ $item->id }}">

                <div>
                    <label class="mb-2 block text-[12px] font-semibold text-gray-500">Facility Name</label>
                    <input type="text" name="name" value="{{ $item->name }}" required class="h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-[13px] text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                </div>
                <div>
                    <label class="mb-2 block text-[12px] font-semibold text-gray-500">Description</label>
                    <input type="text" name="description" value="{{ $item->description }}" class="h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-[13px] text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                </div>
                <div>
                    <label class="mb-2 block text-[12px] font-semibold text-gray-500">Location</label>
                    <input type="text" name="location" value="{{ $item->location }}" class="h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-[13px] text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                </div>
                <div>
                    <label class="mb-2 block text-[12px] font-semibold text-gray-500">Timezone</label>
                    {!! Timezone::selectForm($item->timezone, '-- Select a timezone --', ['required' => 'true', 'class' => 'h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-[13px] text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20', 'name' => 'timezone', 'id' => 'timezone']) !!}
                    <p class="mt-2 text-[12px] text-gray-500">Current time: {{ $item->currentTime }}</p>
                </div>
                <div class="md:col-span-2 xl:col-span-4 flex justify-end">
                    <button type="submit" class="rounded-xl bg-sky-500 px-4 py-2 text-[13px] font-semibold text-white transition hover:bg-sky-400">
                        Update Facility
                    </button>
                </div>
            </form>
        </div>
    </x-bento-card>

    <x-data-table id="facility-workgroups-grid">
        <div x-show="showWorkgroupPanel" class="border-t border-gray-200/70 bg-gray-50/70 p-6" style="display: none;">
            <div class="mb-4 flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-[15px] font-bold text-gray-900" x-text="workgroupPanelTitle"></h3>
                    <p class="text-[12px] text-gray-500">The form posts to the current Laravel workflow.</p>
                </div>
                <button type="button" @click="closeWorkgroupPanel()" class="rounded-xl bg-black/5 px-3 py-2 text-[12px] font-semibold text-gray-600 transition hover:bg-black/10">
                    Close
                </button>
            </div>
            <div x-html="workgroupFormHtml"></div>
        </div>
    </x-data-table>
</div>

<script>
function facilityPage() {
    return {
        showWorkgroupPanel: false,
        workgroupFormHtml: '',
        workgroupPanelTitle: 'Workgroup Form',
        async openWorkgroupForm(id) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', id);

            const response = await fetch('{{ url('workgroup-form') }}', {
                method: 'POST',
                body: formData,
            });
            const data = await response.json();
            if (!data.success) return;

            this.workgroupPanelTitle = id === '0' ? 'Add Workgroup' : 'Edit Workgroup';
            this.workgroupFormHtml = data.content;
            this.showWorkgroupPanel = true;
        },
        closeWorkgroupPanel() {
            this.showWorkgroupPanel = false;
            this.workgroupFormHtml = '';
        },
    };
}

window.closeWorkgroupPanel = function () {
    const root = document.querySelector('[x-data="facilityPage()"]');
    if (root && root.__x) root.__x.$data.closeWorkgroupPanel();
};

window.openFacilityWorkgroupForm = function (id) {
    const root = document.querySelector('[x-data="facilityPage()"]');
    if (root && root.__x) root.__x.$data.openWorkgroupForm(String(id));
};

window.deleteFacilityWorkgroup = async function (id) {
    if (!confirm('Delete this workgroup?')) return;

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('id', id);

    const response = await fetch('{{ url('delete-workgroup') }}', {
        method: 'POST',
        body: formData,
    });
    const data = await response.json();
    notify(data.success ? 'success' : 'failed', data.msg);
    if (data.success && window.facilityWorkgroupsGrid) {
        window.facilityWorkgroupsGrid.forceRender();
    }
};

(function () {
    function initFacilityWorkgroupsGrid() {
        const el = document.getElementById('facility-workgroups-grid');
        if (!el || el._gi) return;
        el._gi = true;

        window.facilityWorkgroupsGrid = Perfectlum.createGrid(el, {
            columns: [
                { name: 'Name', formatter: (c) => gridjs.html(`<a href="/workgroups-info/${c.id}" class="font-medium text-sky-600 hover:underline">${c.name}</a>`) },
                { name: 'Address', formatter: (c) => gridjs.html(`<span class="text-gray-600">${c || '-'}</span>`) },
                { name: 'Phone', formatter: (c) => gridjs.html(`<span class="text-gray-600">${c || '-'}</span>`) },
                { name: 'Workstations', sort: false, formatter: (c) => gridjs.html(`<span class="font-semibold text-gray-700">${c}</span>`) },
                { name: 'Displays', sort: false, formatter: (c) => gridjs.html(`<span class="font-semibold text-gray-700">${c}</span>`) },
                { name: '', sort: false, width: '110px', formatter: (_, row) => {
                    const id = row.cells[0].data.id;
                    return gridjs.html(`
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="openFacilityWorkgroupForm('${id}')" class="rounded-lg bg-sky-50 px-2.5 py-1.5 text-[12px] font-semibold text-sky-600 transition hover:bg-sky-100">Edit</button>
                            <button type="button" onclick="deleteFacilityWorkgroup('${id}')" class="rounded-lg bg-rose-50 px-2.5 py-1.5 text-[12px] font-semibold text-rose-600 transition hover:bg-rose-100">Delete</button>
                        </div>
                    `);
                }},
            ],
            server: {
                url: '/api/workgroups?facility_id={{ $item->id }}',
                then: d => d.data.map(r => [
                    { id: r.id, name: r.name },
                    r.address,
                    r.phone,
                    r.workstationsCount,
                    r.displaysCount,
                    null,
                ]),
                total: d => d.total,
            },
            pagination: { enabled: true, limit: 10, server: { url: (prev, pg, lim) => prev + (prev.includes('?') ? '&' : '?') + 'page=' + (pg + 1) + '&limit=' + lim } },
            search: { enabled: true, server: { url: (prev, kw) => prev + (prev.includes('?') ? '&' : '?') + 'search=' + encodeURIComponent(kw) } },
            sort: { multiColumn: false },
        });
    }

    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', initFacilityWorkgroupsGrid) : initFacilityWorkgroupsGrid();
})();
</script>

@include('common.navigations.footer')
