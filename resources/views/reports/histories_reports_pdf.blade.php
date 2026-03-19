<style>
    table, th, td{
        border: 1px solid black;
        border-collapse: collapse;
        width: 90%;
        font-family: sans-serif;
    }
    th, td {
        padding-top: 5px;
        padding-bottom: 5px;
        padding-left: 5px;
        padding-right: 5px;
    }
    td.subhead {
        color: black;
        text-align: left;
        vertical-align: middle;
        border: 1px solid #ccc;
        font-size: 14px;
        width: 10;
        wrap-text:true;
    }
    td.odd {
        background-color: #ccf1ff; 
    }
    td.even {
        background-color: #ccf1ff; 
    }

    td.content {
        vertical-align: middle;
        border: 1px solid #ccc;
        font-size: 14px;
        wrap-text:true;
    }
    td.header {
        background-color: #002060; 
        color: #FFFFFF;
        border: 1px solid #FFFFFF;
        text-align: center;
        vertical-align: middle;
        height: 30;
    }

    .trodd {
        background-color: #d9e1f2;
    }

    img, svg {
vertical-align: middle;
}

.badge-circle.bg-success {
background-color: rgba(39,174,96,0.2)!important;
}

.badge-circle {
--my-badge-size: 30px;
display: inline-flex;
align-items: center;
justify-content: center;
width: 30px;
height: 30px;
}

.badge-circle.bg-danger {
background-color: rgba(235,87,87,0.2)!important;
}

.rounded-circle {
border-radius: 50%!important;
}

.bg-success {
--bs-bg-opacity: 1;
}
</style>

@php
$path = $site['Site logo'];
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $img_data = file_get_contents($path);
        $logo = 'data:image/' . $type . ';base64,' . base64_encode($img_data);

$path = 'images/qubyx_logo.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $img_data = file_get_contents($path);
        $logo_qubyx = 'data:image/' . $type . ';base64,' . base64_encode($img_data);
@endphp
<img src="{{$logo_qubyx}}" style="max-width:150px; margin-bottom:20px; margin-right:30px;">
<img src="{{$logo}}" style="max-width:200px; margin-bottom:20px;">

@php
    $chunks = array_chunk($data->all(), 13);
@endphp

@foreach($chunks as $chunkIndex => $chunk)
    <table style="page-break-after: always;">
        <tr>
            <td class="odd subhead"><b>Task Name</b></td>
            <td class="even subhead"><b>Pattern</b></td>
            <td class="even subhead"><b>Display</b></td>
            <td class="even subhead"><b>Workstation</b></td>
            <td class="even subhead"><b>Workgroup</b></td>
            <td class="even subhead"><b>Performed Date/Time</b></td>
            <td class="even subhead"><b>Result</b></td>
        </tr>

        @php $i = 0; @endphp
        @foreach($chunk as $d)
            @php
                if ($d->display->manufacturer == null) $d->display->manufacturer = '';
                $display = $d->display->manufacturer . ' ' . $d->display->model . ' (' . $d->display->serial . ')';
            @endphp
            <tr>
                <td class="content {{ $i % 2 == 0 ? 'trodd' : 'treven' }}">{{ $d->name }}</td>
                <td class="content {{ $i % 2 == 0 ? 'trodd' : 'treven' }}">{{ $d->regulation }}</td>
                <td class="content {{ $i % 2 == 0 ? 'trodd' : 'treven' }}">{{ $display }}</td>
                <td class="content {{ $i % 2 == 0 ? 'trodd' : 'treven' }}">{{ $d->display->workstation->name }}</td>
                <td class="content {{ $i % 2 == 0 ? 'trodd' : 'treven' }}">{{ $d->display->workstation->workgroup->name }}</td>
                <td class="content {{ $i % 2 == 0 ? 'trodd' : 'treven' }}">{!! $d->time !!}</td>
                <td class="content {{ $i % 2 == 0 ? 'trodd' : 'treven' }}">{!! $d->resultIcon !!}</td>
            </tr>
            @php $i++; @endphp
        @endforeach
    </table>
@endforeach