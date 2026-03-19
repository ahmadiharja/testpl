<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\EmailTemplate;

class DisplayStatusChangedNotification extends Notification
{
    use Queueable;
    protected $display;
    protected $oldStatus;
    protected $newStatus;
    protected $history;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($display, $oldStatus, $newStatus, $history)
    {
        $this->display = $display;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->history = $history;
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
        // return (new MailMessage)
        //             ->line('The display '.$this->display->serial. ' change status.')
        //             ->line('Status: '.$this->newStatus)
        //             ->line('Error: '.print_r($this->display->errors, true))
        //             //->action('Notification Action', url('/'))
        //             ->line('Thank you for using our application!');

        $email = EmailTemplate::where(['name' => 'Displays',
                                        'status' => 1 ])->first();
        $aStatuses = array(1=>'Success', 2=>'Failed');
        if($email){
            return (new MailMessage)
                ->subject($email->title)
                ->markdown('mail.displaystatuschanged',['email' => $email,'newStatus' => $aStatuses[$this->newStatus], 'display'=>$this->display, 'history' => $this->history]);
        }
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
