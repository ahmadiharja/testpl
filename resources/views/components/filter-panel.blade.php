@props([
    'facilities'      => [],
    'role'            => 'user',
    'prefix'          => '',         {{-- suffix for element IDs. '' = no suffix, '2' = _field2 --}}
    'withScheduleBtn' => false,       {{-- show a Schedule submit button at the end --}}
    'scheduleBtnId'   => 'task_schedule_btn',
    'accentColor'     => 'sky',      {{-- sky | amber | violet | emerald --}}
    'title'           => 'Filter Displays',
    'dotColor'        => 'emerald',
])

{{--
    USAGE:
    <x-filter-panel
        :facilities="$facilities"
        :role="$role"
        prefix="2"
        with-schedule-btn
        accent-color="sky"
    />

    Props:
      facilities       — array/collection — list of facilities
      role             — string  — current user role (for super/other branching)
      prefix           — string  — appended to field IDs (e.g. '' → facility_field, '2' → facility_field2)
      with-schedule-btn— bool    — shows a Schedule submit button as 5th column
      schedule-btn-id  — string  — ID for the schedule button
      accent-color     — string  — focus ring / highlight color
      title            — string  — panel card title
      dot-color        — string  — accent dot color on the bento card header

    Generated IDs (prefix='2'):
      #facility_field2   #workgroups_field2   #workstations_field2
      #displays_field2   #displays-dropdown2

    JS functions expected in the consuming page:
      fetch_workgroups{prefix}(el)
      fetch_workstations{prefix}(el)
      fetch_displays_checklist{prefix}(el)
      (and optionally a form submit handler)
--}}

@php
$suf  = $prefix;  // shorthand
$fid  = 'facility_field'     . $suf;
$wgid = 'workgroups_field'   . $suf;
$wsid = 'workstations_field' . $suf;
$did  = 'displays_field'     . $suf;
$ddid = 'displays-dropdown'  . $suf;
$frmid = 'filter_panel_form' . $suf;

$focusMap = [
    'sky'     => 'focus:border-sky-500 focus:ring-sky-500/20',
    'amber'   => 'focus:border-amber-500 focus:ring-amber-500/20',
    'violet'  => 'focus:border-violet-500 focus:ring-violet-500/20',
    'emerald' => 'focus:border-emerald-500 focus:ring-emerald-500/20',
];
$focus = $focusMap[$accentColor] ?? $focusMap['sky'];

$dotMap = [
    'sky'     => 'bg-sky-400',
    'emerald' => 'bg-emerald-400',
    'amber'   => 'bg-amber-400',
    'violet'  => 'bg-violet-400',
];
$dot = $dotMap[$dotColor] ?? 'bg-emerald-400';

$cols = $withScheduleBtn ? 'md:grid-cols-5' : 'md:grid-cols-4';
@endphp

<div class="rounded-[2rem] overflow-hidden p-6 relative transition-colors duration-500"
     :class="theme === 'perfectlum' ? 'bento-lum' : 'bento-chroma'"
     x-data="{ openDisplays: false, displayLabel: 'Please select' }">
    <h3 class="font-bold text-[15px] flex items-center gap-2 mb-5"
        :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-white'">
        <span class="w-2 h-2 rounded-full {{ $dot }}"></span>
        {{ $title }}
    </h3>

    <form method="post" action=""
          class="grid grid-cols-1 {{ $cols }} gap-4 items-end relative z-10"
          id="{{ $frmid }}">
        @csrf

        {{-- 1. Facility --}}
        <div>
            <label class="block text-[12px] font-medium text-gray-400 mb-2">Select Facility</label>
            <select name="facility{{ $suf }}"
                    id="{{ $fid }}"
                    onchange="fetch_workgroups{{ $suf }}(this)"
                    required
                    class="appearance-none bg-no-repeat w-full h-11 px-4 rounded-xl text-[13px] font-medium outline-none border transition-colors duration-500 focus:ring-2 {{ $focus }}"
                    :class="theme === 'perfectlum' ? 'bg-white border-gray-200 text-gray-800' : 'bg-[#111216] border-white/5 text-white'">
                <option value="">Please select</option>
                @if($role !== 'super')
                    @foreach($facilities as $fc)
                        <option value="{{ $fc['id'] }}">{{ $fc['name'] }}</option>
                    @endforeach
                @else
                    @foreach($facilities as $fc)
                        <option value="{{ $fc->id }}">{{ $fc->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        {{-- 2. Workgroup --}}
        <div>
            <label class="block text-[12px] font-medium text-gray-400 mb-2">Select Workgroup</label>
            <select name="workgroup{{ $suf }}"
                    id="{{ $wgid }}"
                    onchange="fetch_workstations{{ $suf }}(this)"
                    class="appearance-none bg-no-repeat w-full h-11 px-4 rounded-xl text-[13px] font-medium outline-none border transition-colors duration-500 focus:ring-2 {{ $focus }}"
                    :class="theme === 'perfectlum' ? 'bg-white border-gray-200 text-gray-800' : 'bg-[#111216] border-white/5 text-white'">
                <option value="">Select Facility first</option>
            </select>
        </div>

        {{-- 3. Workstation --}}
        <div>
            <label class="block text-[12px] font-medium text-gray-400 mb-2">Select Workstation</label>
            <select name="workstation{{ $suf }}"
                    id="{{ $wsid }}"
                    onchange="fetch_displays_checklist{{ $suf }}(this)"
                    class="appearance-none bg-no-repeat w-full h-11 px-4 rounded-xl text-[13px] font-medium outline-none border transition-colors duration-500 focus:ring-2 {{ $focus }}"
                    :class="theme === 'perfectlum' ? 'bg-white border-gray-200 text-gray-800' : 'bg-[#111216] border-white/5 text-white'">
                <option value="">Select Workgroup first</option>
            </select>
        </div>

        {{-- 4. Display Checklist Dropdown --}}
        <div>
            <label class="block text-[12px] font-medium text-gray-400 mb-2">Select Display</label>
            <div class="relative">
                <button id="{{ $ddid }}"
                        :aria-expanded="openDisplays.toString()"
                        @click="openDisplays = !openDisplays"
                        @click.away="openDisplays = false"
                        type="button"
                        class="w-full h-11 px-4 flex items-center justify-between rounded-xl text-[13px] font-medium outline-none border transition-colors duration-500"
                        :class="theme === 'perfectlum' ? 'bg-white border-gray-200 text-gray-800' : 'bg-[#111216] border-white/5 text-white'">
                    <span class="truncate" x-text="displayLabel"></span>
                    <i data-lucide="chevron-down" class="w-4 h-4 opacity-50 shrink-0"></i>
                </button>
                <div x-show="openDisplays"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute left-0 right-0 z-40 mt-2 max-h-72 overflow-auto shadow-2xl"
                     id="{{ $did }}"
                     :style="theme === 'perfectlum' ? 'background:#ffffff;border:1px solid rgba(0,0,0,0.08);border-radius:1rem;min-width:100%;padding:0.5rem;' : 'background:#161820;border:1px solid rgba(255,255,255,0.08);border-radius:1rem;min-width:100%;padding:0.5rem;'"
                     style="display: none;">
                    <div class="px-3 py-2 text-[12px] text-gray-500 italic">Select Workstation first</div>
                </div>
            </div>
        </div>

        {{-- 5. Optional Schedule Button --}}
        @if($withScheduleBtn)
        <div>
            <button type="submit"
                    id="{{ $scheduleBtnId }}"
                    class="w-full h-11 flex items-center justify-center gap-2 rounded-xl text-[13px] font-semibold text-white transition-all shadow-lg
                           @if($accentColor === 'amber') bg-amber-500 hover:bg-amber-400 shadow-amber-500/20
                           @elseif($accentColor === 'violet') bg-violet-500 hover:bg-violet-400 shadow-violet-500/20
                           @elseif($accentColor === 'emerald') bg-emerald-500 hover:bg-emerald-400 shadow-emerald-500/20
                           @else bg-sky-500 hover:bg-sky-400 shadow-sky-500/20
                           @endif">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Schedule
            </button>
        </div>
        @endif
    </form>
</div>
