@php
    $hasAnswer = false;
    foreach ($questions as $question) {
        if ($question['answer'] != '') {
            $hasAnswer = true;
            break;
        }
    }
@endphp
<x-history-questions variant="classic" :has-answer="$hasAnswer">
    @foreach ($questions as $question)
        <tr>
            <td class="text-nowrap text-primary">{!! $question['text'] !!}</td>
            @if (isset($question['reverse']) AND (strtolower($question['answer']) == ($question['reverse'] == 'yes' ? 'no' : 'yes')))
                <td class="text-body"><span class="text-success"><b>{!! $question['answer'] !!}</b></span></td>
            @else
                <td class="text-body"><span class="text-danger"><b>{!! $question['answer'] !!}</b></span></td>
            @endif
        </tr>
    @endforeach
</x-history-questions>
