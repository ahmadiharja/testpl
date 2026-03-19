<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\EmailTemplate;
use App\Models\User;

class RegistrationNotification extends Notification
{
    use Queueable;
    protected $user;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        //
        $this->user = $user;
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
        $user = $this->user;
        
        $email = EmailTemplate::where(['name' => 'Register',
                                        'status' => 1 ])->first();
        if ($email){
            //$url = route('activate.user', $user->activation_code);
            $url = route('activate.user', ['code' => $user->activation_code]);
            return (new MailMessage)                            
                            ->subject($email->title)                            
                            ->markdown('mail.register',['email' => $email, 'user'=>$user, 'url' => $url]);
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
