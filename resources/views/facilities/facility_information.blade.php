@include('common.navigations.header')

<div class="flex flex-col gap-6 pb-10" x-data="facilityPage()">
    <x-page-header title="{{ __('Facility Information') }}" description="{{ __('Update the facility and manage its workgroups from one place.') }}" icon="building-2">
        <x-slot name="actions">
            <button type="button" @click="openWorkgroupForm('0')" class="rounded-xl bg-sky-500 px-4 py-2 text-[13px] font-semibold text-white transition hover:bg-sky-400">
                {{ __('Add Workgroup') }}
            </button>
        </x-slot>
    </x-page-header>

    <x-bento-card title="{{ __('Facility Details') }}" dot-color="sky">
        <div class="p-6">
            <form method="post" action="" class="grid grid-cols-1 gap-5 md:grid-cols-2" data-facility-details-form data-name-max="100" data-description-min="10">
                {{ csrf_field() }}
                <input type="hidden" name="facility_update" value="{{ $item->id }}">

                <div>
                    <label class="mb-2 block text-[12px] font-semibold text-gray-500">{{ __('Facility Name') }}</label>
                    <input type="text" name="name" value="{{ $item->name }}" required maxlength="100" data-facility-name aria-describedby="facility-details-name-help" class="h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-[13px] text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                    <p id="facility-details-name-help" data-facility-name-message class="mt-2 hidden text-[12px] font-medium text-rose-600"></p>
                </div>
                <div class="md:col-span-2">
                    <label class="mb-2 block text-[12px] font-semibold text-gray-500">{{ __('Description') }}</label>
                    <textarea name="description" data-facility-description aria-describedby="facility-details-description-help" class="min-h-[104px] w-full resize-y rounded-xl border border-gray-200 bg-white px-4 py-3 text-[13px] leading-5 text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">{{ $item->description }}</textarea>
                    <p id="facility-details-description-help" data-facility-description-message class="mt-2 hidden text-[12px] font-medium text-rose-600">{{ __('Description must be at least :min characters.', ['min' => 10]) }}</p>
                </div>
                <div>
                    <label class="mb-2 block text-[12px] font-semibold text-gray-500">{{ __('Location') }}</label>
                    <input type="text" name="location" value="{{ $item->location }}" class="h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-[13px] text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                </div>
                <div>
                    <label class="mb-2 block text-[12px] font-semibold text-gray-500">{{ __('Timezone') }}</label>
                    {!! Timezone::selectForm($item->timezone, __('-- Select a timezone --'), ['required' => 'true', 'class' => 'h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-[13px] text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20', 'name' => 'timezone', 'id' => 'timezone']) !!}
                    <p class="mt-2 text-[12px] text-gray-500">{{ __('Current time:') }} {{ $item->currentTime }}</p>
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="rounded-xl bg-sky-500 px-4 py-2 text-[13px] font-semibold text-white transition hover:bg-sky-400">
                        {{ __('Update Facility') }}
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
                    <p class="text-[12px] text-gray-500">{{ __('The form posts to the current Laravel workflow.') }}</p>
                </div>
                <button type="button" @click="closeWorkgroupPanel()" class="rounded-xl bg-black/5 px-3 py-2 text-[12px] font-semibold text-gray-600 transition hover:bg-black/10">
                    {{ __('Close') }}
                </button>
            </div>
            <div x-html="workgroupFormHtml"></div>
        </div>
    </x-data-table>
</div>

@include('workgroups.form_ui_script')

<script>
function bindFacilityDetailsValidation() {
    const form = document.querySelector('[data-facility-details-form]');
    if (!form || form.dataset.validationBound === '1') return;
    form.dataset.validationBound = '1';

    const setFieldState = (input, invalid) => {
        input?.classList.toggle('border-rose-300', invalid);
        input?.classList.toggle('focus:border-rose-400', invalid);
        input?.classList.toggle('focus:ring-rose-500/20', invalid);
        input?.classList.toggle('focus:border-sky-500', !invalid);
        input?.classList.toggle('focus:ring-sky-500/20', !invalid);
    };

    const nameInput = form.querySelector('[data-facility-name]');
    const nameMessage = form.querySelector('[data-facility-name-message]');
    const nameMax = Number(form.dataset.nameMax || 100);
    const validateName = (force = false) => {
        if (!nameInput || !nameMessage) return true;
        const trimmed = String(nameInput.value || '').trim();
        let message = '';

        if (trimmed.length === 0) {
            message = 'Facility Name is required.';
        } else if (trimmed.length > nameMax) {
            message = `Facility Name must not exceed ${nameMax} characters.`;
        } else if (!/^[A-Za-z0-9][A-Za-z0-9\s._,'()/-]*$/.test(trimmed)) {
            message = 'Facility Name may only contain letters, numbers, spaces, and basic punctuation.';
        }

        const invalid = message !== '';
        nameMessage.textContent = message;
        nameMessage.classList.toggle('hidden', !invalid && !force);
        setFieldState(nameInput, invalid);
        return !invalid;
    };

    const descriptionInput = form.querySelector('[data-facility-description]');
    const descriptionMessage = form.querySelector('[data-facility-description-message]');
    const descriptionMin = Number(form.dataset.descriptionMin || 10);
    const validateDescription = (force = false) => {
        if (!descriptionInput || !descriptionMessage) return true;
        const value = String(descriptionInput.value || '').trim();
        const invalid = value.length > 0 && value.length < descriptionMin;
        descriptionMessage.classList.toggle('hidden', !invalid && !force);
        setFieldState(descriptionInput, invalid);
        return !invalid;
    };

    nameInput?.addEventListener('input', () => validateName());
    nameInput?.addEventListener('blur', () => validateName(true));
    descriptionInput?.addEventListener('input', () => validateDescription());
    descriptionInput?.addEventListener('blur', () => validateDescription(true));
    form.addEventListener('submit', (event) => {
        if (!validateName(true) || !validateDescription(true)) {
            event.preventDefault();
        }
    });
}

document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', bindFacilityDetailsValidation) : bindFacilityDetailsValidation();

function facilityPage() {
    return {
        showWorkgroupPanel: false,
        workgroupFormHtml: '',
        workgroupPanelTitle: 'Workgroup Form',
        async openWorkgroupForm(id) {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id', id);
            if (id === '0') formData.append('facility_id', '{{ $item->id }}');

            const response = await fetch('{{ url('workgroup-form') }}', {
                method: 'POST',
                body: formData,
            });
            const data = await response.json();
            if (!data.success) return;

            this.workgroupPanelTitle = id === '0' ? @js(__('Add Workgroup')) : @js(__('Edit Workgroup'));
            this.workgroupFormHtml = data.content;
            this.showWorkgroupPanel = true;
            this.$nextTick(() => window.WorkgroupFormUI?.init(document.querySelector('[x-data="facilityPage()"]')));
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
