@props([
    'step',
    'index',
    'titleTag' => 'h5',
    'titleClass' => 'text-primary mb-0',
    'titleWrapClass' => 'px-4 mb-3',
    'breakAfterScores' => false,
])

<div id="tab_step_{{ $index }}">
    <div class="{{ $titleWrapClass }}">
        <<?php echo $titleTag; ?> class="{{ $titleClass }}">{!! ucfirst($step['name']) !!}</<?php echo $titleTag; ?>>
    </div>
    @if (isset($step['scores']))
        @include('histories.scores', ['scores' => $step['scores']])
        @if ($breakAfterScores)
            <br>
        @endif
    @endif

    @if (isset($step['questions']))
        @include('histories.questions', ['questions' => $step['questions']])
    @endif

    @if (isset($step['comment']))
        <div><b>Comment:</b> {!! $step['comment'] !!}</div>
    @endif

    @if (isset($step['graphs']))
        @include('histories.graphs', ['graphs' => $step['graphs'], 'step_id' => $index, 'history_id' => $item->id])
    @endif
</div>
