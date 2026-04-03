<form method="post" action="{{ url('update-task') }}" id="schedule_task_form" class="space-y-5 md:space-y-6">
    {{ csrf_field() }}
    <input type="hidden" name="displays" value="{{ $displays }}">
    <input type="hidden" name="workstation2" value="{{ $request->input('workstation2') }}">
    <input type="hidden" name="workgroup2" value="{{ $request->input('workgroup2') }}">
    <input type="hidden" name="facility2" value="{{ $request->input('facility2') }}">
    {{ Form::hidden('id', $task->id) }}
    @if($lockTaskType ?? false)
        <input type="hidden" name="tasktype" value="{{ $task->type ?: 'cal' }}">
        <input type="hidden" name="testpattern" value="{{ $task->testpattern ?: 'SMPTE' }}">
    @endif

    <div class="grid gap-4 md:grid-cols-2">
        @unless($lockTaskType ?? false)
            <label class="block md:col-span-2">
                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Task') }}</span>
                {{ Form::select('tasktype', $tasktype, $task->type, ['class' => 'h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 md:h-11 md:rounded-xl', 'onchange' => 'task_type(this)', 'id'=> 'tasktype', 'placeholder' => __('-- Select Task Type --')]) }}
            </label>
        @endunless

        <label class="block md:col-span-2">
            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Schedule Type') }}</span>
            {{ Form::select('scheduletype', $scheduletype, $task->schtype, ['class' => 'h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 md:h-11 md:rounded-xl', 'onchange' => 'schedule_type(this)','id' =>'scheduletype', 'placeholder' => __('-- Select Schedule Type --')]) }}
        </label>

        @unless($lockTaskType ?? false)
            <label class="block md:col-span-2" id="testpaternId" style="display:none;">
                <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Test Pattern') }}</span>
                {{ Form::select('testpattern', $testpattern, $task->testpattern, ['class' => 'h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 md:h-11 md:rounded-xl', 'placeholder' => __('-- Select Test Pattern --')]) }}
            </label>
        @endunless

        <div class="date-time-fields md:col-span-2 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 md:rounded-2xl">
            <label class="flex items-start gap-3 text-sm text-slate-700">
                <input class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/30" type="checkbox" id="formCheck-3" name="disabletask" @if ($task->disabled == 1) checked @endif>
                <span>{{ __('Disable Task') }}</span>
            </label>
            <p class="mt-3 text-sm text-slate-500">{{ __('Specify the time and date pattern when the task will be performed.') }}</p>
        </div>

        <label class="block date-time-fields">
            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Start Time') }}</span>
            {{ Form::time('starttime', $task->starttime,['class' => 'h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 md:h-11 md:rounded-xl']) }}
        </label>

        <label class="block date-time-fields date-field">
            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Start Date') }}</span>
            {{ Form::date('startdate', $task->startdatedisplay, ['class' => 'h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 md:h-11 md:rounded-xl']) }}
        </label>

        <div class="md:col-span-2" id="perform_task_box" style="display:none;">
            <div id="daily_field_box" class="space-y-3 rounded-[1.5rem] border border-slate-200 bg-white p-4 md:rounded-2xl" style="display:none;">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Daily Pattern') }}</p>
                <label class="flex items-start gap-3 text-sm text-slate-700">
                    <input class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/30" type="radio" id="formCheck-4" name="dailytask" value="1" @if ($task->daysofweek == '1;2;3;4;5;6;7' && $task->nthflag == 1) checked @endif>
                    <span>{{ __('Every day') }}</span>
                </label>
                <label class="flex items-start gap-3 text-sm text-slate-700">
                    <input class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/30" type="radio" id="formCheck-1" name="dailytask" value="2" @if ($task->daysofweek == '1;2;3;4;5' && $task->nthflag == 1) checked @endif>
                    <span>{{ __('On working days only') }}</span>
                </label>
                <div class="grid gap-3 md:grid-cols-[auto_minmax(0,1fr)] md:items-center">
                    <label class="flex items-start gap-3 text-sm text-slate-700">
                        <input class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/30" type="radio" id="formCheck-2" name="dailytask" value="3" @if ($task->everynday != null && $task->nthflag == 0) checked @endif>
                        <span>{{ __('Every') }}</span>
                    </label>
                    <div class="relative">
                        {{ Form::number('dayinmonth', $task->everynday ? $task->everynday : 2, ['class' => 'h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 pr-24 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 md:h-11 md:rounded-xl', 'id' => 'dayinmonth', 'min' => 2, 'max' => 30]) }}
                        <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-xs text-slate-400">{{ __('day (2-30)') }}</span>
                    </div>
                </div>
            </div>

            <div class="week_field_box mt-4 rounded-[1.5rem] border border-slate-200 bg-white p-4 md:rounded-2xl" style="display:none;">
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Weekly Pattern') }}</p>
                <div class="relative">
                    {{ Form::number('week', $task->everynweek ? $task->everynweek : 1, ['class' => 'h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 pr-16 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 md:h-11 md:rounded-xl', 'id' => 'week']) }}
                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-xs text-slate-400">{{ __('week') }}</span>
                </div>
            </div>

            <div class="monthly_field_box mt-4 space-y-4 rounded-[1.5rem] border border-slate-200 bg-white p-4 md:rounded-2xl" style="display:none;">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Monthly Pattern') }}</p>

                <div class="grid gap-3 md:grid-cols-[auto_minmax(0,1fr)] md:items-center">
                    <label class="flex items-start gap-3 text-sm text-slate-700">
                        <input class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/30" type="radio" id="formCheck-22a" name="rdayinmonth" value="1" @if ($task->dayofmonthdisplay != null && $task->nthflag == 1) checked @endif>
                        <span>{{ __('Day') }}</span>
                    </label>
                    {{ Form::number('dayofmonth', $task->dayofmonth ? $task->dayofmonth : 1, ['class' => 'h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 md:h-11 md:rounded-xl', 'id' => 'dayofmonth']) }}
                </div>

                <div class="grid gap-3 md:grid-cols-[auto_minmax(0,1fr)_minmax(0,1fr)] md:items-center">
                    <label class="flex items-start gap-3 text-sm text-slate-700">
                        <input class="mt-1 h-4 w-4 rounded border-slate-300 text-sky-500 focus:ring-sky-500/30" type="radio" id="formCheck-22b" name="rdayinmonth" value="2" @if ($task->weekofmonth != null && $task->nthflag == 0) checked @endif>
                        <span>{{ __('Or on the') }}</span>
                    </label>
                    {{ Form::select('week_of_month', $weekly, $task->weekofmonth, ['class' => 'h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 md:h-11 md:rounded-xl', 'id' => 'week_of_month']) }}
                    {{ Form::select('dayofweek', $dayofweek, $task->dayofweek, ['class' => 'h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 md:h-11 md:rounded-xl']) }}
                </div>

                <label class="block">
                    <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Months') }}</span>
                    {{ Form::select('monthly[]', $monthly, $task->monthesdisplay, ['class' => 'min-h-[9rem] w-full rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 md:rounded-2xl', 'multiple' => 'multiple', 'id' => 'monthly']) }}
                </label>
            </div>
        </div>

        <label class="block week_field_box md:col-span-2" style="display:none;">
            <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ __('Select Weekdays') }}</span>
            {{ Form::select('weekdays[]', $dayofweek, $task->daysofweekdisplay, ['class' => 'min-h-[9rem] w-full rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 md:rounded-2xl', 'multiple' => 'multiple', 'id' => 'weekdays']) }}
        </label>
    </div>

    <div class="flex justify-end gap-3 border-t border-slate-200 pt-5">
        <button class="rounded-2xl bg-black/5 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-black/10 md:rounded-xl md:py-2" type="button" onclick="window.closeTaskEditor && window.closeTaskEditor()">
            {{ __('Cancel') }}
        </button>
        <button class="rounded-2xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-400 md:rounded-xl md:py-2" type="submit">
            {{ __('Save Task') }}
        </button>
    </div>
</form>
