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
        <td colspan="7" style="color: #4a86e8;"><b>All Connected Displays</b></td>
    </tr>
    <!-- <tr>
        <td><b>Date:</b></td>
        <td colspan="7" style="color: #4a86e8"><b>{{$from}} to {{$to}}</b></td>
    </tr> -->
    <tr>
    </tr>

    <tr style="height:100">
        <td colspan="8" class="header" style="height: 100"><b>EQUIPMENT</b></td>
        <td colspan="3" class="header"><b>LOCATION</b></td>
        <td colspan="9" class="header"><b>CONDITION AND QA TESTS</b></td>
        <td colspan="7" class="header"><b>FINANCIAL STATUS</b></td>
    </tr>

    <tr>
        <td class="odd subhead"><b>Display Model</b></td>
        <td class="odd subhead"><b>Display Brand</b></td>
        <td class="odd subhead"><b>Display Serial Number</b></td>
        <td class="odd subhead"><b>Inventory Number</b></td>
        <td class="odd subhead"><b>Hours used</b></td>
        <td class="odd subhead"><b></b></td>
        <td class="odd subhead"><b>Display TYPE</b></td>
        <td class="odd subhead"><b>REMARKS</b></td>

        <td class="even subhead"><b>Workgroup</b></td>
        <td class="even subhead"><b>Room</b></td>
        <td class="even subhead"><b>Workstation</b></td>

        <td class="odd subhead"><b>CONDITION</b></td>
        <td class="odd subhead"><b>Last Calibration</b></td>
        <td class="odd subhead"><b>Calibration result</b></td>
        <td class="odd subhead"><b>Brightness</b></td>
        <td class="odd subhead"><b>Last QA Contancy Test</b></td>
        <td class="odd subhead"><b>Result QA Contancy Test</b></td>
        <td class="odd subhead"><b>Date next scheduled task</b></td>
        <td class="odd subhead"><b>Task type of next Task</b></td>
        <td class="odd subhead"><b>VENDOR</b></td>
        <td class="odd subhead"><b>Warranty period</b></td>

        <td class="even subhead"><b>DATE OF PURCHASE / LEASE</b></td>
        <td class="even subhead"><b>INITIAL VALUE</b></td>
        <td class="even subhead"><b>EXPECTED VALUE AT WARRANTY PERIOD END</b></td>
        <td class="even subhead"><b>ANNUAL STRAIGHT LINE DEPRECIATION</b></td>
        <td class="even subhead"><b>MONTHLY STRAIGHT LINE DEPRECIATION</b></td>
        <td class="even subhead"><b>CURRENT VALUE</b></td>
        <td class="even subhead"><b>Expected replacement date</b></td>
    </tr>
    @foreach($data as $i => $d)
        
        <tr>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->preference('model') }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->preference('Manufacturer') }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->preference('SerialNumber') }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->preference('InventoryNumber') }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->preference('WorkHours') }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}"></td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->preference('TypeOfDisplay') }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}"></td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->workstation->workgroup->name }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->workstation ?  $d->workstation->preference('Room') : ''}}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->workstation->name }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}"></td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->lastCalibrationTask?$d->lastCalibrationTask->getTimeDisplay():''}}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->lastCalibrationTask?$d->lastCalibrationTask->resultDesc:'' }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->lastCalibrationTask?$d->lastCalibrationTask->getHeader('Brightness Level'):'' }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->lastQATask?$d->lastQATask->name:'' }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->lastQATask?$d->lastQATask->resultDesc:'' }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->nextTask ? $d->nextTask->nextrun : '' }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->nextTask ? ($d->nextTask->taskType ? $d->nextTask->taskType->title : '') : '' }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}"></td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}"></td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->purchase_date }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->initial_value }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->expected_value }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->annual_straight_line }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->monthly_straight_line }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->current_value }}</td>
            <td class="content {{ $i % 2 == 0 ? "trodd": "treven" }}">{{ $d->expected_replacement_date }}</td>
        </tr>
    @endforeach
</table>