@php
$user=$user2;
@endphp
<form method="post" >
     {{csrf_field()}}
    
                                                    <div class="row gy-4">
                                                        <div class="col-12">
                                                            <div><label class="form-label fw-semibold">{{ __('Username') }}</label>
                                                                @if($user2->name!='')
            {{Form::text('name', $user->name, ['class' => 'form-control', 'placeholder' => '','required'=>'true','readonly'=>'true'])}}
            @else
            {{Form::text('name', $user->name, ['class' => 'form-control', 'placeholder' => '','required'=>'true'])}}
            @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div><label class="form-label fw-semibold">{{ __('Password') }}</label>
                                                                <input id="password" type="password" placeholder="" class="form-control" name="password" value="{{ old('password') }}" @if($user2->id=='') required @endif>
                                                               </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div><label class="form-label fw-semibold">{{ __('Retype Password') }}</label>
                                                                <input id="password-confirm" type="password" placeholder="" class="form-control" name="password_confirmation"  value="{{ old('password_confirmation') }}" @if($user2->id=='') required @endif>
                                                            </div>
                                                        </div>
                                                         <div class="col-12">
                                                             {{Form::hidden('id', $user2->id)}}
                                                            <div><label class="form-label fw-semibold">{{ __('Full Name') }}</label>
                                                               {{Form::text('fullname', $user2->fullname, ['class' => 'form-control', 'placeholder' => '','required'=>'true'])}}
                                                                </div>
                                                        </div>
                                                         <div class="col-12">
                                                            <div><label class="form-label fw-semibold">{{ __('Email') }}</label>
                                                               {{Form::email('email', $user2->email, ['class' => 'form-control', 'placeholder' => '','required'=>'true'])}}
                                                             </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div><label class="form-label fw-semibold">{{ __('User Level') }}</label>
                                                                @if($user2->name!='')
            {{Form::select('user_level', $userlevels, $user2->getRoleNames()->first(), ['class' => 'form-control', 'placeholder' => __('-- Select User Level --'),'required'=>'true'])}}
            @else
            {{Form::select('user_level', $userlevels, '', ['class' => 'form-control', 'placeholder' => __('-- Select User Level --'),'required'=>'true'])}}
            @endif
                                                            </div>
                                                        </div>
                                                         <div class="col-12">
                                                            <div><label class="form-label fw-semibold">{{ __('Facility') }}</label>
                                                            @if($user2->name!='')
                                                                {{Form::select('facility_id', $facilities, $user->facility_id, ['class' => 'form-control', 'placeholder' => __('-- Select Facility --'),'required'=>'true'])}}
                                                            @else
                                                                {{Form::select('facility_id', $facilities, null, ['class' => 'form-control', 'placeholder' => __('-- Select Facility --'), 'required'=>'true'])}}
                                                            @endif
                                                             </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="1" name="enabled" @if ($user2->enabled == 1) checked @endif />&nbsp;
                                                                <label class="form-check-label fw-semibold" for="enabled">{{ __('Enable') }}</label></div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="d-grid"><button class="btn btn-outline-info rounded-pill" type="button">{{ __('Cancel') }}</button></div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="d-grid"><button class="btn btn-info rounded-pill" type="submit">{{ __('Save') }}</button></div>
                                                        </div>
                                                    </div>
                                                </form>
