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
        <td colspan="4" style="color: #4a86e8;"><b>All Workstations</b></td>
    </tr>
    <!-- <tr>
        <td><b>Date:</b></td>
        <td colspan="7" style="color: #4a86e8"><b>{{$from}} to {{$to}}</b></td>
    </tr> -->

    <tr>
        <!-- Display Model	Display Serial Number	Facility	Workstation	Workgroup -->
        <td class="odd subhead"><b>Display Model</b></td>
        <td class="odd subhead"><b>Display Serial Number</b></td>
        <td class="even subhead"><b>Facility</b></td>
        <td class="even subhead"><b>Workstation</b></td>
        <td class="even subhead"><b>Workgroup</b></td>

    </tr>
    @foreach($data as $i => $d)
        <tr>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d['model'] }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d['serial'] }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d['facility'] }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d['workstation'] }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d['workgroup'] }}</td>
        </tr>
    @endforeach
</table>