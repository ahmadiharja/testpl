<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body{font-family: DejaVu Sans;}
        .header {
            text-align: center;
        }

        .tab-content {
            margin-top: 30px;
        }

        .step-header {
            font-size: 20px;
            color: #2CA8FF;
            font-weight: 700;
            margin-top: 30px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    @php
        $indexedSteps = collect($item->steps)->map(function ($step, $index) {
            return ['step' => $step, 'index' => $index];
        });

        $pdfRenderableSteps = $indexedSteps->filter(function ($entry) use ($graph) {
            $step = $entry['step'];
            $index = $entry['index'];

            if (isset($step['scores']) || isset($step['questions']) || isset($step['comment'])) {
                return true;
            }

            if (!isset($step['graphs'])) {
                return true;
            }

            foreach ($step['graphs'] as $graphIndex => $graphItem) {
                if (!empty($graph['graph_' . $index . '_' . $graphIndex])) {
                    return true;
                }
            }

            return false;
        });

        $commentSteps = $indexedSteps->filter(fn ($entry) => isset($entry['step']['comment']));
        $targetResultSteps = $indexedSteps->filter(fn ($entry) => ($entry['step']['name'] ?? '') === 'Target & Results');
        $luminanceSteps = $indexedSteps->filter(fn ($entry) => ($entry['step']['name'] ?? '') === 'luminance');
        $correctionSteps = $indexedSteps->filter(fn ($entry) => ($entry['step']['name'] ?? '') === 'corrections');
        $jndSteps = $indexedSteps->filter(fn ($entry) => ($entry['step']['name'] ?? '') === 'jnd');
        $dicomSteps = $indexedSteps->filter(fn ($entry) => ($entry['step']['name'] ?? '') === 'dicom');
    @endphp

    <div class="header">
        <h1>{{ $item->name }}</h1>
        <div>Display Model: {{ $item->display->model }} | Serial Number: {{ $item->display->serial }}</div>
        <div>Workstation: {{ $item->display->workstation->name }} | Workgroup: {{ $item->display->workstation->workgroup->name }}</div>
        @if($item->getHeader('Sensor Model') !== '')
        <div>Sensor: {{ $item->getHeader('Sensor Manufacturer') }}, {{ $item->getHeader('Sensor Model') }} | Sensor Serial: {{ $item->getHeader('Sensor Serial') }}</div>
        @endif
        <div>(Performed Date: {{ $item->getTimeDisplay() }})</div>
        <div>Result: {{ $item->status_text }}</div>
    </div>

    <div class="tab-content">
        @foreach ($pdfRenderableSteps as $entry)
            @include('histories.partials.pdf-step-content', [
                'step' => $entry['step'],
                'index' => $entry['index'],
                'graphImages' => $graph,
            ])
        @endforeach

        @include('histories.partials.pdf-step-group', [
            'steps' => $commentSteps,
            'graphImages' => $graph,
        ])

        @include('histories.partials.pdf-step-group', [
            'steps' => $targetResultSteps,
            'graphImages' => $graph,
        ])

        <section class="mb-4">
            <div class="row gy-4 row-cols-1 row-cols-xl-2">
                @include('histories.partials.pdf-step-group', [
                    'steps' => $luminanceSteps,
                    'graphImages' => $graph,
                    'layout' => 'grid',
                    'wrapSection' => false,
                    'gridClass' => '',
                ])

                @include('histories.partials.pdf-step-group', [
                    'steps' => $correctionSteps,
                    'graphImages' => $graph,
                    'layout' => 'grid',
                    'wrapSection' => false,
                    'gridClass' => '',
                ])

                @include('histories.partials.pdf-step-group', [
                    'steps' => $jndSteps,
                    'graphImages' => $graph,
                    'layout' => 'grid',
                    'wrapSection' => false,
                    'gridClass' => '',
                    'cardClass' => 'bg-white border rounded border-info rounded-4 pt-4',
                ])

                @include('histories.partials.pdf-step-group', [
                    'steps' => $dicomSteps,
                    'graphImages' => $graph,
                    'layout' => 'grid',
                    'wrapSection' => false,
                    'gridClass' => '',
                    'cardClass' => 'bg-white border rounded border-info rounded-4 pt-4',
                ])
            </div>
        </section>
    </div>
    
    <div style="position:absolute; bottom:-20px; left:0px; right:0px; text-align:center; font-size:11px;">PerfectLum version: {{ $version }}</div>
</body>

</html>
