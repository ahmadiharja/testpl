<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Workstation;
use Carbon\Carbon;
use App\Notifications\DisconnectedNotification;
use App\User;
use App\Alert;
use App\ErrorLimit;
use Illuminate\Support\Facades\Notification;

class DisconnectedAlertCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alert:disconnected';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set allert if more than 15 days haven`t connection to remote and send email about it.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $limit = ErrorLimit::find('days_not_connected');
        $daysNotConnected = intval($limit?$limit->value:15);
        $workstations = Workstation::whereNull('deleted_at')
            // ->whereDate('last_connected', '<=', Carbon::now()->subDays($daysNotConnected))
            ->whereRaw('DATE(last_connected) <= (NOW() - INTERVAL '.$daysNotConnected.' DAY) AND (last_send is NULL or last_send < last_connected)')
            ->get();

        $list = array();
        foreach ($workstations as $ws) {
            $list[$ws->workgroup->facility->id][] = $ws;
            $this->info($ws->name.'-'.$ws->last_connected);
            
        }
        $alerts = Alert::whereIn('facility_id', array_keys($list))->get();
        $ws_sent = [];
        foreach ($alerts as $alert) {
            Notification::route('mail', $alert->email)
            ->notify(new DisconnectedNotification($list[$alert->facility_id], $daysNotConnected));
            foreach ($list[$alert->facility_id] as $ws) {
                $ws_sent[] = $ws->id;
            }
        }
        //update last send so it does not send duplicated
        Workstation::whereIn('id', $ws_sent)->update(['last_send' => Carbon::now()]);
        //$user->notify(new DisconnectedNotification($list));
        
    }
}
