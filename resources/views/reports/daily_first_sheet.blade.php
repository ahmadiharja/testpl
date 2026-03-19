<style>

    td.subhead {
        color: #FFFFFF;
        text-align: center;
        vertical-align: middle;
        border: 1px solid #FFFFFF;
        width: 10;
        wrap-text:true;
    }
    td.odd {
        background-color: #4472c4; 
       
        
    }
    td.even {
        background-color: #305496; 
        
    }

    td.content {
        vertical-align: middle;
        border: 1px solid #ccc;
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
<table cellspacing="5" cellpadding="5">
<tr>
    <td><b>Report:</b></td>
        <td colspan="7" style="color: #4a86e8;"><b>Displays Not OK</b></td>
    </tr>
    <tr>
    </tr>

    <tr>
        <td>#</td>
        <td class="odd subhead"><b>Serial</b></td>
        <td class="odd subhead"><b>Model</b></td>
        <td class="odd subhead"><b>Workstation Name</b></td>
        <td class="odd subhead"><b>Workgroup Name</b></td>
        <td class="odd subhead"><b>Facility</b></td>
        <td class="odd subhead"><b>Error</b></td>
        <td class="odd subhead"><b>Last Error Task</b></td>
    </tr>
    @foreach($data as $i => $d)
        
        <tr>
            <td>{{$i+1}}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->di_serial }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->di_model }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->ws_title }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->wg_title }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->fc_title }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->errors }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->taskfail }}</td>
        </tr>
    @endforeach
</table>