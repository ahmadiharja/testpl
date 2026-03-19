<?php

namespace App\Jobs;

use Artisan;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\BuildVersionNotification;
use App\Notifications\WorkspaceNotification;

class BuildVersionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $version, $comment, $user;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($version, $comment, $user)
    {
        $this->version = $version;
        $this->comment = $comment;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $exitCode = Artisan::call('build', [
            'version' => $this->version,
            'messages' => $this->comment
        ]);
        
        // send email to user
        $this->user->notify(new BuildVersionNotification($this->version, $this->comment));
        $this->user->notify(new WorkspaceNotification([
            'category' => 'Release Builder',
            'title' => 'Release build is ready',
            'body' => 'Version '.$this->version.' finished building successfully.',
            'severity' => 'success',
            'icon' => 'package-check',
            'url' => url('site-settings?tab=release'),
            'scope' => $this->version,
        ]));

        return 'DONE';
    }
}
