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
<table>
    <tr>
        <td><b>Report:</b></td>
        <td colspan="9" style="color: #4a86e8;"><b>CALIBRATION REPORT</b></td>
    </tr>
    <tr>
        <td><b>Date:</b></td>
        <td colspan="9" style="color: #4a86e8"><b>{{$from}} to {{$to}}</b></td>
    </tr>
    <tr>
    </tr>
    <tr style="background-color: #4a86e8; color: #FFFFFF; font-weight: bold">
        <td>History ID</td>
        <td>Workstation</td>
        <td>Display Manufacturer</td>
        <td>Display Model</td>
        <td>Serial Number</td>
        <td>Date/Time of test</td>
        <td>Result Measured</td>

        <td>Ambient Value</td>
        <td>Calibration Type</td>
        <td>Conformance Points</td>
        <td>Result Max Luminance</td>
        <td>Result Min Luminance</td>
        <td>Result Contrast Ratio</td>
        <td>Color Temperature</td>
        <td>DeltaE ab 2000 (average)</td>
        <td>DeltaE ab 2000 (max)</td>
        <td>AAPM GSDF Deviation</td>
        <td>JND Standard Deviation</td>
        <td>JND Average</td>
        
        
        <td>Software Version</td>
        <td>LUT Interface</td>

        <td>Sensor Manufacturer</td>
        <td>Sensor Model</td>
        <td>Sensor Serial</td>
    </tr>
    @foreach($data as $d)
        <tr>
            <td>{{$d->id}}</td>
            <td>{{ $d->display->workstation->name }}</td>
            <td>{{ $d->display->preference('Model') }}</td>
            <td>{{ $d->display->preference('Manufacturer') }}</td>
            <td>{{ $d->display->serial }}</td>
            <td>{{ $d->perform_date_time }}</td>
            <td>{{ $d->result_desc }}</td>
            
            <td>{{ $d->getStepsMeasured('Ambient Value') }}</td>
            <td>{{$d->getStepsLimit('Calibration Type')? $d->getStepsLimit('Calibration Type').' ('. $d->getStepsMeasured('Calibration Type').')':'' }}</td>
            <td>{{ $d->getStepsLimit('Conformance Points') }}</td>
            <td>{{ $d->getStepsMeasured('Result Max Luminance') }}</td>
            <td>{{ $d->getStepsMeasured('Result Min Luminance') }}</td>
            <td>{{ $d->getStepsMeasured('Result Contrast Ratio') }}</td>
            <td>{{ $d->getStepsMeasured('Color Temperature') }}</td>
            <td>{{ $d->getStepsMeasured('DeltaE ab 2000 (average)') }}</td>
            <td>{{ $d->getStepsMeasured('DeltaE ab 2000 (max)') }}</td>
            <td>{{ $d->getStepsMeasured('AAPM GSDF Deviation')?$d->getStepsMeasured('AAPM GSDF Deviation'):$d->getStepsMeasured('GSDF Deviation') }}</td>
            <td>{{ $d->getStepsMeasured('JND Standard Deviation') }}</td>
            <td>{{ $d->getStepsMeasured('JND Average') }}</td>

            
            <td>{{ $d->display->app }}</td>
            <td>{{ $d->getHeader('LUT Interface') }}</td>
            <td>{{ $d->getHeader('Sensor Manufacturer') }}</td>
            <td>{{ $d->getHeader('Sensor Model') }}</td>
            <td>{{ $d->getHeader('Sensor Serial') }}</td>
        </tr>
    @endforeach
</table>