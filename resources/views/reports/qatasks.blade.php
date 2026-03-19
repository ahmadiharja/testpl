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
        <td colspan="9" style="color: #4a86e8;"><b>QA Tasks</b></td>
    </tr>
    <tr>
        <td><b>Date:</b></td>
        <td colspan="9" style="color: #4a86e8"><b>{{$from}} to {{$to}}</b></td>
    </tr>
    <tr>
    </tr>
    <tr style="background-color: #4a86e8; color: #FFFFFF; font-weight: bold">
        <td>QA Task name</td>
        <td>QA test result</td>
        <td>QA test result comment</td>
        <td>Regulation name</td>
        <td>Display class</td>
        <td>Date the Test was performed</td>
        <td>Workstation name</td>
        <td>Display serial number</td>
        <td>Display model</td>
        <td>Display manufacturer</td>
    </tr>
    @foreach($data as $d)
        <tr>
            <td>{{ $d->name }}</td>
            <td>{{ $d->resultDesc }}</td>
            <td></td>
            <td>{{$d->regulationName}}</td>
            <td>{{$d->classificationName}}</td>
            <td>{{$d->performDate}}</td>
            <td>{{$d->display->workstation->name}}</td>
            <td>{{$d->display->serial}}</td>
            <td>{{$d->display->model}}</td>
            <td>{{$d->display->manufacturer}}</td>
        </tr>
    @endforeach
</table>