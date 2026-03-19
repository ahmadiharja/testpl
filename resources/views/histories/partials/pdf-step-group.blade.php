@props([
    'steps' => collect(),
    'graphImages' => [],
    'sectionClass' => 'mb-4',
    'outerClass' => 'bg-white border rounded border-info rounded-4 pt-4',
    'layout' => 'single',
    'wrapSection' => true,
    'gridClass' => 'row gy-4 row-cols-1 row-cols-xl-2',
    'colClass' => 'col',
    'cardClass' => 'card card-light',
])

@if ($steps->isNotEmpty())
    @if ($wrapSection)
    <section class="{{ $sectionClass }}">
    @endif
        @if ($layout === 'grid')
            <div class="{{ $gridClass }}">
                @foreach ($steps as $entry)
                    <div class="{{ $colClass }}">
                        <div class="{{ $cardClass }}">
                            @include('histories.partials.pdf-step-content', [
                                'step' => $entry['step'],
                                'index' => $entry['index'],
                                'graphImages' => $graphImages,
                            ])
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="{{ $outerClass }}">
                @foreach ($steps as $entry)
                    @include('histories.partials.pdf-step-content', [
                        'step' => $entry['step'],
                        'index' => $entry['index'],
                        'graphImages' => $graphImages,
                    ])
                @endforeach
            </div>
        @endif
    @if ($wrapSection)
    </section>
    @endif
@endif
