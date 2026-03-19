@props([
    'steps' => collect(),
    'sectionClass' => 'mb-4',
    'outerClass' => 'bg-white border rounded border-info rounded-4 pt-4',
    'layout' => 'single',
    'wrapSection' => true,
    'gridClass' => 'row gy-4 row-cols-1 row-cols-xl-2',
    'colClass' => 'col',
    'cardClass' => 'card card-light',
    'titleTag' => 'h5',
    'titleClass' => 'text-primary mb-0',
    'titleWrapClass' => 'px-4 mb-3',
    'breakAfterScores' => false,
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
                            @include('histories.partials.step-content', [
                                'step' => $entry['step'],
                                'index' => $entry['index'],
                                'titleTag' => $titleTag,
                                'titleClass' => $titleClass,
                                'titleWrapClass' => $titleWrapClass,
                                'breakAfterScores' => $breakAfterScores,
                            ])
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="{{ $outerClass }}">
                @foreach ($steps as $entry)
                    @include('histories.partials.step-content', [
                        'step' => $entry['step'],
                        'index' => $entry['index'],
                        'titleTag' => $titleTag,
                        'titleClass' => $titleClass,
                        'titleWrapClass' => $titleWrapClass,
                        'breakAfterScores' => $breakAfterScores,
                    ])
                @endforeach
            </div>
        @endif
    @if ($wrapSection)
    </section>
    @endif
@endif
