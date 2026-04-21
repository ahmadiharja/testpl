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

@include('reports.partials.pdf_logos')

<table>
    <!-- <tr>
        <td><b>Report:</b></td>
        <td colspan="4" style="color: #4a86e8;"><b>All Workstations</b></td>
    </tr> -->
    <tr>
        <!-- Display Model	Display Serial Number	Facility	Workstation	Workgroup -->
        <td class="odd subhead"><b>Display</b></td>
        <td class="even subhead"><b>Workstation</b></td>
        <td class="even subhead"><b>Workgroup</b></td>
        <td class="even subhead"><b>Facility</b></td>
        <td class="even subhead"><b>Task Type</b></td>
        <td class="even subhead"><b>Schedule Type</b></td>
        <td class="even subhead"><b>Due Date</b></td>

    </tr>
    @php $i=0; @endphp
    @foreach($data as $d)
    @php
    //$d->display=json_decode($d->display);
    //if($d->display->manufacturer==null) $d->display->manufacturer='';
    //$display=$d->display->manufacturer . '  ' . $d->display->model.' ('.$d->display->serial.')';
    
    $display=$d['display_model'];

    //if($d->startdate==NULL OR $d->nextrun==NULL OR $d->nextrun==0)
    //$due_date='0';
    //else
    //$due_date=date('d/m/Y H:i', $d->nextrun);
    @endphp
        <tr>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $display }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d['workstation'] }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d['workgroup'] }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d['facility'] }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d['name'] }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d['schtype'] }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d['duedate'] }}</td>
        </tr>
        @php $i++ @endphp
    @endforeach
</table>
