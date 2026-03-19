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

<table>
    <!-- <tr>
        <td><b>Report:</b></td>
        <td colspan="4" style="color: #4a86e8;"><b>All Workstations</b></td>
    </tr> -->
    <tr>
        <!-- Display Model	Display Serial Number	Facility	Workstation	Workgroup -->
        <td class="odd subhead"><b>Name</b></td>
        <td class="even subhead"><b>Address</b></td>
        <td class="even subhead"><b>Phone</b></td>
        <td class="even subhead"><b>Facility</b></td>

    </tr>
    @php $i=0; @endphp
    @foreach($data as $d)
    @if(!isset($d->facility->name)) @php continue; @endphp @endif
        <tr>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->name }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->address }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->phone }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->facility->name }}</td>
        </tr>
        @php $i++ @endphp
    @endforeach
</table>