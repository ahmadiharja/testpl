<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Alert;
use Carbon\Carbon;
use App\Models\ErrorLimit;
use Monolog\Logger;
use App\Models\Workstation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DailyEmailNotification;

class DailyEmailCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alert:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily email';
    private $logger;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function getBody($facility)
    {
        $facility_cond = $facility == 0 || $facility == '' ? '1=1' : 'f.id=' . $facility;

        // TODO temp remove exclude cond
        $results = DB::select("SELECT count(wd.id) AS di_amount FROM displays AS wd
        INNER JOIN workstations AS ws ON wd.workstation_id = ws.id 
        INNER JOIN workgroups AS wg ON ws.workgroup_id = wg.id 
        INNER JOIN facilities AS f ON wg.facility_id = f.id where ${facility_cond} 
        AND wd.status=1 AND wd.connected=1 
        AND wd.deleted_at IS NULL");
     
        $total = $results[0]->di_amount;

        $results = DB::select("SELECT DISTINCT displays.id
        FROM displays 
        INNER JOIN workstations 
        ON workstations.id=displays.workstation_id 
        INNER JOIN workgroups ON workgroups.id=workstations.workgroup_id  
        INNER JOIN facilities As f ON f.id=workgroups.facility_id 
        WHERE $facility_cond AND displays.status=2 AND displays.connected=1 AND displays.deleted_at IS NULL");

        $issuedTotal = count($results);
        $okTotal = $total - $issuedTotal;

        $results = DB::select("(SELECT DISTINCT qa.name task_name, date_format( str_to_date( qa.lastrundate , '%Y-%m-%d' ) , '%d. %b %Y' ) run_dt
                    ,date_format( str_to_date( qa.nextdateFixed , '%Y-%m-%d' ) , '%d. %b %Y' ) next_run_dt
                    ,wd.serial as  wd_title
                    ,ws.name as ws_title
                    FROM qa_tasks qa 
					INNER JOIN displays wd ON qa.display_id=wd.id 
					INNER JOIN workstations ws ON ws.id=wd.workstation_id 
					INNER JOIN workgroups wg ON wg.id=ws.workgroup_id 
					INNER JOIN facilities f ON f.id=wg.facility_id 
                    WHERE $facility_cond AND wd.status=1 AND wd.connected=1 AND wd.deleted_at IS NULL AND str_to_date(qa.lastrundate,'%Y-%m-%d')=CURRENT_DATE() AND wd.is_deleted=0)
					UNION ALL
					(SELECT tm.title task_name
                    ,date_format( str_to_date( qa.startdate , '%Y-%m-%d' ) , '%d. %b %Y' ) run_dt
                    ,'' next_run_dt,wd.serial as  wd_title
                    ,ws.name as ws_title 
                    FROM tasks qa 
					INNER JOIN task_types tm ON tm.key=qa.type 
					INNER JOIN displays wd ON qa.display_id=wd.id 
					INNER JOIN workstations ws ON ws.id=wd.workstation_id 
					INNER JOIN workgroups wg ON wg.id=ws.workgroup_id 
					INNER JOIN facilities f ON f.id=wg.facility_id 
                    WHERE $facility_cond AND wd.status=1 AND wd.connected=1 AND wd.deleted_at IS NULL AND str_to_date(qa.startdate,'%Y-%m-%d')=CURRENT_DATE() AND wd.is_deleted=0)");

        $performed_tasks = '<table width=100% border=1><tr><th>Task name</th><th>Display</th><th>Workstation</th><th>Performed on</th><th>Next Performed on</th></tr>';
        foreach ($results as $task) {
            $performed_tasks .= '<tr><td>' . $task->task_name . '</td><td>' . $task->wd_title . '</td><td>' . $task->ws_title . '</td><td>' . $task->run_dt . '</td><td>' . $task->next_run_dt . '</td></tr>';
        }
        $performed_tasks .= '</table>';
        if (count($results) == 0) $performed_tasks = '';

        $issuedDisplays = '';
        $results = DB::select("(SELECT DISTINCT qa.name task_name
                    , date_format( str_to_date( qa.nextdateFixed , '%Y-%m-%d' ) , '%d. %b %Y' ) run_dt
                    ,wd.serial as wd_title
                    ,ws.name as ws_title 
                    FROM qa_tasks qa 
					INNER JOIN displays wd ON qa.display_id=wd.id 
					INNER JOIN workstations ws ON ws.id=wd.workstation_id 
					INNER JOIN workgroups wg ON wg.id=ws.workgroup_id 
					INNER JOIN facilities f ON f.id=wg.facility_id 
                    WHERE $facility_cond AND wd.connected=1 AND wd.deleted_at IS NULL AND str_to_date(qa.nextdateFixed,'%Y-%m-%d')=CURRENT_DATE() 
                    AND str_to_date(qa.lastrundate,'%Y-%m-%d')<>CURRENT_DATE())
					UNION ALL
					(SELECT tm.title task_name
                    ,date_format( str_to_date( qa.startdate , '%Y-%m-%d' ) , '%d. %b %Y' ) run_dt
                    ,wd.serial as  wd_title
                    ,ws.name as ws_title FROM tasks qa 
					INNER JOIN task_types tm ON tm.key=qa.type 
					INNER JOIN displays wd ON qa.display_id=wd.id 
					INNER JOIN workstations ws ON ws.id=wd.workstation_id 
					INNER JOIN workgroups wg ON wg.id=ws.workgroup_id 
					INNER JOIN facilities f ON f.id=wg.facility_id 
                    WHERE $facility_cond AND wd.connected=1 AND wd.deleted_at IS NULL AND str_to_date(qa.starttime,'%Y-%m-%d')=CURRENT_DATE() 
                    AND str_to_date(qa.startdate,'%Y-%m-%d')<>CURRENT_DATE())");


        $not_performed_tasks = '<table width=100% border=1><tr><th>Task name</th><th>Display</th><th>Workstation</th><th>Next performed on</th></tr>';
        foreach ($results as $task) {
            $not_performed_tasks .= '<tr><td>' . $task->task_name . '</td><td>' . $task->wd_title . '</td><td>' . $task->ws_title . '</td><td>' . $task->run_dt . '</td></tr>';
        }
        $not_performed_tasks .= '</table>';
        if (count($results) == 0) $not_performed_tasks = '';

        // get not ok display detail
        $query =  "SELECT DISTINCT displays.id, displays.errors as error,
        displays.serial as di_serial,
        displays.model as di_model,
        workstations.id , 
        workstations.name AS ws_title, 
        workgroups.name AS wg_title, 
        f.name AS fc_title,
        f.id,
        workgroups.id
        FROM displays 
        INNER JOIN workstations ON workstations.id=displays.workstation_id 
        INNER JOIN workgroups ON workgroups.id=workstations.workgroup_id 
        INNER JOIN facilities as f ON f.id=workgroups.facility_id 
        WHERE $facility_cond AND displays.status=2 AND displays.connected=1 AND displays.deleted_at IS NULL";
        
        $results = DB::select($query);

        $issuedDisplays = "<table width=100% border=1><tr><th>Serial</th><th>Model</th><th>Workstation Name</th><th>Workgroup Name</th><th>Facility</th><th>Error</th><th>Last Error Task</th></tr>";
        foreach ($results as $idi) {
            $issuedDisplays .= "<tr>";
            $this->logger->info('>>: daily email'. json_encode($idi));
            foreach ($idi as $idiKey => $idiValue) {
                if ($idiKey == "ws_title")
                    $issuedDisplays .= '<td>' . addslashes($idiValue) . '</td>';
                elseif ($idiKey == "wg_title")
                    $issuedDisplays .= '<td>' . addslashes($idiValue) . '</td>';
                elseif ($idiKey == "fc_title")
                    $issuedDisplays .= '<td>' . addslashes($idiValue) . '</td>';
                elseif ($idiKey == "di_serial")
                    $issuedDisplays .= '<td>' . addslashes($idiValue) . '</td>';
                elseif ($idiKey == "di_model")
                    $issuedDisplays .= '<td>' . addslashes($idiValue) . '</td>';
            }
            $errors = json_decode($idi->error);
            $errors = is_array($errors)? $errors: [];
            
            $issuedDisplays .= "<td>" . addslashes(join(', ', $errors)) . "</td>";
            // search for qna task faile
            $taskfail = join(', ', $errors);
            $results2 = DB::select("SELECT * FROM qa_tasks t WHERE t.display_id=" . $idi->id . " ORDER BY id DESC LIMIT 1");
            if ($results2[0]->status_id == '2') {
                $taskfail = $results2[0]->name;
            } else {
                $results3 = DB::select("SELECT m.title FROM tasks t INNER JOIN task_types m ON t.type=m.key  WHERE t.display_id=" . $idi->id . " ORDER BY t.id DESC LIMIT 1");
                $taskfail = $results3[0]->title;
            }
            $issuedDisplays .= "<td>" . addslashes($taskfail) . "</td></tr>";
        }
        $issuedDisplays .= '</table>';
        if (count($results) == 0) $issuedDisplays = '';

        $res = 'Date: ' . date('d. M Y') . "<br/>
                <ul>
				<li>{$okTotal} Displays OK</li> 
				<li>{$issuedTotal} Displays Not OK
					{$issuedDisplays}
				</li>
				<li>
					Performed Tasks
					{$performed_tasks}
				</li>
				<li>
					Not Performed Tasks
					{$not_performed_tasks}
				</li>
			   </ul>";
        return $res;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->logger = new Logger('cron');
        $filename = sprintf('%s/logs/sync_%s_%s.log', storage_path(), 'cron', date('Ymd'));
        $this->logger->pushHandler(new StreamHandler($filename, Logger::INFO));

        $alerts = Alert::where('daily_report', 1)->get();
        $this->logger->info('>>: daily email'. json_encode($alerts));
        // $ws_sent = [];
        foreach ($alerts as $alert) {
            $this->logger->info('>>: Facility'. json_encode($alert->facility->name));
            $body = $this->getBody($alert->facility->id);
            $this->logger->info('>>: email - body'. $alert->email. ' - ' .$body);
            Notification::route('mail', $alert->email)
                ->notify(new DailyEmailNotification($alert->facility->name, $body));
            
        }
    }
}
