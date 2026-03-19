@props([
    'variant' => 'panel',
    'tableClass' => '',
    'theadClass' => '',
    'tbodyClass' => '',
])

@php
    $variants = [
        'panel' => [
            'wrap' => 'overflow-hidden rounded-2xl border border-slate-200',
            'scroll' => 'overflow-x-auto',
            'table' => 'min-w-full divide-y divide-slate-200 text-sm',
            'thead' => 'bg-slate-50',
            'th' => 'px-4 py-3 text-left text-[11px] font-bold uppercase tracking-[0.14em] text-slate-400',
            'tbody' => 'divide-y divide-slate-100 bg-white',
        ],
        'print' => [
            'wrap' => '',
            'scroll' => '',
            'table' => 'w-full',
            'thead' => '',
            'th' => '',
            'tbody' => '',
        ],
        'classic' => [
            'wrap' => 'table-responsive rounded-4',
            'scroll' => '',
            'table' => 'table table-sm mb-0 table-light',
            'thead' => 'table-primary',
            'th' => 'fw-semibold',
            'tbody' => '',
        ],
    ];

    $config = $variants[$variant] ?? $variants['panel'];
@endphp

<div {{ $attributes->class($config['wrap']) }}>
    <div class="{{ $config['scroll'] }}">
        <table class="{{ trim($config['table'] . ' ' . $tableClass) }}" width="100%">
            <thead class="{{ trim($config['thead'] . ' ' . $theadClass) }}">
                <tr>
                    <th class="{{ $config['th'] }}">Item</th>
                    <th class="{{ $config['th'] }}">Target</th>
                    <th class="{{ $config['th'] }}">Result</th>
                    <th class="{{ $config['th'] }}">Status</th>
                </tr>
            </thead>
            <tbody class="{{ trim($config['tbody'] . ' ' . $tbodyClass) }}">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
