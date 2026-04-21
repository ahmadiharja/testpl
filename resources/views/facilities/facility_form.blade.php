@php
    $descriptionMin = 10;
    $nameMax = 100;
@endphp

<form method="post" class="space-y-4" data-facility-form data-description-min="{{ $descriptionMin }}" data-name-max="{{ $nameMax }}">
    {{ csrf_field() }}
    <input type="hidden" name="id" value="{{ $item->id }}">

    <div>
        <label class="mb-2 block text-[12px] font-semibold text-gray-500">{{ __('Facility Name') }}</label>
        {{ Form::text('name', $item->name, [
            'required',
            'maxlength' => $nameMax,
            'class' => 'h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-[13px] text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20',
            'placeholder' => __('Facility name'),
            'data-facility-name' => 'true',
            'aria-describedby' => 'facility-name-help',
        ]) }}
        <p id="facility-name-help" data-facility-name-message class="mt-2 hidden text-[12px] font-medium text-rose-600"></p>
    </div>

    <div>
        <label class="mb-2 block text-[12px] font-semibold text-gray-500">{{ __('Description') }}</label>
        {{ Form::textarea('description', $item->description, [
            'class' => 'min-h-[104px] w-full resize-y rounded-xl border border-gray-200 bg-white px-4 py-3 text-[13px] leading-5 text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20',
            'placeholder' => __('Description'),
            'data-facility-description' => 'true',
            'aria-describedby' => 'facility-description-help',
        ]) }}
        <p id="facility-description-help" data-facility-description-message class="mt-2 hidden text-[12px] font-medium text-rose-600">
            {{ __('Description must be at least :min characters.', ['min' => $descriptionMin]) }}
        </p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="mb-2 block text-[12px] font-semibold text-gray-500">{{ __('Location') }}</label>
            {{ Form::text('location', $item->location, ['class' => 'h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-[13px] text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20', 'placeholder' => __('Location')]) }}
        </div>

        <div>
            <label class="mb-2 block text-[12px] font-semibold text-gray-500">{{ __('Timezone') }}</label>
            {!! Timezone::selectForm($item->timezone, __('-- Select a timezone --'), ['required' => 'true', 'class' => 'h-11 w-full rounded-xl border border-gray-200 bg-white px-4 text-[13px] text-gray-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20', 'name' => 'timezone', 'id' => 'timezone']) !!}
        </div>
    </div>

    <div class="flex justify-end gap-3 pt-2">
        <button class="rounded-xl bg-black/5 px-4 py-2 text-[13px] font-semibold text-gray-600 transition hover:bg-black/10" type="button" onclick="window.closeFacilityPanel && window.closeFacilityPanel()">
            {{ __('Cancel') }}
        </button>
        <button class="rounded-xl bg-sky-500 px-4 py-2 text-[13px] font-semibold text-white transition hover:bg-sky-400" type="submit">
            {{ __('Save Changes') }}
        </button>
    </div>
</form>
