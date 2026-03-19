<form method="post" class="space-y-5">
    {{ csrf_field() }}
    <input type="hidden" name="id" value="{{ $alert->id }}">

    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-500">Alert Rule</p>
        <p class="mt-2 text-sm leading-6 text-slate-500">Each alert rule defines a recipient, whether the alert is active, and the facility scope it applies to.</p>
    </div>

    <div>
        <label class="mb-2 block text-[12px] font-semibold uppercase tracking-[0.18em] text-slate-500">Recipient Email</label>
        {{ Form::text('email', $alert->email, ['class' => 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-[13px] text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20', 'placeholder' => 'name@example.com']) }}
    </div>

    <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-[13px] text-slate-700">
        <input class="mt-1 h-4 w-4 rounded border-gray-300 text-sky-500 focus:ring-sky-500/30" type="checkbox" value="1" name="actived" @if ($alert->actived == 1) checked @endif>
        <span>
            <span class="block font-semibold text-slate-900">Enable alert</span>
            <span class="mt-0.5 block text-xs text-slate-500">Disabled rules remain saved but stop receiving notifications.</span>
        </span>
    </label>

    <label class="flex items-start gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-[13px] text-slate-700">
        <input class="mt-1 h-4 w-4 rounded border-gray-300 text-sky-500 focus:ring-sky-500/30" type="checkbox" value="1" name="daily_report" @if ($alert->daily_report == 1) checked @endif>
        <span>
            <span class="block font-semibold text-slate-900">Send daily report</span>
            <span class="mt-0.5 block text-xs text-slate-500">Include this recipient in the scheduled daily summary email.</span>
        </span>
    </label>

    <div>
        <label class="mb-2 block text-[12px] font-semibold uppercase tracking-[0.18em] text-slate-500">Facility Scope</label>
        <div class="relative" data-searchable-select="facility">
            <input type="hidden" name="facility_id" value="{{ $alert->facility_id }}">
            <button
                type="button"
                data-role="trigger"
                class="flex h-11 w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-4 text-left text-[13px] text-slate-900 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                <span data-role="label" class="truncate">All facilities</span>
                <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m6 9 6 6 6-6"/>
                </svg>
            </button>

            <div
                data-role="panel"
                class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-20 hidden rounded-[1.25rem] border border-slate-200 bg-white p-3 shadow-[0_18px_45px_rgba(15,23,42,0.14)]">
                <input
                    data-role="search"
                    type="text"
                    placeholder="Search facilities..."
                    class="mb-2 h-10 w-full rounded-xl border border-slate-200 px-3 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">

                <div class="max-h-56 space-y-1 overflow-y-auto">
                    <button
                        type="button"
                        data-role="option"
                        data-value=""
                        data-label="All facilities"
                        class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-600 transition hover:bg-slate-50">
                        All facilities
                    </button>

                    @foreach($facilities as $facilityId => $facilityName)
                        <button
                            type="button"
                            data-role="option"
                            data-value="{{ $facilityId }}"
                            data-label="{{ $facilityName }}"
                            class="flex w-full items-center rounded-xl px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-sky-50 hover:text-sky-700">
                            {{ $facilityName }}
                        </button>
                    @endforeach

                    <div data-role="empty" class="hidden rounded-xl border border-dashed border-slate-200 px-3 py-3 text-sm text-slate-400">
                        No facilities found.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-3 pt-2">
        <button class="rounded-xl bg-black/5 px-4 py-2 text-[13px] font-semibold text-slate-600 transition hover:bg-black/10" type="button" onclick="window.closeAlertPanel && window.closeAlertPanel()">
            Cancel
        </button>
        <button class="rounded-xl bg-sky-500 px-4 py-2 text-[13px] font-semibold text-white transition hover:bg-sky-400" type="submit">
            Save Alert
        </button>
    </div>
</form>
