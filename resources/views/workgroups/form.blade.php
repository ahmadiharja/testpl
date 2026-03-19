<form method="post" class="space-y-4">
    {{ csrf_field() }}
    <input type="hidden" name="id" value="{{ $item->id }}">

    <div>
        <label class="mb-2 block text-[12px] font-semibold text-gray-500">Workgroup Name</label>
        {{ Form::text('name', $item->name, ['required', 'class' => 'h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-[13px] text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20', 'placeholder' => 'Workgroup name']) }}
    </div>

    <div>
        <label class="mb-2 block text-[12px] font-semibold text-gray-500">Address</label>
        {{ Form::text('address', $item->address, ['class' => 'h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-[13px] text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20', 'placeholder' => 'Address']) }}
    </div>

    <div>
        <label class="mb-2 block text-[12px] font-semibold text-gray-500">Phone Number</label>
        {{ Form::number('phone', $item->phone, ['class' => 'h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-[13px] text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20', 'placeholder' => 'Phone number']) }}
    </div>

    <div>
        <label class="mb-2 block text-[12px] font-semibold text-gray-500">Facility</label>
        {{ Form::select('facility_id', $facilities, $item->facility_id, ['required', 'class' => 'h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-[13px] text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20', 'placeholder' => '-- Select Facility --']) }}
    </div>

    <div class="flex justify-end gap-3 pt-2">
        <button class="rounded-xl bg-black/5 px-4 py-2 text-[13px] font-semibold text-gray-600 transition hover:bg-black/10" type="button" onclick="window.closeWorkgroupPanel && window.closeWorkgroupPanel()">
            Cancel
        </button>
        <button class="rounded-xl bg-sky-500 px-4 py-2 text-[13px] font-semibold text-white transition hover:bg-sky-400" type="submit">
            Submit
        </button>
    </div>
</form>
