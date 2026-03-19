<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\User;
use App\EmailTemplate;


class ActivationNotification extends Notification
{
    use Queueable;

    /**
     * @var User
     */
    protected $user;
    protected $sync_password_raw;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, $sync_password_raw)
    {
        $this->user = $user;
        $this->sync_password_raw = $sync_password_raw;
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
    public function toMail($notifiable){
        
        $email = EmailTemplate::where(['name' => 'Activation',
                                        'status' => 1 ])->first();
        
        if($email){
            return (new MailMessage)                            
                            ->subject($email->title)
                            ->markdown('mail.activation',['email' => $email, 'user'=>$this->user]);
        }
        return '';
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
