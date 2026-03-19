<?php

namespace App\Listeners;

use App\Events\UserActivated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\ActivationNotification;
use App\Notifications\MessageDBNotification;

class SendWelcomeEmail
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserActivated  $event
     * @return void
     */
    public function handle(UserActivated $event)
    {
        // Send registeration email
        if (!config('app.offline')) {
            $event->user->notify(new ActivationNotification($event->user, $event->user->sync_password_raw));     
        }
        
        $event->user->notify(new MessageDBNotification('edit_profile_reminder'));
        
    }
}
