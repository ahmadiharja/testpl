@props([
    'variant' => 'panel',
])

@php
    $variants = [
        'panel' => 'grid gap-4',
        'print' => 'graphs',
        'classic' => '',
    ];
@endphp

<div {{ $attributes->class($variants[$variant] ?? $variants['panel']) }}>
    {{ $slot }}
</div>
