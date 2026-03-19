<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Workstation;
use Illuminate\Support\HtmlString;
class DisconnectedNotification extends Notification
{
    use Queueable;

    private $list;
    private $daysNotConnected;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($list, $daysNotConnected = 15)
    {
        $this->list = $list;
        $this->daysNotConnected = $daysNotConnected;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $html = '<ul>';
        foreach ($this->list as $ws) {
            $html.='<li><a href="'.$ws->link.'" ><b>'.$ws->workgroup->facility->name.'/'.$ws->workgroup->name.'/'.$ws->name.'</b></a> <i>(Last connected: '.$ws->last_connected.')</i></li>';
        }
        $html .= '</ul>';
        return (new MailMessage)
                    ->line(new HtmlString('The following list of workstations is not connected more than '.$this->daysNotConnected.' days:'.$html))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
