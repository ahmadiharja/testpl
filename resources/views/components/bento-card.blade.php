@props([
    'title'    => '',
    'dotColor' => 'sky',   {{-- sky | violet | amber | emerald | rose | indigo --}}
])

{{--
    USAGE:
    <x-bento-card title="All Tasks" dot-color="violet">
        ...content...
        <x-slot name="headerActions">
            <button>...</button>
        </x-slot>
    </x-bento-card>

    Props:
      title     — string — card heading (optional, omit for no header bar)
      dot-color — string — accent dot color name (tailwind color name)
    Slots:
      (default)     — card body content
      headerActions — optional buttons in the header bar right side
--}}

@php
$dotColors = [
    'sky'     => 'bg-sky-400',
    'violet'  => 'bg-violet-400',
    'amber'   => 'bg-amber-400',
    'emerald' => 'bg-emerald-400',
    'rose'    => 'bg-rose-400',
    'indigo'  => 'bg-indigo-400',
    'blue'    => 'bg-blue-400',
    'pink'    => 'bg-pink-400',
];
$dot = $dotColors[$dotColor] ?? 'bg-sky-400';
@endphp

<div class="rounded-[2rem] overflow-hidden transition-colors duration-500"
     :class="theme === 'perfectlum' ? 'bento-lum' : 'bento-chroma'">
    @if($title)
    <div class="px-6 py-5 border-b flex items-center justify-between"
         :class="theme === 'perfectlum' ? 'border-gray-200' : 'border-white/5'">
        <h3 class="font-bold text-[15px] flex items-center gap-2"
            :class="theme === 'perfectlum' ? 'text-gray-800' : 'text-white'">
            <span class="w-2 h-2 rounded-full {{ $dot }}"></span>
            {{ $title }}
        </h3>
        @if(isset($headerActions))
        <div class="flex items-center gap-2">
            {{ $headerActions }}
        </div>
        @endif
    </div>
    @endif

    {{ $slot }}
</div>
