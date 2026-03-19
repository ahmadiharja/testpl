@props([
    'step',
    'index',
    'graphImages' => [],
    'titleClass' => 'step-header',
])

@php
    $hasRenderableGraphImages = false;
    if (isset($step['graphs'])) {
        foreach ($step['graphs'] as $graphIndex => $graphItem) {
            if (!empty($graphImages['graph_' . $index . '_' . $graphIndex])) {
                $hasRenderableGraphImages = true;
                break;
            }
        }
    }
@endphp

<div class="tab-pane {{ $index == 0 ? 'active' : '' }}" id="tab_step_{{ $index }}">
    <div class="{{ $titleClass }}"><b>{!! ucfirst($step['name']) !!}</b></div>
    <hr />

    @if (isset($step['scores']))
        @include('histories.scores', ['scores' => $step['scores']])
    @endif

    @if (isset($step['questions']))
        @include('histories.questions', ['questions' => $step['questions']])
    @endif

    @if (isset($step['comment']))
        <div><b>Comment:</b> {!! $step['comment'] !!}</div>
    @endif

    @if ($hasRenderableGraphImages)
        @foreach ($step['graphs'] as $graphIndex => $graphItem)
            @php
                $graphKey = 'graph_' . $index . '_' . $graphIndex;
            @endphp
            @if (!empty($graphImages[$graphKey]))
                <img src="{{ $graphImages[$graphKey] }}" width="100%" />
            @endif
        @endforeach
    @endif
</div>
