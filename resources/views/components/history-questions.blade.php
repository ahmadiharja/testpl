@props([
    'variant' => 'panel',
    'hasAnswer' => true,
])

@php
    $variants = [
        'panel' => [
            'wrap' => 'grid gap-3',
        ],
        'print' => [
            'wrap' => '',
        ],
        'classic' => [
            'wrap' => 'table-responsive rounded-4 mb-4',
        ],
    ];

    $config = $variants[$variant] ?? $variants['panel'];
@endphp

@if ($variant === 'classic')
    <div {{ $attributes->class($config['wrap']) }}>
        <table class="table table-sm mb-0 table-light" width="100%">
            @if ($hasAnswer)
                <thead>
                    <tr class="table-primary">
                        <th class="fw-semibold">Question</th>
                        <th class="text-nowrap fw-semibold">Answer</th>
                    </tr>
                </thead>
            @endif
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>
@else
    <div {{ $attributes->class($config['wrap']) }}>
        {{ $slot }}
    </div>
@endif
