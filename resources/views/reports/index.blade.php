@extends('layouts.app')

@section('title')
Reports
@endsection

@section('content')
<div class="card-header">
    <h4 class="card-title"><i class="now-ui-icons education_paper"></i> Reports</h4>
</div>
<div class="card-body">
    <ul class="nav nav-pills nav-pills-primary nav-pills-icons" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#alldisplays" role="tablist">
                All Connected Displays
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#allqa" role="tablist">
                All QA Tasks
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#allcal" role="tablist">
                All Calibrations and Verifications
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#allwor" role="allwor">
                All Workstation
            </a>
        </li>
    </ul>
    <div class="tab-content tab-space tab-subcategories">
        <div class="tab-pane active" id="alldisplays">
            {!! Form::open(['action' => 'ReportsController@allDisplays', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
            <p class="small text-muted">* Export all connected displays</p>

            <div class="form-group row">
                <div class="col-sm-7">
                    <button type="submit" class="btn btn-success" export-type="excel">Export</button>
                </div>
            </div>
            {!! Form::close() !!}

        </div>
        <div class="tab-pane" id="allqa">
            {!! Form::open(['action' => 'ReportsController@qaTasks', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
            <p class="small text-muted">* Export all QA tasks in a period</p>
            <div class="form-group">
                <input type="radio" class="period_option" name="qa_period" value="0" checked data-rel-id="qa_div_period"/> <label class="col-form-label">All the times</label>
            </div>

            <div class="form-group">
                <input type="radio" class="period_option" name="qa_period" value="1" data-rel-id="qa_div_period" /> <label class="col-form-label">In a period</label>
            </div>

            <div id="qa_div_period" class="ml-5">
                <div class="form-group row">
                    <label for="exampleFormControlSelect1" class="col-sm-2 col-form-label">Date From</label>
                    <div class="col-sm-4">
                        {{Form::date('date_from', \Carbon\Carbon::today()->subWeek(),['class' => 'form-control', 'disabled' => 'disabled' ])}}
                    </div>
                </div>

                <div class="form-group row">
                    <label for="exampleFormControlSelect1" class="col-sm-2 col-form-label">Date To</label>
                    <div class="col-sm-4">
                        {{Form::date('date_to', \Carbon\Carbon::today(),['class' => 'form-control', 'disabled' => 'disabled' ])}}
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-7">
                    <button type="submit" class="btn btn-success">Export</button>
                    <button type="submit" class="btn btn-info" export-type="pdf">Export Pdf</button>
                    <input type="hidden" value="" name="export_type" />
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        
        <div class="tab-pane" id="allcal">
            {!! Form::open(['action' => 'ReportsController@calTasks', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
            <p class="small text-muted">* Export all Calibrations and Verifications tasks in a period</p>

            <div class="form-group">
                <input type="radio" class="period_option" name="cal_period" checked data-rel-id="cal_div_period" value="0" /> <label class="col-form-label">All the times</label>
            </div>

            <div class="form-group">
                <input type="radio" class="period_option" name="cal_period" data-rel-id="cal_div_period" value="1" /> <label class="col-form-label">In a period</label>
            </div>

            <div id="cal_div_period" class="ml-5">
                <div class="form-group row">
                    <label for="exampleFormControlSelect1" class="col-sm-2 col-form-label">Date From</label>
                    <div class="col-sm-4">
                        {{Form::date('date_from', \Carbon\Carbon::today()->subWeek(),['class' => 'form-control', 'disabled' => 'disabled' ])}}
                    </div>
                </div>

                <div class="form-group row">
                    <label for="exampleFormControlSelect1" class="col-sm-2 col-form-label">Date To</label>
                    <div class="col-sm-4">
                        {{Form::date('date_to', \Carbon\Carbon::today(),['class' => 'form-control', 'disabled' => 'disabled' ])}}
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-7">
                    <button type="submit" class="btn btn-success">Export</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="tab-pane" id="allwor">
            {!! Form::open(['action' => 'ReportsController@allWorkstation', 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
            <p class="small text-muted">* Export all connected workstation</p>


            <div class="form-group row">
                <div class="col-sm-7">
                    <button type="submit" class="btn btn-success">Export</button>
                    <button type="submit" class="btn btn-info" export-type="pdf">Export Pdf</button>
                    <input type="hidden" value="" name="export_type" />
                </div>
            </div>
            {!! Form::close() !!}

        </div>
    </div>
</div>
@endsection

@section('content-script')
<script>
    $(document).ready(function() {
        $('.period_option').change(function() {
            var value = $(this).val();
            var div_id = $(this).data('rel-id');
            if (value == '0') {
                $('#'+div_id).find('input').prop('disabled', true);
            } else {
                $('#'+div_id).find('input').prop('disabled', false);
            }

        });

        $('button[type="submit"]').click(function(e){
            var $this = $(this);
            var $parent = $this.parent();
            var $export_type = $parent.find('input[name="export_type"]');
            var val = $this.attr('export-type');
            $export_type.val(val);
        });
    });
</script>
@endsection