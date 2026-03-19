@include('common.navigations.header')
<style type="text/css">/* Chart.js */
@-webkit-keyframes chartjs-render-animation{from{opacity:0.99}to{opacity:1}}@keyframes chartjs-render-animation{from{opacity:0.99}to{opacity:1}}.chartjs-render-monitor{-webkit-animation:chartjs-render-animation 0.001s;animation:chartjs-render-animation 0.001s;}
</style>
<style> @-webkit-keyframes loadingoverlay_animation__rotate_right { to { -webkit-transform : rotate(360deg); transform : rotate(360deg); } } @keyframes loadingoverlay_animation__rotate_right { to { -webkit-transform : rotate(360deg); transform : rotate(360deg); } } @-webkit-keyframes loadingoverlay_animation__rotate_left { to { -webkit-transform : rotate(-360deg); transform : rotate(-360deg); } } @keyframes loadingoverlay_animation__rotate_left { to { -webkit-transform : rotate(-360deg); transform : rotate(-360deg); } } @-webkit-keyframes loadingoverlay_animation__fadein { 0% { opacity   : 0; -webkit-transform : scale(0.1, 0.1); transform : scale(0.1, 0.1); } 50% { opacity   : 1; } 100% { opacity   : 0; -webkit-transform : scale(1, 1); transform : scale(1, 1); } } @keyframes loadingoverlay_animation__fadein { 0% { opacity   : 0; -webkit-transform : scale(0.1, 0.1); transform : scale(0.1, 0.1); } 50% { opacity   : 1; } 100% { opacity   : 0; -webkit-transform : scale(1, 1); transform : scale(1, 1); } } @-webkit-keyframes loadingoverlay_animation__pulse { 0% { -webkit-transform : scale(0, 0); transform : scale(0, 0); } 50% { -webkit-transform : scale(1, 1); transform : scale(1, 1); } 100% { -webkit-transform : scale(0, 0); transform : scale(0, 0); } } @keyframes loadingoverlay_animation__pulse { 0% { -webkit-transform : scale(0, 0); transform : scale(0, 0); } 50% { -webkit-transform : scale(1, 1); transform : scale(1, 1); } 100% { -webkit-transform : scale(0, 0); transform : scale(0, 0); } } 
</style>

@php
    $indexedSteps = collect($item->steps)->map(function ($step, $index) {
        return ['step' => $step, 'index' => $index];
    });

    $commentSteps = $indexedSteps->filter(fn ($entry) => isset($entry['step']['comment']));
    $targetResultSteps = $indexedSteps->filter(fn ($entry) => ($entry['step']['name'] ?? '') === 'Target & Results');
    $luminanceSteps = $indexedSteps->filter(fn ($entry) => ($entry['step']['name'] ?? '') === 'luminance');
    $correctionSteps = $indexedSteps->filter(fn ($entry) => ($entry['step']['name'] ?? '') === 'corrections');
    $jndSteps = $indexedSteps->filter(fn ($entry) => ($entry['step']['name'] ?? '') === 'jnd');
    $dicomSteps = $indexedSteps->filter(fn ($entry) => ($entry['step']['name'] ?? '') === 'dicom');
@endphp

<main class="main-vertical-layout">
    <div class="container-fluid">
        <section class="py-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <form action="{{ url('histories/export/pdf') }}" method="post" id="print_pdf_form">
                        {{ csrf_field() }}
                        {{ Form::hidden('id', $item->id) }}
                        {{ Form::hidden('graph', '', ['id' => 'graph']) }}
                        <button onclick="printPDF()" type="button" class="btn btn-info rounded-pill me-2 me-lg-3" id="limit-update">Print</button>
                    </form>
                </div>
                <div>
                    <ol class="breadcrumb mb-0 d-none">
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="#"><span>danh154</span></a></li>
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="#"><span>qbx rad&nbsp;</span></a></li>
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="#"><span>Marcs-MacBook</span></a></li>
                        <li class="breadcrumb-item"><a class="breadcrumb-link" href="#"><span>Apple Color LCD (00000000)</span></a></li>
                    </ol>
                </div>
            </div>
        </section>

        <section class="mb-4">
            <div class="card card-light">
                <div class="card-header d-md-flex justify-content-between align-items-center">
                    <h5 class="text-primary mb-0">{{ $item->name }} {!! $item->resultIcon !!}</h5>
                    <p class="text-body mb-0">(Performed Date: {{ $item->getTimeDisplay() }})</p>
                </div>
                <div class="card-body cd2">
                    <div class="row gy-4 row-cols-2 row-cols-md-4">
                        @foreach ($item->header as $name => $value)
                            <div class="col">
                                <h6 class="text-primary mb-0">{{ $name !== 'Serial Number' ? $name : 'Display Serial Number' }}</h6>
                                <p class="mb-0">{{ $value }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        @include('histories.partials.step-group', [
            'steps' => $indexedSteps,
        ])

        @include('histories.partials.step-group', [
            'steps' => $commentSteps,
        ])

        @include('histories.partials.step-group', [
            'steps' => $targetResultSteps,
        ])

        <section class="mb-4">
            <div class="row gy-4 row-cols-1 row-cols-xl-2">
                @include('histories.partials.step-group', [
                    'steps' => $luminanceSteps,
                    'layout' => 'grid',
                    'wrapSection' => false,
                    'gridClass' => '',
                    'titleTag' => 'h4',
                    'titleWrapClass' => 'card-header',
                ])

                @include('histories.partials.step-group', [
                    'steps' => $correctionSteps,
                    'layout' => 'grid',
                    'wrapSection' => false,
                    'gridClass' => '',
                    'titleTag' => 'h4',
                    'titleWrapClass' => 'card-header',
                ])

                @include('histories.partials.step-group', [
                    'steps' => $jndSteps,
                    'layout' => 'grid',
                    'wrapSection' => false,
                    'gridClass' => '',
                    'cardClass' => 'bg-white border rounded border-info rounded-4 pt-4',
                    'breakAfterScores' => true,
                ])

                @include('histories.partials.step-group', [
                    'steps' => $dicomSteps,
                    'layout' => 'grid',
                    'wrapSection' => false,
                    'gridClass' => '',
                    'cardClass' => 'bg-white border rounded border-info rounded-4 pt-4',
                    'breakAfterScores' => true,
                ])
            </div>
        </section>
    </div>
</main>

<script src="{{ url('js/chartjs.min.js') }}"></script>
<script src="{{ url('js/chartjs-plugin-annotation.min.js') }}"></script>
@include('common.navigations.footer')
<script>
    function printPDF() {
        var canvas = document.getElementsByTagName('canvas');
        var graph = {};
        for (var canvas of canvas) {
            if (canvas.getContext) {
                var ctx = canvas.getContext("2d");
                var myImage = canvas.toDataURL("image/png");
                graph[canvas.id] = myImage;
            }
        }
        console.log('graph', graph)
        document.getElementById('graph').value = JSON.stringify(graph);
        $("#print_pdf_form").submit();
    }
</script>
