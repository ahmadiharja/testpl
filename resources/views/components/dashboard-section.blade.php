@props([
    'title',
    'description' => '',
])

<div {{ $attributes->class('flex flex-col gap-5') }}>
    <div class="flex items-center justify-between px-2">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 h-8 w-8 rounded-lg flex items-center justify-center shadow-sm"
                 :class="theme === 'perfectlum' ? 'bg-slate-100 text-slate-500' : 'bg-white/5 text-slate-300'">
                {{ $icon ?? '' }}
            </div>
            <div>
                <h3 class="text-xl font-extrabold tracking-tight theme-lum:text-gray-900 theme-chroma:text-white">{{ $title }}</h3>
                @if($description)
                    <p class="mt-1 text-sm theme-lum:text-gray-500 theme-chroma:text-gray-400">{{ $description }}</p>
                @endif
            </div>
        </div>
        @if(isset($actions))
            <div class="shrink-0">
                {{ $actions }}
            </div>
        @endif
    </div>

    {{ $slot }}
</div>
