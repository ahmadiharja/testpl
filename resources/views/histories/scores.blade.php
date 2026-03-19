<x-history-scores variant="classic">
    @foreach ($scores as $score)
        <tr>
            <td class="text-nowrap text-primary">{!! $score['name'] !!}</td>
            <td class="text-body">{!! $score['limit'] ? $score['limit'] : '-' !!}</td>
            <td class="text-nowrap text-body">{!! $score['measured'] !!}</td>
            <td class="text-body">{!! $score['answer'] == 0 ? '<span class=text-danger><b>Not OK</b></span>' : ($score['answer'] == 1 ? '<span class=text-success><b>OK</b></span>' : '-') !!}</td>
        </tr>
    @endforeach
</x-history-scores>
