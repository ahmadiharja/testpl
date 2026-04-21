<?php 

namespace App\Observers;

use Auth;
use App\Models\Task;
use App\Models\ScheduleType;
use Carbon\Carbon;


class TaskObserver
{
    protected $zero_date;
    protected $curr; // nextrun in DateTime format
    protected $model;
    protected $lastrun;
    protected $startdate;
    protected $starttime;
    

    private function checkday($startpoint) {
       
       $cdate = Carbon::create($this->curr->year, $this->curr->month, $this->curr->day, 0, 0, 0, $this->curr->timezone);
       
       //check ndays
       $ndays = $this->model->ndays;
       $diff = $cdate->diffInDays($startpoint, false);
       if ($ndays!=-1 && $startpoint != $this->zero_date && $diff<$ndays) 
            return false;
       //check daysofweek
       $dayofweek = $this->model->dayofweek;
       if (count($dayofweek)>0 && !in_array($cdate->dayOfWeekIso, $dayofweek)) 
            return false;

        //check monthday
        $monthday = $this->model->monthday;
        if ($monthday!=-1 && !($cdate->day == $monthday || ($monthday>$cdate->daysInMonth && $cdate->day==$cdate->daysInMonth)))
            return false;

        return true;
    }

    private function checkweek($startpoint) {
        
        $cdate = Carbon::create($this->curr->year, $this->curr->month, $this->curr->day, 0, 0, 0, $this->model->timezone);
        //check nweeks
        $nweeks = $this->model->nweeks;

        if ($nweeks!=-1 && $startpoint!=$this->zero_date) {
            $t = Carbon::create($startpoint->year, $startpoint->month,$startpoint->day,0,0,0,$this->model->timezone);
            $t = $t->addDays(-1*($t->dayOfWeek-1));
            $week = $t->diffInDays($cdate) / 7;
            if (!($week==0||$week>=$nweeks)) return false;
        }

        //check month week
        $monthweek = $this->model->monthweek;
        
        if ($monthweek!=-1 && !($cdate->weekOfMonth == $monthweek)) 
            return false;
        return true;
        
    }

    private function checkmonth($startpoint) {
        $cdate = Carbon::create($this->curr->year, $this->curr->month, $this->curr->day, 0, 0, 0, $this->model->timezone);
        //check nmonthes
        $nmonthes = $this->model->nmonthes;
        if ($nmonthes!=-1 && $startpoint!=$this->zero_date && ($cdate->month-$startpoint->month + 12*($cdate->year-$startpoint->year))<$nmonthes) 
            return false;

        //check monthes
        $lmonthes = $this->model->lmonthes;
        if (count($lmonthes)>0 && !in_array($cdate->month, $lmonthes)) 
            return false;
        
       
        return true;
    }

    private function check($startpoint) {
        /*print_r($this->curr);
        $d = $this->checkday($startpoint);
        $w = $this->checkweek($startpoint);
        $m = $this->checkmonth($startpoint);

        print 'res='.$d.'-'.$w.'-'.$m;
        return $d && $w && $m;*/
        return  $this->checkday($startpoint) &&
                    $this->checkweek($startpoint) &&
                    $this->checkmonth($startpoint);
    }

    private function nextIteration($startpoint) {
        $antifreeze = 0;
        
        do
        {
            $this->movecurr(+1);
            $antifreeze++;
        }
        while(!$this->check($startpoint) && $antifreeze<2000);

        if (!$this->check($startpoint)) {
            logger()->warning('TASK_OBSERVER_NEXTITERATION_EXHAUSTED', [
                'task_id' => $this->model->id ?? null,
                'type' => $this->model->type ?? null,
                'schtype' => $this->model->schtype ?? null,
                'startdate' => $this->model->startdate ?? null,
                'starttime' => $this->model->starttime ?? null,
                'lastrun' => $this->model->lastrun ?? null,
                'daysofweek' => $this->model->daysofweek ?? null,
                'dayofmonth' => $this->model->dayofmonth ?? null,
                'weekofmonth' => $this->model->weekofmonth ?? null,
                'monthes' => $this->model->monthes ?? null,
                'everynweek' => $this->model->everynweek ?? null,
                'everynday' => $this->model->everynday ?? null,
            ]);

            return false;
        }

        $day = $this->startdate->day;
        if($this->model->schtype == ScheduleType::ANNUAL || $this->model->schtype == ScheduleType::SEMIANNUAL || $this->model->schtype == ScheduleType::QUARTERLY)
        {
            if($this->curr->day < $day)
                while($this->curr->daysInMonth > $this->curr->day && $this->curr->day < $day)
                    $this->curr = $this->curr->addDays(1);
        }

        return true;
    }

    private function movecurr($size) {
        if ($this->model->schtype == ScheduleType::ANNUAL ||  $this->model->schtype == ScheduleType::SEMIANNUAL || $this->model->schtype == ScheduleType::QUARTERLY) {
            $this->curr = $this->curr->addMonths($size);
        } else {
            $this->curr = $this->curr->addDays($size);
        }
    }

    private function specialcalc() {
        if ($this->model->schtype == ScheduleType::STARTUP) {
            
            $this->curr = $this->zero_date->copy();

            return true;
        } elseif ($this->model->schtype == ScheduleType::ONCE) {
            $this->curr = $this->model->startdatetime->copy();

            if ($this->lastrun >= $this->curr) {
                $this->curr = $this->zero_date->copy();
            }
            return true;
        }

        return false;
    }

    private function calNewTime() {
        
        if ($this->model->disabled) {
            $this->curr = $this->zero_date->copy();
            return;
        }

        // special case for ONCE and STARTUP
        if($this->specialcalc()) {
            return;
        }
        // if startdate more than curr then set curr=startdate-1
        
        if ($this->model->startdatetime > $this->curr) {
            
            $this->curr = $this->model->startdatetime->copy();
            $this->movecurr(-1);
        }

        // loop to find the next run
        
        $startpoint = $this->lastrun->copy();
        $skipped = false;
        do {
            $skipped = false;
            
            $foundNextRun = $this->nextIteration($startpoint);
            if (!$foundNextRun) {
                $this->curr = $this->zero_date->copy();
                return;
            }

            $nextPoint = $this->curr;
            
            $movedTask = false;



        } while ($skipped);
    }

    private function init($model) {
        $this->model = $model;
        $this->zero_date = Carbon::createFromTimestamp(0, $model->timezone);
        $this->lastrun  = Carbon::createFromTimestamp($model->lastrun, $model->timezone);
        $this->startdate = $this->model->startdatetime->copy();
        $this->startdate->hour = $this->startdate->minute = 0;

        $this->starttime = $this->model->startdatetime->copy();
        $this->starttime->day = 1;
        $this->starttime->month = 1;
        $this->starttime->year = 1970;

        // set time
        if ($model->lastrun == 0) {
            $this->curr = Carbon::now($model->timezone);
            $this->movecurr(-1);
        } else {    
            $this->curr = $this->lastrun->copy();
        }
        // reset time to startdatetime
        $this->curr->hour = $this->model->startdatetime->hour;
        $this->curr->minute = $this->model->startdatetime->minute;
        
    }
    /**
     * Observer event saving Task model
     *
     * @param  Task $model
     * @return Boolean
     */
    public function saving($model)
    {
        if ($model->sync == 1) return;

        try{
            // Recalculate nextrun
            $this->init($model);

            $this->calNewTime();

            if ($this->lastrun->greaterThan($this->curr)) {
                $this->curr = $this->zero_date;
            }
        /* $lastrun = $this->model->lastrun;
            if ($nextrun <= $lastrun) {
                $nextrun = 0;
            }*/

            $model->nextrun = $this->curr->getTimestamp();
        }
        catch(\Exception $exception){            
            logger()->error($exception);
            //throw $exception;
        }


    }

}
